---
title: ADR-010 — Static Analysis: PHPStan Level 5
status: Accepted
date: 2024-04
deciders: Staff Software Engineer
---

# ADR-010: Static Analysis: PHPStan Level 6

## Context

Code quality and type safety must be enforced automatically. Manual code review alone is insufficient for catching type errors and edge cases.

## Decision

Use PHPStan at analysis level 5 as mandatory static analysis for all PHP code (baseline in `phpstan-baseline.neon`).

## Rationale

- Catches type mismatches, missing returns, unused parameters
- Level 5: checks return types, type hints, but allows untyped properties
- Integrates with CI pipeline (blocks PR on failure)
- Baseline feature allows incremental adoption on legacy code
- IDE integration (PhpStorm/VS Code) for real-time feedback
- Target to reach Level 6 in future iterations

## Consequences

- All new code must pass PHPStan level 5
- Existing code baseline gradually improved
- CI fails on PHPStan violations
- `phpstan.neon` and `phpstan-baseline.neon` in repository root
- Target to reach Level 6 in future
