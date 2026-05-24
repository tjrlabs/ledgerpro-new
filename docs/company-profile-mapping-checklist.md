# Company Profile Mapping Checklist

## Objective

Map all business modules to `company_profile_id` so every record is explicitly tenant-scoped by the active company profile.

Default bootstrap company profile to create for the seeded admin user:

- Company name: `Ampspark Technologies Private Limited`
- Company email: `thejairaghav@gmail.com`
- User: seeded admin user `thejairaghav@gmail.com`
- Default flag: `is_default = 1`

## Phase 1: Bootstrap Default Company Profile

- [x] Add default company profile bootstrap data for the seeded admin user.
- [x] Keep the bootstrap path consistent with the current migration-owned bootstrap style.
- [x] Update or upsert by `user_id` and/or company email so the insert is idempotent.
- [x] Ensure the seeded company profile points to the seeded admin user ID.
- [x] Ensure `is_default` is set to `1`.
- [x] Validate that login loads this company profile into session.

Target files:

- `database/migrations/2025_06_28_133520_create_company_profile_table.php`
- `app/Providers/AppServiceProvider.php`

## Phase 2: Centralize Active Company Resolution

- [x] Make `session('company_profile.id')` the single active-company source of truth.
- [x] Remove or replace any usage of `auth()->user()->company_profile_id`.
- [x] Add a shared helper/service/trait for resolving the active company profile ID.
- [x] Ensure controllers and repositories use the same resolver.

Immediate cleanup targets:

- `app/Repositories/PaymentRepository.php`
- `app/Repositories/ActionLogRepository.php`

## Phase 3: Add Missing `company_profile_id` Columns

### Employee module

- [x] Add `company_profile_id` to `employee`.
- [x] Add foreign key to `company_profile`.
- [x] Add index on `company_profile_id`.
- [x] Backfill existing employee rows.

### Attendance module

- [x] Add `company_profile_id` to `attendance`.
- [x] Add foreign key to `company_profile`.
- [x] Add index on `company_profile_id`.
- [x] Backfill existing attendance rows.

### Employee attendance rows

- [x] Add `company_profile_id` to `employee_attendanceboard`.
- [x] Add foreign key to `company_profile`.
- [x] Add index on `company_profile_id`.
- [x] Backfill existing rows from parent attendance or employee.

### Payments board module

- [x] Add `company_profile_id` to `payments_board`.
- [x] Add foreign key to `company_profile`.
- [x] Add index on `company_profile_id`.
- [x] Backfill existing boards.

### Client payments board rows

- [x] Add `company_profile_id` to `clients_payments`.
- [x] Add foreign key to `company_profile`.
- [x] Add index on `company_profile_id`.
- [x] Backfill existing rows from parent board or client.

## Phase 4: Fix Tenant Uniqueness Rules

- [x] Make attendance period uniqueness company-scoped instead of global.
- [x] Make payments board month-year uniqueness company-scoped instead of global.
- [x] Review any other unique constraints that should be composite with `company_profile_id`.

Likely targets:

- `attendance`: unique on `company_profile_id + attendance_month_year`
- `payments_board`: unique on `company_profile_id + board_month_year`

## Phase 5: Update Models

### Add `company_profile_id` to fillable and relations

- [x] Update `app/Models/Employee.php`
- [x] Update `app/Models/Attendance.php`
- [x] Update `app/Models/EmployeeAttendanceboard.php`
- [x] Update `app/Models/Reports/PaymentsBoard.php`
- [x] Update `app/Models/Reports/ClientsPayments.php`

### Add company relationship/scopes

- [x] Add `companyProfile()` relation where missing.
- [x] Add `forCompany($companyProfileId)` scopes where missing.
- [x] Review existing models for consistent scope naming and usage.

## Phase 6: Module-By-Module Mapping

### Clients

- [x] Ensure all client reads are scoped to active `company_profile_id`.
- [x] Ensure edit/update/delete flows cannot access another company's client.
- [x] Replace direct model lookups in controllers with scoped repository methods.
- [x] Confirm client fetch-for-board API returns only active-company clients.

Target files:

- `app/Http/Controllers/Clients/ClientsController.php`
- `app/Repositories/ClientsRepository.php`

### Items

- [x] Scope item list, edit, update, and delete by `company_profile_id`.
- [x] Ensure JSON and web responses only expose company-owned items.
- [x] Add any missing repository methods for scoped lookup.

Target files:

- `app/Http/Controllers/Inventory/ItemsController.php`
- `app/Repositories/ItemsRepository.php`
- `app/Models/Inventory/Items.php`

### Transactions: sales, payments, expenses, ledger

- [x] Scope all transaction reads by active `company_profile_id`.
- [x] Scope all transaction updates and deletes by active `company_profile_id`.
- [x] Scope sales list filters by company.
- [x] Scope payment list filters by company.
- [x] Scope expense list filters by company.
- [x] Scope ledger data source by company.
- [x] Review any in-memory collection filtering and move tenant filters to query level.

Target files:

- `app/Repositories/TransactionRepository.php`
- `app/Http/Controllers/Transactions/SalesController.php`
- `app/Http/Controllers/Transactions/PaymentsController.php`
- `app/Http/Controllers/Transactions/ExpensesController.php`
- `app/Http/Controllers/Transactions/LedgerController.php`

### Account balances

- [x] Keep account balances as company-scoped records.
- [x] Verify all reads/writes/deletes enforce company ownership.
- [x] Verify balance lookups for client and period remain tenant-safe.

Target files:

- `app/Repositories/AccountBalanceRepository.php`
- `app/Models/AccountBalance.php`

### Employees

- [x] Write `company_profile_id` during employee creation.
- [x] Scope employee list, search, stats, edit, update, and delete by company.
- [x] Scope salary history and salary updates by company.
- [x] Scope advance payment flow by company.

Target files:

- `app/Models/Employee.php`
- `app/Repositories/EmployeeRepository.php`
- `app/Http/Controllers/Employee/EmployeeController.php`

### Attendance

- [x] Write `company_profile_id` during attendance board creation.
- [x] Scope attendance list, show, edit, update, and delete by company.
- [x] Ensure employee additions to attendance only allow employees from the same company.
- [x] Ensure attendance-row updates and salary payment processing are company-safe.

Target files:

- `app/Models/Attendance.php`
- `app/Models/EmployeeAttendanceboard.php`
- `app/Repositories/AttendanceRepository.php`
- `app/Http/Controllers/Employee/AttendanceController.php`

### Employee balances

- [x] Keep employee balances tied to company profile.
- [x] Ensure employee/company ownership is validated before create/update.
- [x] Review action logging and summaries for tenant safety.

Target files:

- `app/Repositories/EmployeeBalanceRepository.php`
- `app/Models/EmployeeBalance.php`

### Payment board

- [x] Write `company_profile_id` during payments board creation.
- [x] Scope payments board list, show, edit, update, delete, finalize by company.
- [x] Scope client-payment rows in `clients_payments` by company.
- [x] Ensure add-clients only uses clients from the active company.
- [x] Ensure recalculation uses only active-company transactions and balances.
- [x] Ensure finalization writes balances only for the active company.

Target files:

- `app/Models/Reports/PaymentsBoard.php`
- `app/Models/Reports/ClientsPayments.php`
- `app/Repositories/PaymentsBoardRepository.php`
- `app/Repositories/ClientsPaymentsRepository.php`
- `app/Http/Controllers/Reports/PaymentsBoardController.php`

### Legacy repositories still in the codebase

- [x] Move live `PaymentRepository` flows into `TransactionRepository` and retire it.
- [x] Move live `SalesRepository` flows into `TransactionRepository` and retire it.
- [x] Retire unused `ExpenseRepository` in favor of `TransactionRepository`.
- [x] Scope `ActionLogRepository` by session company profile.
- [x] Decide whether each legacy path should be retained, deprecated, or removed.

Target files:

- `app/Repositories/ActionLogRepository.php`

## Phase 7: Data Backfill And Integrity

- [x] Write backfill migrations before making new `company_profile_id` columns non-null.
- [x] Backfill employees to the default company profile for now.
- [x] Backfill attendance rows from attendance or employee ownership.
- [x] Backfill payment boards from related client/transaction ownership where possible.
- [x] Backfill `clients_payments` from parent board or client.
- [x] Add foreign keys after backfill if necessary for safe rollout.
- [x] Add `NOT NULL` constraints after backfill is complete.

## Phase 8: Validation And Tests

### Bootstrap tests

- [x] Verify the seeded admin user has a default company profile.
- [x] Verify login stores the default company profile in session.

### Tenant isolation tests

- [x] Clients are isolated by company.
- [x] Items are isolated by company.
- [x] Transactions are isolated by company.
- [x] Employees are isolated by company.
- [x] Attendance boards are isolated by company.
- [x] Payments boards are isolated by company.
- [x] Cross-company edits and deletes are blocked.

### Workflow regression tests

- [x] Sales create/update/delete remain functional after tenant scoping.
- [x] Payments create/update/delete remain functional after tenant scoping.
- [x] Expenses create/update/delete remain functional after tenant scoping.
- [x] Ledger loads only active-company data.
- [x] Attendance updates and salary payments remain functional after tenant scoping.
- [x] Payment board add/recalculate/finalize remains functional after tenant scoping.

## Suggested Execution Order

- [ ] 1. Add default company profile bootstrap
- [ ] 2. Centralize active company resolution
- [ ] 3. Add missing `company_profile_id` schema columns
- [ ] 4. Update model fillable fields, relationships, and scopes
- [ ] 5. Fix clients and items
- [ ] 6. Fix transactions module
- [ ] 7. Fix employees and attendance
- [ ] 8. Fix payment board and client payment rows
- [ ] 9. Clean up legacy repositories
- [ ] 10. Add tests and validate backfills

## Done Criteria

- [ ] Every business record is directly or explicitly scoped by `company_profile_id`.
- [ ] No repository uses `auth()->user()->company_profile_id` as the active tenant source.
- [ ] All module reads, writes, updates, and deletes are tenant-safe.
- [ ] Payment board and attendance uniqueness rules are company-scoped.
- [ ] The seeded admin user always gets the default company profile.
- [ ] Automated tests cover tenant isolation for all core business modules.
