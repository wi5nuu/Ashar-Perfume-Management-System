---
title: Documentation Metrics
diataxis: reference
owner: Staff Software Engineer
update_frequency: per-release
classification: optional
---

# Documentation Metrics

## File Count by Category

| Category | Count | Files |
|---|---|---|
| Blueprint | 1 | `DOCUMENTATION_BLUEPRINT.md` |
| Index/README | 1 | `README.md` |
| Metrics | 1 | `documentation-metrics.md` |
| Glossary | 1 | `reference/glossary.md` |
| **Tutorials** | **2** | |
| Developer Onboarding | 1 | `tutorials/developer/onboarding.md` |
| Local Env Setup | 1 | `tutorials/developer/setup-local-env.md` |
| **How-To** | **2** | |
| Reset Password | 1 | `how-to/owner/reset-wholesale-password.md` |
| View Customer Orders | 1 | `how-to/owner/view-customer-orders.md` |
| **Reference** | **37** | |
| Business | 2 | `reference/business/business-context.md`, `stakeholder-registry.md` |
| Product | 1 | `reference/product/non-functional-requirements.md` |
| Architecture | 12 | `reference/architecture/c4-level-1-context.md`, `arc42.md`, `architecture-decision-records.md`, ADR-001–010 |
| Laravel | 4 | `reference/laravel/request-lifecycle.md`, `service-layer.md`, `queue-job-reference.md`, `auth-reference.md` |
| Backend | 2 | `reference/backend/folder-structure.md`, `api-response-standard.md` |
| Database | 3 | `reference/database/data-dictionary.md`, `migration-patterns.md`, `relationships.md` |
| API | 3 | `reference/api/endpoint-guide.md`, `api-documentation.md`, `webhook-reference.md` |
| Security | 2 | `reference/security/security-architecture.md`, `vulnerability-management.md` |
| DevOps | 3 | `reference/devops/ci-cd-pipeline.md`, `deployment-strategy.md`, `environment-configuration.md` |
| Cloud | 2 | `reference/cloud/infrastructure-diagram.md`, `cloud-cost-model.md` |
| Testing | 2 | `reference/testing/test-strategy.md`, `test-cases-reference.md` |
| Monitoring | 2 | `reference/monitoring/monitoring-architecture.md`, `alert-rules.md` |
| Operations | 3 | `reference/operations/runbook.md`, `backup-restore.md`, `incident-response.md` |
| Team | 2 | `reference/team/team-structure.md`, `onboarding-roles.md` |
| Compliance | 2 | `reference/compliance/compliance-matrix.md`, `data-privacy.md` |
| Governance | 2 | `reference/governance/change-management.md`, `code-review-process.md` |
| **Explanation** | **2** | |
| Development Workflow | 1 | `explanation/development-workflow.md` |
| Why Laravel | 1 | `explanation/why-laravel.md` |

## Total: ~44 files

## Coverage by Domain

| Domain | Coverage | Doc Files |
|---|---|---|
| Business | Complete | 2 |
| Product | Complete | 1 |
| Requirements | Covered in NFRs | 0 (standalone file) |
| Architecture | Complete | 12 (incl. 10 ADRs) |
| Laravel | Complete | 4 |
| Backend | Complete | 2 |
| Database | Complete | 3 |
| API | Complete | 3 |
| Security | Complete | 2 |
| DevOps | Complete | 3 |
| Cloud | Complete | 2 |
| Testing | Complete | 2 |
| Monitoring | Complete | 2 |
| Operations | Complete | 3 |
| Team | Complete | 2 |
| ADRs | Complete | 10 |
| Compliance | Complete | 2 |
| Governance | Complete | 2 |

## Standards Compliance

| Standard | Status |
|---|---|
| IEEE 29148 (Requirements) | Via stakeholder registry + NFRs |
| ISO/IEC 25010 (Quality) | Via NFRs + compliance matrix |
| arc42 (Architecture) | Via arc42.md |
| C4 Model (Visualization) | Via c4-level-1-context.md |
| OpenAPI 3.x (API docs) | Via endpoint guide + API doc |
| Diátaxis (Organization) | Via directory structure |
| ADR (Decisions) | Via ADR-001–010 |
| BABOK (Business) | Via stakeholder registry |
| ISO/IEC 27001 (Security) | Via security architecture |
| GDPR (Privacy) | Via data privacy doc |
