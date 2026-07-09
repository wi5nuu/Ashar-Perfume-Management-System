---
title: Development Philosophy
diataxis: explanation
owner: Staff Software Engineer
update_frequency: annual
classification: recommended
---

# Development Philosophy

## Principles

### 1. Simple First, Extend Later

Build what's needed now, not what might be needed. Avoid premature abstraction. Refactor when a pattern emerges, not before.

### 2. Convention Over Configuration

Follow Laravel conventions. If there's a standard way to do something in Laravel, use it. Custom solutions are documented in ADRs.

### 3. Explicit Over Implicit

Magic is hard to debug. Service injection, typed returns, and explicit middleware chains are preferred over facades, global helpers, and implicit route model binding.

### 4. Test the Behavior, Not the Implementation

Tests should validate outcomes, not internal implementation details. This allows refactoring without rewriting tests.

### 5. Code is Read More Than Written

Prioritize readability over cleverness. Meaningful variable names, short methods, consistent formatting. PHPStan ensures type safety so humans can focus on logic.

### 6. Security is Everyone's Responsibility

Every developer considers security implications of their changes. Input validation, authorization checks, and XSS prevention are not "someone else's job."

## Practices

| Practice | Why |
|---|---|
| Feature branches | Isolate work, enable code review |
| Small, frequent commits | Easier review, simpler rollback |
| PRs < 400 lines | Review quality degrades with size |
| Mandatory code review | Knowledge sharing, defect prevention |
| CI must pass | Never merge broken code |
| Documentation with code | ADRs for architecture, comments for WHY |
