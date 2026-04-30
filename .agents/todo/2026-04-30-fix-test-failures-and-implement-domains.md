# Todo: Fix Test Failures & Implement Scaffolded Domains

**Source:** `.agents/issues/2026-04-30-requirement-fulfillment-report.md`
**Created:** 2026-04-30
**Priority:** P0 → P4
**Assigned to:** Engineer Agent

---

## Step 1 — Fix Heroicons SVG Missing (7 failures) — P0

**Problem:** `tests/Feature/Settings/SystemSettingTest.php` — 7 tests fail with `ViewException: Svg "o-palette" from set "heroicons" not found`.

**Action:**
1. Search for `o-palette` in all view files: `grep -r 'o-palette' resources/views/`
2. Replace with correct heroicon name (`o-swatch` or `o-paint-brush`)
3. Run: `./vendor/bin/pest tests/Feature/Settings/SystemSettingTest.php`
4. Verify: 0 failures

**Exit criterion:** All 7 SystemSettingTest tests pass.

---

## Step 2 — Fix SetupWizardTest Role Seeding (1 failure) — P1

**Problem:** `tests/Feature/Setup/SetupWizardTest.php` fails with `RoleDoesNotExist: There is no role named 'super_admin'`.

**Action:**
1. Open `tests/Feature/Setup/SetupWizardTest.php`
2. In `beforeEach()` or `setUp()`, ensure roles are seeded before the test triggers `SetupSuperAdminAction`
3. Reference pattern from other test files:
   ```php
   foreach (RoleEnum::cases() as $role) {
       Role::firstOrCreate(['name' => $role->value]);
   }
   ```
4. Run: `./vendor/bin/pest tests/Feature/Setup/SetupWizardTest.php`
5. Verify: 0 failures

**Exit criterion:** SetupWizardTest passes without role errors.

---

## Step 3 — Fix InternshipRegistrationTest Duplicate Names (2 failures) — P1

**Problem:** `tests/Feature/Internship/InternshipRegistrationTest.php` — `TestAlreadyExist` error from `->todo()` syntax.

**Action:**
1. Open `tests/Feature/Internship/InternshipRegistrationTest.php`
2. Replace `it('name')->todo('reason')` pattern with:
   ```php
   it('name', function () {
       todo('reason');
   });
   ```
3. Run: `./vendor/bin/pest tests/Feature/Internship/InternshipRegistrationTest.php`
4. Verify: 0 failures

**Exit criterion:** No duplicate test name errors.

---

## Step 4 — Fix AssignmentTest RBAC Assertion (1 failure) — P2

**Problem:** `tests/Feature/Assignment/AssignmentTest.php` — `->throws()` assertion doesn't match actual behavior.

**Action:**
1. Open `tests/Feature/Assignment/AssignmentTest.php` (lines ~94-105)
2. Inspect what `CreateAssignmentAction` actually does on unauthorized access:
   - Does it throw `\Exception`?
   - Does it throw `AuthorizationException`?
   - Does it return a redirect/403 response?
3. Update the test assertion to match actual behavior
4. Run: `./vendor/bin/pest tests/Feature/Assignment/AssignmentTest.php`
5. Verify: 0 failures

**Exit criterion:** RBAC test passes with correct assertion.

---

## Step 5 — Verify Full Test Suite (Verification Gate) — P1

**Problem:** Need to confirm all fixes result in 0 failures before proceeding to implementation work.

**Action:**
1. Run full test suite: `./vendor/bin/pest`
2. Record results:
   - Total passed
   - Total failed (should be 0)
   - Total todos
   - Total assertions
3. Run Arch tests: `./vendor/bin/pest tests/Arch`
4. Run Quality tests: `./vendor/bin/pest tests/Quality`

**Exit criterion:** 0 failures (todos are expected). Arch & Quality ALL PASS.

---

## Step 6 — Implement Report Domain (Scaffold Exists) — P3

**Scaffold files already exist:**
- `app/Models/GeneratedReport.php`
- `app/Actions/Report/QueueReportGenerationAction.php`
- `app/Actions/Report/DownloadReportAction.php`
- `app/Http/Controllers/ReportController.php`
- `app/Http/Requests/GenerateReportRequest.php`
- `app/Policies/GeneratedReportPolicy.php`
- `tests/Feature/Report/ReportTest.php`
- Routes registered in `routes/web.php`

**What needs to be implemented:**

### 6.1 Migration
```bash
php artisan make:migration create_generated_reports_table
```
Columns: `id (uuid)`, `user_id (uuid, fk)`, `report_type (string)`, `file_path (string, nullable)`, `file_size (int, nullable)`, `status (string, default:pending)`, `filters (json, nullable)`, `error_message (text, nullable)`, `generated_at (datetime, nullable)`, `timestamps`

### 6.2 Factory
Create `database/factories/GeneratedReportFactory.php`

### 6.3 View / Livewire
Create `resources/views/livewire/admin/reports/index.blade.php` — report listing with status badges, generation form, download buttons.

### 6.4 Complete Actions
- `QueueReportGenerationAction`: Implement `GenerateReportJob` dispatch
- `DownloadReportAction`: Implement file serving logic with proper headers

### 6.5 Tests
Replace `->todo()` with actual assertions in `tests/Feature/Report/ReportTest.php`

**Exit criterion:** Report domain fully functional — generate, queue, download reports.

---

## Step 7 — Implement Handbook Domain (Scaffold Exists) — P3

**Scaffold files already exist:**
- `app/Models/Handbook.php`
- `app/Models/HandbookAcknowledgement.php`
- `app/Actions/Guidance/CreateHandbookAction.php`
- `app/Actions/Guidance/AcknowledgeHandbookAction.php`
- `app/Http/Controllers/HandbookController.php`
- `app/Http/Requests/CreateHandbookRequest.php`
- `app/Policies/HandbookPolicy.php`
- `tests/Feature/Guidance/HandbookTest.php`
- Routes registered in `routes/web.php`

**What needs to be implemented:**

### 7.1 Migration
```bash
php artisan make:migration create_handbooks_table
php artisan make:migration create_handbook_acknowledgements_table
```
`handbooks`: `id (uuid)`, `title`, `slug`, `content (text)`, `version`, `is_active`, `published_at`, `created_by (uuid, fk)`, `timestamps`
`handbook_acknowledgements`: `id (uuid)`, `user_id (uuid, fk)`, `handbook_id (uuid, fk)`, `acknowledged_at`, `ip_address`, `timestamps`

### 7.2 Factories
Create `HandbookFactory.php`

### 7.3 Views / Livewire
Create `resources/views/livewire/admin/handbooks/index.blade.php` — handbook CRUD table, create form, acknowledgement button.

### 7.4 Tests
Replace `->todo()` with actual assertions in `tests/Feature/Guidance/HandbookTest.php`

**Exit criterion:** Handbook domain fully functional — CRUD, acknowledge, track acknowledgements.

---

## Step 8 — Implement Schedule Domain (Scaffold Exists) — P3

**Scaffold files already exist:**
- `app/Models/Schedule.php`
- `app/Actions/Schedule/CreateScheduleAction.php`
- `app/Actions/Schedule/UpdateScheduleAction.php`
- `app/Actions/Schedule/DeleteScheduleAction.php`
- `app/Http/Controllers/ScheduleController.php`
- `app/Http/Requests/CreateScheduleRequest.php`
- `app/Http/Requests/UpdateScheduleRequest.php`
- `app/Policies/SchedulePolicy.php`
- `tests/Feature/Schedule/ScheduleTest.php`
- Routes registered in `routes/web.php`

**What needs to be implemented:**

### 8.1 Migration
```bash
php artisan make:migration create_schedules_table
```
Columns: `id (uuid)`, `title`, `description (text, nullable)`, `start_at`, `end_at (nullable)`, `type (string)`, `location (nullable)`, `internship_id (uuid, nullable, fk)`, `created_by (uuid, fk)`, `timestamps`

### 8.2 Factory
Create `ScheduleFactory.php`

### 8.3 Views / Livewire
Create `resources/views/livewire/admin/schedules/index.blade.php` — schedule listing with timeline/calendar view, CRUD form.

### 8.4 Tests
Replace `->todo()` with actual assertions in `tests/Feature/Schedule/ScheduleTest.php`

**Exit criterion:** Schedule domain fully functional — CRUD, timeline view.

---

## Step 9 — Implement Academic Year Domain (Scaffold Exists) — P3

**Scaffold files already exist:**
- `app/Models/AcademicYear.php`
- `app/Actions/AcademicYear/CreateAcademicYearAction.php`
- `app/Actions/AcademicYear/ActivateAcademicYearAction.php`
- `app/Http/Controllers/AcademicYearController.php`
- `app/Policies/AcademicYearPolicy.php`
- `tests/Feature/AcademicYear/AcademicYearTest.php`
- Routes registered in `routes/web.php`

**What needs to be implemented:**

### 9.1 Migration
```bash
php artisan make:migration create_academic_years_table
```
Columns: `id (uuid)`, `name`, `start_date`, `end_date`, `is_active`, `timestamps`

### 9.2 Factory
Create `AcademicYearFactory.php`

### 9.3 Views / Livewire
Create `resources/views/livewire/admin/academic-years/index.blade.php` — listing table, create form, activate button.

### 9.4 Tests
Replace `->todo()` with actual assertions in `tests/Feature/AcademicYear/AcademicYearTest.php`

**Exit criterion:** Academic Year domain fully functional — CRUD, single active year constraint.

---

## Step 10 — Update Issue Report — P2

**Action:**
1. After completing Steps 1-5, update `.agents/issues/2026-04-30-requirement-fulfillment-report.md`:
   - Update "Failed Tests" section (Part 2) with new counts
   - Update "Todo Tests" section (Part 3) with completion status
   - Update Verification Summary (Appendix) with fresh numbers
2. Mark each completed step in this todo file with ✅ and completion date.

**Exit criterion:** Issue report reflects current state.

---

## Delegation Notes for Engineer Agent

- **Execute Steps 1-5 first** — no implementation work until tests are clean (0 failures)
- **Steps 6-9 can be done in parallel** — they are independent domains
- **Follow existing patterns** — check `app/Actions/Attendance/ClockInAction.php` for action style, `app/Policies/SchoolPolicy.php` for policy style
- **Do NOT delete or modify `modules/` directory** — it is retained for reference
- **Do NOT refactor unrelated code** — keep changes minimal per step
- **After completing each step:** run relevant tests before moving to next step
- **Report back** with what was done, any blockers, and test results
