---
title: Infrastructure Diagram
diataxis: reference
standards:
  - C4 Level 2-3
  - arc42 §5
owner: DevOps Lead
update_frequency: per-infra-change
classification: mandatory
---

# Infrastructure Diagram (C4 Level 2 — Container)

```mermaid
flowchart TD
    subgraph Internet
        CDN[CDN - Cloudflare]
    end
    subgraph VPC[VPC - Production]
        subgraph ALB[Application Load Balancer]
            ALB1[ALB - HTTPS]
        end
        subgraph ECS[ECS Cluster]
            Web1[Web Container\nNginx + PHP-FPM]
            Worker1[Worker Container\nHorizon]
            Scheduler1[Scheduler Container\nphp artisan schedule:run]
        end
        subgraph Data[Data Layer]
            RDS1[(RDS MySQL 8.0\nMulti-AZ)]
            Redis1[(ElastiCache Redis\nCluster Mode)]
            EFS1[(EFS - Shared Storage\nLogs/Uploads)]
        end
    end
    Internet --> CDN --> ALB1 --> Web1
    Web1 --> RDS1
    Web1 --> Redis1
    Web1 --> EFS1
    Worker1 --> Redis1
    Worker1 --> RDS1
    Scheduler1 --> ECS
```

## Resource Specification (Production)

| Resource | Spec | Scaling |
|---|---|---|
| Web Container | 2 vCPU, 4 GB RAM | Min 2, Max 6 (CPU 70%) |
| Worker Container | 2 vCPU, 4 GB RAM | Min 1, Max 4 (Queue depth) |
| RDS Instance | db.r6g.large (2 vCPU, 16 GB) | Multi-AZ, auto-scaling storage |
| Redis | cache.r6g.large (1.3 GB) | Cluster mode, 2 shards |
| EFS | General Purpose | Bursting throughput |
