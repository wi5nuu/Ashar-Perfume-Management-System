---
title: Deploy to Staging
diataxis: how-to
owner: DevOps Lead
update_frequency: per-release
classification: mandatory
---

# Deploy to Staging

```bash
# 1. Build and push image
docker build -t apms-staging:latest .
docker tag apms-staging:latest {aws-account}.dkr.ecr.{region}.amazonaws.com/apms-staging:latest
docker push {aws-account}.dkr.ecr.{region}.amazonaws.com/apms-staging:latest

# 2. Update ECS service
aws ecs update-service \
  --cluster apms-staging \
  --service web \
  --force-new-deployment

# 3. Run migrations
aws ecs execute-command \
  --cluster apms-staging \
  --task $(aws ecs list-tasks --cluster apms-staging --query 'taskArns[0]' --output text) \
  --command "php artisan migrate --force" \
  --interactive

# 4. Verify
curl -I https://staging.apms.local/health
```
