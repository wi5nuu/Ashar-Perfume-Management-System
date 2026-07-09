---
title: Scale an ECS Service
diataxis: how-to
owner: DevOps Lead
update_frequency: on-demand
classification: recommended
---

# Scale an ECS Service

## Manual Scaling

```bash
# Scale web service to desired count
aws ecs update-service \
  --cluster apms-prod \
  --service web \
  --desired-count 4
```

## Auto Scaling

```bash
# Register auto-scaling target
aws application-autoscaling register-scalable-target \
  --service-namespace ecs \
  --resource-id service/apms-prod/web \
  --scalable-dimension ecs:service:DesiredCount \
  --min-capacity 2 \
  --max-capacity 6

# Create scaling policy (CPU-based)
aws application-autoscaling put-scaling-policy \
  --policy-name cpu-scaling \
  --service-namespace ecs \
  --resource-id service/apms-prod/web \
  --scalable-dimension ecs:service:DesiredCount \
  --target-tracking-scaling-policy-configuration \
    TargetValue=70.0,\
    PredefinedMetricSpecification={PredefinedMetricType=ECSServiceAverageCPUUtilization}
```

## Verify

```bash
aws ecs describe-services \
  --cluster apms-prod \
  --services web \
  --query 'services[0].deployments'
```
