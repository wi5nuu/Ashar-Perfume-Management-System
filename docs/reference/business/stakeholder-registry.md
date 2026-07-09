---
title: Stakeholder Registry
diataxis: reference
standards:
  - BABOK §2.3
owner: Product Manager
update_frequency: on-change
classification: mandatory
---

# Stakeholder Registry

## Personas

### Ashar (Owner)

| Attribute | Value |
|---|---|
| **Role** | Business Owner, System Administrator |
| **Technical Level** | Low |
| **Goals** | Monitor profit across branches, manage wholesale customers, view reports |
| **Pain Points** | Manual reconciliation, lack of real-time visibility |
| **System Access** | Full access (Owner role) |

### Sari (Store Manager)

| Attribute | Value |
|---|---|
| **Role** | Branch Store Manager |
| **Technical Level** | Medium |
| **Goals** | Manage inventory, oversee staff, reconcile daily sales |
| **Pain Points** | Stock discrepancies, employee attendance tracking |
| **System Access** | Manager role — inventory, reports, employee management |

### Rudi (Cashier)

| Attribute | Value |
|---|---|
| **Role** | Retail Cashier |
| **Technical Level** | Low |
| **Goals** | Process transactions quickly, handle returns |
| **Pain Points** | Slow system, complex discount application |
| **System Access** | Cashier role — POS only |

### Dewi (Wholesale Customer)

| Attribute | Value |
|---|---|
| **Role** | Wholesale Buyer (reseller) |
| **Technical Level** | Medium |
| **Goals** | Place orders, track shipment, check loyalty points |
| **Pain Points** | No self-service portal, must call/WA to order |
| **System Access** | Wholesale customer portal (limited) |

### Bagus (Warehouse Staff)

| Attribute | Value |
|---|---|
| **Role** | Warehouse Operator |
| **Technical Level** | Low |
| **Goals** | Pack orders, update inventory, manage stock requests |
| **Pain Points** | Unclear picking lists, stock location |
| **System Access** | Warehouse role — inventory, orders |

## Stakeholder Influence-Interest Matrix

```
                    High Influence
                         │
    Keep Satisfied       │     Manage Closely
    (Regulators)         │     (Owner, Store Manager)
                         │
─────────────────────────┼─────────────────────────
                         │
    Monitor              │     Keep Informed
    (Warehouse Staff)    │     (Wholesale Customers)
                         │
                    Low Influence
```
