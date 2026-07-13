# Laravel Filament Ecommerce

## Requirements

- PHP 8.5 or later
- Composer v2
- SQLite for local development
- MySQL and Redis for production-grade environments

## Installation

1. Download the project zip file from the repository.
1. Extract the zip file to a directory on your local machine.
1. Copy the .env.example file to .env and update the necessary variables such as database credentials and application URL.

```bash
cp .env.example .env
```

1. Install the project dependencies by running the following command in the project directory:

```bash
composer install
```

1. Generate the application key by running the following command:

```bash
php artisan key:generate
```

1. Create a database for the project and update the database credentials in the `.env` file. For the default local setup, create the SQLite file instead:

```bash
touch database/database.sqlite
```

The provided `.env.example` already uses SQLite, file sessions, array/file cache, and sync queues so Redis is not required for local development.

When you move to production, switch `.env` back to MySQL / Redis-backed services.

1. Run the database migrations to create the necessary tables by running the following command:

```bash
php artisan migrate:fresh --seed
```

1. Create a symbolic link from the public directory to the storage directory by running the following command:

```bash
php artisan storage:link
```

1. Start the development server by running the following command:

```bash
php artisan serve
```

The project is now installed and ready to use. You can access it by navigating to <http://localhost:8000/admin> in your web browser.

1. Login in admin panel by using credentials.

```text
username: value from SUPER_ADMIN_EMAIL in .env
password: secret
```

You can set a customer-specific owner account per deployment with:

```text
SUPER_ADMIN_NAME
SUPER_ADMIN_EMAIL
SUPER_ADMIN_PASSWORD_HASH
```

For faster per-customer onboarding, run the bootstrap command after migrations:

```bash
php artisan app:bootstrap-customer \
  --owner-name="Acme Owner" \
  --owner-email="owner@acme.test" \
  --site-name="Acme Store" \
  --legal-name="Acme Trading LLC" \
  --support-email="support@acme.test" \
  --timezone="Asia/Manila" \
  --locale="en_US" \
  --currency="PHP" \
  --order-prefix="ACME" \
  --allowed-payment-methods="cash,g_cash" \
  --default-payment-method="cash"
```

Or use a deployment profile file and override only what you need:

```bash
php artisan app:bootstrap-customer --profile=customer-profile.example.json
```

CLI options override profile values when both are provided.

---

## Production Deployment

### Pre-Deployment Checklist

**Environment Setup:**
- [ ] PHP 8.5+ installed and verified
- [ ] Composer v2 installed
- [ ] MySQL database created and accessible
- [ ] Redis instance running (for queue, cache, sessions)
- [ ] Application domain/URL configured
- [ ] SSL certificate installed
- [ ] Email service configured (Mail driver)
- [ ] Queue worker process manager configured (Supervisor or similar)

**Configuration Files:**
- [ ] `.env` file created with production values:
  ```
  APP_ENV=production
  APP_DEBUG=false
  DB_HOST, DB_PORT, DB_DATABASE, DB_USERNAME, DB_PASSWORD
  REDIS_HOST, REDIS_PORT
  MAIL_DRIVER, MAIL_HOST, MAIL_PORT, MAIL_USERNAME, MAIL_PASSWORD
  CACHE_DRIVER=redis
  SESSION_DRIVER=cookie
  QUEUE_CONNECTION=redis
  ```
- [ ] `APP_KEY` generated (unique per deployment)
- [ ] `SUPER_ADMIN_PASSWORD_HASH` set (if using pre-configured super admin)

**Code Deployment:**
- [ ] Latest code pulled from repository
- [ ] `.env` file configured for target environment
- [ ] Composer dependencies installed: `composer install --no-dev --optimize-autoloader`

### First-Run Commands

Execute these commands on first deployment to initialize database and configurations:

```bash
# 1. Run migrations and seed initial data
php artisan migrate:fresh --seed

# 2. Create storage link
php artisan storage:link

# 3. Bootstrap customer (either via profile or CLI options)
# Option A: Using profile file
php artisan app:bootstrap-customer --profile=deployment-profile.json

# Option B: Using CLI options
php artisan app:bootstrap-customer \
  --owner-name="Customer Owner" \
  --owner-email="owner@example.com" \
  --site-name="Customer Store" \
  --legal-name="Legal Company Name" \
  --support-email="support@example.com" \
  --timezone="UTC" \
  --locale="en_US" \
  --currency="USD" \
  --order-prefix="ORD" \
  --allowed-payment-methods="stripe,paypal" \
  --default-payment-method="stripe"

# 4. Clear and cache configuration
php artisan config:cache
php artisan route:cache
php artisan view:cache
```

### Start Queue Worker

Queue worker processes async jobs (invoice generation, notifications):

```bash
# Supervisor configuration (recommended for production)
[program:herd-ecom-queue]
process_name=%(program_name)s_%(process_num)02d
command=php /path/to/artisan queue:work redis --sleep=3 --tries=3 --timeout=90
autostart=true
autorestart=true
startsecs=10
stopwaitsecs=3600
numprocs=1
redirect_stderr=true
stdout_logfile=/var/log/herd-ecom-queue.log

# Or manually
php artisan queue:work redis --sleep=3 --tries=3
```

### Post-Deployment Verification

Test the application is working correctly:

```bash
# 1. Test admin login
curl -s https://your-domain.com/admin | grep -q "Login" && echo "Admin panel accessible" || echo "Admin panel failed"

# 2. Test database connection
php artisan tinker
> DB::connection()->getPDO() // Should succeed

# 3. Test Redis connection
php artisan tinker
> Redis::ping() // Should return "PONG"

# 4. Test queue processing
php artisan queue:work redis --once // Process one job and exit

# 5. Verify admin user exists
php artisan tinker
> App\Models\Admin::count() // Should return >= 1

# 6. Check file permissions
ls -la storage/app/private/ # Should be writable by web server
ls -la bootstrap/cache/ # Should be writable by web server
```

### Troubleshooting

**Issue: "SQLSTATE[HY000]: General error" after deployment**
- Cause: Missing migrations
- Fix: Run `php artisan migrate:fresh --seed`

**Issue: Queue jobs not processing**
- Cause: Queue worker not running or Redis unavailable
- Fix: Check Supervisor status: `sudo supervisorctl status herd-ecom-queue`
- Verify Redis: `redis-cli ping` should return "PONG"

**Issue: "Allowed memory exhausted" during migration**
- Cause: Large seeder or memory limit too low
- Fix: Run `php artisan migrate:fresh --seed` with increased PHP memory: `php -d memory_limit=512M artisan migrate:fresh --seed`

**Issue: Admin login fails with "Invalid credentials"**
- Cause: Incorrect super admin email/password hash
- Fix: Verify via: `php artisan tinker` then `App\Models\Admin::first()`

**Issue: Media uploads fail**
- Cause: Storage symlink not created or permissions wrong
- Fix: `php artisan storage:link` and verify `public/storage` points to `storage/app/public`

**Issue: CORS errors on API calls**
- Cause: API domain not configured
- Fix: Update `config/cors.php` to allow your frontend domain

---

## Feature Flags

### Branch Feature

Control multi-branch behavior via environment variable:

```bash
# Enable branch feature
BRANCH_FEATURE_ENABLED=true

# Disable branch feature (single-branch mode)
BRANCH_FEATURE_ENABLED=false
```

When disabled:
- Branch navigation hidden from admin panel
- All data treated as single-branch
- Branch columns/filters omitted
- Lower storage/performance overhead

---

## Performance Optimization

The application includes several optimizations:

**Database:**
- 15 strategic indexes on high-traffic tables
- N+1 query prevention via eager loading
- Query result caching (1-hour TTL) with Observer invalidation

**Admin Panel:**
- Async invoice generation (queue-based, not blocking UI)
- Product resource optimized (3,000 queries → 4 queries)
- Fast pagination throughout

**Monitoring:**
Monitor these metrics in production:
- Queue worker health (no stuck jobs > 5 min)
- Redis memory usage (should stay < 80% of allocated)
- Database query execution time (p95 < 200ms)
- Admin panel response time (< 500ms)

---

## Support

For deployment questions or issues:
1. Check logs: `tail -f storage/logs/laravel.log`
2. Review this section: "Troubleshooting"
3. Contact: support via issue tracker

## Production Deployment Checklist

Use this checklist for each customer deployment to reduce operator error and ensure consistent onboarding.

### Pre-Deployment Configuration

1. **Environment Variables Setup**
   - [ ] Set `APP_URL` to production domain
   - [ ] Set `APP_DEBUG=false`
   - [ ] Set `APP_ENV=production`
   - [ ] Set `SUPER_ADMIN_EMAIL` to customer's admin email
   - [ ] Set `SUPER_ADMIN_NAME` to customer's admin name
   - [ ] Generate and set `SUPER_ADMIN_PASSWORD_HASH` (use `php artisan tinker` then `Hash::make('password')`)
   - [ ] Set `APP_KEY` (generated during setup)

2. **Database Configuration**
   - [ ] Set `DB_HOST`, `DB_PORT`, `DB_DATABASE`, `DB_USERNAME`, `DB_PASSWORD` for MySQL
   - [ ] Ensure database exists and is empty before migration
   - [ ] Set timezone to match customer region

3. **Cache & Session Services**
   - [ ] Set `CACHE_DRIVER` (Redis recommended for production)
   - [ ] Set `SESSION_DRIVER=cookie` or `redis` as configured
   - [ ] Set `REDIS_HOST`, `REDIS_PORT`, `REDIS_PASSWORD` if using Redis
   - [ ] Test Redis connection: `php artisan tinker` → `Redis::ping()`

4. **Queue & Mail Services**
   - [ ] Set `QUEUE_CONNECTION` (Redis or database for background jobs)
   - [ ] Set `MAIL_DRIVER`, `MAIL_HOST`, `MAIL_PORT`, `MAIL_USERNAME`, `MAIL_PASSWORD`
   - [ ] Configure `MAIL_FROM_ADDRESS` for customer branding
   - [ ] Test mail: `php artisan tinker` → `Mail::raw('test', fn($m) => $m->to('test@example.com'))`

5. **Feature Flags**
   - [ ] Set `BRANCH_FEATURE_ENABLED` based on customer requirements
   - [ ] Verify other feature flags align with customer plan

### First-Run Deployment

1. **Execute Migrations and Seeding**
   ```bash
   php artisan migrate --force
   php artisan db:seed --class=Database\\Seeders\\DatabaseSeeder
   ```

2. **Bootstrap Customer Account**
   ```bash
   php artisan app:bootstrap-customer \
     --owner-name="Customer Name" \
     --owner-email="owner@customer.test" \
     --site-name="Customer Store" \
     --legal-name="Customer Legal LLC" \
     --support-email="support@customer.test" \
     --timezone="Customer/Timezone" \
     --locale="en_US" \
     --currency="USD" \
     --order-prefix="CUST"
   ```
   Or use profile file: `php artisan app:bootstrap-customer --profile=customer.json`

3. **Link Storage**
   ```bash
   php artisan storage:link
   ```

4. **Clear Caches**
   ```bash
   php artisan config:clear
   php artisan cache:clear
   php artisan route:clear
   php artisan view:clear
   ```

5. **Set File Permissions**
   ```bash
   chmod -R 775 storage bootstrap/cache
   chown -R www-data:www-data storage bootstrap/cache
   ```

### Post-Deployment Verification

1. **Admin Access**
   - [ ] Login to `/admin` with SUPER_ADMIN_EMAIL and generated password
   - [ ] Verify Dashboard displays without errors
   - [ ] Check that all navigation items are visible (or respecting branch feature flag)

2. **Core Features**
   - [ ] Create a test product with SKUs
   - [ ] Create a test customer
   - [ ] Create a test order and verify order flow
   - [ ] Delete and restore a test product (verify soft-delete works)
   - [ ] Verify media gallery displays correctly

3. **Branch Feature Flag Verification** (if `BRANCH_FEATURE_ENABLED=true`)
   - [ ] Verify Branch resource is accessible in navigation
   - [ ] Create and assign a branch
   - [ ] Verify branch filters appear in product/order lists
   - [ ] Verify multi-tenant data isolation

4. **Health Checks**
   - [ ] Verify database connection: `php artisan tinker` → `DB::connection()->getPdo()`
   - [ ] Verify Redis connection (if configured): `php artisan tinker` → `Redis::ping()`
   - [ ] Verify mail service: Check MAIL_FROM_ADDRESS is set and valid
   - [ ] Monitor queue jobs: `php artisan queue:work` (test one job execution)

5. **Performance Baseline**
   - [ ] Load products page - should complete in <500ms
   - [ ] Search functionality - should return results in <200ms
   - [ ] Order creation flow - should complete in <1s
   - [ ] Generate invoice - should queue without blocking UI

### Rollback Plan

If critical issues occur:

1. **Restore from Database Backup**
   ```bash
   mysql -u user -p database < backup.sql
   ```

2. **Restore from Filesystem Backup**
   ```bash
   rsync -av backup/storage/ storage/
   ```

3. **Clear All Caches**
   ```bash
   php artisan cache:flush
   php artisan config:clear
   ```

4. **Verify Rollback**
   - [ ] Login to admin panel succeeds
   - [ ] Orders and customers are restored
   - [ ] Products display correctly

### Support & Troubleshooting

- **Login fails**: Verify SUPER_ADMIN_EMAIL and SUPER_ADMIN_PASSWORD_HASH in .env
- **502 Bad Gateway**: Check PHP-FPM/application server logs: `tail -f storage/logs/laravel.log`
- **Slow queries**: Enable query logging and check database indexes (see performance-roadmap for optimization)
- **Mail not sending**: Verify credentials with email provider and test with `php artisan tinker`
- **Jobs not processing**: Verify queue connection is configured and queue worker is running

### Post-Deployment Support

1. **Monitor Logs**
   - [ ] Set up log aggregation (Sentry, Loggly, etc.)
   - [ ] Configure alerts for errors and warnings

2. **Schedule Regular Tasks**
   - [ ] Run `php artisan schedule:work` or add to crontab for scheduled commands
   - [ ] Backup database daily
   - [ ] Backup storage directory daily

3. **Document Customer-Specific Settings**
   - [ ] Save profile JSON file for future deployments
   - [ ] Document any custom feature flag settings
   - [ ] Record timezone and locale choices

