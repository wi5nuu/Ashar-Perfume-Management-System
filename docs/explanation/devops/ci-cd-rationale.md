---
title: CI/CD Rationale
diataxis: explanation
owner: DevOps Lead
update_frequency: quarterly
classification: recommended
---

# CI/CD Rationale

## Why This Pipeline Design?

Every CI/CD stage exists to catch a specific class of problems before they reach production.

| Stage | Catches | Without It |
|---|---|---|
| Build | Missing dependencies, syntax errors | Broken deployment |
| Test | Logic errors, regressions | Undiscovered bugs in production |
| Static analysis | Type errors, unused code | Technical debt accumulation |
| Security scan | Vulnerable dependencies | CVEs in production |
| Staging deploy | Environment-specific issues | Production-only failures |
| E2E tests | Integration failures | User-facing bugs |
| Blue-green deploy | Deployment errors | Downtime during fix |

## Why Blue-Green?

Retail operations run 12+ hours daily. Even 5 minutes of downtime during business hours can mean lost sales and frustrated cashiers. Blue-green deployment:
- Switches traffic atomically (no "partial deploy" state)
- Keeps the old version running for instant rollback
- Lets smoke tests run against the new environment before switching

## Why Not Canary Deployments?

Canary (gradual traffic shifting) would require:
- Session affinity configuration
- Two versions handling requests simultaneously
- Real-time error rate monitoring
- Automated rollback on threshold breach

These add complexity without proportional benefit for a single-application monolith. Blue-green is simpler and sufficient.
