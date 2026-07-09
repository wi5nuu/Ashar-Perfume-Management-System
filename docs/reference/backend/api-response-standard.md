---
title: API Response Standard
diataxis: reference
standards:
  - OpenAPI 3.x
owner: Staff Software Engineer
update_frequency: per-release
classification: mandatory
---

# API Response Standard

## Success Response Envelope

```json
{
    "success": true,
    "data": { ... },
    "message": "Operation completed successfully"
}
```

## Collection Response (with Pagination)

```json
{
    "success": true,
    "data": [ ... ],
    "meta": {
        "current_page": 1,
        "last_page": 10,
        "per_page": 15,
        "total": 145,
        "from": 1,
        "to": 15
    },
    "links": {
        "first": "/api/resource?page=1",
        "last": "/api/resource?page=10",
        "prev": null,
        "next": "/api/resource?page=2"
    }
}
```

## Error Response

```json
{
    "success": false,
    "message": "Human-readable error description",
    "errors": {
        "field_name": ["Validation error 1", "Validation error 2"]
    }
}
```

## HTTP Status Code Convention

| Code | Usage |
|---|---|
| 200 | Successful GET, PUT, PATCH |
| 201 | Successful POST (resource created) |
| 204 | Successful DELETE (no content) |
| 400 | Validation error / bad request |
| 401 | Unauthenticated |
| 403 | Forbidden (authorization failure) |
| 404 | Resource not found |
| 409 | Conflict (duplicate, state conflict) |
| 422 | Unprocessable entity (validation) |
| 429 | Rate limited |
| 500 | Internal server error |

## Standard Headers

| Header | Description |
|---|---|
| `X-RateLimit-Limit` | Max requests per window |
| `X-RateLimit-Remaining` | Requests remaining in current window |
| `X-RateLimit-Reset` | Unix timestamp of window reset |
| `X-Request-Id` | Unique request identifier (UUID) |
