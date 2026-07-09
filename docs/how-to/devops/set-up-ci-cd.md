---
title: Set Up CI/CD Pipeline
diataxis: how-to
owner: DevOps Lead
update_frequency: on-change
classification: mandatory
---

# Set Up CI/CD Pipeline

## Prerequisites

- GitHub repository with `main` and `develop` branches
- AWS ECS cluster + task definitions
- Docker image repository (ECR)

## Steps

1. Create `.github/workflows/deploy.yml`:
```yaml
name: Deploy

on:
  push:
    branches: [main]

jobs:
  test:
    runs-on: ubuntu-latest
    steps:
      - uses: actions/checkout@v4
      - uses: shivammathur/setup-php@v2
        with:
          php-version: 8.2
      - run: composer install --no-interaction
      - run: php artisan test

  deploy:
    needs: test
    runs-on: ubuntu-latest
    steps:
      - uses: actions/checkout@v4
      - run: docker build -t apms:latest .
      - run: docker push {ecr-repo}/apms:latest
      - run: aws ecs update-service --cluster apms-prod --service web --force-new-deployment
```

2. Configure secrets in GitHub: `AWS_ACCESS_KEY_ID`, `AWS_SECRET_ACCESS_KEY`, `DOCKER_REGISTRY`
3. Push to `main` to trigger deployment
