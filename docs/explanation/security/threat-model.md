---
title: Threat Model Overview
diataxis: explanation
prerequisites:
  - reference/security/security-architecture.md
owner: Security Lead
update_frequency: semi-annual
classification: mandatory
---

# Threat Model Overview

## Why These Threats Matter?

APMS handles financial transactions, customer data, and business operations. A security breach could:
- Lose customer trust (especially B2B customers)
- Cause financial loss (fraudulent transactions)
- Disrupt retail operations
- Expose personal data (GDPR implications)

## Threat Landscape

| Threat | Likelihood | Impact | Mitigation |
|---|---|---|---|
| SQL Injection | Low | Critical | Eloquent ORM (parameterized queries) |
| XSS (Stored) | Medium | High | InputSanitizer middleware + Blade escaping |
| CSRF | Low | High | CSRF token on all state-changing requests |
| Session Hijacking | Low | Critical | SessionSecurity middleware (IP + UA check) |
| Brute Force Login | Medium | Medium | LoginThrottle (5 attempts/15 min) |
| DDoS | Low | High | CloudFront + WAF + rate limiting |
| Data Breach (DB) | Low | Critical | Encryption at rest, network isolation |
| Privilege Escalation | Low | High | Role middleware + Policies (defense in depth) |
| Insecure Direct Object Reference | Low | High | Policy-based authorization on all model access |

## Why Not OWASP Top 10 Compliance Checklist?

The OWASP Top 10 is a starting point, not a guarantee. Each vulnerability class is addressed specifically:
- **A01: Broken Access Control** → Role middleware + Policies + route grouping
- **A02: Cryptographic Failures** → TLS 1.3 + bcrypt + AES-256
- **A03: Injection** → Eloquent ORM + input validation
- **A04: Insecure Design** → Service layer + form requests + ADRs
- **A05: Security Misconfiguration** → Config management + CI/CD validation
- **A06: Vulnerable Components** → Composer audit + Snyk
- **A07: Auth Failures** → Sanctum + session security + throttle
- **A08: Data Integrity Failures** → CSRF + signed webhooks
- **A09: Logging Failures** → Activity logger + CloudWatch
- **A10: SSRF** → No external URL fetching from server
