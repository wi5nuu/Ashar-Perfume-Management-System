---
title: Set Up a CloudWatch Alert
diataxis: how-to
owner: DevOps Lead
update_frequency: on-demand
classification: mandatory
---

# Set Up a CloudWatch Alert

## Via AWS Console

1. Open **CloudWatch** → **Alarms** → **Create alarm**
2. Select metric (e.g., ECS CPUUtilization)
3. Set conditions:
   - Threshold type: Static
   - Condition: CPUUtilization > 80%
   - Period: 5 minutes
4. Configure actions:
   - SNS topic: `apms-alerts`
   - Add email/Slack subscribers
5. Name the alarm and create

## Via CLI

```bash
aws cloudwatch put-metric-alarm \
  --alarm-name "apms-high-cpu" \
  --metric-name CPUUtilization \
  --namespace AWS/ECS \
  --statistic Average \
  --period 300 \
  --evaluation-periods 2 \
  --threshold 80 \
  --comparison-operator GreaterThanThreshold \
  --alarm-actions arn:aws:sns:region:account:apms-alerts
```
