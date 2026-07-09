---
title: Docker Development Setup
diataxis: tutorial
prerequisites:
  - tutorials/developer/setup-local-env.md
owner: DevOps Lead
update_frequency: on-change
classification: mandatory
---

# Docker Development Setup

## Learning Objectives

By the end of this tutorial, you will:
- Understand the Docker setup for local development
- Build and run the application in containers
- Debug common container issues

## Container Architecture

```yaml
services:
  app:
    image: apms:latest
    build:
      context: .
      dockerfile: Dockerfile
    volumes:
      - .:/var/www/html
    ports:
      - "8080:80"
    depends_on:
      - mysql
      - redis

  mysql:
    image: mysql:8.0
    environment:
      MYSQL_DATABASE: apms
      MYSQL_USER: apms
      MYSQL_PASSWORD: secret
      MYSQL_ROOT_PASSWORD: root
    ports:
      - "3307:3306"
    volumes:
      - mysql_data:/var/lib/mysql

  redis:
    image: redis:7-alpine
    ports:
      - "6380:6379"
```

## Building and Running

```bash
# Build the image
docker compose build

# Start services in background
docker compose up -d

# Run migrations
docker compose exec app php artisan migrate

# View logs
docker compose logs -f app

# Stop services
docker compose down
```

## Common Commands

```bash
# Enter container shell
docker compose exec app bash

# Run artisan commands
docker compose exec app php artisan tinker
docker compose exec app php artisan route:list

# Run tests
docker compose exec app php artisan test

# Rebuild after dependency changes
docker compose build --no-cache app
```

## Troubleshooting

| Symptom | Likely Cause | Solution |
|---|---|---|
| Connection refused (MySQL) | Container not ready | Wait, or `docker compose restart mysql` |
| Permission denied (storage) | Wrong file ownership | `docker compose exec app chown -R www-data:www-data storage` |
| Class not found | Composer not installed | `docker compose exec app composer install` |
| Migration table missing | APP_KEY not set | `docker compose exec app php artisan key:generate` |
