<?php

declare(strict_types=1);

namespace App\Support;

use Barryvdh\LaravelIdeHelper\Console\ModelsCommand;
use Barryvdh\LaravelIdeHelper\Contracts\ModelHookInterface;
use Illuminate\Database\Eloquent\Model;

class MyCustomHook implements ModelHookInterface
{
    public function run(ModelsCommand $command, Model $model): void
    {
        $command->unsetMethod('factory');

        $command->unsetMethod('newModelQuery');
        $command->unsetMethod('newQuery');
        $command->unsetMethod('query');

        $command->unsetMethod('onlyTrashed');
        $command->unsetMethod('withTrashed');
        $command->unsetMethod('withoutTrashed');

        $command->unsetMethod('withTrashed');

        $command->unsetMethod('role');
        $command->unsetMethod('permission');
        $command->unsetMethod('withoutPermission');
        $command->unsetMethod('withoutRole');

        $command->unsetMethod('ordered');
    }
}
