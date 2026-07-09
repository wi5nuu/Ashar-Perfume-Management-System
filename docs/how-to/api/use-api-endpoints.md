---
title: Use API Endpoints
diataxis: how-to
owner: Backend Lead
update_frequency: per-release
classification: recommended
---

# Use API Endpoints

## Authentication

```bash
# Get token
curl -X POST /api/login \
  -H "Content-Type: application/json" \
  -d '{"email":"user@example.com","password":"..."}'

# Use token
curl /api/products \
  -H "Authorization: Bearer {token}"
```

## Common Endpoints

```bash
# List products
curl /api/products

# Get product detail
curl /api/products/{id}

# List branches
curl /api/branches

# Get user profile
curl /api/user -H "Authorization: Bearer {token}"
```

## Pagination

```bash
# Paginated requests
curl "/api/products?page=1&per_page=15"
```

Response includes `meta.current_page`, `meta.last_page`, `meta.total`.
