---
title: Cloud Cost Model
diataxis: reference
owner: DevOps Lead
update_frequency: monthly
classification: optional
---

# Cloud Cost Model

## Estimated Monthly Costs (Production)

| Resource | Estimated Cost (USD) |
|---|---|
| ECS (Web + Worker) | ~$150 |
| RDS MySQL Multi-AZ | ~$300 |
| ElastiCache Redis | ~$80 |
| EFS Storage | ~$30 |
| Load Balancer | ~$25 |
| NAT Gateway | ~$35 |
| Data Transfer | ~$50 |
| **Total** | **~$670** |

## Cost Optimization Strategies

- Reserved instances for RDS (1-year term: ~30% savings)
- Auto-scaling to 0 for staging during non-business hours
- Right-sizing based on CloudWatch metrics
- S3 Lifecycle policies for backup rotation
- CloudFront caching to reduce origin load
