---
title: Non-Functional Requirements
diataxis: reference
standards:
  - ISO/IEC 25010
owner: Principal Architect
update_frequency: per-release
classification: mandatory
---

# Non-Functional Requirements

All NFRs are categorized per the **ISO/IEC 25010** quality model.

## Performance Efficiency

### Time Behavior

| ID | Requirement | Target | Source |
|---|---|---|---|
| REQ-NFR-001 | POS transaction shall commit in < 2 seconds | P95 < 2s | BIZ-001 |
| REQ-NFR-002 | Dashboard page shall load in < 3 seconds | P95 < 3s | BIZ-005 |
| REQ-NFR-003 | API responses shall return in < 500ms | P95 < 500ms | — |
| REQ-NFR-004 | Report generation shall complete in < 5 seconds | Max 5s | BIZ-005 |

### Resource Utilization

| ID | Requirement | Target |
|---|---|---|
| REQ-NFR-005 | PHP-FPM memory per request shall not exceed 64MB | Max 64MB |
| REQ-NFR-006 | Database connection pool shall not exceed 150 concurrent connections | Max 150 |

### Capacity

| ID | Requirement | Target |
|---|---|---|
| REQ-NFR-007 | System shall support 100+ concurrent cashier operations | 100 CCU |
| REQ-NFR-008 | System shall handle 10,000+ daily transactions | 10K/day |
| REQ-NFR-009 | Inventory shall scale to 50,000+ product SKUs | 50K SKUs |

## Reliability

| ID | Requirement | Target |
|---|---|---|
| REQ-NFR-010 | System uptime during business hours (07:00–22:00) | 99.9% |
| REQ-NFR-011 | POS shall function with intermittent network connectivity | Offline-capable |
| REQ-NFR-012 | Backup shall complete within 1 hour | Max 1hr |
| REQ-NFR-013 | Recovery Point Objective (RPO) | ≤ 15 minutes |
| REQ-NFR-014 | Recovery Time Objective (RTO) | ≤ 2 hours |

## Security

| ID | Requirement | Target |
|---|---|---|
| REQ-NFR-015 | All passwords shall be hashed with bcrypt | Cost factor ≥ 12 |
| REQ-NFR-016 | API shall use token-based authentication (Sanctum) | — |
| REQ-NFR-017 | All data in transit shall use TLS 1.3 | — |
| REQ-NFR-018 | Role-based access control for all operations | Per RBAC matrix |
| REQ-NFR-019 | Rate limiting on all public endpoints | 100 req/min |
| REQ-NFR-020 | Failed login attempts shall be throttled | 5 attempts/15min |

## Maintainability

| ID | Requirement | Target |
|---|---|---|
| REQ-NFR-021 | Codebase shall follow PSR-12 coding standards | — |
| REQ-NFR-022 | Test coverage shall be ≥ 80% for business logic | Line coverage |
| REQ-NFR-023 | New features shall include unit and feature tests | — |

## Usability

| ID | Requirement | Target |
|---|---|---|
| REQ-NFR-024 | POS interface shall be usable with touch screen | — |
| REQ-NFR-025 | Reports shall be exportable to PDF and Excel | — |
| REQ-NFR-026 | Search results shall appear within 2 seconds | — |
