---
title: Test Strategy
diataxis: reference
standards:
  - ISO/IEC 25010
  - arc42 §9
owner: QA Lead
update_frequency: quarterly
classification: mandatory
---

# Test Strategy

## Test Pyramid

```mermaid
flowchart TD
    subgraph E2E[E2E / Browser Tests]
        Dusk[Dusk Tests]
    end
    subgraph Integration[Integration Tests]
        Feature[Feature Tests]
        Api[API Tests]
    end
    subgraph Unit[Unit Tests]
        Models[Model Tests]
        Services[Service Tests]
        Actions[Action Tests]
    end
    Unit --> Integration --> E2E
```

## Test Coverage Targets

| Layer | Target | Tool |
|---|---|---|
| Unit (Models) | >90% | PHPUnit |
| Unit (Services) | >85% | PHPUnit |
| Unit (Services) | >85% | PHPUnit |
| Feature (Controllers) | >80% | PHPUnit |
| Feature (API) | >75% | PHPUnit |
| Browser (E2E) | Critical paths | Laravel Dusk |

## Naming Convention

```
tests/
├── Unit/
│   ├── Models/
│   │   └── ProductTest.php
│   ├── Services/
│   │   └── TransactionServiceTest.php

├── Feature/
│   ├── Controllers/
│   │   └── WholesaleOrderControllerTest.php
│   └── Api/
│       └── ProductApiTest.php
└── Browser/
    ├── WholesaleCustomerPortalTest.php
    └── OwnerDashboardTest.php
```

## Testing Rules

- Factory states for every model variant
- Database transactions for feature tests
- HTTP test helpers for authenticated routes
- `RefreshDatabase` for feature tests
- `DatabaseTransactions` for unit tests touching DB
- Mock external services (WhatsApp, RajaOngkir)
- No `sleep()` calls (use `Http::fake()` instead)
