---
title: Backup Strategy Rationale
diataxis: explanation
owner: DevOps Lead
update_frequency: quarterly
classification: recommended
---

# Backup Strategy Rationale

## Why Daily + Continuous Backups?

| Backup Type | RPO (Recovery Point Objective) | RTO (Recovery Time Objective) |
|---|---|---|
| Daily snapshot | Up to 24 hours data loss | ~30 minutes restore |
| Continuous (binlog) | ~5 seconds data loss | ~1 hour apply |

Daily snapshots alone could lose a full day of transactions. Continuous binlog backups reduce data loss to seconds. Together they meet the business requirement of < 5 minute RPO.

## Why RDS Snapshots Instead of mysqldump?

| Method | Speed | Consistency | Impact |
|---|---|---|---|
| RDS Snapshot | Minutes (EBS-level) | Crash-consistent | No performance impact |
| mysqldump | Hours (for large DB) | Transaction-consistent | Locks tables, IO spike |

RDS snapshots are nearly instant and don't affect database performance. mysqldump on a multi-GB database would cause noticeable slowdown during business hours.

## Why S3 for Upload Backups?

Uploaded files (product images, receipts) are stored on EFS and backed up to S3. S3 provides:
- 99.999999999% durability (11 9s)
- Lifecycle policies for cost-effective tiering
- Cross-region replication option
- Versioning for accidental deletion recovery

## Why Test Restores Monthly?

A backup that hasn't been tested is not a backup. Monthly restore tests verify:
- Backup files are not corrupted
- Restore procedure is documented and works
- Team members know the restore process
- RTO targets are achievable
