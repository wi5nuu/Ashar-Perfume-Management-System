---
title: Alert Rules
diataxis: reference
owner: DevOps Lead
update_frequency: quarterly
classification: mandatory
---

# Alert Rules

## Alert Severities

| Severity | Response Time | Channel |
|---|---|---|
| P1 — Critical | 15 min | SMS + Slack + Email |
| P2 — High | 1 hour | Slack + Email |
| P3 — Medium | 4 hours | Slack |
| P4 — Low | 24 hours | Email |

## Alert Definitions

| Alert Name | Condition | Severity | Action |
|---|---|---|---|
| Application Down | Health check fails 3 consecutive checks | P1 | Restart ECS service |
| High Error Rate | 5xx > 2% for 5 min | P1 | Auto-rollback previous deploy |
| High Latency | P95 > 3s for 10 min | P2 | Scale up ECS tasks |
| Queue Backlog | Queue depth > 500 | P2 | Scale up workers |
| DB Connection Saturation | Connections > 80% | P2 | Kill idle connections |
| Low Disk Space | Disk < 10% | P3 | Clean logs / increase EFS |
| SSL Certificate Expiry | Expires in < 14 days | P3 | Renew certificate |
| Budget Threshold | Monthly cost > 80% budget | P3 | Review resource usage |
| High Login Failures | > 20 failures in 5 min | P2 | Check for brute force |
