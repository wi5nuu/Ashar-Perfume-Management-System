---
title: Local Environment Setup
diataxis: tutorial
standards: []
owner: DevOps Engineer
update_frequency: on-change
classification: mandatory
---

# Local Environment Setup

## Prerequisites

- PHP 8.2+
- Composer 2.x
- MySQL 8.0+ or MariaDB 10.6+
- Node.js 18+ and npm
- Redis 6+
- Git

## Step 1: Clone the Repository

```bash
git clone <repository-url> apms
cd apms
```

## Step 2: Install Dependencies

```bash
composer install
npm install && npm run build
```

## Step 3: Environment Configuration

```bash
cp .env.example .env
php artisan key:generate
```

Edit `.env` with your local values:

```
DB_DATABASE=apms
DB_USERNAME=root
DB_PASSWORD=

REDIS_HOST=127.0.0.1
REDIS_PASSWORD=null
REDIS_PORT=6379
```

## Step 4: Database Setup

```bash
php artisan migrate
php artisan db:seed
```

## Step 5: Storage Links

```bash
php artisan storage:link
```

## Step 6: Start Development Server

```bash
php artisan serve
# In another terminal:
npm run dev
# In another terminal (if working with queues):
php artisan queue:work
```

## Step 7: Verify Installation

```bash
php artisan test
```

Visit `http://localhost:8000` and log in with:
- **Email:** owner@apms.test
- **Password:** password

## Troubleshooting

| Problem | Solution |
|---|---|
| `PDOException: SQLSTATE[HY000]` | Check MySQL is running and `.env` credentials are correct |
| `RuntimeException: No application encryption key` | Run `php artisan key:generate` |
| `Class not found` | Run `composer dump-autoload` |
| `npm run dev` fails | Run `npx vite` directly to see detailed errors |
