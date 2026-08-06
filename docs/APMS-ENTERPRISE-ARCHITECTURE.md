# 🏢 APMS — Enterprise Architecture Blueprint
# Ashar Grosir Perfume Management System

> **Target:** Membangun sistem manajemen grosir parfum enterprise-grade dengan arsitektur terpisah antara Laravel Backend API dan Mobile App Native (Android/iOS)
>
> **Audience:** Development Team & AI Agent untuk implementasi end-to-end

---

## 📋 MASTER TABLE OF CONTENTS

| # | Section | Description |
|---|---------|-------------|
| 1 | [SYSTEM OVERVIEW](#1-system-overview) | Konsep & arsitektur sistem |
| 2 | [ARCHITECTURE DESIGN](#2-architecture-design) | Separasi Laravel-Mobile architecture |
| 3 | [LARAVEL BACKEND API](#3-laravel-backend-api) | RESTful API specification |
| 4 | [DATABASE DESIGN](#4-database-design) | Schema & migration strategy |
| 5 | [AUTHENTICATION & SECURITY](#5-authentication--security) | Auth layer & security |
| 6 | [MOBILE APP ARCHITECTURE](#6-mobile-app-architecture) | Native mobile development |
| 7 | [API DOCUMENTATION](#7-api-documentation) | API docs & versioning |
| 8 | [DEPLOYMENT STRATEGY](#8-deployment-strategy) | CI/CD & deployment |
| 9 | [TESTING STRATEGY](#9-testing-strategy) | Testing & QA |
| 10 | [MONITORING & LOGGING](#10-monitoring--logging) | Observability |

---

## 1. SYSTEM OVERVIEW

### 1.1 Apa Itu APMS?

**APMS (Ashar Grosir Perfume Management System)** adalah sistem manajemen bisnis grosir parfum enterprise-grade dengan arsitektur modern:

- **Backend:** Laravel 11 RESTful API (PHP 8.2+)
- **Mobile:** Native Android (Kotlin) & iOS (Swift)
- **Database:** MySQL 8.0+ / PostgreSQL 15+
- **Cache:** Redis 7+
- **Storage:** MinIO / S3 untuk file storage

### 1.2 Fitur Utama

**Manajemen Produk**
- Katalog produk dengan variasi (ukuran, konsentrasi)
- Manajemen stok multi-warehouse
- Barcode/QR code scanning
- Bulk upload produk (Excel/CSV)

**Transaksi & Penjualan**
- Point of Sale (POS) mobile
- Manajemen order & invoice
- Multi payment method (cash, transfer, e-wallet)
- Retur & refund

**Manajemen Customer**
- Database customer & supplier
- Credit limit & payment term
- Customer loyalty program
- WhatsApp integration untuk notifikasi

**Laporan & Analitik**
- Dashboard real-time
- Laporan penjualan (harian/bulanan/tahunan)
- Analisis profit & margin
- Export laporan (PDF/Excel)

**Multi-User & Role**
- Role-based access control (RBAC)
- Admin, Manager, Kasir, Sales
- Activity log & audit trail

---

## 2. ARCHITECTURE DESIGN

### 2.1 System Architecture Diagram


`
┌─────────────────────────────────────────────────────────────┐
│                      CLIENT LAYER                            │
├─────────────────────────────────────────────────────────────┤
│  Android App (Kotlin)  │  iOS App (Swift)  │  Web Dashboard │
│  - Jetpack Compose     │  - SwiftUI        │  - React/Vue   │
│  - Retrofit + OkHttp   │  - URLSession     │  - Axios       │
│  - Room Database       │  - CoreData       │  - LocalStorage│
└──────────────┬──────────────────┬───────────────┬───────────┘
               │                  │               │
               └──────────────────┼───────────────┘
                                  │
                          ┌───────▼────────┐
                          │   API Gateway   │
                          │   (Laravel)     │
                          │   Rate Limiting │
                          │   API Versioning│
                          └───────┬────────┘
                                  │
               ┌──────────────────┼──────────────────┐
               │                  │                  │
       ┌───────▼────────┐ ┌──────▼──────┐  ┌───────▼────────┐
       │ Authentication │ │   Business   │  │   Integration  │
       │   Service      │ │    Logic     │  │    Services    │
       │ - JWT/Sanctum  │ │   - CRUD     │  │  - WhatsApp    │
       │ - OAuth 2.0    │ │   - Business │  │  - Payment GW  │
       │ - 2FA          │ │     Rules    │  │  - Cloud Store │
       └───────┬────────┘ └──────┬──────┘  └───────┬────────┘
               │                  │                  │
               └──────────────────┼──────────────────┘
                                  │
                  ┌───────────────┼───────────────┐
                  │               │               │
          ┌───────▼────┐  ┌──────▼──────┐  ┌────▼─────┐
          │   MySQL    │  │    Redis    │  │  MinIO   │
          │  Database  │  │    Cache    │  │  Storage │
          │  (Master)  │  │   Session   │  │   Files  │
          └────────────┘  └─────────────┘  └──────────┘
`

### 2.2 Technology Stack

**Backend (Laravel API)**
`
├── Framework: Laravel 11.x
├── PHP: 8.2+
├── API Style: RESTful JSON API
├── Authentication: Laravel Sanctum / JWT
├── Database: MySQL 8.0+ / PostgreSQL 15+
├── Cache: Redis 7+
├── Queue: Redis Queue / RabbitMQ
├── Storage: MinIO / AWS S3
├── Search: Laravel Scout + Meilisearch
└── Documentation: Scribe / Swagger OpenAPI
`

**Mobile App (Android Native)**
`
├── Language: Kotlin 1.9+
├── Min SDK: 24 (Android 7.0)
├── Target SDK: 34 (Android 14)
├── UI: Jetpack Compose
├── Architecture: MVVM + Clean Architecture
├── DI: Hilt (Dagger)
├── Network: Retrofit + OkHttp
├── Local DB: Room
├── Image Loading: Coil
├── Async: Coroutines + Flow
└── Build: Gradle (Kotlin DSL)
`

**DevOps & Infrastructure**
`
├── VCS: Git + GitHub/GitLab
├── CI/CD: GitHub Actions / GitLab CI
├── Container: Docker + Docker Compose
├── Orchestration: Kubernetes (optional)
├── Monitoring: Sentry + New Relic
├── Logging: ELK Stack / Loki
└── Deployment: AWS / DigitalOcean / VPS
`

### 2.3 Communication Protocol

**API Communication**
- Protocol: HTTPS (TLS 1.3)
- Format: JSON
- Versioning: URL-based (v1, v2)
- Base URL: https://api.ashargrosir.com/v1

**Request/Response Pattern**
`json
// Request
POST /v1/orders
Authorization: Bearer {access_token}
Content-Type: application/json

{
  "customer_id": 123,
  "items": [
    {"product_id": 45, "quantity": 10, "price": 150000}
  ],
  "payment_method": "transfer"
}

// Success Response (200)
{
  "success": true,
  "message": "Order created successfully",
  "data": {
    "id": 789,
    "order_number": "ORD-20260718-001",
    "total": 1500000,
    "status": "pending"
  },
  "meta": {
    "timestamp": "2026-07-18T09:15:00Z",
    "request_id": "req_abc123"
  }
}

// Error Response (422)
{
  "success": false,
  "message": "Validation failed",
  "errors": {
    "customer_id": ["Customer not found"],
    "items.0.quantity": ["Insufficient stock"]
  },
  "meta": {
    "timestamp": "2026-07-18T09:15:00Z",
    "request_id": "req_abc123"
  }
}
`

---

## 3. LARAVEL BACKEND API

### 3.1 Project Structure

`
laravel-api/
├── app/
│   ├── Http/
│   │   ├── Controllers/
│   │   │   └── Api/
│   │   │       └── V1/
│   │   │           ├── AuthController.php
│   │   │           ├── ProductController.php
│   │   │           ├── OrderController.php
│   │   │           ├── CustomerController.php
│   │   │           └── ReportController.php
│   │   ├── Middleware/
│   │   │   ├── ApiVersion.php
│   │   │   ├── RateLimiter.php
│   │   │   └── RolePermission.php
│   │   ├── Requests/
│   │   │   └── Api/
│   │   │       └── V1/
│   │   │           ├── StoreOrderRequest.php
│   │   │           └── UpdateProductRequest.php
│   │   └── Resources/
│   │       └── Api/
│   │           └── V1/
│   │               ├── ProductResource.php
│   │               ├── OrderResource.php
│   │               └── CustomerResource.php
│   ├── Models/
│   │   ├── User.php
│   │   ├── Product.php
│   │   ├── Order.php
│   │   ├── OrderItem.php
│   │   ├── Customer.php
│   │   ├── Payment.php
│   │   └── Stock.php
│   ├── Services/
│   │   ├── OrderService.php
│   │   ├── PaymentService.php
│   │   ├── StockService.php
│   │   └── WhatsAppService.php
│   ├── Repositories/
│   │   ├── ProductRepository.php
│   │   ├── OrderRepository.php
│   │   └── CustomerRepository.php
│   └── Traits/
│       ├── ApiResponse.php
│       └── HasRoles.php
├── database/
│   ├── migrations/
│   ├── seeders/
│   └── factories/
├── routes/
│   └── api.php
├── tests/
│   ├── Feature/
│   └── Unit/
└── .env.example
`

### 3.2 API Endpoints Specification

**Authentication Endpoints**

\\\
POST   /v1/auth/register          - Register new user
POST   /v1/auth/login             - Login user
POST   /v1/auth/logout            - Logout user
POST   /v1/auth/refresh           - Refresh access token
GET    /v1/auth/me                - Get current user
POST   /v1/auth/forgot-password   - Request password reset
POST   /v1/auth/reset-password    - Reset password
POST   /v1/auth/verify-2fa        - Verify 2FA code
\\\

**Product Endpoints**

\\\
GET    /v1/products               - List all products (paginated)
POST   /v1/products               - Create new product
GET    /v1/products/{id}          - Get product detail
PUT    /v1/products/{id}          - Update product
DELETE /v1/products/{id}          - Delete product
POST   /v1/products/bulk-upload   - Bulk upload products (CSV/Excel)
GET    /v1/products/search        - Search products
POST   /v1/products/{id}/variants - Add product variant
\\\

**Order Endpoints**

\\\
GET    /v1/orders                 - List all orders
POST   /v1/orders                 - Create new order
GET    /v1/orders/{id}            - Get order detail
PUT    /v1/orders/{id}            - Update order
DELETE /v1/orders/{id}            - Cancel order
POST   /v1/orders/{id}/payment    - Record payment
POST   /v1/orders/{id}/return     - Process return
GET    /v1/orders/{id}/invoice    - Generate invoice PDF
\\\

**Customer Endpoints**

\\\
GET    /v1/customers              - List all customers
POST   /v1/customers              - Create new customer
GET    /v1/customers/{id}         - Get customer detail
PUT    /v1/customers/{id}         - Update customer
DELETE /v1/customers/{id}         - Delete customer
GET    /v1/customers/{id}/orders  - Get customer orders
GET    /v1/customers/{id}/balance - Get customer balance
\\\

**Stock Endpoints**

\\\
GET    /v1/stocks                 - List all stocks
POST   /v1/stocks/adjustment      - Stock adjustment
GET    /v1/stocks/history         - Stock history
GET    /v1/stocks/low-stock       - Low stock alert
\\\

**Report Endpoints**

\\\
GET    /v1/reports/dashboard      - Dashboard statistics
GET    /v1/reports/sales          - Sales report
GET    /v1/reports/profit         - Profit analysis
GET    /v1/reports/stock          - Stock report
POST   /v1/reports/export         - Export report (PDF/Excel)
\\\

### 3.3 Controller Example

**OrderController.php**

\\\php
<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\V1\StoreOrderRequest;
use App\Http\Resources\Api\V1\OrderResource;
use App\Services\OrderService;
use App\Traits\ApiResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class OrderController extends Controller
{
    use ApiResponse;

    public function __construct(
        private OrderService \
    ) {}

    /**
     * Display a listing of orders.
     *
     * @param Request \
     * @return JsonResponse
     */
    public function index(Request \): JsonResponse
    {
        \ = [
            'status' => \->query('status'),
            'customer_id' => \->query('customer_id'),
            'date_from' => \->query('date_from'),
            'date_to' => \->query('date_to'),
        ];

        \ = \->query('per_page', 15);
        \ = \->orderService->getOrders(\, \);

        return \->successResponse(
            OrderResource::collection(\),
            'Orders retrieved successfully'
        );
    }

    /**
     * Store a newly created order.
     *
     * @param StoreOrderRequest \
     * @return JsonResponse
     */
    public function store(StoreOrderRequest \): JsonResponse
    {
        try {
            \ = \->orderService->createOrder(\->validated());

            return \->successResponse(
                new OrderResource(\),
                'Order created successfully',
                201
            );
        } catch (\Exception \) {
            return \->errorResponse(
                'Failed to create order: ' . \->getMessage(),
                500
            );
        }
    }

    /**
     * Display the specified order.
     *
     * @param int \
     * @return JsonResponse
     */
    public function show(int \): JsonResponse
    {
        \ = \->orderService->getOrderById(\);

        if (!\) {
            return \->errorResponse('Order not found', 404);
        }

        return \->successResponse(
            new OrderResource(\),
            'Order retrieved successfully'
        );
    }

    /**
     * Update the specified order.
     *
     * @param StoreOrderRequest \
     * @param int \
     * @return JsonResponse
     */
    public function update(StoreOrderRequest \, int \): JsonResponse
    {
        try {
            \ = \->orderService->updateOrder(\, \->validated());

            if (!\) {
                return \->errorResponse('Order not found', 404);
            }

            return \->successResponse(
                new OrderResource(\),
                'Order updated successfully'
            );
        } catch (\Exception \) {
            return \->errorResponse(
                'Failed to update order: ' . \->getMessage(),
                500
            );
        }
    }

    /**
     * Cancel the specified order.
     *
     * @param int \
     * @return JsonResponse
     */
    public function destroy(int \): JsonResponse
    {
        try {
            \ = \->orderService->cancelOrder(\);

            if (!\) {
                return \->errorResponse('Order not found or cannot be cancelled', 404);
            }

            return \->successResponse(
                null,
                'Order cancelled successfully'
            );
        } catch (\Exception \) {
            return \->errorResponse(
                'Failed to cancel order: ' . \->getMessage(),
                500
            );
        }
    }
}
\\\

### 3.4 Service Layer Example

**OrderService.php**

\\\php
<?php

namespace App\Services;

use App\Models\Order;
use App\Models\OrderItem;
use App\Repositories\OrderRepository;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class OrderService
{
    public function __construct(
        private OrderRepository \,
        private StockService \,
        private WhatsAppService \
    ) {}

    /**
     * Get orders with filters and pagination.
     */
    public function getOrders(array \, int \)
    {
        return \->orderRepository->getWithFilters(\, \);
    }

    /**
     * Get order by ID.
     */
    public function getOrderById(int \): ?Order
    {
        return \->orderRepository->findById(\);
    }

    /**
     * Create new order with transaction.
     */
    public function createOrder(array \): Order
    {
        return DB::transaction(function () use (\) {
            // Generate order number
            \ = \->generateOrderNumber();

            // Calculate total
            \ = collect(\['items'])->sum(function (\) {
                return \['quantity'] * \['price'];
            });

            // Create order
            \ = Order::create([
                'order_number' => \,
                'customer_id' => \['customer_id'],
                'user_id' => auth()->id(),
                'total' => \,
                'payment_method' => \['payment_method'],
                'status' => 'pending',
                'notes' => \['notes'] ?? null,
            ]);

            // Create order items and update stock
            foreach (\['items'] as \) {
                OrderItem::create([
                    'order_id' => \->id,
                    'product_id' => \['product_id'],
                    'quantity' => \['quantity'],
                    'price' => \['price'],
                    'subtotal' => \['quantity'] * \['price'],
                ]);

                // Update stock
                \->stockService->reduceStock(
                    \['product_id'],
                    \['quantity']
                );
            }

            // Send WhatsApp notification
            \->whatsAppService->sendOrderConfirmation(\);

            Log::info('Order created', ['order_id' => \->id]);

            return \->load(['items.product', 'customer']);
        });
    }

    /**
     * Update existing order.
     */
    public function updateOrder(int \, array \): ?Order
    {
        \ = \->getOrderById(\);

        if (!\ || \->status !== 'pending') {
            return null;
        }

        return DB::transaction(function () use (\, \) {
            // Restore stock from old items
            foreach (\->items as \) {
                \->stockService->addStock(\->product_id, \->quantity);
            }

            // Delete old items
            \->items()->delete();

            // Calculate new total
            \ = collect(\['items'])->sum(function (\) {
                return \['quantity'] * \['price'];
            });

            // Update order
            \->update([
                'customer_id' => \['customer_id'],
                'total' => \,
                'payment_method' => \['payment_method'],
                'notes' => \['notes'] ?? null,
            ]);

            // Create new items and update stock
            foreach (\['items'] as \) {
                OrderItem::create([
                    'order_id' => \->id,
                    'product_id' => \['product_id'],
                    'quantity' => \['quantity'],
                    'price' => \['price'],
                    'subtotal' => \['quantity'] * \['price'],
                ]);

                \->stockService->reduceStock(
                    \['product_id'],
                    \['quantity']
                );
            }

            return \->fresh(['items.product', 'customer']);
        });
    }

    /**
     * Cancel order and restore stock.
     */
    public function cancelOrder(int \): bool
    {
        \ = \->getOrderById(\);

        if (!\ || !\in_array(\->status, ['pending', 'processing'])) {
            return false;
        }

        return DB::transaction(function () use (\) {
            // Restore stock
            foreach (\->items as \) {
                \->stockService->addStock(\->product_id, \->quantity);
            }

            // Update order status
            \->update(['status' => 'cancelled']);

            Log::info('Order cancelled', ['order_id' => \->id]);

            return true;
        });
    }

    /**
     * Generate unique order number.
     */
    private function generateOrderNumber(): string
    {
        \ = now()->format('Ymd');
        \ = Order::whereDate('created_at', now())
            ->orderBy('id', 'desc')
            ->first();

        \ = \ ? (int) substr(\->order_number, -3) + 1 : 1;

        return 'ORD-' . \ . '-' . str_pad(\, 3, '0', STR_PAD_LEFT);
    }
}
\\\

---


## 4. DATABASE DESIGN

### 4.1 Entity Relationship Diagram (ERD)

```
┌─────────────┐         ┌─────────────┐         ┌─────────────┐
│    users    │         │  customers  │         │  suppliers  │
├─────────────┤         ├─────────────┤         ├─────────────┤
│ id          │         │ id          │         │ id          │
│ name        │         │ name        │         │ name        │
│ email       │         │ phone       │         │ company     │
│ password    │         │ address     │         │ phone       │
│ role        │         │ credit_limit│         │ address     │
│ is_active   │         │ balance     │         │ email       │
└─────────────┘         └─────────────┘         └─────────────┘
       │                        │                        │
       │                        │                        │
       ▼                        ▼                        │
┌─────────────┐         ┌─────────────┐                │
│   orders    │◄────────┤order_items  │                │
├─────────────┤         ├─────────────┤                │
│ id          │         │ id          │                │
│ order_number│         │ order_id    │                │
│ customer_id │         │ product_id  │                │
│ user_id     │         │ quantity    │                │
│ total       │         │ price       │                │
│ status      │         │ subtotal    │                │
│ created_at  │         └─────────────┘                │
└─────────────┘                │                        │
       │                       │                        │
       │                       ▼                        │
       │              ┌─────────────┐                  │
       │              │  products   │◄─────────────────┘
       │              ├─────────────┤
       │              │ id          │
       │              │ sku         │
       │              │ name        │
       │              │ category    │
       │              │ price       │
       │              │ cost        │
       │              │ supplier_id │
       │              └─────────────┘
       │                     │
       ▼                     ▼
┌─────────────┐      ┌─────────────┐
│  payments   │      │   stocks    │
├─────────────┤      ├─────────────┤
│ id          │      │ id          │
│ order_id    │      │ product_id  │
│ amount      │      │ warehouse_id│
│ method      │      │ quantity    │
│ status      │      │ min_stock   │
│ paid_at     │      │ updated_at  │
└─────────────┘      └─────────────┘
```

### 4.2 Database Schema

**users table**

```sql
CREATE TABLE users (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(255) NOT NULL,
    email VARCHAR(255) UNIQUE NOT NULL,
    email_verified_at TIMESTAMP NULL,
    password VARCHAR(255) NOT NULL,
    phone VARCHAR(20),
    role ENUM('admin', 'manager', 'kasir', 'sales') DEFAULT 'kasir',
    is_active BOOLEAN DEFAULT TRUE,
    last_login_at TIMESTAMP NULL,
    remember_token VARCHAR(100),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX idx_email (email),
    INDEX idx_role (role)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
```

**customers table**

```sql
CREATE TABLE customers (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    code VARCHAR(50) UNIQUE NOT NULL,
    name VARCHAR(255) NOT NULL,
    phone VARCHAR(20),
    email VARCHAR(255),
    address TEXT,
    city VARCHAR(100),
    province VARCHAR(100),
    postal_code VARCHAR(10),
    credit_limit DECIMAL(15, 2) DEFAULT 0,
    balance DECIMAL(15, 2) DEFAULT 0,
    payment_term INT DEFAULT 0 COMMENT 'Days',
    tax_number VARCHAR(50),
    is_active BOOLEAN DEFAULT TRUE,
    notes TEXT,
    created_by BIGINT UNSIGNED,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX idx_code (code),
    INDEX idx_name (name),
    INDEX idx_phone (phone),
    FOREIGN KEY (created_by) REFERENCES users(id) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
```

**suppliers table**

```sql
CREATE TABLE suppliers (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    code VARCHAR(50) UNIQUE NOT NULL,
    name VARCHAR(255) NOT NULL,
    company VARCHAR(255),
    phone VARCHAR(20),
    email VARCHAR(255),
    address TEXT,
    city VARCHAR(100),
    province VARCHAR(100),
    postal_code VARCHAR(10),
    tax_number VARCHAR(50),
    payment_term INT DEFAULT 0,
    is_active BOOLEAN DEFAULT TRUE,
    notes TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX idx_code (code),
    INDEX idx_name (name)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
```

**products table**

```sql
CREATE TABLE products (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    sku VARCHAR(100) UNIQUE NOT NULL,
    barcode VARCHAR(100) UNIQUE,
    name VARCHAR(255) NOT NULL,
    description TEXT,
    category VARCHAR(100),
    brand VARCHAR(100),
    size VARCHAR(50),
    unit VARCHAR(20) DEFAULT 'pcs',
    supplier_id BIGINT UNSIGNED,
    cost DECIMAL(15, 2) DEFAULT 0,
    price DECIMAL(15, 2) NOT NULL,
    wholesale_price DECIMAL(15, 2),
    image VARCHAR(255),
    is_active BOOLEAN DEFAULT TRUE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX idx_sku (sku),
    INDEX idx_barcode (barcode),
    INDEX idx_name (name),
    INDEX idx_category (category),
    FOREIGN KEY (supplier_id) REFERENCES suppliers(id) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
```

**stocks table**

```sql
CREATE TABLE stocks (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    product_id BIGINT UNSIGNED NOT NULL,
    warehouse_id BIGINT UNSIGNED DEFAULT 1,
    quantity INT DEFAULT 0,
    min_stock INT DEFAULT 10,
    max_stock INT DEFAULT 1000,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    UNIQUE KEY unique_product_warehouse (product_id, warehouse_id),
    INDEX idx_quantity (quantity),
    FOREIGN KEY (product_id) REFERENCES products(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
```

**orders table**

```sql
CREATE TABLE orders (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    order_number VARCHAR(50) UNIQUE NOT NULL,
    customer_id BIGINT UNSIGNED NOT NULL,
    user_id BIGINT UNSIGNED NOT NULL,
    subtotal DECIMAL(15, 2) DEFAULT 0,
    discount DECIMAL(15, 2) DEFAULT 0,
    tax DECIMAL(15, 2) DEFAULT 0,
    total DECIMAL(15, 2) NOT NULL,
    payment_method VARCHAR(50),
    payment_status ENUM('pending', 'partial', 'paid') DEFAULT 'pending',
    status ENUM('pending', 'processing', 'completed', 'cancelled') DEFAULT 'pending',
    notes TEXT,
    invoice_date DATE,
    due_date DATE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX idx_order_number (order_number),
    INDEX idx_customer_id (customer_id),
    INDEX idx_status (status),
    INDEX idx_created_at (created_at),
    FOREIGN KEY (customer_id) REFERENCES customers(id) ON DELETE RESTRICT,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE RESTRICT
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
```

**order_items table**

```sql
CREATE TABLE order_items (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    order_id BIGINT UNSIGNED NOT NULL,
    product_id BIGINT UNSIGNED NOT NULL,
    quantity INT NOT NULL,
    price DECIMAL(15, 2) NOT NULL,
    discount DECIMAL(15, 2) DEFAULT 0,
    subtotal DECIMAL(15, 2) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    INDEX idx_order_id (order_id),
    INDEX idx_product_id (product_id),
    FOREIGN KEY (order_id) REFERENCES orders(id) ON DELETE CASCADE,
    FOREIGN KEY (product_id) REFERENCES products(id) ON DELETE RESTRICT
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
```

**payments table**

```sql
CREATE TABLE payments (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    payment_number VARCHAR(50) UNIQUE NOT NULL,
    order_id BIGINT UNSIGNED NOT NULL,
    amount DECIMAL(15, 2) NOT NULL,
    method ENUM('cash', 'transfer', 'card', 'ewallet') NOT NULL,
    reference_number VARCHAR(100),
    status ENUM('pending', 'success', 'failed') DEFAULT 'pending',
    paid_at TIMESTAMP NULL,
    notes TEXT,
    created_by BIGINT UNSIGNED,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX idx_order_id (order_id),
    INDEX idx_payment_number (payment_number),
    FOREIGN KEY (order_id) REFERENCES orders(id) ON DELETE CASCADE,
    FOREIGN KEY (created_by) REFERENCES users(id) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
```

### 4.3 Laravel Migration Example

**2026_01_01_000001_create_products_table.php**

```php
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('products', function (Blueprint $table) {
            $table->id();
            $table->string('sku', 100)->unique();
            $table->string('barcode', 100)->unique()->nullable();
            $table->string('name');
            $table->text('description')->nullable();
            $table->string('category', 100)->nullable();
            $table->string('brand', 100)->nullable();
            $table->string('size', 50)->nullable();
            $table->string('unit', 20)->default('pcs');
            $table->foreignId('supplier_id')->nullable()->constrained()->nullOnDelete();
            $table->decimal('cost', 15, 2)->default(0);
            $table->decimal('price', 15, 2);
            $table->decimal('wholesale_price', 15, 2)->nullable();
            $table->string('image')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();

            $table->index(['sku', 'barcode', 'name', 'category']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('products');
    }
};
```

---

## 5. AUTHENTICATION & SECURITY

### 5.1 Authentication Flow

**Laravel Sanctum Token-Based Authentication**

```
┌──────────────┐                                    ┌──────────────┐
│ Mobile App   │                                    │ Laravel API  │
└──────┬───────┘                                    └──────┬───────┘
       │                                                   │
       │  POST /v1/auth/login                             │
       │  { email, password }                             │
       ├──────────────────────────────────────────────────►
       │                                                   │
       │                          Validate credentials    │
       │                          Generate access token   │
       │                          (Sanctum)               │
       │                                                   │
       │  200 OK                                           │
       │  { token, user, expires_in }                     │
       ◄──────────────────────────────────────────────────┤
       │                                                   │
       │  Store token in secure storage                   │
       │  (EncryptedSharedPreferences)                    │
       │                                                   │
       │  GET /v1/products                                │
       │  Authorization: Bearer {token}                   │
       ├──────────────────────────────────────────────────►
       │                                                   │
       │                          Verify token            │
       │                          Check permissions       │
       │                          Return data             │
       │                                                   │
       │  200 OK                                           │
       │  { data: [...] }                                 │
       ◄──────────────────────────────────────────────────┤
```

### 5.2 Laravel Authentication Implementation

**AuthController.php**

```php
<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\V1\LoginRequest;
use App\Http\Requests\Api\V1\RegisterRequest;
use App\Models\User;
use App\Traits\ApiResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

class AuthController extends Controller
{
    use ApiResponse;

    /**
     * Register new user.
     */
    public function register(RegisterRequest $request): JsonResponse
    {
        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'phone' => $request->phone,
            'role' => $request->role ?? 'kasir',
        ]);

        $token = $user->createToken('mobile-app')->plainTextToken;

        return $this->successResponse([
            'user' => $user,
            'token' => $token,
            'token_type' => 'Bearer',
            'expires_in' => config('sanctum.expiration') * 60,
        ], 'User registered successfully', 201);
    }

    /**
     * Login user.
     */
    public function login(LoginRequest $request): JsonResponse
    {
        $user = User::where('email', $request->email)->first();

        if (!$user || !Hash::check($request->password, $user->password)) {
            throw ValidationException::withMessages([
                'email' => ['The provided credentials are incorrect.'],
            ]);
        }

        if (!$user->is_active) {
            return $this->errorResponse('Account is inactive', 403);
        }

        // Revoke old tokens
        $user->tokens()->delete();

        // Create new token
        $token = $user->createToken('mobile-app', ['*'], now()->addDays(30))->plainTextToken;

        // Update last login
        $user->update(['last_login_at' => now()]);

        return $this->successResponse([
            'user' => $user,
            'token' => $token,
            'token_type' => 'Bearer',
            'expires_in' => 30 * 24 * 60 * 60, // 30 days in seconds
        ], 'Login successful');
    }

    /**
     * Logout user.
     */
    public function logout(Request $request): JsonResponse
    {
        $request->user()->currentAccessToken()->delete();

        return $this->successResponse(null, 'Logged out successfully');
    }

    /**
     * Get current authenticated user.
     */
    public function me(Request $request): JsonResponse
    {
        return $this->successResponse($request->user(), 'User retrieved successfully');
    }

    /**
     * Refresh access token.
     */
    public function refresh(Request $request): JsonResponse
    {
        $user = $request->user();

        // Revoke current token
        $request->user()->currentAccessToken()->delete();

        // Create new token
        $token = $user->createToken('mobile-app', ['*'], now()->addDays(30))->plainTextToken;

        return $this->successResponse([
            'token' => $token,
            'token_type' => 'Bearer',
            'expires_in' => 30 * 24 * 60 * 60,
        ], 'Token refreshed successfully');
    }
}
```

### 5.3 Role-Based Access Control (RBAC)

**RolePermission Middleware**

```php
<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class RolePermission
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next, string ...\): Response
    {
        if (!$request->user()) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthenticated',
            ], 401);
        }

        if (!in_array($request->user()->role, \)) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized. Insufficient permissions.',
            ], 403);
        }

        return $next($request);
    }
}
```

**Routes with Role Protection (api.php)**

```php
<?php

use App\Http\Controllers\Api\V1;
use Illuminate\Support\Facades\Route;

// Public routes
Route::prefix('v1')->group(function () {
    Route::post('/auth/login', [V1\AuthController::class, 'login']);
    Route::post('/auth/register', [V1\AuthController::class, 'register']);
});

// Protected routes
Route::prefix('v1')->middleware(['auth:sanctum'])->group(function () {
    
    // Auth routes
    Route::post('/auth/logout', [V1\AuthController::class, 'logout']);
    Route::get('/auth/me', [V1\AuthController::class, 'me']);
    Route::post('/auth/refresh', [V1\AuthController::class, 'refresh']);

    // Products - all authenticated users
    Route::apiResource('products', V1\ProductController::class);

    // Orders - all authenticated users
    Route::apiResource('orders', V1\OrderController::class);
    Route::post('orders/{id}/payment', [V1\OrderController::class, 'payment']);

    // Customers - all authenticated users
    Route::apiResource('customers', V1\CustomerController::class);

    // Reports - manager and admin only
    Route::middleware(['role:admin,manager'])->prefix('reports')->group(function () {
        Route::get('/dashboard', [V1\ReportController::class, 'dashboard']);
        Route::get('/sales', [V1\ReportController::class, 'sales']);
        Route::get('/profit', [V1\ReportController::class, 'profit']);
        Route::post('/export', [V1\ReportController::class, 'export']);
    });

    // Users management - admin only
    Route::middleware(['role:admin'])->group(function () {
        Route::apiResource('users', V1\UserController::class);
        Route::post('users/{id}/activate', [V1\UserController::class, 'activate']);
        Route::post('users/{id}/deactivate', [V1\UserController::class, 'deactivate']);
    });
});
```

### 5.4 Security Best Practices

**API Rate Limiting**

```php
// app/Providers/RouteServiceProvider.php
protected function configureRateLimiting()
{
    RateLimiter::for('api', function (Request \) {
        return Limit::perMinute(60)->by(\->user()?->id ?: \->ip());
    });

    RateLimiter::for('auth', function (Request \) {
        return Limit::perMinute(5)->by(\->ip());
    });
}

// routes/api.php
Route::middleware(['throttle:auth'])->group(function () {
    Route::post('/v1/auth/login', [AuthController::class, 'login']);
});

Route::middleware(['throttle:api', 'auth:sanctum'])->group(function () {
    // Protected routes
});
```

**CORS Configuration (config/cors.php)**

```php
return [
    'paths' => ['api/*'],
    'allowed_methods' => ['*'],
    'allowed_origins' => [
        env('FRONTEND_URL', 'http://localhost:3000'),
        env('MOBILE_APP_URL', 'capacitor://localhost'),
    ],
    'allowed_origins_patterns' => [],
    'allowed_headers' => ['*'],
    'exposed_headers' => [],
    'max_age' => 0,
    'supports_credentials' => true,
];
```

**Environment Variables (.env)**

```env
# App
APP_NAME="APMS API"
APP_ENV=production
APP_KEY=base64:YOUR_APP_KEY
APP_DEBUG=false
APP_URL=https://api.ashargrosir.com

# Database
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=apms_production
DB_USERNAME=apms_user
DB_PASSWORD=YOUR_STRONG_PASSWORD

# Redis
REDIS_HOST=127.0.0.1
REDIS_PASSWORD=YOUR_REDIS_PASSWORD
REDIS_PORT=6379

# Sanctum
SANCTUM_STATEFUL_DOMAINS=ashargrosir.com,app.ashargrosir.com
SESSION_DOMAIN=.ashargrosir.com

# Storage
FILESYSTEM_DISK=s3
AWS_ACCESS_KEY_ID=YOUR_ACCESS_KEY
AWS_SECRET_ACCESS_KEY=YOUR_SECRET_KEY
AWS_DEFAULT_REGION=ap-southeast-1
AWS_BUCKET=apms-storage

# WhatsApp Integration
WHATSAPP_API_URL=https://api.whatsapp.com
WHATSAPP_API_KEY=YOUR_WHATSAPP_API_KEY
```

---


## 6. MOBILE APP ARCHITECTURE

### 6.1 Android Native Architecture

**Architecture Pattern: MVVM + Clean Architecture**

```
┌─────────────────────────────────────────────────────────┐
│                    PRESENTATION LAYER                    │
├─────────────────────────────────────────────────────────┤
│  UI (Jetpack Compose)  │  ViewModel  │  UI State/Events │
│  - Screens             │  - Business │  - State holders │
│  - Components          │    Logic    │  - Event handlers│
│  - Navigation          │  - UI State │                  │
└────────────┬────────────────────────┬───────────────────┘
             │                        │
             ▼                        ▼
┌─────────────────────────────────────────────────────────┐
│                     DOMAIN LAYER                         │
├─────────────────────────────────────────────────────────┤
│  Use Cases  │  Domain Models  │  Repository Interfaces │
│  - Business │  - Entities     │  - Contracts           │
│    Rules    │  - Value Objs   │                        │
└────────────┬────────────────────────┬───────────────────┘
             │                        │
             ▼                        ▼
┌─────────────────────────────────────────────────────────┐
│                      DATA LAYER                          │
├─────────────────────────────────────────────────────────┤
│  Repositories │  Data Sources  │  Network  │  Database  │
│  - Impl       │  - Remote API  │  - Retrofit│  - Room   │
│  - Mappers    │  - Local Cache │  - OkHttp  │  - DAO    │
└─────────────────────────────────────────────────────────┘
```

### 6.2 Android Project Structure

```
android-app/
├── app/
│   ├── src/
│   │   └── main/
│   │       ├── java/com/ashargrosir/apms/
│   │       │   ├── di/                      # Dependency Injection
│   │       │   │   ├── AppModule.kt
│   │       │   │   ├── NetworkModule.kt
│   │       │   │   └── DatabaseModule.kt
│   │       │   ├── data/                    # Data Layer
│   │       │   │   ├── local/
│   │       │   │   │   ├── dao/
│   │       │   │   │   │   ├── ProductDao.kt
│   │       │   │   │   │   └── OrderDao.kt
│   │       │   │   │   ├── entities/
│   │       │   │   │   │   ├── ProductEntity.kt
│   │       │   │   │   │   └── OrderEntity.kt
│   │       │   │   │   └── AppDatabase.kt
│   │       │   │   ├── remote/
│   │       │   │   │   ├── api/
│   │       │   │   │   │   ├── AuthApi.kt
│   │       │   │   │   │   ├── ProductApi.kt
│   │       │   │   │   │   └── OrderApi.kt
│   │       │   │   │   ├── dto/
│   │       │   │   │   │   ├── ProductDto.kt
│   │       │   │   │   │   └── OrderDto.kt
│   │       │   │   │   └── interceptors/
│   │       │   │   │       ├── AuthInterceptor.kt
│   │       │   │   │       └── ErrorInterceptor.kt
│   │       │   │   └── repository/
│   │       │   │       ├── ProductRepositoryImpl.kt
│   │       │   │       └── OrderRepositoryImpl.kt
│   │       │   ├── domain/                  # Domain Layer
│   │       │   │   ├── model/
│   │       │   │   │   ├── Product.kt
│   │       │   │   │   ├── Order.kt
│   │       │   │   │   └── Customer.kt
│   │       │   │   ├── repository/
│   │       │   │   │   ├── ProductRepository.kt
│   │       │   │   │   └── OrderRepository.kt
│   │       │   │   └── usecase/
│   │       │   │       ├── GetProductsUseCase.kt
│   │       │   │       ├── CreateOrderUseCase.kt
│   │       │   │       └── SyncDataUseCase.kt
│   │       │   ├── presentation/            # Presentation Layer
│   │       │   │   ├── auth/
│   │       │   │   │   ├── LoginScreen.kt
│   │       │   │   │   ├── LoginViewModel.kt
│   │       │   │   │   └── LoginState.kt
│   │       │   │   ├── products/
│   │       │   │   │   ├── ProductListScreen.kt
│   │       │   │   │   ├── ProductDetailScreen.kt
│   │       │   │   │   └── ProductViewModel.kt
│   │       │   │   ├── orders/
│   │       │   │   │   ├── OrderListScreen.kt
│   │       │   │   │   ├── CreateOrderScreen.kt
│   │       │   │   │   └── OrderViewModel.kt
│   │       │   │   ├── navigation/
│   │       │   │   │   └── AppNavigation.kt
│   │       │   │   └── components/
│   │       │   │       ├── LoadingDialog.kt
│   │       │   │       ├── ErrorDialog.kt
│   │       │   │       └── CustomTextField.kt
│   │       │   ├── utils/
│   │       │   │   ├── Constants.kt
│   │       │   │   ├── Extensions.kt
│   │       │   │   └── DateUtils.kt
│   │       │   └── ApmsApplication.kt
│   │       ├── res/                         # Resources
│   │       │   ├── drawable/
│   │       │   ├── values/
│   │       │   │   ├── colors.xml
│   │       │   │   ├── strings.xml
│   │       │   │   └── themes.xml
│   │       │   └── xml/
│   │       │       └── network_security_config.xml
│   │       └── AndroidManifest.xml
│   └── build.gradle.kts
├── gradle/
│   └── libs.versions.toml              # Version Catalog
└── build.gradle.kts
```

### 6.3 Gradle Configuration (Version Catalog)

**gradle/libs.versions.toml**

```toml
[versions]
kotlin = "1.9.23"
compose = "1.6.4"
composeCompiler = "1.5.11"
androidGradlePlugin = "8.3.1"
minSdk = "24"
targetSdk = "34"
compileSdk = "34"

hilt = "2.51"
retrofit = "2.9.0"
okhttp = "4.12.0"
room = "2.6.1"
coroutines = "1.8.0"
lifecycle = "2.7.0"
navigation = "2.7.7"
coil = "2.6.0"

[libraries]
# Kotlin
kotlin-stdlib = { module = "org.jetbrains.kotlin:kotlin-stdlib", version.ref = "kotlin" }
kotlinx-coroutines-core = { module = "org.jetbrains.kotlinx:kotlinx-coroutines-core", version.ref = "coroutines" }
kotlinx-coroutines-android = { module = "org.jetbrains.kotlinx:kotlinx-coroutines-android", version.ref = "coroutines" }

# Compose
compose-ui = { module = "androidx.compose.ui:ui", version.ref = "compose" }
compose-material3 = { module = "androidx.compose.material3:material3", version = "1.2.1" }
compose-ui-tooling-preview = { module = "androidx.compose.ui:ui-tooling-preview", version.ref = "compose" }
compose-ui-tooling = { module = "androidx.compose.ui:ui-tooling", version.ref = "compose" }
compose-navigation = { module = "androidx.navigation:navigation-compose", version.ref = "navigation" }

# Hilt
hilt-android = { module = "com.google.dagger:hilt-android", version.ref = "hilt" }
hilt-compiler = { module = "com.google.dagger:hilt-compiler", version.ref = "hilt" }
hilt-navigation-compose = { module = "androidx.hilt:hilt-navigation-compose", version = "1.2.0" }

# Retrofit
retrofit = { module = "com.squareup.retrofit2:retrofit", version.ref = "retrofit" }
retrofit-gson = { module = "com.squareup.retrofit2:converter-gson", version.ref = "retrofit" }
okhttp = { module = "com.squareup.okhttp3:okhttp", version.ref = "okhttp" }
okhttp-logging = { module = "com.squareup.okhttp3:logging-interceptor", version.ref = "okhttp" }

# Room
room-runtime = { module = "androidx.room:room-runtime", version.ref = "room" }
room-compiler = { module = "androidx.room:room-compiler", version.ref = "room" }
room-ktx = { module = "androidx.room:room-ktx", version.ref = "room" }

# Lifecycle
lifecycle-viewmodel-ktx = { module = "androidx.lifecycle:lifecycle-viewmodel-ktx", version.ref = "lifecycle" }
lifecycle-runtime-compose = { module = "androidx.lifecycle:lifecycle-runtime-compose", version.ref = "lifecycle" }

# Coil (Image Loading)
coil-compose = { module = "io.coil-kt:coil-compose", version.ref = "coil" }

# DataStore
datastore-preferences = { module = "androidx.datastore:datastore-preferences", version = "1.0.0" }

[plugins]
android-application = { id = "com.android.application", version.ref = "androidGradlePlugin" }
kotlin-android = { id = "org.jetbrains.kotlin.android", version.ref = "kotlin" }
hilt-android = { id = "com.google.dagger.hilt.android", version.ref = "hilt" }
kotlin-kapt = { id = "org.jetbrains.kotlin.kapt", version.ref = "kotlin" }
```

**app/build.gradle.kts**

```kotlin
plugins {
    alias(libs.plugins.android.application)
    alias(libs.plugins.kotlin.android)
    alias(libs.plugins.kotlin.kapt)
    alias(libs.plugins.hilt.android)
}

android {
    namespace = "com.ashargrosir.apms"
    compileSdk = 34

    defaultConfig {
        applicationId = "com.ashargrosir.apms"
        minSdk = 24
        targetSdk = 34
        versionCode = 1
        versionName = "1.0.0"

        buildConfigField("String", "API_BASE_URL", "\"https://api.ashargrosir.com/v1/\"")
        
        vectorDrawables {
            useSupportLibrary = true
        }
    }

    buildTypes {
        debug {
            isDebuggable = true
            buildConfigField("String", "API_BASE_URL", "\"https://dev-api.ashargrosir.com/v1/\"")
        }
        release {
            isMinifyEnabled = true
            isShrinkResources = true
            proguardFiles(
                getDefaultProguardFile("proguard-android-optimize.txt"),
                "proguard-rules.pro"
            )
        }
    }

    compileOptions {
        sourceCompatibility = JavaVersion.VERSION_17
        targetCompatibility = JavaVersion.VERSION_17
    }

    kotlinOptions {
        jvmTarget = "17"
    }

    buildFeatures {
        compose = true
        buildConfig = true
    }

    composeOptions {
        kotlinCompilerExtensionVersion = "1.5.11"
    }

    packaging {
        resources {
            excludes += "/META-INF/{AL2.0,LGPL2.1}"
        }
    }
}

dependencies {
    // Kotlin
    implementation(libs.kotlin.stdlib)
    implementation(libs.kotlinx.coroutines.core)
    implementation(libs.kotlinx.coroutines.android)

    // Compose
    implementation(libs.compose.ui)
    implementation(libs.compose.material3)
    implementation(libs.compose.ui.tooling.preview)
    implementation(libs.compose.navigation)
    debugImplementation(libs.compose.ui.tooling)

    // Hilt
    implementation(libs.hilt.android)
    kapt(libs.hilt.compiler)
    implementation(libs.hilt.navigation.compose)

    // Retrofit
    implementation(libs.retrofit)
    implementation(libs.retrofit.gson)
    implementation(libs.okhttp)
    implementation(libs.okhttp.logging)

    // Room
    implementation(libs.room.runtime)
    implementation(libs.room.ktx)
    kapt(libs.room.compiler)

    // Lifecycle
    implementation(libs.lifecycle.viewmodel.ktx)
    implementation(libs.lifecycle.runtime.compose)

    // Coil
    implementation(libs.coil.compose)

    // DataStore
    implementation(libs.datastore.preferences)
}

kapt {
    correctErrorTypes = true
}
```

### 6.4 Network Layer Implementation

**NetworkModule.kt**

```kotlin
package com.ashargrosir.apms.di

import com.ashargrosir.apms.BuildConfig
import com.ashargrosir.apms.data.local.TokenManager
import com.ashargrosir.apms.data.remote.api.AuthApi
import com.ashargrosir.apms.data.remote.api.ProductApi
import com.ashargrosir.apms.data.remote.api.OrderApi
import com.ashargrosir.apms.data.remote.interceptors.AuthInterceptor
import com.ashargrosir.apms.data.remote.interceptors.ErrorInterceptor
import dagger.Module
import dagger.Provides
import dagger.hilt.InstallIn
import dagger.hilt.components.SingletonComponent
import okhttp3.OkHttpClient
import okhttp3.logging.HttpLoggingInterceptor
import retrofit2.Retrofit
import retrofit2.converter.gson.GsonConverterFactory
import java.util.concurrent.TimeUnit
import javax.inject.Singleton

@Module
@InstallIn(SingletonComponent::class)
object NetworkModule {

    @Provides
    @Singleton
    fun provideLoggingInterceptor(): HttpLoggingInterceptor {
        return HttpLoggingInterceptor().apply {
            level = if (BuildConfig.DEBUG) {
                HttpLoggingInterceptor.Level.BODY
            } else {
                HttpLoggingInterceptor.Level.NONE
            }
        }
    }

    @Provides
    @Singleton
    fun provideAuthInterceptor(tokenManager: TokenManager): AuthInterceptor {
        return AuthInterceptor(tokenManager)
    }

    @Provides
    @Singleton
    fun provideErrorInterceptor(): ErrorInterceptor {
        return ErrorInterceptor()
    }

    @Provides
    @Singleton
    fun provideOkHttpClient(
        loggingInterceptor: HttpLoggingInterceptor,
        authInterceptor: AuthInterceptor,
        errorInterceptor: ErrorInterceptor
    ): OkHttpClient {
        return OkHttpClient.Builder()
            .addInterceptor(loggingInterceptor)
            .addInterceptor(authInterceptor)
            .addInterceptor(errorInterceptor)
            .connectTimeout(30, TimeUnit.SECONDS)
            .readTimeout(30, TimeUnit.SECONDS)
            .writeTimeout(30, TimeUnit.SECONDS)
            .build()
    }

    @Provides
    @Singleton
    fun provideRetrofit(okHttpClient: OkHttpClient): Retrofit {
        return Retrofit.Builder()
            .baseUrl(BuildConfig.API_BASE_URL)
            .client(okHttpClient)
            .addConverterFactory(GsonConverterFactory.create())
            .build()
    }

    @Provides
    @Singleton
    fun provideAuthApi(retrofit: Retrofit): AuthApi {
        return retrofit.create(AuthApi::class.java)
    }

    @Provides
    @Singleton
    fun provideProductApi(retrofit: Retrofit): ProductApi {
        return retrofit.create(ProductApi::class.java)
    }

    @Provides
    @Singleton
    fun provideOrderApi(retrofit: Retrofit): OrderApi {
        return retrofit.create(OrderApi::class.java)
    }
}
```

**AuthInterceptor.kt**

```kotlin
package com.ashargrosir.apms.data.remote.interceptors

import com.ashargrosir.apms.data.local.TokenManager
import kotlinx.coroutines.flow.first
import kotlinx.coroutines.runBlocking
import okhttp3.Interceptor
import okhttp3.Response
import javax.inject.Inject

class AuthInterceptor @Inject constructor(
    private val tokenManager: TokenManager
) : Interceptor {

    override fun intercept(chain: Interceptor.Chain): Response {
        val request = chain.request()

        // Skip auth for login/register endpoints
        if (request.url.encodedPath.contains("/auth/login") ||
            request.url.encodedPath.contains("/auth/register")
        ) {
            return chain.proceed(request)
        }

        // Get token from DataStore
        val token = runBlocking {
            tokenManager.getToken().first()
        }

        // Add token to request if available
        val newRequest = if (token != null) {
            request.newBuilder()
                .addHeader("Authorization", "Bearer \")
                .addHeader("Accept", "application/json")
                .build()
        } else {
            request.newBuilder()
                .addHeader("Accept", "application/json")
                .build()
        }

        return chain.proceed(newRequest)
    }
}
```

**ProductApi.kt**

```kotlin
package com.ashargrosir.apms.data.remote.api

import com.ashargrosir.apms.data.remote.dto.ApiResponse
import com.ashargrosir.apms.data.remote.dto.ProductDto
import retrofit2.http.*

interface ProductApi {

    @GET("products")
    suspend fun getProducts(
        @Query("page") page: Int = 1,
        @Query("per_page") perPage: Int = 20,
        @Query("search") search: String? = null,
        @Query("category") category: String? = null
    ): ApiResponse<List<ProductDto>>

    @GET("products/{id}")
    suspend fun getProductById(
        @Path("id") id: Long
    ): ApiResponse<ProductDto>

    @POST("products")
    suspend fun createProduct(
        @Body product: ProductDto
    ): ApiResponse<ProductDto>

    @PUT("products/{id}")
    suspend fun updateProduct(
        @Path("id") id: Long,
        @Body product: ProductDto
    ): ApiResponse<ProductDto>

    @DELETE("products/{id}")
    suspend fun deleteProduct(
        @Path("id") id: Long
    ): ApiResponse<Unit>

    @GET("products/search")
    suspend fun searchProducts(
        @Query("q") query: String
    ): ApiResponse<List<ProductDto>>
}
```

### 6.5 ViewModel Example

**ProductViewModel.kt**

```kotlin
package com.ashargrosir.apms.presentation.products

import androidx.lifecycle.ViewModel
import androidx.lifecycle.viewModelScope
import com.ashargrosir.apms.domain.model.Product
import com.ashargrosir.apms.domain.usecase.GetProductsUseCase
import com.ashargrosir.apms.utils.Resource
import dagger.hilt.android.lifecycle.HiltViewModel
import kotlinx.coroutines.flow.*
import kotlinx.coroutines.launch
import javax.inject.Inject

@HiltViewModel
class ProductViewModel @Inject constructor(
    private val getProductsUseCase: GetProductsUseCase
) : ViewModel() {

    private val _uiState = MutableStateFlow<ProductUiState>(ProductUiState.Loading)
    val uiState: StateFlow<ProductUiState> = _uiState.asStateFlow()

    private val _searchQuery = MutableStateFlow("")
    val searchQuery: StateFlow<String> = _searchQuery.asStateFlow()

    init {
        loadProducts()
    }

    fun loadProducts() {
        viewModelScope.launch {
            getProductsUseCase().collect { result ->
                _uiState.value = when (result) {
                    is Resource.Loading -> ProductUiState.Loading
                    is Resource.Success -> ProductUiState.Success(result.data ?: emptyList())
                    is Resource.Error -> ProductUiState.Error(result.message ?: "Unknown error")
                }
            }
        }
    }

    fun searchProducts(query: String) {
        _searchQuery.value = query
        // Implement search logic
    }

    fun refreshProducts() {
        loadProducts()
    }
}

sealed class ProductUiState {
    object Loading : ProductUiState()
    data class Success(val products: List<Product>) : ProductUiState()
    data class Error(val message: String) : ProductUiState()
}
```

**ProductListScreen.kt (Jetpack Compose)**

```kotlin
package com.ashargrosir.apms.presentation.products

import androidx.compose.foundation.clickable
import androidx.compose.foundation.layout.*
import androidx.compose.foundation.lazy.LazyColumn
import androidx.compose.foundation.lazy.items
import androidx.compose.material.icons.Icons
import androidx.compose.material.icons.filled.Add
import androidx.compose.material.icons.filled.Search
import androidx.compose.material3.*
import androidx.compose.runtime.*
import androidx.compose.ui.Alignment
import androidx.compose.ui.Modifier
import androidx.compose.ui.unit.dp
import androidx.hilt.navigation.compose.hiltViewModel
import coil.compose.AsyncImage

@OptIn(ExperimentalMaterial3Api::class)
@Composable
fun ProductListScreen(
    viewModel: ProductViewModel = hiltViewModel(),
    onProductClick: (Long) -> Unit,
    onAddProductClick: () -> Unit
) {
    val uiState by viewModel.uiState.collectAsState()
    val searchQuery by viewModel.searchQuery.collectAsState()

    Scaffold(
        topBar = {
            TopAppBar(
                title = { Text("Products") },
                actions = {
                    IconButton(onClick = { /* TODO: Search */ }) {
                        Icon(Icons.Default.Search, contentDescription = "Search")
                    }
                }
            )
        },
        floatingActionButton = {
            FloatingActionButton(onClick = onAddProductClick) {
                Icon(Icons.Default.Add, contentDescription = "Add Product")
            }
        }
    ) { paddingValues ->
        when (uiState) {
            is ProductUiState.Loading -> {
                Box(
                    modifier = Modifier
                        .fillMaxSize()
                        .padding(paddingValues),
                    contentAlignment = Alignment.Center
                ) {
                    CircularProgressIndicator()
                }
            }
            is ProductUiState.Success -> {
                val products = (uiState as ProductUiState.Success).products
                LazyColumn(
                    modifier = Modifier
                        .fillMaxSize()
                        .padding(paddingValues),
                    contentPadding = PaddingValues(16.dp),
                    verticalArrangement = Arrangement.spacedBy(8.dp)
                ) {
                    items(products) { product ->
                        ProductItem(
                            product = product,
                            onClick = { onProductClick(product.id) }
                        )
                    }
                }
            }
            is ProductUiState.Error -> {
                Box(
                    modifier = Modifier
                        .fillMaxSize()
                        .padding(paddingValues),
                    contentAlignment = Alignment.Center
                ) {
                    Column(horizontalAlignment = Alignment.CenterHorizontally) {
                        Text(
                            text = (uiState as ProductUiState.Error).message,
                            color = MaterialTheme.colorScheme.error
                        )
                        Spacer(modifier = Modifier.height(16.dp))
                        Button(onClick = { viewModel.refreshProducts() }) {
                            Text("Retry")
                        }
                    }
                }
            }
        }
    }
}

@Composable
fun ProductItem(
    product: Product,
    onClick: () -> Unit
) {
    Card(
        modifier = Modifier
            .fillMaxWidth()
            .clickable(onClick = onClick)
    ) {
        Row(
            modifier = Modifier
                .fillMaxWidth()
                .padding(16.dp),
            verticalAlignment = Alignment.CenterVertically
        ) {
            AsyncImage(
                model = product.image,
                contentDescription = product.name,
                modifier = Modifier.size(64.dp)
            )
            Spacer(modifier = Modifier.width(16.dp))
            Column(modifier = Modifier.weight(1f)) {
                Text(
                    text = product.name,
                    style = MaterialTheme.typography.titleMedium
                )
                Text(
                    text = product.sku,
                    style = MaterialTheme.typography.bodySmall,
                    color = MaterialTheme.colorScheme.onSurfaceVariant
                )
                Text(
                    text = "Rp ",
                    style = MaterialTheme.typography.titleSmall,
                    color = MaterialTheme.colorScheme.primary
                )
            }
        }
    }
}
```

---

## 7. API DOCUMENTATION

### 7.1 API Documentation Tools

**Laravel Scribe Configuration**

```bash
# Install Scribe
composer require --dev knuckleswtf/scribe

# Publish configuration
php artisan vendor:publish --tag=scribe-config

# Generate documentation
php artisan scribe:generate
```

**config/scribe.php**

```php
return [
    'title' => 'APMS API Documentation',
    'description' => 'API documentation for Ashar Grosir Perfume Management System',
    'base_url' => env('APP_URL', 'https://api.ashargrosir.com'),
    'routes' => [
        [
            'match' => [
                'prefixes' => ['api/v1/*'],
                'domains' => ['*'],
            ],
            'include' => ['*'],
            'exclude' => [],
        ],
    ],
    'type' => 'laravel',
    'static' => [
        'output_path' => 'public/docs',
    ],
    'laravel' => [
        'add_routes' => true,
        'docs_url' => '/docs',
    ],
    'postman' => [
        'enabled' => true,
        'overrides' => [],
    ],
    'openapi' => [
        'enabled' => true,
        'overrides' => [],
    ],
    'auth' => [
        'enabled' => true,
        'default' => false,
        'in' => 'bearer',
        'name' => 'Authorization',
        'use_value' => env('SCRIBE_AUTH_KEY'),
    ],
];
```

### 7.2 API Versioning Strategy

**URL-Based Versioning**

```
https://api.ashargrosir.com/v1/products    # Current version
https://api.ashargrosir.com/v2/products    # Future version
```

**Version Middleware**

```php
<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class ApiVersion
{
    public function handle(Request \, Closure \, string \)
    {
        \->attributes->set('api_version', \);
        
        return \(\);
    }
}
```

### 7.3 API Response Standards

**Success Response Format**

```json
{
  "success": true,
  "message": "Operation successful",
  "data": {
    // Response data
  },
  "meta": {
    "timestamp": "2026-07-18T10:30:00Z",
    "request_id": "req_abc123",
    "version": "v1"
  }
}
```

**Paginated Response Format**

```json
{
  "success": true,
  "message": "Products retrieved successfully",
  "data": [
    // Array of items
  ],
  "meta": {
    "current_page": 1,
    "per_page": 20,
    "total": 156,
    "last_page": 8,
    "from": 1,
    "to": 20,
    "timestamp": "2026-07-18T10:30:00Z",
    "request_id": "req_abc123"
  },
  "links": {
    "first": "https://api.ashargrosir.com/v1/products?page=1",
    "last": "https://api.ashargrosir.com/v1/products?page=8",
    "prev": null,
    "next": "https://api.ashargrosir.com/v1/products?page=2"
  }
}
```

**Error Response Format**

```json
{
  "success": false,
  "message": "Validation failed",
  "errors": {
    "email": ["The email field is required."],
    "password": ["The password must be at least 8 characters."]
  },
  "meta": {
    "timestamp": "2026-07-18T10:30:00Z",
    "request_id": "req_abc123",
    "error_code": "VALIDATION_ERROR"
  }
}
```

---

