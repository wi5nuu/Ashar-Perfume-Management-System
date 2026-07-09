---
title: C4 Level 1 — System Context Diagram
diataxis: reference
standards:
  - C4 Level 1
owner: Principal Architect
update_frequency: per-release
classification: mandatory
---

# C4 Level 1: System Context Diagram

## Diagram

```mermaid
C4Context
  title System Context — APMS

  Person(owner, "Owner", "System administrator, business owner")
  Person(manager, "Store Manager", "Manages daily operations")
  Person(cashier, "Cashier", "Processes retail transactions")
  Person(warehouse, "Warehouse Staff", "Manages stock and orders")
  Person(ws_customer, "Wholesale Customer", "Bulk purchaser")
  Person(retail_customer, "Retail Customer", "Walk-in buyer")

  System_Boundary(apms, "APMS") {
    System(web_app, "APMS Web App", "Laravel + Blade + Bootstrap")
  }

  System_Ext(mysql, "MySQL Database", "Primary data store")
  System_Ext(redis, "Redis", "Cache, queue, sessions")
  System_Ext(minio, "Object Storage", "Product images, receipts")
  System_Ext(mail, "SMTP / Mailgun", "Email notifications")
  System_Ext(wa, "WhatsApp API", "Order notifications")
  System_Ext(google, "Google Auth", "OAuth login")

  Rel(owner, web_app, "Uses", "HTTPS")
  Rel(manager, web_app, "Uses", "HTTPS")
  Rel(cashier, web_app, "Uses", "HTTPS")
  Rel(warehouse, web_app, "Uses", "HTTPS")
  Rel(ws_customer, web_app, "Uses", "HTTPS")
  Rel(retail_customer, web_app, "Views invoice", "HTTPS")

  Rel(web_app, mysql, "Reads/Writes", "SQL")
  Rel(web_app, redis, "Reads/Writes", "TCP")
  Rel(web_app, minio, "Reads/Writes", "S3 API")
  Rel(web_app, mail, "Sends", "SMTP")
  Rel(web_app, wa, "Sends", "HTTP")
  Rel(web_app, google, "Authenticates", "OAuth 2.0")
```

## Description

The APMS system is a monolithic Laravel web application serving six user groups:

1. **Owner** — Full system access, manages wholesale customers, views reports
2. **Store Manager** — Day-to-day operations, inventory, staff management
3. **Cashier** — POS retail transaction processing
4. **Warehouse Staff** — Inventory fulfillment and stock management
5. **Wholesale Customer** — Self-service portal for order placement and tracking
6. **Retail Customer** — Limited access (invoice viewing only)

The system integrates with five external systems for data persistence, caching, messaging, and authentication.
