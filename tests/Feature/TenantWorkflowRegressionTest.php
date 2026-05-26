<?php

namespace Tests\Feature;

use App\Models\Attendance;
use App\Models\Client;
use App\Models\CompanyProfile;
use App\Models\Employee;
use App\Models\EmployeeAttendanceboard;
use App\Models\Inventory\Items;
use App\Models\Reports\ClientsPayments;
use App\Models\Reports\PaymentsBoard;
use App\Models\Reports\ProfitLossReport;
use App\Models\Transaction;
use App\Models\User;
use App\Repositories\AttendanceRepository;
use App\Repositories\TransactionRepository;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class TenantWorkflowRegressionTest extends TestCase
{
    use RefreshDatabase;

    public function test_clients_are_isolated_and_cross_company_client_access_is_blocked(): void
    {
        [$user, $company] = $this->createCompanyContext('primary');
        [, $otherCompany] = $this->createCompanyContext('secondary');

        $client = $this->createClient($company, 'Primary Client');
        $otherClient = $this->createClient($otherCompany, 'Secondary Client');

        $this->actingAs($user)
            ->withSession($this->companySession($company))
            ->get(route('clients.index'))
            ->assertOk()
            ->assertViewHas('clients', fn ($clients) => $clients->count() === 1 && $clients->first()->id === $client->id);

        $this->actingAs($user)
            ->withSession($this->companySession($company))
            ->get(route('clients.edit', $otherClient->id))
            ->assertNotFound();

        $this->actingAs($user)
            ->withSession($this->companySession($company))
            ->delete(route('clients.destroy', $otherClient->id))
            ->assertRedirect(route('clients.index'))
            ->assertSessionHas('error');

        $this->assertDatabaseHas('clients', ['id' => $otherClient->id]);
    }

    public function test_clients_allow_two_character_names_negative_opening_balances_and_alphabetical_ordering(): void
    {
        [$user, $company] = $this->createCompanyContext('primary');

        $this->createClient($company, 'Zulu Client');
        $this->createClient($company, 'Delta Client');

        $this->actingAs($user)
            ->withSession($this->companySession($company))
            ->post(route('clients.store'), [
                'client_name' => 'Ab',
                'display_name' => 'Ab',
                'client_email' => 'ab@example.com',
                'client_phone' => '1234567890',
                'client_type' => 'Business',
                'client_tax_number' => null,
                'is_active' => '1',
                'add_opening_balance' => '1',
                'account_balance' => '-25.50',
                'applicable_month' => '1',
                'applicable_year' => '2025',
            ])
            ->assertRedirect(route('clients.index'))
            ->assertSessionHas('success');

        $this->assertDatabaseHas('clients', [
            'company_profile_id' => $company->id,
            'client_name' => 'Ab',
        ]);

        $client = Client::where('company_profile_id', $company->id)
            ->where('client_name', 'Ab')
            ->firstOrFail();

        $this->assertDatabaseHas('accounts_balance', [
            'company_profile_id' => $company->id,
            'client_id' => $client->id,
            'month' => 1,
            'year' => 2025,
            'opening_balance' => -25.50,
        ]);

        $this->actingAs($user)
            ->withSession($this->companySession($company))
            ->get(route('clients.index'))
            ->assertOk()
            ->assertViewHas('clients', function ($clients) {
                return $clients->pluck('client_name')->values()->all() === [
                    'Ab',
                    'Delta Client',
                    'Zulu Client',
                ];
            });
    }

    public function test_items_are_isolated_by_company(): void
    {
        [$user, $company] = $this->createCompanyContext('primary');
        [, $otherCompany] = $this->createCompanyContext('secondary');

        $item = $this->createItem($company, 'Primary Item');
        $this->createItem($otherCompany, 'Secondary Item');

        $response = $this->actingAs($user)
            ->withSession($this->companySession($company))
            ->getJson(route('items.index'));

        $response->assertOk()
            ->assertJsonCount(1, 'data')
            ->assertJsonPath('data.0.id', $item->id);
    }

    public function test_transactions_and_ledger_are_isolated_by_company(): void
    {
        [$user, $company] = $this->createCompanyContext('primary');
        [, $otherCompany] = $this->createCompanyContext('secondary');

        $client = $this->createClient($company, 'Ledger Client');
        $otherClient = $this->createClient($otherCompany, 'Other Ledger Client');

        $transaction = $this->createTransaction($company, $client, 'sale', ['total_amount' => 100]);
        $this->createTransaction($otherCompany, $otherClient, 'sale', ['total_amount' => 200]);

        session($this->companySession($company));
        $transactions = app(TransactionRepository::class)->getAllTransactions();

        $this->assertCount(1, $transactions);
        $this->assertSame($transaction->id, $transactions->first()->id);

        $this->actingAs($user)
            ->withSession($this->companySession($company))
            ->get(route('ledger.index', [
                'date_range' => 'custom',
                'start_date' => '2025-01-01',
                'end_date' => '2025-01-31',
            ]))
            ->assertOk()
            ->assertViewHas('transactions', fn ($ledgerTransactions) => $ledgerTransactions->count() === 1 && $ledgerTransactions->first()->id === $transaction->id);
    }

    public function test_attendance_boards_are_isolated_and_cross_company_access_is_blocked(): void
    {
        [$user, $company] = $this->createCompanyContext('primary');
        [, $otherCompany] = $this->createCompanyContext('secondary');

        $attendance = Attendance::create([
            'company_profile_id' => $company->id,
            'attendance_month_year' => 'January, 2025',
            'start_date' => '2025-01-01 00:00:00',
            'end_date' => '2025-01-31 23:59:59',
            'total_days' => 31,
            'employee_count' => 0,
            'total_salary_paid' => 0,
            'total_advance_paid' => 0,
            'total_overtime_hours' => 0,
            'total_overtime_paid' => 0,
            'previous_balance_adjusted' => 0,
            'balance_carry_forward' => 0,
        ]);

        $otherAttendance = Attendance::create([
            'company_profile_id' => $otherCompany->id,
            'attendance_month_year' => 'February, 2025',
            'start_date' => '2025-02-01 00:00:00',
            'end_date' => '2025-02-28 23:59:59',
            'total_days' => 28,
            'employee_count' => 0,
            'total_salary_paid' => 0,
            'total_advance_paid' => 0,
            'total_overtime_hours' => 0,
            'total_overtime_paid' => 0,
            'previous_balance_adjusted' => 0,
            'balance_carry_forward' => 0,
        ]);

        session($this->companySession($company));
        $attendances = app(AttendanceRepository::class)->getAllAttendances();

        $this->assertCount(1, $attendances);
        $this->assertSame($attendance->id, $attendances->first()->id);

        $this->actingAs($user)
            ->withSession($this->companySession($company))
            ->get(route('attendance.getemployees', $otherAttendance))
            ->assertStatus(404);
    }

    public function test_sales_create_update_and_delete_work_after_tenant_scoping(): void
    {
        [$user, $company] = $this->createCompanyContext('primary');
        $client = $this->createClient($company, 'Sales Client');
        $payment = $this->createTransaction($company, $client, 'payment', [
            'transaction_date' => '2025-01-12',
            'payment_method' => 'cash',
            'base_amount' => 60,
            'total_amount' => 60,
            'paid' => 1,
        ]);

        $storePayload = [
            'client_id' => $client->id,
            'sale_date' => '2025-01-10',
            'sales_type' => 'invoice',
            'base_amount' => 100,
            'tax_amount' => 18,
            'tax_rate' => 18,
            'total_amount' => 118,
            'tds' => 0,
            'tds_rate' => 0,
            'due_date' => '2025-01-15',
            'paid' => 0,
            'notes' => 'Original sale',
        ];

        $this->actingAs($user)
            ->withSession($this->companySession($company))
            ->post(route('sales.store'), $storePayload)
            ->assertRedirect(route('sales.index'));

        $sale = Transaction::where('company_profile_id', $company->id)->where('transaction_type', 'sale')->firstOrFail();
        $this->assertSame('Original sale', $sale->notes);

        $updatePayload = $storePayload;
        $updatePayload['total_amount'] = 150;
        $updatePayload['payment_id'] = $payment->id;
        $updatePayload['notes'] = 'Updated sale';

        $this->actingAs($user)
            ->withSession($this->companySession($company))
            ->put(route('sales.update', $sale->id), $updatePayload)
            ->assertRedirect(route('sales.index'));

        $this->assertDatabaseHas('transactions', [
            'id' => $sale->id,
            'company_profile_id' => $company->id,
            'total_amount' => 150,
            'paid' => 1,
            'payment_id' => $payment->id,
            'notes' => 'Updated sale',
        ]);

        $this->actingAs($user)
            ->withSession($this->companySession($company))
            ->delete(route('sales.destroy', $sale->id))
            ->assertRedirect(route('sales.index'));

        $this->assertDatabaseMissing('transactions', ['id' => $sale->id]);
    }

    public function test_sales_edit_only_exposes_unlinked_payments_for_the_same_client(): void
    {
        [$user, $company] = $this->createCompanyContext('primary');
        $client = $this->createClient($company, 'Sales Client');
        $otherClient = $this->createClient($company, 'Other Sales Client');

        $sale = $this->createTransaction($company, $client, 'sale', [
            'transaction_date' => '2025-01-10',
            'sales_type' => 'invoice',
            'base_amount' => 100,
            'tax_amount' => 18,
            'tax_rate' => 18,
            'total_amount' => 118,
            'paid' => 1,
        ]);

        $currentLinkedPayment = $this->createTransaction($company, $client, 'payment', [
            'transaction_date' => '2025-01-12',
            'payment_method' => 'cash',
            'base_amount' => 60,
            'total_amount' => 60,
            'paid' => 1,
        ]);

        $sale->update(['payment_id' => $currentLinkedPayment->id]);

        $availablePayment = $this->createTransaction($company, $client, 'payment', [
            'transaction_date' => '2025-01-14',
            'payment_method' => 'bank_transfer',
            'base_amount' => 80,
            'total_amount' => 80,
            'paid' => 1,
        ]);

        $unavailablePayment = $this->createTransaction($company, $client, 'payment', [
            'transaction_date' => '2025-01-16',
            'payment_method' => 'cash',
            'base_amount' => 45,
            'total_amount' => 45,
            'paid' => 1,
        ]);

        $this->createTransaction($company, $client, 'sale', [
            'transaction_date' => '2025-01-17',
            'sales_type' => 'invoice',
            'base_amount' => 45,
            'tax_amount' => 0,
            'tax_rate' => 0,
            'total_amount' => 45,
            'paid' => 1,
            'payment_id' => $unavailablePayment->id,
        ]);

        $otherClientPayment = $this->createTransaction($company, $otherClient, 'payment', [
            'transaction_date' => '2025-01-18',
            'payment_method' => 'cash',
            'base_amount' => 90,
            'total_amount' => 90,
            'paid' => 1,
        ]);

        $response = $this->actingAs($user)
            ->withSession($this->companySession($company))
            ->get(route('sales.edit', $sale->id));

        $response->assertOk()
            ->assertSee(sprintf('#%d | INR 60.00 | 12 Jan 2025', $currentLinkedPayment->id))
            ->assertSee(sprintf('#%d | INR 80.00 | 14 Jan 2025', $availablePayment->id))
            ->assertViewHas('availablePaymentsByClient', function ($availablePaymentsByClient) use ($client, $currentLinkedPayment, $availablePayment, $unavailablePayment, $otherClient, $otherClientPayment) {
                $clientOptionIds = collect($availablePaymentsByClient[$client->id] ?? [])->pluck('id')->map(fn ($id) => (int) $id)->all();
                $otherClientOptionIds = collect($availablePaymentsByClient[$otherClient->id] ?? [])->pluck('id')->map(fn ($id) => (int) $id)->all();
                $expectedClientOptionIds = [$availablePayment->id, $currentLinkedPayment->id];

                sort($clientOptionIds);
                sort($expectedClientOptionIds);
                sort($otherClientOptionIds);

                return $clientOptionIds === $expectedClientOptionIds
                    && !in_array($unavailablePayment->id, $clientOptionIds, true)
                    && $otherClientOptionIds === [$otherClientPayment->id];
            });
    }

    public function test_payments_create_update_and_delete_work_after_tenant_scoping(): void
    {
        [$user, $company] = $this->createCompanyContext('primary');
        $client = $this->createClient($company, 'Payments Client');

        $storePayload = [
            'client_id' => $client->id,
            'amount_paid' => 60,
            'payment_date' => '2025-01-12',
            'payment_method' => 'cash',
            'notes' => 'Original payment',
        ];

        $this->actingAs($user)
            ->withSession($this->companySession($company))
            ->post(route('payments.store'), $storePayload)
            ->assertRedirect(route('payments.index'));

        $payment = Transaction::where('company_profile_id', $company->id)->where('transaction_type', 'payment')->firstOrFail();

        $updatePayload = $storePayload;
        $updatePayload['amount_paid'] = 75;
        $updatePayload['notes'] = 'Updated payment';

        $this->actingAs($user)
            ->withSession($this->companySession($company))
            ->put(route('payments.update', $payment->id), $updatePayload)
            ->assertRedirect(route('payments.index'));

        $this->assertDatabaseHas('transactions', [
            'id' => $payment->id,
            'company_profile_id' => $company->id,
            'total_amount' => 75,
            'notes' => 'Updated payment',
        ]);

        $this->actingAs($user)
            ->withSession($this->companySession($company))
            ->delete(route('payments.destroy', $payment->id))
            ->assertRedirect(route('payments.index'));

        $this->assertDatabaseMissing('transactions', ['id' => $payment->id]);
    }

    public function test_expenses_create_update_and_delete_work_after_tenant_scoping(): void
    {
        [$user, $company] = $this->createCompanyContext('primary');

        $storePayload = [
            'expense_type' => 'cash',
            'expense_date' => '2025-01-20',
            'base_amount' => 50,
            'tax_amount' => 9,
            'tax_rate' => 18,
            'total_amount' => 59,
            'paid' => 1,
            'notes' => 'Original expense',
        ];

        $this->actingAs($user)
            ->withSession($this->companySession($company))
            ->post(route('expenses.store'), $storePayload)
            ->assertRedirect(route('expenses.index'));

        $expense = Transaction::where('company_profile_id', $company->id)->where('transaction_type', 'expense')->firstOrFail();

        $updatePayload = $storePayload;
        $updatePayload['total_amount'] = 79;
        $updatePayload['notes'] = 'Updated expense';

        $this->actingAs($user)
            ->withSession($this->companySession($company))
            ->put(route('expenses.update', $expense->id), $updatePayload)
            ->assertRedirect(route('expenses.index'));

        $this->assertDatabaseHas('transactions', [
            'id' => $expense->id,
            'company_profile_id' => $company->id,
            'total_amount' => 79,
            'notes' => 'Updated expense',
        ]);

        $this->actingAs($user)
            ->withSession($this->companySession($company))
            ->delete(route('expenses.destroy', $expense->id))
            ->assertRedirect(route('expenses.index'));

        $this->assertDatabaseMissing('transactions', ['id' => $expense->id]);
    }

    public function test_attendance_update_and_salary_payment_work_after_tenant_scoping(): void
    {
        [$user, $company] = $this->createCompanyContext('primary');
        $employee = Employee::create([
            'company_profile_id' => $company->id,
            'first_name' => 'Attendance',
            'last_name' => 'Employee',
            'gender' => 'male',
            'mobile_number' => '9999999999',
            'status' => 'active',
            'salary' => 3100,
            'salary_hours' => 8,
            'department' => 'MI',
            'designation' => 'Worker',
            'joining_date' => '2025-01-01',
        ]);

        $attendance = Attendance::create([
            'company_profile_id' => $company->id,
            'attendance_month_year' => 'January, 2025',
            'start_date' => '2025-01-01 00:00:00',
            'end_date' => '2025-01-31 23:59:59',
            'total_days' => 31,
            'employee_count' => 0,
            'total_salary_paid' => 0,
            'total_advance_paid' => 0,
            'total_overtime_hours' => 0,
            'total_overtime_paid' => 0,
            'previous_balance_adjusted' => 0,
            'balance_carry_forward' => 0,
        ]);

        $this->actingAs($user)
            ->withSession($this->companySession($company))
            ->post(route('attendance.addemployees', $attendance), ['employees' => [$employee->id]])
            ->assertOk()
            ->assertJsonPath('success', true);

        $row = EmployeeAttendanceboard::where('attendance_id', $attendance->id)->where('employee_id', $employee->id)->firstOrFail();

        $this->actingAs($user)
            ->withSession($this->companySession($company))
            ->put(route('attendance.update', $attendance->id), [
                'employees' => [[
                    'id' => $row->id,
                    'employee_id' => $employee->id,
                    'present_days' => 10,
                    'overtime_hours' => 2,
                    'bonus_amount' => 50,
                    'advance_deducted' => 20,
                    'remarks' => 'Updated attendance',
                ]],
            ])
            ->assertRedirect(route('attendance.edit', $attendance->id));

        $row->refresh();
        $this->assertSame(10, $row->present_days);

        $this->actingAs($user)
            ->withSession($this->companySession($company))
            ->post(route('attendance.pay', $row->id), [
                'record_id' => $row->id,
                'amount_paid' => 900,
                'not_directly_paid' => true,
            ])
            ->assertOk()
            ->assertJsonPath('success', true)
            ->assertJsonPath('data.not_directly_paid', true);

        $this->assertDatabaseHas('employee_attendanceboard', [
            'id' => $row->id,
            'company_profile_id' => $company->id,
            'paid_amount' => 900,
            'not_directly_paid' => 1,
        ]);
    }

    public function test_payments_board_add_recalculate_and_finalize_work_after_tenant_scoping(): void
    {
        [$user, $company] = $this->createCompanyContext('primary');
        $alphaClient = $this->createClient($company, 'Alpha Client');
        $client = $this->createClient($company, 'Board Client');

        $board = PaymentsBoard::create([
            'company_profile_id' => $company->id,
            'board_month_year' => '01-2025',
            'start_date' => '2025-01-01',
            'end_date' => '2025-01-31',
            'total_days' => 31,
            'clients_count' => 0,
            'total_pre_gst_amount' => 0,
            'total_gst_amount' => 0,
            'total_cash_sales' => 0,
            'total_tds' => 0,
            'total_previous_balance' => 0,
            'total_amount' => 0,
            'total_net_amount' => 0,
            'total_paid_amount' => 0,
            'total_unpaid_amount' => 0,
        ]);

        $this->createTransaction($company, $client, 'sale', [
            'transaction_date' => '2025-01-10',
            'sales_type' => 'invoice',
            'base_amount' => 100,
            'tax_amount' => 0,
            'total_amount' => 100,
            'paid' => 0,
        ]);

        $this->createTransaction($company, $client, 'payment', [
            'transaction_date' => '2025-02-15',
            'payment_method' => 'cash',
            'base_amount' => 40,
            'total_amount' => 40,
            'paid' => 1,
        ]);

        $this->actingAs($user)
            ->withSession($this->companySession($company))
            ->post(route('reports.payments.board.add-clients', $board->id), [
                'client_ids' => [$client->id, $alphaClient->id],
            ])
            ->assertOk()
            ->assertJsonPath('success', true);

        $clientPayment = ClientsPayments::where('payments_board_id', $board->id)->where('client_id', $client->id)->firstOrFail();

        $this->actingAs($user)
            ->withSession($this->companySession($company))
            ->post(route('reports.payments.board.recalculate-client', $clientPayment->id))
            ->assertOk()
            ->assertJsonPath('success', true);

        $this->assertDatabaseHas('clients_payments', [
            'id' => $clientPayment->id,
            'company_profile_id' => $company->id,
            'paid_amount' => 40,
        ]);

        $payment = Transaction::where('company_profile_id', $company->id)
            ->where('transaction_type', 'payment')
            ->where('client_id', $client->id)
            ->firstOrFail();

        $payment->update([
            'base_amount' => 55,
            'total_amount' => 55,
        ]);

        $this->actingAs($user)
            ->withSession($this->companySession($company))
            ->post(route('reports.payments.board.recalculate', $board->id))
            ->assertOk()
            ->assertJsonPath('success', true)
            ->assertJsonPath('data.recalculated_clients_count', 2);

        $this->assertDatabaseHas('clients_payments', [
            'id' => $clientPayment->id,
            'company_profile_id' => $company->id,
            'paid_amount' => 55,
        ]);

        $this->assertDatabaseHas('payments_board', [
            'id' => $board->id,
            'company_profile_id' => $company->id,
            'total_paid_amount' => 55,
        ]);

        $this->actingAs($user)
            ->withSession($this->companySession($company))
            ->get(route('reports.payments.board.show', $board->id))
            ->assertOk()
            ->assertViewHas('clientsPayments', function ($clientsPayments) use ($alphaClient, $client) {
                return $clientsPayments->pluck('client.client_name')->values()->all() === [
                    $alphaClient->client_name,
                    $client->client_name,
                ];
            });

        $this->actingAs($user)
            ->withSession($this->companySession($company))
            ->post(route('reports.payments.board.finalize', $board->id))
            ->assertOk()
            ->assertJsonPath('success', true);

        $this->assertDatabaseHas('accounts_balance', [
            'company_profile_id' => $company->id,
            'client_id' => $client->id,
            'month' => 2,
            'year' => 2025,
            'opening_balance' => 45,
        ]);

        $this->assertDatabaseHas('payments_board', [
            'id' => $board->id,
            'company_profile_id' => $company->id,
        ]);

        $this->assertNotNull($board->fresh()->finalized_at);
    }

    public function test_profit_loss_requires_finalized_payments_board_and_uses_board_sales_and_month_expenses(): void
    {
        [$user, $company] = $this->createCompanyContext('primary');
        $client = $this->createClient($company, 'Profit Client');

        $board = PaymentsBoard::create([
            'company_profile_id' => $company->id,
            'board_month_year' => '01-2025',
            'start_date' => '2025-01-01',
            'end_date' => '2025-01-31',
            'total_days' => 31,
            'clients_count' => 0,
            'total_pre_gst_amount' => 0,
            'total_gst_amount' => 0,
            'total_cash_sales' => 0,
            'total_tds' => 0,
            'total_previous_balance' => 0,
            'total_amount' => 0,
            'total_net_amount' => 0,
            'total_paid_amount' => 0,
            'total_unpaid_amount' => 0,
        ]);

        $this->createTransaction($company, $client, 'sale', [
            'transaction_date' => '2025-01-10',
            'sales_type' => 'invoice',
            'base_amount' => 100,
            'tax_amount' => 18,
            'tax_rate' => 18,
            'total_amount' => 118,
            'paid' => 0,
        ]);

        $this->createTransaction($company, $client, 'sale', [
            'transaction_date' => '2025-01-12',
            'sales_type' => 'cash',
            'base_amount' => 20,
            'tax_amount' => 0,
            'tax_rate' => 0,
            'total_amount' => 20,
            'paid' => 1,
        ]);

        $this->createTransaction($company, null, 'expense', [
            'transaction_date' => '2025-01-22',
            'sales_type' => 'cash',
            'base_amount' => 30,
            'tax_amount' => 0,
            'tax_rate' => 0,
            'total_amount' => 30,
            'paid' => 1,
        ]);

        $this->actingAs($user)
            ->withSession($this->companySession($company))
            ->post(route('reports.payments.board.add-clients', $board->id), [
                'client_ids' => [$client->id],
            ])
            ->assertOk()
            ->assertJsonPath('success', true);

        $this->assertEquals(20.0, (float) $board->fresh()->total_cash_sales);
        $this->assertEquals(100.0, (float) $board->fresh()->total_pre_gst_amount);
        $this->assertEquals(18.0, (float) $board->fresh()->total_gst_amount);

        $this->actingAs($user)
            ->withSession($this->companySession($company))
            ->from(route('reports.profit-loss.create'))
            ->post(route('reports.profit-loss.store'), [
                'profit_loss_month' => '01',
                'profit_loss_year' => '2025',
            ])
            ->assertRedirect(route('reports.profit-loss.create'))
            ->assertSessionHasErrors()
            ->assertSessionHas('errors', fn ($errors) => in_array('Finalize the payments board for this period before creating P/L.', $errors->all(), true));

        $this->actingAs($user)
            ->withSession($this->companySession($company))
            ->post(route('reports.payments.board.finalize', $board->id))
            ->assertOk()
            ->assertJsonPath('success', true);

        $response = $this->actingAs($user)
            ->withSession($this->companySession($company))
            ->post(route('reports.profit-loss.store'), [
                'profit_loss_month' => '01',
                'profit_loss_year' => '2025',
            ]);

        $report = ProfitLossReport::where('company_profile_id', $company->id)
            ->where('board_month_year', '01-2025')
            ->firstOrFail();

        $response->assertRedirect(route('reports.profit-loss.show', $report->id));

        $this->assertDatabaseHas('profit_loss_reports', [
            'id' => $report->id,
            'company_profile_id' => $company->id,
            'payments_board_id' => $board->id,
            'total_income' => 138,
            'total_expenses' => 30,
            'total_gst' => 18,
            'profit_loss' => 90,
        ]);
    }

    private function createCompanyContext(string $prefix): array
    {
        $user = User::factory()->create([
            'email' => $prefix . '@example.com',
        ]);

        $company = CompanyProfile::create([
            'user_id' => $user->id,
            'company_name' => ucfirst($prefix) . ' Company',
            'company_email' => $prefix . '@example.com',
            'is_default' => 1,
        ]);

        return [$user, $company];
    }

    private function companySession(CompanyProfile $company): array
    {
        return ['company_profile' => $company];
    }

    private function createClient(CompanyProfile $company, string $name): Client
    {
        return Client::create([
            'company_profile_id' => $company->id,
            'client_name' => $name,
            'display_name' => $name,
            'client_email' => strtolower(str_replace(' ', '.', $name)) . '@example.com',
            'client_phone' => '1234567890',
            'client_type' => 'business',
            'client_tax_number' => null,
            'is_active' => 1,
        ]);
    }

    private function createItem(CompanyProfile $company, string $name): Items
    {
        return Items::create([
            'company_profile_id' => $company->id,
            'item_type' => 'product',
            'item_name' => $name,
            'item_description' => $name . ' description',
            'item_sku' => strtoupper(substr($name, 0, 4)) . rand(100, 999),
            'item_price' => 10,
            'item_unit' => 'pcs',
            'item_hsn_code' => 'HSN001',
        ]);
    }

    private function createTransaction(CompanyProfile $company, ?Client $client, string $type, array $overrides = []): Transaction
    {
        return Transaction::create(array_merge([
            'company_profile_id' => $company->id,
            'client_id' => $client?->id,
            'transaction_type' => $type,
            'transaction_date' => '2025-01-01',
            'sales_type' => $type === 'expense' ? 'cash' : 'invoice',
            'base_amount' => 10,
            'tax_amount' => 0,
            'tax_rate' => 0,
            'tds' => 0,
            'tds_rate' => 0,
            'total_amount' => 10,
            'due_date' => null,
            'paid' => $type === 'payment',
            'payment_method' => $type === 'payment' ? 'cash' : null,
            'notes' => $type . ' note',
        ], $overrides));
    }
}