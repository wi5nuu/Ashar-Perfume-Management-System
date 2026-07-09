---
title: Infrastructure Choices
diataxis: explanation
owner: DevOps Lead
update_frequency: quarterly
classification: recommended
---

# Infrastructure Choices

## Why AWS ECS Instead of Kubernetes?

| Factor | ECS | Kubernetes |
|---|---|---|
| Setup complexity | Low (console/CLI + task definitions) | High (control plane, CNI, ingress) |
| Maintenance | Managed (no control plane to manage) | Requires dedicated ops time |
| Learning curve | Moderate | Steep |
| Cost | No control plane cost | Control plane nodes + overhead |
| Scaling | Auto-scaling built-in | Requires HPA + Cluster Autoscaler |
| Team expertise | Familiar | Would need training |

For a team of 3-5 engineers, ECS provides container orchestration without the operational overhead of Kubernetes. If the team grows and complexity demands increase, migration to EKS is possible.

## Why RDS Multi-AZ?

Single-AZ RDS has a recovery time of ~15 minutes during an availability zone failure. Multi-AZ:
- Synchronous standby replica in another AZ
- Automatic failover in ~60 seconds
- Same connection string (no app change)
- Backups taken from standby (no performance impact)

The 2x cost is justified by the business requirement: retail cannot afford 15 minutes of downtime.

## Why CloudFront?

A CDN caches static assets (CSS, JS, images) at edge locations close to users. Benefits:
- Faster page loads for customers in different regions
- Reduced load on the application server
- DDoS absorption at the edge
- SSL termination at the edge
