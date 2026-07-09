---
title: Operations Runbook
diataxis: reference
owner: DevOps Lead
update_frequency: quarterly
classification: mandatory
---

# Operations Runbook

## Application Restart

```bash
# Graceful restart via ECS
aws ecs update-service --cluster apms-prod --service web --force-new-deployment

# Rollback to previous task definition
aws ecs update-service --cluster apms-prod --service web --task-definition apms-web:42
```

## Database Maintenance

```bash
# Migration (production)
php artisan migrate --force

# Rollback
php artisan migrate:rollback --step=1 --force

# Seed (staging only)
php artisan db:seed --class=ProductionSeeder
```

## Cache Management

```bash
# Clear all cache
php artisan optimize:clear

# Rebuild
php artisan optimize

# Clear specific cache
php artisan cache:clear         # Application cache
php artisan config:clear        # Config cache
php artisan route:clear         # Route cache
php artisan view:clear          # Blade view cache
```

## Queue Management

```bash
# Restart all workers
php artisan queue:restart

# Clear failed jobs
php artisan queue:flush

# Retry failed jobs
php artisan queue:retry all
```

## Storage

```bash
# Create storage symlink
php artisan storage:link

# Clean temporary files
php artisan app:clean-expired-data
```
