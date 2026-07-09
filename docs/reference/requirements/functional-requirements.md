---
title: Functional Requirements
diataxis: reference
standards:
  - IEEE 29148
owner: Staff Software Engineer
update_frequency: per-release
classification: mandatory
---

# Functional Requirements

## REQ-FUNC-001: POS Transaction Processing

**ID:** REQ-FUNC-001
**Priority:** Critical
**Source:** Business stakeholder

The system **shall** allow cashiers to process retail transactions by scanning/selecting products, applying discounts, calculating totals including PPN (10%, configurable via `config/business.php`), accepting multiple payment methods, and printing receipts.

## REQ-FUNC-002: Inventory Management

**ID:** REQ-FUNC-002
**Priority:** Critical
**Source:** Business stakeholder

The system **shall** track inventory levels per branch per product, decrement stock on sale, and alert when stock falls below minimum threshold.

## REQ-FUNC-003: Wholesale Ordering

**ID:** REQ-FUNC-003
**Priority:** High
**Source:** Business stakeholder

The system **shall** allow wholesale customers to register, browse products, place orders, track order status, and view order history via a customer portal.

## REQ-FUNC-004: Order Lifecycle

**ID:** REQ-FUNC-004
**Priority:** High
**Source:** Operations

The system **shall** support the following order statuses: pending → reviewed → on_progress → packed → shipped → delivered → completed. Cancellation **may** occur at any point before shipping.

## REQ-FUNC-005: Multi-Branch Support

**ID:** REQ-FUNC-005
**Priority:** High
**Source:** Business stakeholder

The system **shall** support multiple branches (cabang) with per-branch inventory, users, transactions, and reporting, accessible from a central owner dashboard.

## REQ-FUNC-006: User Roles & Permissions

**ID:** REQ-FUNC-006
**Priority:** High
**Source:** Security

The system **shall** support the following roles: owner, admin, admin_pusat, manager, cashier, supervisor, warehouse, employee, wholesale_customer. Each role **shall** have distinct access permissions enforced at route and model level.

## REQ-FUNC-007: Reporting

**ID:** REQ-FUNC-007
**Priority:** Medium
**Source:** Business stakeholder

The system **shall** generate daily sales reports, monthly revenue summaries, inventory valuation reports, and commission calculations.

## REQ-FUNC-008: Customer Loyalty

**ID:** REQ-FUNC-008
**Priority:** Low
**Source:** Business stakeholder

The system **may** support a customer loyalty program with tier-based benefits (Bronze, Silver, Gold, Platinum) based on purchase volume.

## REQ-FUNC-009: Inter-Branch Stock Transfer

**ID:** REQ-FUNC-009
**Priority:** Medium
**Source:** Operations

The system **shall** support inter-branch stock transfer requests with approval workflow: request → approve → prepare → ship → receive.

## REQ-FUNC-010: Wholesale Password Reset

**ID:** REQ-FUNC-010
**Priority:** High
**Source:** Support

The system **shall** allow wholesale customers to request a password reset, which generates a request that the owner can approve, producing a new password displayed in the owner panel.
