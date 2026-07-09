---
title: Team Structure & RACI
diataxis: reference
owner: Project Lead
update_frequency: quarterly
classification: mandatory
---

# Team Structure & RACI

## Team Roles

| Role | Responsibility | Reports To |
|---|---|---|
| **Project Lead** | Overall project direction, stakeholder management | Owner |
| **Staff Software Engineer** | Architecture decisions, code quality, tech lead | Project Lead |
| **Backend Lead** | API design, database schema, service layer | Staff Software Engineer |
| **Frontend Developer** | Blade templates, UI/UX, JavaScript | Staff Software Engineer |
| **DevOps Lead** | Infrastructure, CI/CD, monitoring, security | Staff Software Engineer |
| **Database Architect** | Schema design, migration strategy, query optimization | Staff Software Engineer |
| **QA Lead** | Test strategy, test automation, release quality | Staff Software Engineer |
| **Security Lead** | Security architecture, vulnerability management | Project Lead |

## RACI Matrix

| Activity | Project Lead | Staff Eng | Backend Lead | DevOps Lead | QA Lead |
|---|---|---|---|---|---|
| Architecture decisions | A | R | C | C | I |
| Feature development | I | A | R | C | C |
| Database changes | I | C | R | I | C |
| Infrastructure changes | I | C | I | R | I |
| Testing strategy | A | C | C | I | R |
| Security review | C | R | C | C | I |
| Release management | A | R | C | R | C |
| Incident response | I | A | C | R | C |

**R** = Responsible (doer), **A** = Accountable (decision-maker), **C** = Consulted, **I** = Informed
