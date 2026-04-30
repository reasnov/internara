# Todo: Security Reviews, Test Coverage & UI Layout Consistency

**Source:**
- `.agents/issues/2026-04-30-security-review-domains.md`
- `.agents/issues/2026-04-30-remaining-todo-tests.md`
- `.agents/issues/2026-04-30-ui-layout-audit.md`

**Created:** 2026-04-30
**Priority:** P1 → P2
**Status:** OPEN

---

## Step 1 — Security Review: Internship Domain — P1

**Source:** `security-review-domains.md` — Domain 1

**Action:**
1. Review `app/Actions/Internship/` for input validation and authorization gates
2. Review `app/Policies/Internship*Policy.php` — are rules enforced at correct boundary?
3. Verify student limited to one active registration
4. Verify students can only see their own placement/report data
5. Review official document file upload: type restrictions, size limits, path traversal prevention
6. Fix any issues found, update tests for security-relevant paths
7. Remove `!` marker from checklist once domain passes review

**Exit criterion:** No unvalidated input, no authorization bypass, all security paths tested.

---

## Step 2 — Security Review: Attendance Domain — P1

**Source:** `security-review-domains.md` — Domain 2

**Action:**
1. Review `ClockInAction` / `ClockOutAction` — can students backdate attendance?
2. Review `JournalManager` — can students edit submitted journals?
3. Verify duplicate clock-in prevention
4. Review absence request approval chain authorization
5. Fix any issues found, add tests for security paths

**Exit criterion:** No time manipulation possible, submitted journals immutable, approval chain enforced.

---

## Step 3 — Security Review: Supervision Domain — P1

**Source:** `security-review-domains.md` — Domain 3

**Action:**
1. Review `SupervisionLog` / `MonitoringVisit` policies — mentor limited to assigned students?
2. Review status transitions — are invalid transitions prevented?
3. Fix COL2 WRONG field mapping error in tests
4. Fix or replace todo test with real assertion
5. Verify foreign key constraints are enforced

**Exit criterion:** Mentor isolation enforced, valid status transitions only, tests passing.

---

## Step 4 — Security Review: Assessment Domain — P1

**Source:** `security-review-domains.md` — Domain 4

**Action:**
1. Review assignment submission — deadline enforcement, who can submit
2. Review grading — only teacher can grade assigned submissions
3. Review competency tracking — data integrity of skill progress
4. Review certificate generation — authorization and data accuracy
5. Fix incomplete tests, add assertions

**Exit criterion:** Grading restricted to authorized teachers, deadlines enforced, tests complete.

---

## Step 5 — Fix Attendance Timing Tests (3 todos) — P2

**Source:** `remaining-todo-tests.md` — Attendance

**Action:**
1. Open `tests/Feature/Attendance/AttendanceTest.php`
2. Use `Carbon::setTestNow()` to freeze time in each test
3. Replace exact time comparisons with `Carbon::setTestNow()` approach
4. Run: `./vendor/bin/pest tests/Feature/Attendance/AttendanceTest.php`
5. Verify: 0 todos, tests pass

**Exit criterion:** All 3 attendance tests pass without timing issues.

---

## Step 6 — Fix Supervision Field Mapping (1 todo) — P2

**Source:** `remaining-todo-tests.md` — Supervision

**Action:**
1. Open `tests/Feature/Supervision/SupervisionTest.php`
2. Find the test with COL2 WRONG — identify expected vs actual column name
3. Inspect migration and model to find correct column name
4. Update test assertion
5. Run: `./vendor/bin/pest tests/Feature/Supervision/SupervisionTest.php`
6. Verify: 0 todos, test passes

**Exit criterion:** Supervision test passes with correct column reference.

---

## Step 7 — Implement Assignment Submit/Verify (2 todos) — P2

**Source:** `remaining-todo-tests.md` — Assignment

**Action:**
1. Open `app/Actions/Assignment/SubmitAssignmentAction.php` — inspect parameter mismatch
2. Fix action method signature to match expected input
3. Open submission status update logic — use correct status enum value
4. Replace `todo()` with real assertions in `tests/Feature/Assignment/AssignmentTest.php`
5. Run: `./vendor/bin/pest tests/Feature/Assignment/AssignmentTest.php`
6. Verify: 0 todos, tests pass

**Exit criterion:** Submit and verify tests pass with real assertions.

---

## Step 8 — Fix Auth Layout Duplication — P2

**Source:** `ui-layout-audit.md`

**Action:**
1. Open `resources/views/components/layouts/auth.blade.php`
2. Change from standalone DOCTYPE to `@extends('components.layouts.base')`
3. Extract auth-specific content into the `body` slot
4. Remove duplicated `<html>`, `<head>`, `<body>` tags
5. Verify auth pages (login, forgot password, reset password) still render correctly

**Exit criterion:** `auth.blade.php` extends `base.blade.php`, no duplicated HTML structure.

---

## Step 9 — Migrate Scaffolded Views to maryUI — P2

**Source:** `ui-layout-audit.md` + `known-issues.md` (P3 maryUI workaround)

**Action:**
1. Investigate root cause of `Using $this when not in object context` error with `x-mary-*` components
2. Once fixed, migrate scaffolded views from plain HTML to maryUI:
   - `resources/views/livewire/admin/reports/index.blade.php`
   - `resources/views/livewire/admin/handbooks/index.blade.php`
   - `resources/views/livewire/admin/schedules/index.blade.php`
   - `resources/views/livewire/admin/academic-years/index.blade.php`
3. Use `x-mary-table`, `x-mary-badge`, `x-mary-modal` consistent with existing admin views
4. Verify all 4 domain tests still pass

**Exit criterion:** Scaffolded views use maryUI components consistently with rest of application.

---

## Step 10 — Wire Student Registration (1 todo) — P2

**Source:** `remaining-todo-tests.md` — Student

**Action:**
1. Inspect existing internship registration flow
2. Wire student registration test to use actual Livewire or route
3. Replace `todo()` with real assertions
4. Run relevant test
5. Verify: 0 todos, test passes

**Exit criterion:** Student registration test has real assertions and passes.

---

## Delegation Notes

- **Steps 1-4 are security reviews** — read code, find actual issues, fix them. Do not add complexity for theoretical risks.
- **Steps 5-7 are test fixes** — straightforward, each has a known root cause.
- **Steps 8-9 are UI improvements** — layout consistency and maryUI migration.
- **Step 10 depends on internship flow** — may need coordination with other work.
- **After completing steps:** run full test suite to confirm no regressions.
- **Do NOT delete or modify `modules/` directory** — retained for reference.
