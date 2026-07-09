---
title: Performance Requirements
diataxis: reference
standards:
  - IEEE 29148
  - ISO/IEC 25010
owner: DevOps Lead
update_frequency: quarterly
classification: mandatory
---

# Performance Requirements

## REQ-PERF-001: Page Load Time

**ID:** REQ-PERF-001
**Priority:** High
**Source:** User Experience

The system **shall** load dashboard and POS pages within **2 seconds** (P95) under normal load. Static assets (CSS/JS/images) **shall** load within **1 second**.

## REQ-PERF-002: Transaction Processing

**ID:** REQ-PERF-002
**Priority:** Critical
**Source:** Operations

The system **shall** complete a POS transaction (including inventory decrement) within **3 seconds** from submit to receipt display.

## REQ-PERF-003: Concurrent Users

**ID:** REQ-PERF-003
**Priority:** Medium
**Source:** Scalability

The system **shall** support **50 concurrent users** across all branches with response times within acceptable thresholds.

## REQ-PERF-004: Report Generation

**ID:** REQ-PERF-004
**Priority:** Low
**Source:** Operations

Daily reports **shall** generate within **5 minutes**. Monthly reports **may** take up to **15 minutes**.

## REQ-PERF-005: Uptime

**ID:** REQ-PERF-005
**Priority:** Critical
**Source:** Business

The system **shall** achieve **99.9% uptime** during business hours (08:00–21:00 WIB, 6 days/week). Planned maintenance windows **shall** be scheduled outside business hours.

## REQ-PERF-006: API Response Time

**ID:** REQ-PERF-006
**Priority:** Medium
**Source:** API consumers

The API **shall** respond within **500ms** (P95) for read endpoints and **2 seconds** (P95) for write endpoints under normal load.
