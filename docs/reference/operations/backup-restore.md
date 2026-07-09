---
title: Backup & Restore
diataxis: reference
owner: DevOps Lead
update_frequency: quarterly
classification: mandatory
---

# Backup & Restore

## Backup Schedule

| Data | Frequency | Retention | Location |
|---|---|---|---|
| Database | Daily | 30 days | S3 + RDS snapshot |
| Database (transaction logs) | Continuous | 7 days | RDS automated |
| Uploaded files | Daily | 30 days | S3 bucket |
| Application logs | Hourly | 14 days | S3 + CloudWatch |
| Configuration | Per change | Indefinite | Git + S3 |

## RDS Snapshots

```bash
# Manual snapshot
aws rds create-db-snapshot \
    --db-instance-identifier apms-prod \
    --db-snapshot-identifier apms-prod-$(date +%Y%m%d-%H%M)

# Restore from snapshot
aws rds restore-db-instance-from-db-snapshot \
    --db-instance-identifier apms-prod-restored \
    --db-snapshot-identifier apms-prod-20250601-0000
```

## S3 Backup

```bash
# Backup uploads to S3
aws s3 sync /path/to/storage/app/public s3://apms-backups/uploads/$(date +%Y/%m/%d)

# Restore from S3
aws s3 sync s3://apms-backups/uploads/2025/06/01 /path/to/storage/app/public
```

## Restore Test

- Full restore test: Monthly (first Saturday)
- Verification: Application boots, migrations run, data queries match
- Document results in operations log
