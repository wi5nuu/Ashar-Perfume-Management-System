---
title: ADR-005 — API Auth: Laravel Sanctum
status: Accepted
date: 2024-02
deciders: Staff Software Engineer
---

# ADR-005: API Auth: Laravel Sanctum

## Context

The system requires token-based API authentication for third-party integrations and mobile access while maintaining session-based web authentication.

## Decision

Use Laravel Sanctum for API token authentication alongside web session auth.

## Rationale

- Single package for both SPA and token-based auth
- Native Laravel integration
- Simple token management (create/revoke)
- No OAuth complexity for internal API authentication
- Google OAuth (laravel/socialite) used for wholesale customer login, separate from API token auth
- Ability-based token scoping

## Consequences

- Tokens stored in `personal_access_tokens` table
- Token expiry management required (currently no expiry)
- Google OAuth (Socialite) already added for wholesale customer login
- Future OAuth providers can be added alongside Sanctum if needed
