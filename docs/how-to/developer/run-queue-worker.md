---
title: Run Queue Workers
diataxis: how-to
owner: Developer
update_frequency: on-demand
classification: mandatory
---

# Run Queue Workers

## Local Development

```bash
# Start default worker (processes jobs synchronously)
php artisan queue:work

# Start worker for specific queue
php artisan queue:work --queue=high,default,low

# Process a single job
php artisan queue:work --once
```

## Production (Horizon)

```bash
# Monitor queue dashboard
php artisan horizon:status
php artisan horizon:pause
php artisan horizon:continue
php artisan horizon:terminate
php artisan horizon:snapshot
```

## Failed Jobs

```bash
# List failed jobs
php artisan queue:failed

# Retry specific job
php artisan queue:retry {id}

# Retry all failed jobs
php artisan queue:retry all

# Remove failed job
php artisan queue:forget {id}

# Clear all failed jobs
php artisan queue:flush
```
