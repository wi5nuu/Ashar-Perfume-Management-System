---
title: View Application Logs
diataxis: how-to
owner: DevOps Lead
update_frequency: on-demand
classification: mandatory
---

# View Application Logs

## Laravel Logs

```bash
# Local development
tail -f storage/logs/laravel.log

# Production (CloudWatch)
aws logs filter-log-events \
  --log-group-name /ecs/apms-prod/web \
  --filter-pattern "ERROR" \
  --start-time $(date -d '5 minutes ago' +%s)000
```

## Nginx Access Logs

```bash
aws logs filter-log-events \
  --log-group-name /ecs/apms-prod/nginx \
  --filter-pattern "5xx" \
  --start-time $(date -d '1 hour ago' +%s)000
```

## Tail in Real Time

```bash
aws logs tail /ecs/apms-prod/web --follow
```
