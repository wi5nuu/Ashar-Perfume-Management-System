---
title: Middleware Stack Rationale
diataxis: explanation
owner: Staff Software Engineer
update_frequency: on-change
classification: recommended
---

# Middleware Stack Rationale

## Why This Middleware Design?

Every request passes through a chain of middleware before reaching a controller. The order is deliberate:

### Global Middleware (always runs)

1. **TrustProxies** — Must be first so the app sees the real client IP behind load balancers/proxies. All downstream middleware depend on accurate IP detection.

2. **EncryptCookies / StartSession** — Session initialization before CSRF and auth checks. CSRF protection needs the session to store the token.

3. **VerifyCsrfToken** — Protects state-changing requests (POST/PUT/DELETE). Positioned after session is ready but before auth — even unauthenticated state-changing requests need CSRF protection.

4. **SubstituteBindings** — Route model binding. Runs after security checks so we don't unnecessarily load models for requests that will be rejected anyway.

5. **Custom Security Middlewares** (InputSanitizer, IpSecurity, LoginThrottle, SessionSecurity, SecurityHeaders, HttpsProtocol) — Defense-in-depth. Each handles a specific attack vector independently.

### Route Middleware (per route group)

- **auth** — Session-based authentication. Applied to all internal routes.
- **verified** — Email verification check. Applied to POS, shift, and transaction routes.
- **role** — Role-based authorization. Applied per route group via `RoleMiddleware::class`. Order matters: authenticate first, then authorize.
- **throttle** — Rate limiting via Laravel's `ThrottleRequests`. Applied per-route for abuse prevention. Additionally `throttle.login` alias for login-specific limiting.

## Why Custom Security Middleware Instead of Package?

Off-the-shelf packages (e.g., laravel-security) are either too opinionated, too heavy, or don't cover Indonesia-specific threats (e.g., certain XSS vectors common in Indonesian web apps). Custom middleware gives us:
- Full control over ordering
- Indonesian-language error messages
- Minimal performance overhead
- Easy testing
