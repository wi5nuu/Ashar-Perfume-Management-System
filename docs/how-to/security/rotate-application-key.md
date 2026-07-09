---
title: Rotate Application Key
diataxis: how-to
owner: DevOps Lead
update_frequency: on-demand
classification: mandatory
---

# Rotate Application Key

## When to Rotate

- After a team member with access leaves
- Suspected key compromise
- Quarterly rotation (recommended)

## Steps

```bash
# Generate new key (prevents downtime by retaining old encryption)
php artisan key:generate --show
# Copy the new key

# Update .env
APP_KEY=base64:{new_key}

# Clear cache
php artisan config:clear
php artisan config:cache
```

## Important

- Key rotation re-encrypts nothing — it only affects new data
- Existing encrypted data remains readable until the old key is removed
- The old key is stored in `config/app.php` as `previous_keys`
