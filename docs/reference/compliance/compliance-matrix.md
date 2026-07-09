---
title: Compliance Matrix
diataxis: reference
standards:
  - ISO/IEC 27001
  - ISO/IEC 25010
  - GDPR
owner: Project Lead
update_frequency: quarterly
classification: mandatory
---

# Compliance Matrix

## Regulatory Compliance

| Requirement | Applicable Standard | Implementation | Status |
|---|---|---|---|
| Data encryption at rest | ISO 27001 A.10 | AES-256-CBC (APP_KEY) | Implemented |
| Data encryption in transit | ISO 27001 A.10 | TLS 1.3 | Implemented |
| Access control | ISO 27001 A.9 | Role-based middleware + Policies | Implemented |
| Audit logging | ISO 27001 A.12 | Activity log model + Notifications | Implemented |
| Backup & recovery | ISO 27001 A.12 | RDS snapshots + S3 backup | Implemented |
| Vulnerability management | ISO 27001 A.12 | Composer audit + Snyk | Implemented |
| Incident response | ISO 27001 A.16 | Runbook + SEV definitions | Implemented |
| Supplier management | ISO 27001 A.15 | Third-party risk in vendor onboarding | Documented |
| Business continuity | ISO 27001 A.17 | Blue-green deploy + Multi-AZ | Partially implemented |

## Data Privacy (GDPR Alignment)

| Right | Implementation |
|---|---|
| Right to access | User data export endpoint |
| Right to rectification | Profile edit in settings |
| Right to erasure | Soft delete + scheduled hard delete after 90 days |
| Right to restrict processing | Deactivate account (can_login = false) |
| Right to data portability | CSV export for transactions |
| Right to object | Opt-out for marketing communications |

## Quality Standards (ISO/IEC 25010)

| Characteristic | Measures |
|---|---|
| Functional suitability | Test coverage >80%, acceptance criteria per story |
| Performance efficiency | P95 < 2s, load test quarterly |
| Compatibility | Cross-browser (Chrome, Firefox, Edge), mobile responsive |
| Usability | Accessibility target WCAG 2.1 AA |
| Reliability | 99.9% uptime, Multi-AZ deployment |
| Security | OWASP Top 10 mitigated, SAST/DAST scans |
| Maintainability | PHPStan L6, Pint, ADRs |
| Portability | Docker containerized, ECS deployable |
