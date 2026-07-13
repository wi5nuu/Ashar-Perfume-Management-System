# Enterprise Documentation Blueprint — Laravel Application

**Diátaxis mode:** Reference / Explanation  
**Governing standards:** IEEE 29148, ISO/IEC 25010, arc42, C4 Model, OpenAPI 3.x, Diátaxis Framework, ADR (Nygard), BABOK  
**Owner:** Principal Software Architect / Engineering Manager  
**Update frequency:** Quarterly review, or on architectural change  
**Maturity level:** Enterprise → FAANG/Big Tech

---


## Table of Contents

1. [Documentation Governance & Standards Mapping](#1-documentation-governance--standards-mapping)
2. [Directory Tree (Diátaxis-Organized)](#2-directory-tree-diátaxis-organized)
3. [Document Inventory by Domain](#3-document-inventory-by-domain)
   - 3.1 Business Documentation
   - 3.2 Product Documentation
   - 3.3 Requirements Documentation
   - 3.4 Laravel Architecture Documentation
   - 3.5 Software Architecture (arc42)
   - 3.6 Backend Documentation
   - 3.7 Database Documentation
   - 3.8 API Documentation
   - 3.9 Security Documentation
   - 3.10 DevOps Documentation
   - 3.11 Cloud Documentation
   - 3.12 Testing Documentation
   - 3.13 Monitoring Documentation
   - 3.14 Operations Documentation
   - 3.15 Team Documentation
   - 3.16 Architecture Decision Records
   - 3.17 Compliance Documentation
   - 3.18 Documentation Governance
4. [Cross-Document Relationship Matrix](#4-cross-document-relationship-matrix)
5. [Document Maturity Mapping](#5-document-maturity-mapping)
6. [Self-Review: Principal Engineer Audit](#6-self-review-principal-engineer-audit)

---

## 1. Documentation Governance & Standards Mapping

### 1.1 Standards Compliance Matrix

Every document in this blueprint SHALL declare conformance to one or more of the following standards in its metadata header:

| Standard | Domain | Required Elements When Conforming |
|---|---|---|
| **IEEE 29148** | Requirements Engineering | §1 Introduction, §2 Overall Description, §3 System Features, §4 External Interface Requirements, §5 Non-Functional Requirements, §6 Appendices; unique requirement IDs (`REQ-{domain}-{NNN}`); "shall/should/may" language |
| **ISO/IEC 25010** | Software Quality | NFRs categorized by: Functional Suitability, Reliability, Performance Efficiency, Usability, Security, Compatibility, Maintainability, Portability |
| **arc42** | Architecture Documentation | All 12 sections: §1 Intro & Goals, §2 Constraints, §3 Context & Scope, §4 Solution Strategy, §5 Building Block View, §6 Runtime View, §7 Deployment View, §8 Crosscutting Concepts, §9 Architecture Decisions, §10 Quality Requirements, §11 Risks & Technical Debt, §12 Glossary |
| **C4 Model** | Architecture Diagrams | Level 1 (System Context), Level 2 (Container), Level 3 (Component), Level 4 (Code) hierarchy strictly observed |
| **OpenAPI 3.x** | API Documentation | Valid `openapi.yaml`/`openapi.json` as source of truth; `paths`, `schemas`, `securitySchemes`, `components/responses`; all REST endpoints; examples on every operation |
| **Diátaxis** | All Documents | Each document classified as exactly one of: Tutorial (learning), How-To (task), Reference (information), Explanation (understanding); style and tone match the mode |
| **ADR (Nygard)** | Architecture Decisions | Title, Status, Context, Decision, Consequences; indexed into arc42 §9 |
| **BABOK** | Business Analysis | Business rules traceable to requirements; stakeholder analysis; process modeling |

### 1.2 Document Metadata Header Template

Every documentation file SHALL begin with a YAML front-matter block:

```yaml
---
title: Document Title
diataxis: [tutorial | how-to | reference | explanation]
standards:
  - IEEE 29148
  - ISO/IEC 25010
  - arc42 §5
  - C4 Level 2
owner: Team/Role Name
update_frequency: [quarterly | per-release | on-change | on-demand]
prerequisites:
  - Document A
  - Document B
dependencies:
  - ADR-XXX
  - SRS §Y.Z
classification: [mandatory | recommended | optional]
audience: [Engineers | DevOps | QA | Product | All]
---
```

### 1.3 Requirement ID Naming Convention

All IEEE 29148-conformant requirements SHALL use the format:

```
REQ-{DOMAIN}-{NNN}
```

Domains: `BIZ` (Business), `FUNC` (Functional), `NFR` (Non-Functional), `UI` (User Interface), `API` (API), `SEC` (Security), `DATA` (Data), `INT` (Integration), `PERF` (Performance), `COMP` (Compliance)

### 1.4 ADR Numbering

All ADRs SHALL use the format `ADR-{NNN}` with sequential numbering, and SHALL be indexed in arc42 §9.

---

## 2. Directory Tree (Diátaxis-Organized)

```
docs/
├── README.md                          # Documentation home, navigation, quickstart
├── GLOSSARY.md                        # Master glossary (arc42 §12)
│
├── tutorials/                         # LEARNING-ORIENTED — step by step
│   ├── business/
│   │   └── domain-walkthrough.md      # Business domain overview tutorial
│   ├── developer/
│   │   ├── onboarding.md              # New developer onboarding (2-week plan)
│   │   ├── first-module.md            # Build your first module
│   │   ├── first-api-endpoint.md      # Create and test your first API endpoint
│   │   ├── setup-local-env.md         # Set up local development environment
│   │   └── writing-tests.md           # Getting started with testing
│   └── devops/
│       ├── deploy-first-release.md    # Deploy your first release
│       └── ci-cd-pipeline-walkthrough.md
│
├── how-to/                            # TASK-ORIENTED — goal driven
│   ├── business/
│   │   ├── how-to-define-kpis.md
│   │   └── how-to-perform-stakeholder-analysis.md
│   ├── backend/
│   │   ├── add-new-service.md
│   │   ├── add-new-action.md
│   │   ├── add-new-observer.md
│   │   ├── add-new-event-listener.md
│   │   ├── add-new-queue-job.md
│   │   ├── add-new-scheduled-task.md
│   │   ├── add-new-notification.md
│   │   ├── add-new-form-request.md
│   │   ├── add-new-api-resource.md
│   │   ├── implement-repository.md
│   │   ├── implement-dto.md
│   │   ├── handle-file-uploads.md
│   │   └── implement-custom-validation-rule.md
│   ├── database/
│   │   ├── add-new-migration.md
│   │   ├── add-new-seeder.md
│   │   ├── write-efficient-query.md
│   │   ├── perform-data-migration.md
│   │   ├── backup-database.md
│   │   └── restore-database.md
│   ├── api/
│   │   ├── add-new-api-version.md
│   │   ├── add-new-endpoint.md
│   │   ├── implement-rate-limiting.md
│   │   └── implement-webhook.md
│   ├── security/
│   │   ├── fix-xss-vulnerability.md
│   │   ├── fix-sql-injection.md
│   │   ├── fix-idor-vulnerability.md
│   │   ├── implement-rbac.md
│   │   ├── rotate-api-keys.md
│   │   └── conduct-security-review.md
│   ├── devops/
│   │   ├── docker-compose-guide.md
│   │   ├── kubernetes-deployment.md
│   │   ├── ci-cd-pipeline-guide.md
│   │   ├── add-new-queue-worker.md
│   │   ├── configure-horizon.md
│   │   ├── configure-supervisor.md
│   │   ├── zero-downtime-deploy.md
│   │   └── rollback-release.md
│   ├── cloud/
│   │   ├── provision-aws-infrastructure.md
│   │   ├── configure-cdn.md
│   │   ├── configure-object-storage.md
│   │   ├── configure-managed-database.md
│   │   ├── configure-managed-redis.md
│   │   └── configure-iam.md
│   ├── testing/
│   │   ├── write-unit-test.md
│   │   ├── write-feature-test.md
│   │   ├── write-api-test.md
│   │   ├── write-browser-test.md
│   │   ├── run-load-test.md
│   │   ├── implement-mutation-testing.md
│   │   └── setup-test-ci.md
│   ├── monitoring/
│   │   ├── set-up-grafana-dashboard.md
│   │   ├── set-up-prometheus-alerts.md
│   │   ├── set-up-sentry.md
│   │   ├── set-up-elk-stack.md
│   │   └── set-up-opentelemetry.md
│   ├── operations/
│   │   ├── runbook-database-failover.md
│   │   ├── runbook-cache-failure.md
│   │   ├── runbook-queue-backlog.md
│   │   ├── runbook-high-cpu.md
│   │   ├── runbook-high-memory.md
│   │   ├── runbook-tls-expiry.md
│   │   ├── incident-response-guide.md
│   │   └── disaster-recovery-guide.md
│   └── team/
│       ├── create-pull-request.md
│       ├── conduct-code-review.md
│       ├── use-git-flow.md
│       ├── write-commit-message.md
│       ├── conduct-sprint-planning.md
│       └── conduct-retrospective.md
│
├── reference/                         # INFORMATION-ORIENTED — precise, complete
│   ├── business/
│   │   ├── business-context.md        # §1 IEEE 29148 — Business Context & Vision
│   │   ├── stakeholder-registry.md    # Stakeholders, Personas, Roles
│   │   ├── business-rules-catalog.md  # All business rules with IDs
│   │   ├── glossary.md                # Domain glossary (cross-ref to arc42 §12)
│   │   └── success-metrics.md         # KPIs, OKRs, Success Metrics
│   ├── product/
│   │   ├── prd.md                     # Product Requirements Document
│   │   ├── feature-matrix.md          # Feature inventory with status
│   │   ├── functional-requirements.md # FR catalog (IEEE 29148 §3)
│   │   └── non-functional-requirements.md  # NFR catalog (ISO/IEC 25010)
│   ├── requirements/
│   │   ├── brd.md                     # Business Requirements Document (BABOK + IEEE 29148)
│   │   ├── srs.md                     # Software Requirements Specification (IEEE 29148 full)
│   │   ├── use-case-specifications.md  # Fully dressed use cases
│   │   ├── domain-model.md            # Domain model with entities, value objects, aggregates
│   │   ├── data-flow-diagrams.md      # DFDs contextualized
│   │   └── requirements-traceability-matrix.md  # RTM: BR → FR → SRS ID → TC ID
│   ├── architecture/
│   │   ├── arc42.md                   # Full arc42 document (§1–§12)
│   │   ├── c4-level-1-context.md      # System Context diagram + description
│   │   ├── c4-level-2-containers.md   # Container diagram + descriptions
│   │   ├── c4-level-3-components.md   # Component diagrams (per container)
│   │   ├── c4-level-4-code.md         # Code-level diagrams (selected only)
│   │   ├── uml-sequence.md            # UML Sequence diagrams
│   │   ├── uml-state.md               # UML State machine diagrams
│   │   ├── uml-class.md               # UML Class diagrams
│   │   ├── erd.md                     # Entity-Relationship diagrams
│   │   ├── deployment-diagram.md      # Deployment view (arc42 §7)
│   │   ├── infrastructure-diagram.md  # Network / infrastructure topology
│   │   └── service-dependency.md      # Service dependency graph
│   ├── laravel/
│   │   ├── request-lifecycle.md       # Full request lifecycle tracing
│   │   ├── service-container.md       # Bindings, resolutions, contextual binding
│   │   ├── service-providers.md       # All providers: register, boot, deferral
│   │   ├── middleware-stack.md        # Global, route, grouped middleware
│   │   ├── route-reference.md         # All named routes with middleware
│   │   ├── controller-conventions.md  # Controller naming, method signatures
│   │   ├── form-request-reference.md  # All form requests with rules
│   │   ├── validation-reference.md    # Custom rules, rule classes
│   │   ├── eloquent-models.md         # All models, relationships, scopes, accessors
│   │   ├── eloquent-relationships.md  # Relationship map (all types)
│   │   ├── repository-reference.md    # All repository interfaces + implementations
│   │   ├── service-layer.md           # All service classes and their responsibilities
│   │   ├── action-pattern.md          # Action classes inventory
│   │   ├── observer-registry.md       # All observers and events they hook into
│   │   ├── event-reference.md         # All events, listeners, subscribers
│   │   ├── notification-reference.md  # All notification classes + channels
│   │   ├── mail-reference.md          # All mailable classes
│   │   ├── queue-job-reference.md     # All jobs, queues, connections
│   │   ├── scheduler-reference.md     # All scheduled tasks + frequencies
│   │   ├── broadcast-reference.md     # All broadcast channels, events
│   │   ├── policy-gate-reference.md   # All policies, gates, abilities
│   │   ├── auth-reference.md          # Auth guards, providers, custom guards
│   │   ├── cache-reference.md         # Cache stores, keys (prefix convention), TTLs
│   │   ├── session-reference.md       # Session drivers, config
│   │   ├── filesystem-reference.md    # Disks, visibility, driver config
│   │   ├── api-resource-reference.md  # All API Resource classes, collections
│   │   ├── exception-reference.md     # Custom exceptions, handling, rendering
│   │   ├── logging-reference.md       # Log channels, levels, custom handlers
│   │   ├── configuration-reference.md # All config files and their overridable values
│   │   ├── environment-reference.md   # All .env variables with descriptions
│   │   ├── package-registry.md        # All Composer packages, purpose, versions
│   │   └── module-map.md              # Module/directory structure (DDD-style if used)
│   ├── backend/
│   │   ├── folder-structure.md        # Full directory tree with purpose
│   │   ├── namespace-conventions.md
│   │   ├── coding-standards.md        # PSR-12, project-specific overrides
│   │   ├── naming-conventions.md      # Classes, methods, variables, tables, columns
│   │   ├── api-versioning.md          # Version strategy (URL/header/accept)
│   │   ├── api-response-standard.md   # Envelope format, pagination meta, error shape
│   │   ├── error-handling-strategy.md # Exception hierarchy, HTTP status mapping
│   │   ├── validation-strategy.md     # Form request vs. manual, rule organization
│   │   ├── dto-conventions.md         # Data Transfer Object patterns
│   │   ├── pagination-conventions.md  # Cursor vs. offset, default page sizes
│   │   ├── filtering-conventions.md   # Query parameter filter convention
│   │   ├── sorting-conventions.md     # Sort parameter convention
│   │   └── search-strategy.md         # Full-text vs. Elastic/Meilisearch/Algolia
│   ├── database/
│   │   ├── physical-schema.md         # Full physical schema (table DDL representations)
│   │   ├── logical-schema.md          # Logical data model
│   │   ├── data-dictionary.md         # Every table, column, type, default, nullable
│   │   ├── constraint-registry.md     # PKs, FKs, unique, check constraints
│   │   ├── index-registry.md          # All indexes with columns and type
│   │   ├── view-definitions.md        # All database views
│   │   ├── trigger-registry.md        # All triggers
│   │   ├── seeder-reference.md        # All seeders, order, dependencies
│   │   ├── factory-reference.md       # All model factories
│   │   ├── migration-strategy.md      # Naming, squashing, irreversible migrations
│   │   ├── backup-strategy.md         # Schedule, retention, encryption
│   │   ├── restore-strategy.md        # PITR, full restore, partial restore
│   │   └── archiving-strategy.md      # Partitioning, archival tables, retention
│   ├── api/
│   │   ├── openapi.yaml               # SOURCE OF TRUTH — full OAS 3.x spec
│   │   └── openapi.json               # Auto-generated from YAML
│   ├── security/
│   │   ├── threat-model.md            # STRIDE per component
│   │   ├── owasp-mapping.md           # OWASP Top 10 + ASVS controls mapping
│   │   ├── rbac-matrix.md             # Role → Permission → Resource matrix
│   │   ├── permission-catalog.md      # All permissions, gates, policies
│   │   ├── secret-management.md       # Vault/AWS Secrets Manager/.env encryption
│   │   ├── encryption-at-rest.md      # Laravel encryption, model casting
│   │   ├── encryption-in-transit.md   # TLS, mTLS, HSTS
│   │   └── audit-log-schema.md        # Audit table structure, retention
│   ├── devops/
│   │   ├── dockerfile-reference.md    # Multi-stage Dockerfile breakdown
│   │   ├── docker-compose-reference.md
│   │   ├── kubernetes-manifests-dir/  # Directory of K8s manifests
│   │   ├── ci-cd-pipeline-reference.md # Pipeline stages, jobs, artifacts
│   │   ├── terraform-modules-dir/     # Terraform module reference
│   │   └── ansible-playbooks-dir/     # Ansible role/variable reference
│   ├── cloud/
│   │   ├── aws-architecture.md        # VPC, subnets, NAT, IGW, endpoints
│   │   ├── resource-inventory.md      # All cloud resources with tags
│   │   ├── iam-policies.md            # All IAM roles, policies, trust relations
│   │   └── cost-allocation.md         # Cost centers, tags, budgets
│   ├── testing/
│   │   ├── test-strategy.md           # Overall test approach, pyramid, quadrants
│   │   ├── test-case-catalog.md       # All test cases traceable to SRS IDs
│   │   ├── coverage-report.md         # Coverage by module, targets
│   │   └── performance-baselines.md   # Load test baselines, SLOs
│   ├── monitoring/
│   │   ├── metrics-catalog.md         # All application metrics with type, unit
│   │   ├── alert-rules.md             # All alert rules, thresholds, severity
│   │   ├── dashboard-catalog.md       # Grafana dashboard UIDs, panels
│   │   ├── log-structure.md           # Log format, fields, levels, correlation IDs
│   │   └── tracing-guide.md           # OpenTelemetry spans, sampling
│   ├── operations/
│   │   ├── slo-documentation.md       # Service Level Objectives
│   │   ├── sli-documentation.md       # Service Level Indicators
│   │   ├── capacity-plan.md           # Current capacity, growth projections
│   │   ├── scaling-guide.md           # Horizontal/vertical scaling procedures
│   │   ├── dr-plan.md                 # Disaster Recovery plan (RTO, RPO)
│   │   └── bc-plan.md                 # Business Continuity plan
│   ├── compliance/
│   │   ├── gdpr-compliance.md         # GDPR article mapping
│   │   ├── iso-27001-controls.md      # Annex A control mapping
│   │   ├── soc-2-controls.md          # SOC 2 trust principles mapping
│   │   ├── data-retention-policy.md   # Retention schedules by data class
│   │   ├── privacy-policy.md          # End-user privacy policy
│   │   └── audit-trail-spec.md        # Audit trail requirements, schema, retention
│   └── team/
│       ├── git-conventions.md         # Branch naming, commit format, merge strategy
│       ├── pr-template.md             # Pull Request template
│       ├── code-review-checklist.md   # Review checklist (security, perf, style)
│       └── tools-and-credentials.md   # Tool inventory, credential storage
│
└── explanation/                       # UNDERSTANDING-ORIENTED — discursive
    ├── business/
    │   ├── business-vision.md         # Vision, mission, strategic goals
    │   ├── business-process-models.md # BPMN or UML activity diagrams
    │   └── monetization-strategy.md   # Revenue model, pricing rationale
    ├── product/
    │   ├── product-vision.md          # Product vision, roadmap narrative
    │   ├── user-story-map.md          # Story mapping narrative
    │   └── competitive-analysis.md    # Market positioning rationale
    ├── architecture/
    │   ├── architecture-philosophy.md # DDD, Clean, Hexagonal rationale
    │   ├── modular-monolith-rationale.md
    │   ├── event-driven-rationale.md  # Why events, which patterns
    │   ├── microservice-readiness.md  # Assessment: when to split
    │   ├── domain-model-rationale.md  # Ubiquitous language, bounded contexts
    │   └── technical-debt-log.md      # arc42 §11 — Technical debt register
    ├── security/
    │   ├── security-philosophy.md     # Defense-in-depth, zero trust rationale
    │   ├── encryption-rationale.md    # Why specific algorithms, key rotation
    │   └── compliance-rationale.md    # Why GDPR/ISO/SOC2 apply
    ├── decisions/                     # ADRs (arc42 §9) — Nygard format
    │   ├── ADR-001-database-choice.md
    │   ├── ADR-002-auth-strategy.md
    │   ├── ADR-003-cache-strategy.md
    │   ├── ADR-004-queue-strategy.md
    │   ├── ADR-005-search-strategy.md
    │   ├── ADR-006-deployment-strategy.md
    │   ├── ADR-007-cloud-provider.md
    │   ├── ADR-008-api-versioning.md
    │   ├── ADR-009-frontend-framework.md
    │   ├── ADR-010-payment-provider.md
    │   ├── ADR-011-file-storage.md
    │   ├── ADR-012-logging-strategy.md
    │   ├── ADR-013-monitoring-strategy.md
    │   ├── ADR-014-ci-cd-tool.md
    │   ├── ADR-015-iac-tool.md
    │   ├── ADR-016-testing-framework.md
    │   └── ADR-017-package-adoption-xxx.md  # One per major third-party
    ├── devops/
    │   ├── infrastructure-philosophy.md  # Immutable infrastructure, cattle vs pets
    │   └── deployment-rationale.md       # Blue/green, canary, rolling rationale
    ├── operations/
    │   ├── incident-management-philosophy.md
    │   └── chaos-engineering-rationale.md
    └── team/
        ├── workflow-philosophy.md         # Why Git Flow, why Scrum, etc.
        ├── agile-rationale.md
        └── engineering-culture.md
```

---

## 3. Document Inventory by Domain

### 3.1 Business Documentation

| File | Diátaxis | Standard | Owner | Frequency | Classification |
|---|---|---|---|---|---|
| `business/business-context.md` | Reference | IEEE 29148 §1.2 | Product Manager | Quarterly | Mandatory |
| `business/stakeholder-registry.md` | Reference | BABOK §2.3 | Product Manager | On-change | Mandatory |
| `business/business-rules-catalog.md` | Reference | IEEE 29148 §3, BABOK §8.4 | Business Analyst | Per-release | Mandatory |
| `business/glossary.md` | Reference | IEEE 29148 §6, arc42 §12 | Tech Writer | Per-release | Mandatory |
| `business/success-metrics.md` | Reference | IEEE 29148 §2.4 | Product Manager | Quarterly | Recommended |
| `explanation/business/business-vision.md` | Explanation | IEEE 29148 §1.1 | CTO/CEO | Annually | Mandatory |
| `explanation/business/business-process-models.md` | Explanation | BABOK §6.3 | Business Analyst | Per-release | Recommended |
| `tutorials/business/domain-walkthrough.md` | Tutorial | BABOK §3.1 | Tech Writer | Onboarding | Recommended |
| `how-to/business/how-to-define-kpis.md` | How-To | BABOK §11.4 | Product Manager | On-demand | Optional |

**Relationships:** Business Rules → Functional Requirements (see Requirements Traceability Matrix). Glossary cross-referenced by arc42 §12.

---

### 3.2 Product Documentation

| File | Diátaxis | Standard | Owner | Frequency | Classification |
|---|---|---|---|---|---|
| `reference/product/prd.md` | Reference | IEEE 29148, BABOK | Product Manager | Per-release | Mandatory |
| `reference/product/feature-matrix.md` | Reference | — | Product Manager | Per-release | Mandatory |
| `reference/product/functional-requirements.md` | Reference | IEEE 29148 §3 | Product Manager | Per-release | Mandatory |
| `reference/product/non-functional-requirements.md` | Reference | ISO/IEC 25010 | Architect | Per-release | Mandatory |
| `explanation/product/product-vision.md` | Explanation | — | Product Manager | Annually | Mandatory |
| `explanation/product/user-story-map.md` | Explanation | — | Product Owner | Per-release | Recommended |
| `explanation/product/competitive-analysis.md` | Explanation | — | Product Manager | Annually | Optional |

**ISO/IEC 25010 NFR Categorization:**
All NFRs SHALL be tagged with exactly one sub-characteristic from: `functional_completeness`, `functional_correctness`, `functional_appropriateness`, `time_behavior`, `resource_utilization`, `capacity`, `coexistence`, `interoperability`, `appropriateness_recognizability`, `learnability`, `operability`, `user_error_protection`, `accessibility`, `faultlessness`, `fault_tolerance`, `recoverability`, `confidentiality`, `integrity`, `non-repudiation`, `accountability`, `authenticity`, `maintainability_modularity`, `maintainability_reusability`, `maintainability_analyzability`, `maintainability_modifiability`, `maintainability_testability`, `adaptability`, `installability`, `replaceability`.

---

### 3.3 Requirements Documentation

| File | Diátaxis | Standard | Owner | Frequency | Classification |
|---|---|---|---|---|---|
| `reference/requirements/brd.md` | Reference | IEEE 29148, BABOK | Business Analyst | Per-release | Mandatory |
| `reference/requirements/srs.md` | Reference | IEEE 29148 (full) | Business Analyst/Architect | Per-release | Mandatory |
| `reference/requirements/use-case-specifications.md` | Reference | IEEE 29148 §3.3 | Business Analyst | Per-release | Mandatory |
| `reference/requirements/domain-model.md` | Reference | IEEE 29148 §6 | Architect | Per-release | Mandatory |
| `reference/requirements/data-flow-diagrams.md` | Reference | IEEE 29148 §2.2 | Architect | Per-release | Recommended |
| `reference/requirements/requirements-traceability-matrix.md` | Reference | IEEE 29148 §5.5 | QA Lead | Per-release | Mandatory |

**SRS Structure (IEEE 29148):**
1. Introduction (§1) — Purpose, scope, definitions, references, overview
2. Overall Description (§2) — Product perspective, functions, user characteristics, constraints, assumptions, dependencies
3. System Features (§3) — Feature groupings, each with ID, description, priority, stimuli/responses
4. External Interface Requirements (§4) — User, hardware, software, communications interfaces
5. Non-Functional Requirements (§5) — Per ISO/IEC 25010 categories (not ad-hoc)
6. Appendices (§6) — Glossary, models, issues list, to-be-determined items

**Requirement Traceability Matrix Columns:**
Business Rule ID → Functional Requirement ID → SRS Section ID → Use Case ID → Test Case ID → ADR Reference

---

### 3.4 Laravel Architecture Documentation

All files under `reference/laravel/`. Each is **Reference** mode, **Mandatory**, owned by **Staff Software Engineer**, updated **on-change**.

| File | arc42 Mapping | C4 Level |
|---|---|---|
| `request-lifecycle.md` | §6 Runtime View | — |
| `service-container.md` | §5 Building Block View | L3 |
| `service-providers.md` | §5 Building Block View | L3 |
| `middleware-stack.md` | §8 Crosscutting | L2 |
| `route-reference.md` | §5 Building Block View | L2 |
| `controller-conventions.md` | §5 Building Block View | L3 |
| `form-request-reference.md` | §8 Crosscutting | L3 |
| `validation-reference.md` | §8 Crosscutting | L3 |
| `eloquent-models.md` | §5 Building Block View | L3 |
| `eloquent-relationships.md` | §5 Building Block View | L4 |
| `repository-reference.md` | §5 Building Block View | L3 |
| `service-layer.md` | §5 Building Block View | L3 |
| `action-pattern.md` | §5 Building Block View | L3 |
| `observer-registry.md` | §6 Runtime View | L3 |
| `event-reference.md` | §6 Runtime View | L3 |
| `notification-reference.md` | §8 Crosscutting | L3 |
| `mail-reference.md` | §8 Crosscutting | L3 |
| `queue-job-reference.md` | §6 Runtime View | L2 |
| `scheduler-reference.md` | §6 Runtime View | L2 |
| `broadcast-reference.md` | §6 Runtime View | L3 |
| `policy-gate-reference.md` | §8 Crosscutting | L3 |
| `auth-reference.md` | §8 Crosscutting | L2 |
| `cache-reference.md` | §8 Crosscutting | L2 |
| `session-reference.md` | §8 Crosscutting | L2 |
| `filesystem-reference.md` | §8 Crosscutting | L2 |
| `api-resource-reference.md` | §5 Building Block View | L3 |
| `exception-reference.md` | §8 Crosscutting | L3 |
| `logging-reference.md` | §8 Crosscutting | L2 |
| `configuration-reference.md` | §8 Crosscutting | L1 |
| `environment-reference.md` | §8 Crosscutting | L1 |
| `package-registry.md` | §2 Constraints | L1 |
| `module-map.md` | §5 Building Block View | L2 |

**Relationships:** Each Laravel reference document cross-references the corresponding arc42 section and C4 level where the component appears.

---

### 3.5 Software Architecture (arc42)

| File | Diátaxis | Standard | Owner | Frequency | Classification |
|---|---|---|---|---|---|
| `reference/architecture/arc42.md` | Reference | arc42 all 12 sections | Principal Architect | Per-release | Mandatory |
| `reference/architecture/c4-level-1-context.md` | Reference | C4 Level 1 | Principal Architect | Per-release | Mandatory |
| `reference/architecture/c4-level-2-containers.md` | Reference | C4 Level 2 | Principal Architect | Per-release | Mandatory |
| `reference/architecture/c4-level-3-components.md` | Reference | C4 Level 3 | Staff Engineer | Per-release | Mandatory |
| `reference/architecture/c4-level-4-code.md` | Reference | C4 Level 4 | Staff Engineer | On-demand | Optional |
| `reference/architecture/uml-sequence.md` | Reference | UML 2.x | Staff Engineer | On-demand | Recommended |
| `reference/architecture/uml-state.md` | Reference | UML 2.x | Staff Engineer | On-demand | Recommended |
| `reference/architecture/uml-class.md` | Reference | UML 2.x | Staff Engineer | Per-release | Recommended |
| `reference/architecture/erd.md` | Reference | ER Notation | Database Architect | Per-release | Mandatory |
| `reference/architecture/deployment-diagram.md` | Reference | arc42 §7, C4 Deployment | DevOps Architect | Per-release | Mandatory |
| `reference/architecture/infrastructure-diagram.md` | Reference | arc42 §7 | Cloud Architect | Per-release | Mandatory |
| `reference/architecture/service-dependency.md` | Reference | arc42 §6 | SRE | Per-release | Mandatory |
| `explanation/architecture/architecture-philosophy.md` | Explanation | arc42 §4 | Principal Architect | Annually | Mandatory |
| `explanation/architecture/modular-monolith-rationale.md` | Explanation | arc42 §4 | Principal Architect | On-change | Recommended |
| `explanation/architecture/event-driven-rationale.md` | Explanation | arc42 §4 | Principal Architect | On-change | Recommended |
| `explanation/architecture/microservice-readiness.md` | Explanation | arc42 §4 | Principal Architect | Quarterly | Recommended |
| `explanation/architecture/domain-model-rationale.md` | Explanation | arc42 §4 | Principal Architect | Per-release | Mandatory |
| `explanation/architecture/technical-debt-log.md` | Explanation | arc42 §11 | All Engineers | Per-sprint | Mandatory |

**arc42 §9 (Architecture Decisions):** Index file that lists all ADR-xxx documents with status (proposed/accepted/deprecated/superseded). Each ADR is a separate file under `explanation/decisions/`.

**arc42 §12 (Glossary):** Single source of truth. The master `GLOSSARY.md` at `docs/` root.

---

### 3.6 Backend Documentation

| File | Diátaxis | Owner | Frequency | Classification |
|---|---|---|---|---|
| `reference/backend/folder-structure.md` | Reference | Staff Engineer | On-change | Mandatory |
| `reference/backend/namespace-conventions.md` | Reference | Staff Engineer | On-change | Mandatory |
| `reference/backend/coding-standards.md` | Reference | Staff Engineer | Quarterly | Mandatory |
| `reference/backend/naming-conventions.md` | Reference | Staff Engineer | Quarterly | Mandatory |
| `reference/backend/api-versioning.md` | Reference | Staff Engineer | On-change | Mandatory |
| `reference/backend/api-response-standard.md` | Reference | Staff Engineer | Per-release | Mandatory |
| `reference/backend/error-handling-strategy.md` | Reference | Staff Engineer | Per-release | Mandatory |
| `reference/backend/validation-strategy.md` | Reference | Staff Engineer | Per-release | Mandatory |
| `reference/backend/dto-conventions.md` | Reference | Staff Engineer | Per-release | Recommended |
| `reference/backend/pagination-conventions.md` | Reference | Staff Engineer | On-change | Mandatory |
| `reference/backend/filtering-conventions.md` | Reference | Staff Engineer | On-change | Recommended |
| `reference/backend/sorting-conventions.md` | Reference | Staff Engineer | On-change | Recommended |
| `reference/backend/search-strategy.md` | Reference | Staff Engineer | On-change | Recommended |

Each `how-to/backend/*.md` file references the relevant reference document for conventions.

---

### 3.7 Database Documentation

| File | Diátaxis | Standard | Owner | Frequency | Classification |
|---|---|---|---|---|---|
| `reference/database/physical-schema.md` | Reference | ER Notation | Database Architect | Per-migration | Mandatory |
| `reference/database/logical-schema.md` | Reference | ER Notation | Database Architect | Per-migration | Mandatory |
| `reference/database/data-dictionary.md` | Reference | — | Database Architect | Per-migration | Mandatory |
| `reference/database/constraint-registry.md` | Reference | — | Database Architect | Per-migration | Mandatory |
| `reference/database/index-registry.md` | Reference | — | Database Architect | Per-migration | Mandatory |
| `reference/database/view-definitions.md` | Reference | — | Database Architect | On-change | Recommended |
| `reference/database/trigger-registry.md` | Reference | — | Database Architect | On-change | Recommended |
| `reference/database/seeder-reference.md` | Reference | — | Staff Engineer | Per-migration | Mandatory |
| `reference/database/factory-reference.md` | Reference | — | Staff Engineer | Per-migration | Mandatory |
| `reference/database/migration-strategy.md` | Reference | — | Staff Engineer | Quarterly | Mandatory |
| `reference/database/backup-strategy.md` | Reference | — | DevOps/SRE | Quarterly | Mandatory |
| `reference/database/restore-strategy.md` | Reference | — | DevOps/SRE | Quarterly | Mandatory |
| `reference/database/archiving-strategy.md` | Reference | — | Database Architect | Per-release | Recommended |

**Cross-reference:** `reference/architecture/erd.md` provides the visual ER diagram. `reference/database/data-dictionary.md` provides the column-level detail.

---

### 3.8 API Documentation

| File | Diátaxis | Standard | Owner | Frequency | Classification |
|---|---|---|---|---|---|
| `reference/api/openapi.yaml` | Reference | OpenAPI 3.x | Staff Engineer | Per-release | Mandatory |
| `reference/api/openapi.json` | Reference | OpenAPI 3.x | Staff Engineer | Per-release | Mandatory |

**Rules:**
- `openapi.yaml` is the **single source of truth**. All changes to the API SHALL be made by editing this file BEFORE writing controller code (API-first).
- The file SHALL include:
  - `info.title`, `info.version`, `info.description`
  - `servers` (dev, staging, prod)
  - `paths` — every endpoint: `get`, `post`, `put`, `patch`, `delete`
  - `components.schemas` — all request/response DTOs with typed properties
  - `components.responses` — reusable error responses (400, 401, 403, 404, 422, 429, 500)
  - `components.securitySchemes` — Bearer JWT, Sanctum token, API key
  - `security` — default security requirement applied globally
  - `tags` — grouping by domain/resource
- Examples SHALL be provided on every `requestBody` and every `response`.
- Webhooks SHALL use OpenAPI 3.x `callbacks` or reference an AsyncAPI spec.
- Rate limiting headers (`X-RateLimit-Limit`, `X-RateLimit-Remaining`, `X-RateLimit-Reset`) SHALL be documented in `headers`.
- Pagination SHALL use a reusable `components/schemas/Pagination` schema.

**Generated from spec:** Swagger UI and Redoc are presentation layers generated from `openapi.yaml`, not maintained separately.

---

### 3.9 Security Documentation

| File | Diátaxis | Standard | Owner | Frequency | Classification |
|---|---|---|---|---|---|
| `reference/security/threat-model.md` | Reference | STRIDE | Security Engineer | Per-release | Mandatory |
| `reference/security/owasp-mapping.md` | Reference | OWASP ASVS | Security Engineer | Quarterly | Mandatory |
| `reference/security/rbac-matrix.md` | Reference | NIST RBAC | Security Engineer | Per-release | Mandatory |
| `reference/security/permission-catalog.md` | Reference | — | Security Engineer | Per-release | Mandatory |
| `reference/security/secret-management.md` | Reference | — | DevOps/Security | Quarterly | Mandatory |
| `reference/security/encryption-at-rest.md` | Reference | AES-256 | Security Engineer | Quarterly | Mandatory |
| `reference/security/encryption-in-transit.md` | Reference | TLS 1.3 | Security Engineer | Quarterly | Mandatory |
| `reference/security/audit-log-schema.md` | Reference | — | Security Engineer | Per-release | Recommended |
| `explanation/security/security-philosophy.md` | Explanation | — | Security Architect | Annually | Mandatory |
| `explanation/security/encryption-rationale.md` | Explanation | — | Security Architect | On-change | Recommended |
| `explanation/security/compliance-rationale.md` | Explanation | — | Security Architect | Annually | Mandatory |

**OWASP Top 10 Coverage:** Every OWASP Top 10 category SHALL have at least one document (How-To or Reference) that describes the mitigation strategy for this project.

| OWASP Category | Mitigation Document(s) |
|---|---|
| A01: Broken Access Control | `reference/security/rbac-matrix.md`, `reference/laravel/policy-gate-reference.md` |
| A02: Cryptographic Failures | `reference/security/encryption-at-rest.md`, `reference/security/encryption-in-transit.md` |
| A03: Injection | `how-to/security/fix-sql-injection.md`, `reference/laravel/eloquent-models.md` |
| A04: Insecure Design | `reference/architecture/arc42.md`, `explanation/architecture/architecture-philosophy.md` |
| A05: Security Misconfiguration | `how-to/security/conduct-security-review.md`, `reference/devops/dockerfile-reference.md` |
| A06: Vulnerable Components | `reference/laravel/package-registry.md`, automated Dependabot |
| A07: Auth Failures | `reference/laravel/auth-reference.md`, `ADR-002-auth-strategy.md` |
| A08: Data Integrity Failures | `reference/laravel/queue-job-reference.md` (signed payloads) |
| A09: Logging & Monitoring Failures | `reference/monitoring/log-structure.md`, `reference/security/audit-log-schema.md` |
| A10: SSRF | `how-to/security/fix-xss-vulnerability.md`, middleware layer |

---

### 3.10 DevOps Documentation

| File | Diátaxis | Owner | Frequency | Classification |
|---|---|---|---|---|
| `reference/devops/dockerfile-reference.md` | Reference | DevOps | On-change | Mandatory |
| `reference/devops/docker-compose-reference.md` | Reference | DevOps | On-change | Mandatory |
| `reference/devops/kubernetes-manifests-dir/` | Reference | DevOps | Per-release | Recommended |
| `reference/devops/ci-cd-pipeline-reference.md` | Reference | DevOps | Per-release | Mandatory |
| `reference/devops/terraform-modules-dir/` | Reference | DevOps | On-change | Recommended |
| `reference/devops/ansible-playbooks-dir/` | Reference | DevOps | On-change | Optional |
| `how-to/devops/docker-compose-guide.md` | How-To | DevOps | On-change | Mandatory |
| `how-to/devops/kubernetes-deployment.md` | How-To | DevOps | On-change | Recommended |
| `how-to/devops/ci-cd-pipeline-guide.md` | How-To | DevOps | Per-release | Mandatory |
| `how-to/devops/add-new-queue-worker.md` | How-To | DevOps | On-change | Mandatory |
| `how-to/devops/configure-horizon.md` | How-To | DevOps | On-change | Mandatory |
| `how-to/devops/configure-supervisor.md` | How-To | DevOps | On-change | Mandatory |
| `how-to/devops/zero-downtime-deploy.md` | How-To | DevOps | On-change | Mandatory |
| `how-to/devops/rollback-release.md` | How-To | DevOps | On-change | Mandatory |
| `explanation/devops/infrastructure-philosophy.md` | Explanation | DevOps Architect | Annually | Recommended |
| `explanation/devops/deployment-rationale.md` | Explanation | DevOps Architect | Annually | Recommended |

**CI/CD Pipeline Reference SHALL document:**
- Pipeline trigger conditions
- Stage definitions and ordering
- Artifact persistence rules
- Environment promotion gates (dev → staging → canary → prod)
- Secret injection strategy
- Test execution order and parallelism
- Deployment strategy (blue/green, rolling, canary)
- Rollback automation
- Slack/email notification configuration

---

### 3.11 Cloud Documentation

| File | Diátaxis | Owner | Frequency | Classification |
|---|---|---|---|---|
| `reference/cloud/aws-architecture.md` | Reference | Cloud Architect | On-change | Mandatory |
| `reference/cloud/resource-inventory.md` | Reference | Cloud Architect | Monthly | Mandatory |
| `reference/cloud/iam-policies.md` | Reference | Cloud Architect | On-change | Mandatory |
| `reference/cloud/cost-allocation.md` | Reference | Cloud Architect | Monthly | Recommended |
| `how-to/cloud/provision-aws-infrastructure.md` | How-To | Cloud Architect | On-change | Mandatory |
| `how-to/cloud/configure-cdn.md` | How-To | Cloud Architect | On-change | Recommended |
| `how-to/cloud/configure-object-storage.md` | How-To | Cloud Architect | On-change | Mandatory |
| `how-to/cloud/configure-managed-database.md` | How-To | Cloud Architect | On-change | Mandatory |
| `how-to/cloud/configure-managed-redis.md` | How-To | Cloud Architect | On-change | Mandatory |
| `how-to/cloud/configure-iam.md` | How-To | Cloud Architect | On-change | Mandatory |

**Cloud architecture documentation SHALL include:**
- VPC topology with CIDR ranges, subnets (public/private), NAT gateways, Internet Gateway, VPC Endpoints
- Multi-AZ deployment strategy
- Auto-scaling policies and thresholds
- CDN (CloudFront) distribution configuration
- WAF rules
- Route53 DNS configuration
- RDS configuration (instance class, storage type, backup window, maintenance window)
- ElastiCache Redis configuration (node type, shards, replicas)
- S3 bucket policies and lifecycle rules
- ECS/EKS cluster configuration

---

### 3.12 Testing Documentation

| File | Diátaxis | Standard | Owner | Frequency | Classification |
|---|---|---|---|---|---|
| `reference/testing/test-strategy.md` | Reference | ISO 29119 | QA Lead | Quarterly | Mandatory |
| `reference/testing/test-case-catalog.md` | Reference | IEEE 29148 §5.5 | QA Lead | Per-release | Mandatory |
| `reference/testing/coverage-report.md` | Reference | — | QA Lead | Per-release | Mandatory |
| `reference/testing/performance-baselines.md` | Reference | — | QA Lead | Per-release | Recommended |
| `how-to/testing/write-unit-test.md` | How-To | — | QA Lead | On-change | Mandatory |
| `how-to/testing/write-feature-test.md` | How-To | — | QA Lead | On-change | Mandatory |
| `how-to/testing/write-api-test.md` | How-To | — | QA Lead | On-change | Mandatory |
| `how-to/testing/write-browser-test.md` | How-To | — | QA Lead | On-change | Recommended |
| `how-to/testing/run-load-test.md` | How-To | — | QA/SRE | Per-release | Recommended |
| `how-to/testing/implement-mutation-testing.md` | How-To | — | QA Lead | Per-release | Optional |
| `how-to/testing/setup-test-ci.md` | How-To | — | DevOps | On-change | Mandatory |

**Test Pyramid Compliance:**
- **Unit tests** (60-70% of total) — PHPUnit, test individual classes/methods in isolation
- **Feature/Integration tests** (20-25%) — Test HTTP endpoints, database interactions, queue jobs
- **Browser tests** (5-10%) — Laravel Dusk for critical user journeys
- **API tests** (5-10%) — Dedicated contract tests against OpenAPI spec

**Requirement Traceability:** Every test case in `test-case-catalog.md` SHALL include the SRS ID it validates. The `requirements-traceability-matrix.md` SHALL include the Test Case ID column.

---

### 3.13 Monitoring Documentation

| File | Diátaxis | Owner | Frequency | Classification |
|---|---|---|---|---|
| `reference/monitoring/metrics-catalog.md` | Reference | SRE | Per-release | Mandatory |
| `reference/monitoring/alert-rules.md` | Reference | SRE | Per-release | Mandatory |
| `reference/monitoring/dashboard-catalog.md` | Reference | SRE | Per-release | Mandatory |
| `reference/monitoring/log-structure.md` | Reference | Staff Engineer | On-change | Mandatory |
| `reference/monitoring/tracing-guide.md` | Reference | Staff Engineer | On-change | Recommended |
| `how-to/monitoring/set-up-grafana-dashboard.md` | How-To | SRE | On-change | Recommended |
| `how-to/monitoring/set-up-prometheus-alerts.md` | How-To | SRE | On-change | Mandatory |
| `how-to/monitoring/set-up-sentry.md` | How-To | Staff Engineer | On-change | Mandatory |
| `how-to/monitoring/set-up-elk-stack.md` | How-To | DevOps | On-change | Recommended |
| `how-to/monitoring/set-up-opentelemetry.md` | How-To | Staff Engineer | On-change | Recommended |

**Metrics Categories (RED Method):**
- **Rate** — Requests per second (RPS) per endpoint, per queue, per service
- **Errors** — HTTP 4xx/5xx rates, exception rates, job failure rates
- **Duration** — P50/P95/P99 latency per endpoint, per queue job, per external service call

**Alert Severity Levels:**
- P0 (Critical) — Respond within 15 minutes, 24/7
- P1 (High) — Respond within 1 hour, business hours
- P2 (Medium) — Respond within 8 hours, business hours
- P3 (Low) — Respond within 5 business days

---

### 3.14 Operations Documentation

| File | Diátaxis | Owner | Frequency | Classification |
|---|---|---|---|---|
| `reference/operations/slo-documentation.md` | Reference | SRE | Quarterly | Mandatory |
| `reference/operations/sli-documentation.md` | Reference | SRE | Quarterly | Mandatory |
| `reference/operations/capacity-plan.md` | Reference | SRE/Cloud Arch | Monthly | Mandatory |
| `reference/operations/scaling-guide.md` | Reference | SRE | Quarterly | Mandatory |
| `reference/operations/dr-plan.md` | Reference | SRE | Quarterly | Mandatory |
| `reference/operations/bc-plan.md` | Reference | SRE | Quarterly | Mandatory |
| `how-to/operations/runbook-database-failover.md` | How-To | SRE | Quarterly | Mandatory |
| `how-to/operations/runbook-cache-failure.md` | How-To | SRE | Quarterly | Mandatory |
| `how-to/operations/runbook-queue-backlog.md` | How-To | SRE | Quarterly | Mandatory |
| `how-to/operations/runbook-high-cpu.md` | How-To | SRE | Quarterly | Mandatory |
| `how-to/operations/runbook-high-memory.md` | How-To | SRE | Quarterly | Mandatory |
| `how-to/operations/runbook-tls-expiry.md` | How-To | SRE | Quarterly | Mandatory |
| `how-to/operations/incident-response-guide.md` | How-To | SRE | Quarterly | Mandatory |
| `how-to/operations/disaster-recovery-guide.md` | How-To | SRE | Quarterly | Mandatory |
| `explanation/operations/incident-management-philosophy.md` | Explanation | Engineering Mgr | Annually | Recommended |
| `explanation/operations/chaos-engineering-rationale.md` | Explanation | SRE | Annually | Optional |

**Runbook Structure (each runbook):**
1. **Metadata** — Title, severity, owner, response time
2. **Symptoms** — How to detect (alert name, dashboard panel, log pattern)
3. **Initial Diagnosis** — Commands to run, queries to check
4. **Resolution Steps** — Numbered, specific, with commands and expected outputs
5. **Rollback Steps** — If resolution involves a change, how to revert
6. **Post-Incident** — Verification steps, data to capture for postmortem
7. **References** — Links to related runbooks, ADRs, architectural docs

---

### 3.15 Team Documentation

| File | Diátaxis | Owner | Frequency | Classification |
|---|---|---|---|---|
| `reference/team/git-conventions.md` | Reference | Engineering Mgr | Onboarding | Mandatory |
| `reference/team/pr-template.md` | Reference | Engineering Mgr | Onboarding | Mandatory |
| `reference/team/code-review-checklist.md` | Reference | Engineering Mgr | Quarterly | Mandatory |
| `reference/team/tools-and-credentials.md` | Reference | DevOps | On-change | Mandatory |
| `tutorials/developer/onboarding.md` | Tutorial | Engineering Mgr | Quarterly | Mandatory |
| `tutorials/developer/first-module.md` | Tutorial | Staff Engineer | Per-release | Recommended |
| `tutorials/developer/first-api-endpoint.md` | Tutorial | Staff Engineer | Per-release | Recommended |
| `tutorials/developer/setup-local-env.md` | Tutorial | DevOps | On-change | Mandatory |
| `tutorials/developer/writing-tests.md` | Tutorial | QA Lead | Per-release | Recommended |
| `how-to/team/create-pull-request.md` | How-To | Engineering Mgr | Onboarding | Mandatory |
| `how-to/team/conduct-code-review.md` | How-To | Engineering Mgr | Onboarding | Mandatory |
| `how-to/team/use-git-flow.md` | How-To | Engineering Mgr | Onboarding | Mandatory |
| `how-to/team/write-commit-message.md` | How-To | Engineering Mgr | Onboarding | Mandatory |
| `explanation/team/workflow-philosophy.md` | Explanation | Engineering Mgr | Annually | Recommended |
| `explanation/team/agile-rationale.md` | Explanation | Engineering Mgr | Annually | Recommended |
| `explanation/team/engineering-culture.md` | Explanation | CTO/Eng Mgr | Annually | Recommended |

**Onboarding Tutorial Structure (2-week plan):**
- Day 1-2: Environment setup, local dev, first `php artisan serve`
- Day 3-4: Reading the codebase (arc42, Laravel references, folder structure)
- Day 5-7: First task — small bug fix with PR
- Day 8-10: First feature — simple endpoint with tests
- Day 11-12: Understanding the deployment pipeline
- Day 13-14: On-call shadowing, runbook review

---

### 3.16 Architecture Decision Records

All files under `explanation/decisions/`. Each is **Explanation** mode, **Nygard ADR format**. Owner: **Principal Architect**. Updated **on-change**.

| File | Title | Status |
|---|---|---|
| `ADR-001-database-choice.md` | Database Selection | Accepted |
| `ADR-002-auth-strategy.md` | Authentication Strategy | Accepted |
| `ADR-003-cache-strategy.md` | Cache Strategy | Accepted |
| `ADR-004-queue-strategy.md` | Queue/Dispatch Strategy | Accepted |
| `ADR-005-search-strategy.md` | Search Engine Strategy | Proposed |
| `ADR-006-deployment-strategy.md` | Deployment Strategy | Accepted |
| `ADR-007-cloud-provider.md` | Cloud Provider Decision | Accepted |
| `ADR-008-api-versioning.md` | API Versioning Strategy | Accepted |
| `ADR-009-frontend-framework.md` | Frontend Framework | Accepted |
| `ADR-010-payment-provider.md` | Payment Provider | Proposed |
| `ADR-011-file-storage.md` | File Storage Strategy | Accepted |
| `ADR-012-logging-strategy.md` | Centralized Logging | Accepted |
| `ADR-013-monitoring-strategy.md` | Monitoring & Alerting | Accepted |
| `ADR-014-ci-cd-tool.md` | CI/CD Tooling | Accepted |
| `ADR-015-iac-tool.md` | Infrastructure as Code | Accepted |
| `ADR-016-testing-framework.md` | Testing Framework | Accepted |
| `ADR-017-package-adoption-xxx.md` | Per major third-party package | Varies |

**ADR Template (Nygard Format):**

```yaml
---
title: ADR-{NNN}: {Title}
status: [Proposed | Accepted | Deprecated | Superseded]
date: {YYYY-MM-DD}
deciders: {List of decision-makers}
---
```

```
## Context
{What is the issue motivating this decision? What forces are at play?}

## Decision
{What is the change that we're proposing and/or doing?}

## Consequences
{What becomes easier or harder? What trade-offs are accepted?}
{List positive consequences.}
{List negative consequences.}
{List neutral consequences.}
```

---

### 3.17 Compliance Documentation

| File | Diátaxis | Standard | Owner | Frequency | Classification |
|---|---|---|---|---|---|
| `reference/compliance/gdpr-compliance.md` | Reference | GDPR | DPO/Security | Annually | Mandatory |
| `reference/compliance/iso-27001-controls.md` | Reference | ISO 27001 | DPO/Security | Annually | Recommended |
| `reference/compliance/soc-2-controls.md` | Reference | SOC 2 | DPO/Security | Annually | Recommended |
| `reference/compliance/data-retention-policy.md` | Reference | GDPR Art 5(1)(e) | DPO | Annually | Mandatory |
| `reference/compliance/privacy-policy.md` | Reference | GDPR Art 12-14 | Legal | Annually | Mandatory |
| `reference/compliance/audit-trail-spec.md` | Reference | — | Security | Per-release | Mandatory |

**GDPR Compliance Document Structure:**
- Article mapping table (Art 5 → Data minimization, Art 6 → Lawful basis, Art 7 → Consent, Art 12-14 → Privacy notice, Art 15 → Access, Art 16 → Rectification, Art 17 → Erasure, Art 20 → Portability, Art 32 → Security, Art 33 → Breach notification, Art 35 → DPIA)
- Data processing register (what data, purpose, lawful basis, retention, third-party sharing)
- Technical controls map (encryption, access control, audit logging, backup)
- Data Subject Access Request (DSAR) procedure
- Breach notification procedure

---

### 3.18 Documentation Governance

This document serves as the governance specification. Key governance policies:

| Policy | Detail |
|---|---|
| **Document Review Cadence** | Every document reviewed at its stated `update_frequency`. Review performed by `owner`. |
| **Document Lifecycle** | Draft → Review → Approved → Published → Deprecated → Archived |
| **Deprecation** | Deprecated documents remain accessible with a banner pointing to the replacement. |
| **Archival** | Archived after 1 year of deprecation or 2 years without update. |
| **Change Process** | PR-based. Documentation changes require at least one reviewer from the owning team. |
| **Metrics** | Documentation freshness (% of docs updated within frequency window), completeness (% of mandatory docs created), coverage (% of code with linked docs). |
| **Tooling** | Markdown with YAML front matter for metadata. Mermaid.js for diagrams (pre-commit rendered to SVG). Vale/LyFT for style checking. DocSearch or similar for search. CI validates metadata headers. |

---

## 4. Cross-Document Relationship Matrix

| Source Document | References | Referenced By |
|---|---|---|
| `SRS.md` (IEEE 29148) | `BRD.md`, `PRD.md`, `use-case-specifications.md` | `test-case-catalog.md`, `test-strategy.md`, `requirements-traceability-matrix.md` |
| `arc42.md` | All `reference/laravel/*.md`, `ADR-*`, `reference/architecture/*` | `onboarding.md`, `explanation/architecture/*` |
| `openapi.yaml` | `reference/laravel/api-resource-reference.md`, `reference/backend/api-response-standard.md` | `how-to/testing/write-api-test.md`, `how-to/backend/add-new-endpoint.md` |
| `reference/laravel/eloquent-models.md` | `reference/database/data-dictionary.md`, `reference/architecture/erd.md` | `how-to/database/add-new-migration.md`, `how-to/database/write-efficient-query.md` |
| `reference/security/rbac-matrix.md` | `reference/laravel/policy-gate-reference.md`, `reference/laravel/auth-reference.md` | `how-to/security/implement-rbac.md`, `how-to/team/conduct-code-review.md` |
| `runbook-database-failover.md` | `reference/database/backup-strategy.md`, `reference/database/restore-strategy.md`, `reference/cloud/aws-architecture.md` | `incident-response-guide.md`, `dr-plan.md` |
| `onboarding.md` | `setup-local-env.md`, `reference/laravel/*`, `reference/backend/coding-standards.md`, `reference/team/git-conventions.md` | — |

**Traceability Chain (example):**

```
Business Rule (BR-001: "Orders > $10k require manager approval")
  → Functional Requirement (FR-042: "System shall enforce approval workflow")
    → SRS ID (REQ-FUNC-042: Approval workflow section)
      → Use Case (UC-07: "Approve high-value order")
        → Test Case (TC-FUNC-042-001: "Attempt order > $10k without approval")
          → Code Reference (app/Services/OrderApprovalService.php)
            → ADR (ADR-010: Workflow engine choice)
```

---

## 5. Document Maturity Mapping

This table maps at which organizational maturity level each document and standard typically becomes adopted.

| Maturity Level | Characteristics | Standards Adopted | Typical Documents Created | Missing / Optional |
|---|---|---|---|---|
| **Startup** (~1-10 engineers) | Monolith, single deployment, no dedicated roles | — | README, basic API docs (Postman), ADR-001 through ADR-003 | SRS, arc42, C4, OpenAPI, security docs, runbooks |
| **SME** (~10-50 engineers) | Modular monolith, staging env, dedicated QA | PSR-12, basic OpenAPI | Folder structure, coding standards, basic onboarding, first ADRs, ERD | Full IEEE 29148 SRS, arc42, C4 Levels 3-4, Compliance, Capacity Plan |
| **Scale-up** (~50-200 engineers) | Multiple teams, microservice-adjacent monolith, on-call rotation | OpenAPI 3.x (partial), C4 Levels 1-2, Basic arc42 | SRS (light), test strategy, CI/CD reference, monitoring metrics, runbooks for critical services, RBAC matrix | Full arc42, full IEEE 29148, ISO/IEC 25010 NFR taxonomy, Diátaxis quadrants, full Compliance (GDPR/ISO) |
| **Enterprise** (~200-1000 engineers) | Formal architecture review, dedicated SRE/security/QA roles, multiple products | **arc42** (full), **C4** (all levels), **IEEE 29148** (full), **ISO/IEC 25010**, **OWASP ASVS**, formal **OpenAPI** governance | All documents in this blueprint labeled Mandatory | Continuous improvement of Explanation docs |
| **FAANG / Big Tech** (1000+ eng) | Autonomous teams, formal architecture board, site reliability engineering, security champions program | All standards in Section 0, plus: **UML 2.x** (formal), **BPMN 2.0**, **SysML**, **AsyncAPI**, **CloudEvents**, **SCA/SBOM**, **SLSA**, **SPDX**, **OpenTelemetry** | All documents in this blueprint, plus fine-grained per-service/ per-domain documentation; auto-generated from code where possible (OpenAPI from annotations, ADRs from RFC process) | — (all documents exist and are maintained) |

**Recommended Adoption Path:**
1. **Immediately** (regardless of maturity): `reference/backend/*`, `reference/laravel/*`, `ADR-001` through `ADR-004`, `reference/team/git-conventions.md`, `tutorials/developer/setup-local-env.md`, `tutorials/developer/onboarding.md`
2. **Within first product release**: `reference/api/openapi.yaml`, `reference/testing/test-strategy.md`, `reference/security/owasp-mapping.md`, `reference/security/rbac-matrix.md`, `reference/architecture/c4-level-1-context.md`, `reference/architecture/c4-level-2-containers.md`
3. **Before 10 engineers**: `reference/architecture/arc42.md` (light), `reference/requirements/srs.md` (light), `reference/monitoring/metrics-catalog.md`, `reference/monitoring/alert-rules.md`, `how-to/operations/incident-response-guide.md`
4. **Before 50 engineers**: Full arc42, full SRS, full test-case-catalog with traceability, full security documentation (threat model, ASVS level 1), DR plan, capacity plan
5. **Before 200 engineers**: Full IEEE 29148 SRS, full ISO/IEC 25010 NFR classification, C4 Levels 3-4, full compliance documentation, runbooks for every critical service, on-call playbooks

---

## 6. Self-Review: Principal Engineer Audit

### 6.1 Completeness Check

| Domain | All Mandatory Docs Defined? | Gaps |
|---|---|---|
| Business Documentation | ✅ All mandatory defined | — |
| Product Documentation | ✅ All mandatory defined | — |
| Requirements Documentation | ✅ All mandatory defined | — |
| Laravel Architecture | ✅ All 31 reference files defined | — |
| Software Architecture (arc42) | ✅ arc42 file + 12 C4/UML files + 7 Explanation files | — |
| Backend Documentation | ✅ 13 reference files + 12 how-to files | — |
| Database Documentation | ✅ 13 reference files + 4 how-to files | — |
| API Documentation | ✅ 2 files (OAS source of truth) | — |
| Security Documentation | ✅ 9 reference + 3 explanation + 6 how-to files | — |
| DevOps Documentation | ✅ 6 reference dirs + 8 how-to + 2 explanation | — |
| Cloud Documentation | ✅ 4 reference + 5 how-to files | — |
| Testing Documentation | ✅ 4 reference + 7 how-to files | — |
| Monitoring Documentation | ✅ 5 reference + 5 how-to files | — |
| Operations Documentation | ✅ 6 reference + 8 how-to + 2 explanation files | — |
| Team Documentation | ✅ 4 reference + 4 tutorial + 3 how-to + 3 explanation | — |
| Architecture Decision Records | ✅ 17 ADRs defined | Consider adding ADR for rate limiting strategy, webhook signature strategy |
| Compliance Documentation | ✅ 6 reference files | — |
| Documentation Governance | ✅ This document | — |

### 6.2 Standards Conformance Audit

| Standard | Is every document correctly tagged? | Non-conformances found |
|---|---|---|
| IEEE 29148 | SRS, BRD, use-case-specifications, business-context, success-metrics tag IEEE 29148 | — |
| ISO/IEC 25010 | NFR document tagged. All NFRs categorized per 25010 sub-characteristics | Ensure How-To files reviewing NFRs also reference the standard |
| arc42 | arc42.md tagged. All reference/laravel/*.md map to arc42 sections | — |
| C4 Model | Architecture diagrams tagged with C4 level. No mixed levels | Ensure C4 level is explicitly stated in each diagram document |
| OpenAPI 3.x | openapi.yaml tagged. Swagger/Redoc are generated, not separate | — |
| Diátaxis | Every file listed above has a Diátaxis mode assigned | Verify each file's tone/format matches its assigned mode during review |
| ADR (Nygard) | All 17 ADRs use Nygard format with Context/Decision/Consequences | — |

### 6.3 Architecture Diagram Coverage Check

| Concern | Diagram Type | C4 Level | Present? |
|---|---|---|---|
| Whole system in its environment | System Context | L1 | ✅ `c4-level-1-context.md` |
| High-level technical building blocks | Container | L2 | ✅ `c4-level-2-containers.md` |
| Internal structure of each container | Component | L3 | ✅ `c4-level-3-components.md` |
| Key class/module internals | Code | L4 | ✅ `c4-level-4-code.md` (selected) |
| Request flow through components | Sequence (UML) | N/A | ✅ `uml-sequence.md` |
| State transitions of key entities | State (UML) | N/A | ✅ `uml-state.md` |
| Class hierarchy / interfaces | Class (UML) | N/A | ✅ `uml-class.md` |
| Data relationships | ERD | N/A | ✅ `erd.md` |
| Physical deployment | Deployment | arc42 §7 | ✅ `deployment-diagram.md` |
| Network topology | Infrastructure | N/A | ✅ `infrastructure-diagram.md` |
| Service dependencies | Dependency | N/A | ✅ `service-dependency.md` |
| Business process flow | Activity/BPMN | N/A | ✅ `explanation/business/business-process-models.md` |
| Data movement | DFD | N/A | ✅ `reference/requirements/data-flow-diagrams.md` |

### 6.4 Risk Assessment

| Risk | Impact | Mitigation |
|---|---|---|
| **Documentation drift** — Code changes without documentation updates | High | CI gate: PR cannot merge if modified module lacks doc update. Automated freshness dashboard. |
| **Standard fatigue** — Teams skip standards due to complexity | Medium | Start with Mandatory classification only; promote to Recommended/Optional per maturity level. |
| **C4 diagram bloat** — Too many Level 4 diagrams maintained | Medium | Limit Level 4 diagrams to components identified as high-risk or frequently misunderstood. Automate generation where possible. |
| **OpenAPI becoming outdated** | High | Enforce API-first workflow: spec is edited before controller code. CI validates spec validity and endpoint coverage. |
| **ADR not being consulted** | Medium | Index ADRs in arc42 §9 checklist. Require ADR reference in PR description for architectural changes. |

---

## Appendix A: Document Metadata Quick Reference

```yaml
---
title: Short descriptive name
diataxis: tutorial | how-to | reference | explanation
standards:
  - IEEE 29148 §3.2
  - ISO/IEC 25010
owner: Team Name
update_frequency: quarterly | per-release | on-change | on-demand
classification: mandatory | recommended | optional
audience: engineers | devops | qa | product | all
prerequisites:
  - path/to/prerequisite-doc.md
dependencies:
  - ADR-001
---
```

## Appendix B: Mermaid.js Diagram Standards

Where diagrams are embedded in Markdown (before full C4 rendering tooling), use Mermaid.js with these conventions:

- **Sequence:** `sequenceDiagram` — actors as participants, solid lines for sync, dotted for async
- **Class:** `classDiagram` — include visibility markers, relationships (--> for association, --|> for inheritance, --o for composition)
- **State:** `stateDiagram-v2` — states as nodes, transitions as arrows with triggers
- **ER:** `erDiagram` — entities with attributes, relationships with cardinality
- **Flowchart:** `flowchart TD` — for decision flows, business processes
- **C4 (via Mermaid C4 extension):** `C4Context`, `C4Container`, `C4Component` — use official C4 diagram syntax

## Appendix C: Recommended Tooling Stack

| Purpose | Tool | Rationale |
|---|---|---|
| Documentation site generator | Docusaurus / VitePress | MDX support, search, versioning, diagram rendering |
| Diagram as code | Mermaid.js (in-code) + PlantUML (complex) | Version-controlled, diffable, CI-renderable |
| API specification | OpenAPI 3.x + Redoc | Source of truth; Redoc for human-readable rendering |
| Style & consistency | Vale.sh + write-good | Automated prose linting |
| ADR tracking | adr-tools CLI / Log4brao | Standardized ADR creation and lifecycle |
| Documentation CI | GitHub Actions / GitLab CI | Validate metadata, render diagrams, check freshness |
| Search | Algolia DocSearch / Meilisearch | Full-text search across docs |
| Coverage | custom script | Report % of documented classes, routes, permissions |
