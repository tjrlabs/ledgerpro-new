# LedgerPro Codebase Audit

## Scope

This report is based on a static review of the Laravel codebase as it exists today. It covers the implemented ledger modules, highlights features that appear incorrect or incomplete in code, and suggests pragmatic enhancements for each area.

Primary implementation surfaces reviewed:

- `routes/web.php`
- `app/Http/Controllers/**`
- `app/Repositories/**`
- `app/Models/**`
- `database/migrations/**`
- `tests/**`

## Executive Summary

The app already covers the main operational areas of a small ledger/business system:

- Sales entry and filtering
- Client payment tracking
- Expense tracking
- Client ledger views
- Client management and opening balances
- Employee management, salary history, advances, and attendance boards
- Item catalog with pricing
- Payment board reporting for unpaid/outstanding client balances

The biggest codebase risks are cross-cutting rather than UI-level:

1. Tenant isolation is inconsistent. Many repositories ignore `company_profile_id`, even though the project is clearly designed around session-based company context.
2. Ledger math is inconsistent across modules. Some balances are calculated as if payments are additional income rather than settlement against receivables.
3. The payment board/reporting module has multiple concrete defects: wrong validation table names, wrong route names, wrong field names, and writes to columns that do not exist.
4. There is almost no automated coverage for business modules. The current test suite only covers auth/profile scaffolding.
5. The codebase contains architectural drift: legacy repositories/models still exist beside the newer `transactions`-based flow, which increases maintenance risk.

## Cross-Cutting Findings

### 1. Multi-tenant boundaries are not enforced consistently

The app relies on `session('company_profile.id')`, but many repository reads and updates are not scoped by company:

- `TransactionRepository` reads, finds, updates, and deletes transactions without tenant filters.
- `ClientsRepository::getAllClients()` returns all clients instead of company-scoped clients.
- `EmployeeRepository` and `AttendanceRepository` have no company scoping at all.
- `ItemsController::edit()` uses `Items::findOrFail($id)` directly without company checks.

Impact:

- Data leakage across companies/tenants is possible.
- Records can potentially be edited or deleted across tenant boundaries if IDs are known.

Recommendation:

- Add a shared company-scope strategy at model/repository level and make all reads/writes tenant-aware by default.

### 2. Business modules have no meaningful automated tests

The `tests` directory contains only auth/profile/example tests. There are no feature or unit tests covering:

- sales
- payments
- expenses
- client ledger
- clients
- employees
- attendance
- items
- payment board

Impact:

- Regressions in totals, balances, and workflows are likely to go unnoticed.

Recommendation:

- Add feature tests for the transaction flows first, then repository tests for ledger/payment board calculations.

### 3. Duplicate domain paths increase risk

The current web flow mainly uses `TransactionRepository`, but older repositories like `SalesRepository`, `ExpenseRepository`, and `PaymentRepository` are still present. This suggests a partial migration from dedicated tables/models to the unified `transactions` table.

Impact:

- Team members can accidentally extend the wrong implementation path.
- Some controllers still mix old and new patterns.

Recommendation:

- Choose one canonical domain model per module and remove or isolate legacy paths.

## Module Review

## Sales Tracking

### What is implemented

Sales are handled through `Transactions\SalesController` and stored in the unified `transactions` table with `transaction_type = sale`.

Implemented behavior includes:

- cash and invoice sales
- tax and TDS fields
- date-range filtering
- filtering by client and payment status
- create, edit, update, delete

### What is not coded correctly

1. Sales queries are not consistently company-scoped.
   `TransactionRepository::getSalesTransactions()`, `getTransactionsByDateRange()`, `findTransaction()`, `updateTransaction()`, and `deleteTransaction()` do not enforce `company_profile_id`.

2. Filtering is partly done in memory after broad fetches.
   `SalesController::index()` fetches all matching transactions for a date range and then filters the collection in PHP, which is less reliable and less scalable than query-level filtering.

3. Sales are not itemized against the item catalog.
   The sales flow stores aggregate monetary values only. It does not appear to reference the item catalog or store line items, quantities, or price snapshots.

### Suggested enhancements

- Add sales line items linked to the item catalog.
- Support invoice number generation and PDF export.
- Add partial-payment allocation against specific sales/invoices.
- Add aging buckets for unpaid invoices.
- Add company-scoped query helpers to all transaction reads.

## Client Payment Tracking

### What is implemented

Client payments are also stored in `transactions` with `transaction_type = payment`.

Implemented behavior includes:

- create, edit, update, delete payment entries
- client and payment-method filtering
- date-range filtering
- payment methods: cash, bank transfer, cash transfer

### What is not coded correctly

1. A route exists for payment details, but there is no corresponding controller method.
   `routes/web.php` defines `/payments/details/{id}` mapped to `PaymentsController::details`, but `PaymentsController` does not implement `details()`.

2. Payment queries are not company-scoped.
   `TransactionRepository::getFilteredPayments()`, `findPaymentTransaction()`, `updatePaymentTransaction()`, and `deletePaymentTransaction()` do not filter by `company_profile_id`.

3. Pagination is manual and collection-based.
   `PaymentsController::index()` paginates an in-memory collection using `forPage()`, which means pagination metadata and database-level efficiency are missing.

4. Payments are tracked as generic client payments rather than explicit invoice allocations.
   The code supports a `payment_id` relation on sales, but there is no strong end-to-end allocation model for partial settlement across multiple invoices.

### Suggested enhancements

- Implement a payment details screen or remove the broken route.
- Add invoice allocation with partial settlement support.
- Add payment reference numbers, attachments, and reconciliation status.
- Convert payment list filtering/pagination to database-level queries.

## Expense Tracking

### What is implemented

Expenses are handled through `Transactions\ExpensesController` and stored in `transactions` with `transaction_type = expense`.

Implemented behavior includes:

- create, edit, update, delete expenses
- cash and invoice expense types
- paid/unpaid flags
- date and amount filtering
- summary totals on the index page

### What is not coded correctly

1. A duplicate route exists, but there is no corresponding controller method.
   `routes/web.php` defines `/expenses/duplicate/{id}` mapped to `ExpensesController::duplicate`, but `ExpensesController` does not implement `duplicate()`.

2. Expense queries are not consistently company-scoped in the active transaction-based path.
   `TransactionRepository::getFilteredExpenses()` does not filter by `company_profile_id`.

3. The codebase still contains a separate `ExpenseRepository` using an older expense model/table path, while the active web flow uses `TransactionRepository`.

### Suggested enhancements

- Implement expense duplication or remove the broken route.
- Add vendors/categories/attachments.
- Add recurring expense templates.
- Add GST/TDS reporting views for expense compliance.

## Client Ledger

### What is implemented

The ledger page consolidates transactions and lets users filter by:

- date range
- client
- transaction type

The effective display is limited to sales and payments.

### What is not coded correctly

1. The ledger balance formula is incorrect for a client receivables ledger.
   `LedgerController::index()` excludes expenses, but computes:

- `total_income` from sales
- `total_payments` from payments
- `net_balance = total_income + total_payments - total_expenses`

This adds payments to sales instead of treating payments as settlement against receivables. For a client ledger, the expected outstanding logic is typically closer to:

- outstanding = sales - payments

2. The controller still contains dead logic for expenses after explicitly filtering them out.

3. The ledger is not a true running ledger.
   It does not calculate opening balance + transaction-by-transaction running balance per client.

### Suggested enhancements

- Rework the ledger into a proper running balance statement.
- Add opening balance integration from `account_balances`.
- Add export to PDF/Excel.
- Add aging analysis and client statement generation.

## Client Management

### What is implemented

Client management includes:

- create/edit/update/delete clients
- opening balance handling via `AccountBalanceRepository`
- client picker support for the payment board

### What is not coded correctly

1. Client access is not consistently tenant-scoped.
   `ClientsController::edit()` uses `Client::findOrFail($id)` directly instead of the scoped repository method.

2. The payment-board client JSON payload uses wrong fields.
   `ClientsController::fetchForBoard()` maps:

- `email` from `$client->email`
- `phone` from `$client->phone`
- `company_name` from `$client->company_name`

But the `clients` model fields are `client_email` and `client_phone`, and `company_name` is not part of the reviewed model.

Impact:

- the API may return null or incorrect client details in the board UI.

3. `ClientsRepository::getAllClients()` is not company-scoped.

4. `ClientsRepository::updateClient()` wraps `SuccessData` inside `ResponseData` instead of returning `SuccessData` directly, unlike the rest of the codebase. That inconsistency makes response handling harder to reason about.

### Suggested enhancements

- Add strict company scoping to all client reads.
- Add client status/history/audit trail.
- Add credit limits, payment terms, and GST/tax metadata validation.
- Add a client statement page linked directly from the client screen.

## Employee Management And Attendance

### What is implemented

Employee features include:

- employee CRUD
- salary history
- salary update flow
- advance payment flow
- attendance board creation/editing
- salary calculation using attendance, overtime, bonus, advance deduction, and previous balance
- salary payment processing with action logging

### What is not coded correctly

1. Employee and attendance modules are not tenant-scoped.
   There is no `company_profile_id` in the reviewed employee/attendance flow, unlike the rest of the app.

2. `EmployeeController::payAdvance()` returns the wrong payload.
   The JSON response sets `updated_advance_due` to the entire repository response object instead of the numeric value inside it.

3. `EmployeeAttendanceboard::scopeWithAdvanceDue()` references an `advance_due` column that is commented out in the model and not present in the reviewed attendance-row fillable fields.

4. `EmployeeAttendanceboard::getAttendancePercentageAttribute()` uses `$this->attendance->working_days`, but attendance creation populates `total_days` and not `working_days`.

5. `Attendance::getTotalNetSalaryAttribute()` references `total_bonus_paid`, which is not present in the reviewed fillable/casts/schema path.

6. Attendance salary balance handling is conceptually mixed.
   Current logic uses `employee.outstanding_balance` as previous balance, then stores `balance_carry_forward` back on both the attendance row and the employee. That works as a rough carry-forward mechanism, but it is not backed by a dedicated monthly employee balance process in the active flow.

### Suggested enhancements

- Add tenant/company ownership to employees and attendance boards.
- Separate salary-calculation state from persistent employee balance state.
- Add attendance import, leave types, holiday calendars, and shift support.
- Add payroll registers, payslip generation, and approval workflow.
- Add tests for salary math, overtime, advance deduction, and carry-forward behavior.

## Items Management

### What is implemented

Items support:

- create/edit/update/list
- price, SKU, HSN code, type, description
- JSON responses for some endpoints

### What is not coded correctly

1. Item access is not consistently tenant-scoped.
   `ItemsController::edit()` and repository update/delete flows do not ensure the record belongs to the current company.

2. The item catalog is not integrated into the sales workflow.
   Sales are entered as aggregate values rather than item lines, so item pricing is currently administrative rather than transactional.

3. A destroy method exists in the controller, but there is no web route for item deletion in `routes/web.php`.

### Suggested enhancements

- Add item deletion route and authorization checks.
- Link items to sales line items with quantity, unit, tax, and price snapshot fields.
- Add inventory stock, reorder levels, and purchase-side tracking if stock is in scope.
- Add search/filtering on item index.

## Payment Board Showing Client Unpaid Status

### What is implemented

This is the strongest reporting module conceptually. It supports:

- monthly payments board creation
- adding clients to a board
- per-client board rows with cash sales, invoice values, TDS, previous balance, total amount, paid amount, remarks
- recalculation and finalization workflows

### What is not coded correctly

1. Validation points at the wrong table name.
   `PaymentsBoardController::update()` validates `clients.*.id` with `exists:client_payments,id`, but the actual table is `clients_payments`.

2. One redirect points at the wrong route name.
   `PaymentsBoardController::edit()` redirects to `reports.payments-board`, but the defined route name is `reports.payments.board`.

3. Board total updates write fields that do not exist in the schema.
   The code updates `total_subtotal_amount` and `total_outstanding`, but the `payments_board` migration defines `total_net_amount` and `total_unpaid_amount`.

Impact:

- recalculated totals will not persist as intended.

4. `PaymentsBoardRepository::recalculateTotals()` is effectively a stub and does not implement recalculation logic.

5. `PaymentsBoardController::recalculateClientPayment()` uses the month accessor incorrectly.
   `PaymentsBoard::$month` returns the month name, such as `September`, but the controller casts it to int for account-balance lookup. That yields `0`, so exact-period balance lookup is effectively broken and falls back to the most recent balance.

6. Payment board and client-payment rows are not company-scoped.
   These models do not include `company_profile_id`, so board data is indirectly scoped only through clients and transactions, which is fragile.

7. Previous balance logic is duplicated and inconsistent.
   `ClientsPaymentsRepository::addClientsToBoard()` already injects previous balance from `AccountBalanceRepository`, but `calculateClientTransactionAmounts()` separately computes a prior-period balance from old transactions.

8. Finalization writes `is_finalized` and `finalized_at`, but those fields are not present in the reviewed `payments_board` migration/model fillable list.

### Suggested enhancements

- Fix route names, validation table names, and schema-field mismatches first.
- Add explicit board status fields and persist them in schema.
- Make the board company-scoped.
- Use one authoritative balance source for previous-period carry-forward.
- Add monthly collection KPIs, aging bands, and board exports.
- Add immutable snapshot behavior once a board is finalized.

## Reports

### What is implemented

The only real reporting module currently implemented is the payment board.

### What is missing or incomplete

There is no broader reporting layer yet for:

- sales summary by month/client/type
- expense summary by category/type
- receivables aging
- profit and loss style summary
- GST/TDS tax summaries
- employee payroll reports

### Suggested enhancements

- Add dashboard-level KPI reports.
- Add downloadable monthly summary reports.
- Add client statement, collections, and overdue reports.
- Add payroll and expense analytics.

## Priority Fix List

If this app is going to be actively used, these are the highest-value fixes first:

1. Enforce `company_profile_id` scoping across transactions, clients, items, and all applicable reporting queries.
2. Fix broken routes and method mismatches:
   - payments `details`
   - expenses `duplicate`
   - payments board redirect route name
3. Fix payment board correctness issues:
   - wrong validation table name
   - wrong total field names vs schema
   - broken month-to-period conversion
   - missing finalized fields in schema/model
4. Correct client ledger balance logic to produce a true running outstanding balance.
5. Add tests for the main business modules, especially transaction math and payment board finalization.

## Suggested Product Roadmap

### Phase 1: Stabilize core ledger correctness

- tenant-safe queries
- route cleanup
- payment board fixes
- ledger balance fixes
- business test coverage

### Phase 2: Make the system operationally stronger

- itemized sales
- invoice allocation for payments
- richer client profiles and statements
- payroll balance formalization

### Phase 3: Add reporting and operational polish

- exports
- dashboards
- aging and collection analytics
- tax summaries
- approval/finalization workflows
