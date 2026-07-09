---
title: Team Topology & Communication
diataxis: explanation
owner: Project Lead
update_frequency: quarterly
classification: recommended
---

# Team Topology & Communication

## Why This Team Structure?

A small team of 5-7 engineers requires a **generalizing specialist** model — each person has a primary specialty but can work across the stack when needed.

```
Project Lead
    │
    └── Staff Software Engineer (Tech Lead)
            │
            ├── Backend Lead (Laravel, DB, API)
            ├── Frontend Developer (Blade, JS, CSS)
            ├── DevOps Lead (Infrastructure, CI/CD, Security)
            └── QA Lead (Testing, Automation)
```

## Why Not Separate Frontend & Backend Teams?

The application uses server-rendered Blade templates with minimal JavaScript. Separating frontend and backend would create:
- Context switching overhead
- API negotiation friction
- Unnecessary abstraction for simple views

A full-stack Laravel developer handles both efficiently. The "Frontend Developer" role focuses on CSS, JavaScript interactions, and UI polish within the Blade ecosystem.

## Why Embedded QA Instead of Separate QA Team?

Embedded QA (one person working within the development team) provides:
- Faster feedback loops (QA involved from sprint planning)
- Better context (understands the feature being tested)
- Ownership of quality (not just "throwing over the wall")
- Direct communication with developers

## Communication Patterns

| Purpose | Channel | Frequency |
|---|---|---|
| Daily sync | Standup | Daily (15 min) |
| Feature discussion | Slack thread | Asynchronous |
| Code review | GitHub PR | Per PR |
| Architecture decision | ADR + meeting | Per decision |
| Incident response | Slack #incidents | Real-time |
| Retrospective | Meeting | Bi-weekly |
