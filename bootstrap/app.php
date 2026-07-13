<?php

/*
 * Copyright (c) 2026 Trupa Technologies
 * All rights reserved.
 *
 * Developed by Boncanca Collins
 * GitHub: @iamtomc, @boncanca
 * Organization: trupabranding
 *
 * 1. Usage Permissions
 *    This software is proprietary to Trupa Technologies. The following restrictions apply:
 *    ✅ Allowed:
 *
 *     - Private use within the authorized organization.
 *     - Internal modifications.
 *     🚫 Not Allowed:
 *
 *     - Redistribution, sublicensing, or public sharing.
 *     - Commercial use outside of the authorized organization.
 * 2. Disclaimer of Warranty
 *    This software is provided "as is", without any warranty of any kind, express or implied, including but not limited to:
 *
 *     - Merchantability
 *     - Fitness for a particular purpose
 *     - Non-infringement
 * 3. Liability Limitation
 *    Under no circumstances shall the author(s) or copyright holders be liable for any claims, damages, or other liabilities arising from the use of this software.
 *
 * 4. Legal Enforcement
 *    Unauthorized use, distribution, or modification is strictly prohibited and may result in legal consequences.
 *
 * 📩 For inquiries, contact: hello@trupabranding.com
 * 🌐 Official Website: https://trupabranding.com
 * 📱 GitHub Organization: https://github.com/trupabranding
 */

declare(strict_types=1);

use App\Exceptions\DeletingResourceException;
use App\Http\Middleware\RequireJson;
use App\Http\Middleware\SentryUserContext;
use App\Jobs\QueueName;
use Dedoc\Scramble\Scramble;
use Filament\Facades\Filament;
use Illuminate\Auth\Console\ClearResetsCommand;
use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Database\Console\PruneCommand as ModelPrune;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Foundation\Console\AboutCommand;
use Illuminate\Queue\Console\PruneBatchesCommand;
use Laravel\Horizon\Console\SnapshotCommand as HorizonSnapshotCommand;
use Laravel\Sanctum\Console\Commands\PruneExpired as SanctumPruneExpired;
use League\Flysystem\UnableToRetrieveMetadata;
use Livewire\Features\SupportConsoleCommands\Commands\S3CleanupCommand as LivewireS3CleanupCommand;
use Lloricode\SpatieImageOptimizerHealthCheck\ImageOptimizerCheck;
use Lloricode\SpatieImageOptimizerHealthCheck\Optimizer;
use Illuminate\Support\Facades\Route;
use Sentry\Laravel\Integration;
use Spatie\Backup\Commands\MonitorCommand as SpatieBackUpMonitor;
use Spatie\CpuLoadHealthCheck\CpuLoadCheck;
use Spatie\Health\Checks\Checks\CacheCheck;
use Spatie\Health\Checks\Checks\DatabaseCheck;
use Spatie\Health\Checks\Checks\DatabaseConnectionCountCheck;
use Spatie\Health\Checks\Checks\DebugModeCheck;
use Spatie\Health\Checks\Checks\EnvironmentCheck;
use Spatie\Health\Checks\Checks\HorizonCheck;
use Spatie\Health\Checks\Checks\OptimizedAppCheck;
use Spatie\Health\Checks\Checks\QueueCheck;
use Spatie\Health\Checks\Checks\RedisCheck;
use Spatie\Health\Checks\Checks\RedisMemoryUsageCheck;
use Spatie\Health\Checks\Checks\ScheduleCheck;
use Spatie\Health\Checks\Checks\UsedDiskSpaceCheck;
use Spatie\Health\Commands\DispatchQueueCheckJobsCommand as SpatieHealthDispatchQueueCheckJobsCommand;
use Spatie\Health\Commands\ScheduleCheckHeartbeatCommand as SpatieHealthScheduleCheckHeartbeatCommand;
use Spatie\Health\Facades\Health;
use Spatie\ScheduleMonitor\Models\MonitoredScheduledTaskLogItem;
use Spatie\SecurityAdvisoriesHealthCheck\SecurityAdvisoriesCheck;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use VictoRD11\SslCertificationHealthCheck\SslCertificationExpiredCheck;

$appEnv = (string) env('APP_ENV', 'production');
$useRedisApiThrottle = (bool) env(
    'API_RATE_LIMIT_REDIS',
    $appEnv === 'production' && class_exists('Redis')
);
$horizonEnabled = (bool) env('HORIZON_ENABLED', $appEnv === 'production');
$queueHealthEnabled = (bool) env('HEALTH_QUEUE_ENABLED', env('QUEUE_CONNECTION', 'database') !== 'sync');
$redisHealthEnabled = (bool) env(
    'HEALTH_REDIS_ENABLED',
    $horizonEnabled
        || env('QUEUE_CONNECTION', 'database') === 'redis'
        || env('CACHE_STORE', 'database') === 'redis'
);

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
        then: function () {

            //        Route::middleware('web')
            //            ->group(function () {
            //                Route::redirect('/', 'admin');
            //            });

            Route::group([
                'prefix' => 'admin',
            ], function () {
                Route::group(['as' => 'api.docs.v1'], function () {
                    Scramble::registerUiRoute(path: 'docs/api/v1', api: 'v1');
                    Route::group(['as' => '.json'], function () {
                        Scramble::registerJsonSpecificationRoute(path: 'docs/api/v1.json', api: 'v1');
                    });
                });
            });
        }
    )
    ->withMiddleware(function (Middleware $middleware) use ($useRedisApiThrottle) {

        $middleware
            ->redirectGuestsTo(fn () => Filament::getLoginUrl())
            ->append([
                SentryUserContext::class,
            ])
            ->api([
                RequireJson::class,
            ])
            ->throttleApi(redis: $useRedisApiThrottle)
            ->validateCsrfTokens(except: [
                'support-bubble',
            ]);

    })
    ->withExceptions(function (Exceptions $exceptions) {

        Integration::handles($exceptions);

        $exceptions
            ->dontReport([
                DeletingResourceException::class,
            ])
            ->render(function (NotFoundHttpException $e, $request) {
                if ($request->is('api/*')) {
                    return response()->json([
                        'message' => 'Record not found.',
                    ], 404);
                }
            })
            ->reportable(function (UnableToRetrieveMetadata $e) {
                abort(404, trans('File not found.'));
            });

    })
    ->withSchedule(function (Schedule $schedule) use ($horizonEnabled, $queueHealthEnabled) {

        $schedule
            ->daily()
            ->group(function (Schedule $schedule) {

                $schedule->command(LivewireS3CleanupCommand::class);
                $schedule->command(SanctumPruneExpired::class, ['--hours' => 24]);
                $schedule->command(PruneBatchesCommand::class, [
                    '--hours' => 24,
                    '--unfinished' => 24,
                    '--cancelled' => 24,
                ]);
                $schedule->command(ModelPrune::class, [
                    '--model' => MonitoredScheduledTaskLogItem::class,
                ]);

            });

        $schedule->command(ClearResetsCommand::class, ['admins'])
            ->everyFifteenMinutes();

        if ($horizonEnabled) {
            $schedule->command(HorizonSnapshotCommand::class)
                ->everyMinute();
        }

        if ($queueHealthEnabled) {
            $schedule->command(SpatieHealthDispatchQueueCheckJobsCommand::class)
                ->everyMinute();
        }

        $schedule->command(SpatieBackUpMonitor::class)
            ->at('03:00');

        // We recommend to put this command as the very last command in your schedule.
        // https://spatie.be/docs/laravel-health/available-checks/schedule
        $schedule->command(SpatieHealthScheduleCheckHeartbeatCommand::class)
            ->everyMinute();
    })
    ->booted(function () use ($horizonEnabled, $queueHealthEnabled, $redisHealthEnabled): void {
        AboutCommand::add('Disk', fn () => [
            'Default' => config()->string('filesystems.default'),
            'Filament' => config()->string('filament.default_filesystem_disk'),
            'Livewire temporary_file_upload' => config('livewire.temporary_file_upload.disk') ?? 'null',
            'Media Library' => config()->string('media-library.disk_name'),
        ]);
        $checks = [
            CacheCheck::new(),
            CpuLoadCheck::new()
                ->failWhenLoadIsHigherInTheLast5Minutes(2.0)
                ->failWhenLoadIsHigherInTheLast15Minutes(1.5),
            DatabaseCheck::new(),
            DatabaseConnectionCountCheck::new(),
            DebugModeCheck::new(),
            EnvironmentCheck::new(),
            ScheduleCheck::new(),
            SslCertificationExpiredCheck::new()
                ->url(config()->string('app.url'))
                ->warnWhenSslCertificationExpiringDay(24)
                ->failWhenSslCertificationExpiringDay(14),
            UsedDiskSpaceCheck::new(),
            OptimizedAppCheck::new(),
            SecurityAdvisoriesCheck::new(),
            ImageOptimizerCheck::new()
                ->addChecks([
                    Optimizer::JPEGOPTIM,
                    Optimizer::OPTIPNG,
                    Optimizer::PNGQUANT,
                ]),
        ];

        if ($queueHealthEnabled) {
            $checks[] = QueueCheck::new()
                ->onQueue(QueueName::sortByPriorities());
        }

        if ($redisHealthEnabled) {
            $checks[] = RedisCheck::new();
            $checks[] = RedisMemoryUsageCheck::new()
                ->failWhenAboveMb(1000);
        }

        if ($horizonEnabled) {
            $checks[] = HorizonCheck::new();
        }

        Health::checks($checks);
    })
    ->create();
