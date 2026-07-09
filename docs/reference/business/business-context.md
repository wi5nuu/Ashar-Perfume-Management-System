---
title: Business Context & Vision
diataxis: reference
standards:
  - IEEE 29148 §1.2
owner: Product Manager
update_frequency: quarterly
classification: mandatory
---

# Business Context & Vision

## Product Perspective

APMS (Ashar Parfum Management System) is a comprehensive enterprise management system for AL'ASHAR PARFUM, a perfume retail and wholesale business. The system manages point-of-sale, inventory, wholesale operations, employee management, customer relationships, and financial reporting across multiple branches.

## Business Domain

The application operates in the **retail perfume and wholesale distribution** domain, serving two primary customer segments:

1. **Retail Customers** — Walk-in store customers purchasing individual perfume products
2. **Wholesale Customers** — Business customers purchasing in bulk for resale

## Key Business Objectives

| ID | Objective | Success Metric |
|---|---|---|
| BIZ-001 | Streamline retail POS operations | < 30 seconds per transaction |
| BIZ-002 | Manage multi-branch inventory centrally | Real-time stock visibility across all branches |
| BIZ-003 | Support wholesale order lifecycle from order to delivery | Order fulfillment < 48 hours |
| BIZ-004 | Provide customer loyalty and referral system | 20% repeat purchase rate increase |
| BIZ-005 | Generate financial and operational reports | All reports generated in < 5 seconds |
| BIZ-006 | Role-based access control for employees | Zero unauthorized access incidents |

## Stakeholders

| Stakeholder | Role | Primary Concern |
|---|---|---|
| Owner (Ashar) | Business owner, system administrator | Profitability, operational oversight |
| Store Manager | Manages daily store operations | Inventory accuracy, team productivity |
| Cashier | Processes retail transactions | Fast, error-free checkout |
| Warehouse Staff | Manages stock and fulfillment | Accurate inventory counts |
| Wholesale Customer | Bulk purchaser | Order tracking, account management |
| Admin Staff | Back-office operations | Data entry, reporting |

## Business Constraints

- **Regulatory:** Indonesian tax regulations (PPN) for transaction reporting
- **Operational:** Multiple branch locations require real-time data synchronization
- **Technical:** Must support offline-capable POS with eventual consistency
- **Compliance:** Customer data privacy per Indonesian UU ITE and GDPR-aligned practices
