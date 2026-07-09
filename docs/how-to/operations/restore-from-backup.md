---
title: Restore From Database Backup
diataxis: how-to
owner: DevOps Lead
update_frequency: on-demand
classification: mandatory
---

# Restore From Database Backup

## From RDS Snapshot

```bash
# Find latest snapshot
aws rds describe-db-snapshots \
  --db-instance-identifier apms-prod \
  --query 'DBSnapshots[-1].DBSnapshotIdentifier'

# Restore snapshot to new instance
aws rds restore-db-instance-from-db-snapshot \
  --db-instance-identifier apms-prod-restored \
  --db-snapshot-identifier apms-prod-20250601-0000

# Update .env with new RDS endpoint
# Run migrations if needed
php artisan migrate --force
```

## From Manual SQL Dump

```bash
# Restore from gzipped dump
gunzip -c backup_$(date +%Y%m%d).sql.gz | \
  mysql -h $DB_HOST -u $DB_USER -p$DB_PASSWORD $DB_DATABASE
```
