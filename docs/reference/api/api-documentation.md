---
title: API Documentation
diataxis: reference
standards:
  - OpenAPI 3.x
  - arc42 §7
owner: Backend Lead
update_frequency: per-release
classification: mandatory
---

# API Documentation

## Standards

- API follows **RESTful** conventions
- Request/response bodies use **JSON**
- Authentication via **Sanctum** token (Bearer) or **session cookie** (web)
- All endpoints documented in `openapi.yaml` (OpenAPI 3.x spec)

## Versioning

- Current version: `v1`
- Version prefix: `/api/v1/` (planned for future)
- Backward-compatible additions do not bump version
- Breaking changes require new version + deprecation notice

## Authentication

### Bearer Token (API)
```
Authorization: Bearer {token}
```

### Session Cookie (Web)
- Laravel session cookie set on login
- CSRF-Protected via `X-XSRF-TOKEN` or `_token` parameter

## Error Codes

| Code | Meaning |
|---|---|
| `AUTH_UNAUTHORIZED` | Missing/invalid token |
| `AUTH_FORBIDDEN` | Insufficient permissions |
| `AUTH_EXPIRED` | Token expired |
| `VALIDATION_ERROR` | Request validation failure |
| `RESOURCE_NOT_FOUND` | Entity not found |
| `DUPLICATE_ENTRY` | Unique constraint violation |
| `RATE_LIMIT_EXCEEDED` | Too many requests |
| `INTERNAL_ERROR` | Server-side failure |

## Deprecation Policy

- Deprecated endpoints return `Warning: 299 Deprecated` header
- Minimum 3 months between deprecation and removal
- Deprecation documented in changelog
