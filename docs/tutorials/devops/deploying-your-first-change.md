---
title: Deploying Your First Change
diataxis: tutorial
prerequisites:
  - tutorials/developer/setup-local-env.md
owner: DevOps Lead
update_frequency: on-change
classification: mandatory
---

# Deploying Your First Change

## Learning Objectives

By the end of this tutorial, you will:
- Understand the deployment pipeline
- Deploy a code change to staging
- Verify the deployment

## Prerequisites

- GitHub access to the repository
- AWS CLI configured with appropriate credentials
- Docker installed locally

## Step 1: Make Your Change

```bash
git checkout -b feat/my-first-deploy
# Make your code changes
git add .
git commit -m "feat: my first change"
git push -u origin feat/my-first-deploy
```

## Step 2: Create a Pull Request

1. Open GitHub and create a PR from `feat/my-first-deploy` to `develop`
2. CI will run automatically (build, test, static analysis)
3. Request a review
4. After approval, merge into `develop`

## Step 3: Automatic Staging Deploy

Merging to `develop` triggers automatic deployment to staging:
1. CI builds the Docker image
2. Pushes to ECR repository
3. Updates ECS staging service
4. Runs database migrations

## Step 4: Verify

```bash
# Check the deployment
curl -I https://staging.apms.local/health

# View running tasks
aws ecs list-tasks --cluster apms-staging

# Check logs for errors
aws logs tail /ecs/apms-staging/web --since 5m
```

## Step 5: Deploy to Production

1. Create a PR from `develop` to `main`
2. Get approval
3. Merge to `main`
4. CI deploys to production using blue-green strategy
5. Monitor CloudWatch for errors
