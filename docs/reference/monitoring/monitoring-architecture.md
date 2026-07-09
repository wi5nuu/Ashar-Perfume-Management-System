---
title: Monitoring Architecture
diataxis: reference
standards:
  - arc42 §6
owner: DevOps Lead
update_frequency: quarterly
classification: mandatory
---

# Monitoring Architecture

```mermaid
flowchart TD
    subgraph Sources
        App[Application Logs]
        Nginx[Nginx Logs]
        MySQL[MySQL Logs]
        System[System Metrics]
    end
    subgraph Collection
        CloudWatch[CloudWatch Agent]
        LaravelLog[Laravel Log Channel]
    end
    subgraph Storage
        CWLogs[CloudWatch Logs]
        CWMetrics[CloudWatch Metrics]
        S3Logs[S3 - Log Archive]
    end
    subgraph Visualization
        CWAlarms[CloudWatch Alarms]
        Dashboard[Custom Dashboard]
    end
    subgraph Alerting
        SNS[SNS Topic]
        Email[Email]
        Slack[Slack Webhook]
        SMS[SMS]
    end
    Sources --> Collection --> Storage --> Visualization --> Alerting
```

## Monitoring Stack

| Component | Tool | Purpose |
|---|---|---|
| Log aggregation | CloudWatch Logs | Centralized log storage |
| Metrics | CloudWatch Metrics | CPU, memory, request count, latency |
| APM | — (planned: Laravel Telescope) | Performance tracing |
| Uptime | CloudWatch Synthetics | Synthetic transaction monitoring |
| Error tracking | — (planned: Sentry) | Exception aggregation |
| Queue monitoring | Laravel Horizon | Queue status & throughput |

## Key Metrics

| Metric | Source | Alert Threshold |
|---|---|---|
| CPU Utilization | ECS | >80% for 5 min |
| Memory Utilization | ECS | >85% for 5 min |
| 5xx Error Rate | ALB | >1% for 5 min |
| P95 Response Time | ALB | >2000ms for 5 min |
| Queue Depth | Horizon | >100 for 10 min |
| DB Connections | RDS | >80% max_connections |
| Disk Usage | EFS | >85% |
