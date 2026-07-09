---
title: Why Laravel?
diataxis: explanation
owner: Staff Software Engineer
update_frequency: annual
classification: recommended
---

# Why Laravel?

## Context

APMS is a retail management system for a perfume business. The key requirements are:

1. **Rapid development** — features like POS, inventory, wholesale ordering
2. **Admin panel** — rich server-rendered UI for store operations
3. **Customer portal** — wholesale customer self-service
4. **Reporting** — daily sales, inventory, commission reports
5. **Integration** — WhatsApp notifications, shipping APIs

## Why Not Other Options?

### Ruby on Rails
- Less team familiarity
- Smaller PHP ecosystem in local market
- Performance concerns for real-time POS

### Node.js (Express/Next.js)
- Less mature ORM for complex queries
- Async model adds complexity for simple CRUD
- Team's PHP expertise

### Django
- Python ORM less intuitive for complex reporting
- Smaller PHP hiring pool

### Plain PHP
- No built-in features (auth, queue, ORM, mail)
- Slower development velocity
- More boilerplate

## Decision Summary

| Factor | Laravel | Rails | Node.js | Django | Plain PHP |
|---|---|---|---|---|---|
| Team expertise | High | Low | Low | Low | High |
| Built-in features | High | High | Low | Medium | None |
| Performance | Medium | Medium | High | Medium | Low (without optimization) |
| Hiring pool (local) | Large | Small | Medium | Small | Large |
| Ecosystem | Large | Medium | Large | Medium | Small |
| ORM quality | High | High | Medium | Medium | None |

Laravel provides the best balance of **development speed**, **team capability**, and **ecosystem support** for this project.
