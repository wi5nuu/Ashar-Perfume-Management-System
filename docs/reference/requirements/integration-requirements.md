---
title: Integration Requirements
diataxis: reference
standards:
  - IEEE 29148
owner: Backend Lead
update_frequency: per-release
classification: mandatory
---

# Integration Requirements

## REQ-INT-001: WhatsApp Notification

**ID:** REQ-INT-001
**Priority:** High
**Source:** Operations

The system **shall** send order status notifications to wholesale customers via WhatsApp API. The notification **shall** include order number, status update, and tracking number (if applicable).

## REQ-INT-002: RajaOngkir Shipping

**ID:** REQ-INT-002
**Priority:** Medium
**Source:** Operations

The system **shall** integrate with RajaOngkir API to calculate shipping costs and provide tracking information for wholesale orders.

## REQ-INT-003: Email Notifications

**ID:** REQ-INT-003
**Priority:** Low
**Source:** Product

The system **may** send email notifications for password reset confirmations, order confirmations, and marketing communications via SMTP.

## REQ-INT-004: Receipt Printing

**ID:** REQ-INT-004
**Priority:** Medium
**Source:** Operations

The system **shall** support receipt printing via ESC/POS thermal printers connected to the POS terminal.
