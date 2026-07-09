---
title: Code Review Process
diataxis: reference
owner: Staff Software Engineer
update_frequency: semi-annual
classification: mandatory
---

# Code Review Process

## Requirements

- Every PR requires **at least one approval** before merging
- Author cannot merge their own PR
- PR must pass CI pipeline (tests, static analysis, linting)
- Draft PRs for work-in-progress

## Review Checklist

### Architecture & Design
- Follows Service Layer pattern?
- New route uses correct middleware?
- Database migration is backward-compatible?
- Cache invalidation considered?

### Code Quality
- PHPStan level 6 passes?
- No unused imports/variables?
- Form request for validation?
- Error handling appropriate?

### Security
- Input validated and sanitized?
- Authorization checked (Gate/Policy)?
- SQL injection risk? (ORM used?)
- XSS potential? (blade escaping?)
- CSRF protection on forms?

### Testing
- Unit tests for new logic?
- Feature test for new endpoint?
- Edge cases covered?
- Tests pass locally?

## PR Template

```markdown
## Description
Brief description of changes

## Type
- [ ] Feature
- [ ] Bugfix
- [ ] Refactor
- [ ] Documentation
- [ ] Infrastructure

## Testing
- [ ] Unit tests added/updated
- [ ] Feature tests added/updated
- [ ] Manual testing performed

## Checklist
- [ ] PHPStan passes
- [ ] No new warnings
- [ ] Migrations are reversible
- [ ] Documentation updated (if applicable)
```
