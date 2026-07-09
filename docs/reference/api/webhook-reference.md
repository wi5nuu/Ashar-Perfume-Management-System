---
title: Webhook Reference
diataxis: reference
standards:
  - OpenAPI 3.x
owner: Backend Lead
update_frequency: per-release
classification: optional
---

# Webhook Reference

## Available Webhooks

| Webhook | Trigger | Payload |
|---|---|---|
| `order.created` | Wholesale order placed | Order object |
| `order.status_changed` | Wholesale order status update | Order ID, old status, new status |
| `inventory.low_stock` | Stock below minimum threshold | Product, branch, current stock |
| `customer.registered` | New wholesale customer | Customer object |

## Payload Format

```json
{
    "event": "order.created",
    "timestamp": "2025-06-01T12:00:00Z",
    "data": { ... },
    "signature": "sha256=..."
}
```

## Security

- Payload signed with HMAC-SHA256 using shared secret
- Verify signature before processing
- Retry on `5xx` response (up to 3 times, exponential backoff)
