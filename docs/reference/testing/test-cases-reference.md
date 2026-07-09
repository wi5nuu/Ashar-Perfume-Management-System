---
title: Test Cases Reference
diataxis: reference
owner: QA Lead
update_frequency: per-release
classification: mandatory
---

# Test Cases Reference

## Critical Test Paths

### Wholesale Order Lifecycle
1. Customer creates order → status `pending`
2. Owner reviews → status `reviewed`
3. Warehouse prepares → status `packed`
4. Courier ships → status `shipped`
5. Customer receives → status `completed`
6. Customer cancel → status `cancelled`

### Password Reset Flow
1. Customer clicks "Lupa Password" → form renders
2. Customer submits email → request stores in `password_reset_requests`
3. Owner views request → pending tab shows new request
4. Owner resolves → new password generated, email sent
5. Customer logs in with new password → success

### Inventory Transfer
1. Branch A requests stock → request created (status `pending`)
2. Branch B approves → status `approved`
3. Warehouse dispatches → status `shipped`
4. Branch A receives → inventory updated, request completed

## Smoke Test Suite

| Test | Expected Result |
|---|---|
| Dashboard loads | 200, displays KPIs |
| Login with correct credentials | Redirect to dashboard |
| Login with wrong password | Error message, throttle |
| CRUD product | Successful create/read/update/delete |
| Process transaction | Receipt generated, inventory decremented |
| Wholesale customer portal login | Customer dashboard |
| Generate report | PDF/Excel download |
