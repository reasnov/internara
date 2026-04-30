# Issue: Failed Tests Post-Module Cleanup

**Created:** 2026-04-30  
**Source:** Engineer Agent audit (todo: 2026-04-30-fix-checklist-accuracy-and-test-blocker.md, Step 3)  
**Priority:** P1 (fix before production release)  
**Status:** OPEN

---

## Summary

After resolving the legacy module dependency blocker (Step 1 of todo), the test suite executes but reports **9 failed tests** out of 216 total (197 passed, 9 failed, 10 todos, 446 assertions).

---

## Failed Tests Breakdown

### Category A: View Rendering — Heroicons SVG Missing (7 failures)

**File:** `tests/Feature/Settings/SystemSettingTest.php`  
**Error:** `ViewException: Svg by name "o-palette" from set "heroicons" not found`  
**Tests affected:** All 7 tests in this file

| Test | Duration |
|------|----------|
| system settings page renders for authenticated user | ~1s |
| admin can save system settings | ~1s |
| system settings validates required fields | ~1s |
| system settings validates academic year format | ~1s |
| system settings validates locale options | ~1s |
| saved settings persist after redirect | ~1s |
| mail settings are saved correctly | ~1s |

**Root cause:** The view rendered by these tests references `x-icon name="o-palette" set="heroicons"` but the icon name `o-palette` does not exist in the `blade-heroicons` set. The correct name is likely `o-swatch` or `o-paint-brush`.

**Fix location:** View file containing `x-icon name="o-palette"` — check `resources/views/livewire/settings/` or related settings views.

**Impact:** Settings page cannot render — blocks admin configuration workflow.

---

### Category B: Test Seed Issue — Role Not Created (1 failure)

**File:** `tests/Feature/Setup/SetupWizardTest.php`  
**Error:** `RoleDoesNotExist: There is no role named 'super_admin' for guard 'web'`  
**Location:** `app/Actions/Setup/SetupSuperAdminAction.php:31`

**Root cause:** The setup wizard test triggers `SetupSuperAdminAction` which attempts to assign the `super_admin` role, but the test's `beforeEach()` does not seed this role. Other test files use `foreach (RoleEnum::cases() as $role) { Role::firstOrCreate(...) }` but SetupWizardTest may be missing this or the order of operations is wrong.

**Fix location:** `tests/Feature/Setup/SetupWizardTest.php` — ensure `super_admin` role is created before the wizard action runs, or add it to the test's seed data.

---

### Category C: Pest Test Syntax — Duplicate Test Names (2 failures)

**File:** `tests/Feature/Internship/InternshipRegistrationTest.php`  
**Error:** `TestAlreadyExist`

**Root cause:** The `->todo()` syntax was applied incorrectly. When using `it('description')->todo('reason')`, Pest creates a separate test entry for the todo marker, causing a duplicate test name conflict.

**Affected tests:**
- `it('allows student to register for internship')->todo('...')`
- `it('prevents duplicate registration')->todo('...')`

**Fix:** Use the function body syntax instead:
```php
it('test name', function () {
    todo('reason');
});
```

---

### Category D: RBAC Test Pattern (1 failure)

**File:** `tests/Feature/Assignment/AssignmentTest.php`  
**Error:** RBAC test `->throws()` assertion failure

**Root cause:** The test uses `->throws(\Exception::class)` but the action may not throw an exception — it may fail silently or return an error response instead.

**Fix location:** `tests/Feature/Assignment/AssignmentTest.php` line 94-105 — verify what `CreateAssignmentAction` actually does when called without proper authorization, then adjust the test assertion to match actual behavior.

---

## Recommended Fix Priority

| Priority | Category | Effort | Risk |
|----------|----------|--------|------|
| P0 | A: Heroicons SVG missing | Low | Low — simple icon name fix |
| P1 | B: Role seed missing | Low | Low — add role creation to test |
| P2 | C: Test syntax duplicates | Low | Low — revert to function syntax |
| P2 | D: RBAC assertion mismatch | Medium | Medium — may require action behavior change |

---

## Related Files

- `app/Providers/AppServiceProvider.php` — Pulse gate, author signature
- `config/modules.php` — Disabled (returns empty array)
- `config/modules-livewire.php` — Disabled (returns empty array)
- `phpunit.xml` — Module test paths removed
- `tests/Pest.php` — Module test discovery removed
- `vite.config.js` — Module Tailwind paths removed
