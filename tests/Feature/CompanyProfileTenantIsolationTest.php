<?php

namespace Tests\Feature;

use App\Models\CompanyProfile;
use App\Models\Employee;
use App\Models\Reports\PaymentsBoard;
use App\Models\User;
use App\Repositories\EmployeeRepository;
use App\Repositories\PaymentsBoardRepository;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CompanyProfileTenantIsolationTest extends TestCase
{
    use RefreshDatabase;

    public function test_seeded_admin_user_has_a_default_company_profile(): void
    {
        $user = User::where('email', 'thejairaghav@gmail.com')->first();

        $this->assertNotNull($user);
        $this->assertDatabaseHas('company_profile', [
            'user_id' => $user->id,
            'company_name' => 'Ampspark Technologies Private Limited',
            'company_email' => 'thejairaghav@gmail.com',
            'is_default' => 1,
        ]);
    }

    public function test_login_stores_the_default_company_profile_in_session(): void
    {
        $user = User::factory()->create();

        $defaultCompany = CompanyProfile::create([
            'user_id' => $user->id,
            'company_name' => 'Default Company',
            'company_email' => 'default@example.com',
            'is_default' => 1,
        ]);

        CompanyProfile::create([
            'user_id' => $user->id,
            'company_name' => 'Secondary Company',
            'company_email' => 'secondary@example.com',
            'is_default' => 0,
        ]);

        $response = $this->post('/login', [
            'email' => $user->email,
            'password' => 'password',
        ]);

        $response->assertRedirect(route('dashboard', absolute: false));
        $this->assertAuthenticatedAs($user);
        $this->assertSame($defaultCompany->id, session('company_profile.id'));
    }

    public function test_employee_repository_only_returns_records_for_the_active_company(): void
    {
        [$primaryCompany, $secondaryCompany] = $this->createCompanyProfiles();

        $primaryEmployee = Employee::create([
            'company_profile_id' => $primaryCompany->id,
            'first_name' => 'Primary',
            'last_name' => 'Employee',
            'gender' => 'male',
            'mobile_number' => '1111111111',
            'status' => 'active',
            'salary' => 10000,
            'salary_hours' => 8,
            'department' => 'MI',
            'designation' => 'Operator',
            'joining_date' => '2025-01-01',
        ]);

        $secondaryEmployee = Employee::create([
            'company_profile_id' => $secondaryCompany->id,
            'first_name' => 'Secondary',
            'last_name' => 'Employee',
            'gender' => 'female',
            'mobile_number' => '2222222222',
            'status' => 'active',
            'salary' => 12000,
            'salary_hours' => 8,
            'department' => 'SMT',
            'designation' => 'Inspector',
            'joining_date' => '2025-02-01',
        ]);

        session(['company_profile' => ['id' => $primaryCompany->id]]);

        $repository = app(EmployeeRepository::class);
        $employees = $repository->getAllEmployees();

        $this->assertCount(1, $employees);
        $this->assertTrue($employees->contains('id', $primaryEmployee->id));
        $this->assertNull($repository->findEmployee($secondaryEmployee->id));
    }

    public function test_payments_board_repository_only_returns_records_for_the_active_company(): void
    {
        [$primaryCompany, $secondaryCompany] = $this->createCompanyProfiles();

        $primaryBoard = PaymentsBoard::create([
            'company_profile_id' => $primaryCompany->id,
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

        $secondaryBoard = PaymentsBoard::create([
            'company_profile_id' => $secondaryCompany->id,
            'board_month_year' => '02-2025',
            'start_date' => '2025-02-01',
            'end_date' => '2025-02-28',
            'total_days' => 28,
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

        session(['company_profile' => ['id' => $primaryCompany->id]]);

        $repository = app(PaymentsBoardRepository::class);
        $boards = $repository->getAllPaymentsBoards();

        $this->assertCount(1, $boards);
        $this->assertTrue($boards->contains('id', $primaryBoard->id));
        $this->assertNull($repository->findPaymentsBoard($secondaryBoard->id));
    }

    /**
     * @return array{0: CompanyProfile, 1: CompanyProfile}
     */
    private function createCompanyProfiles(): array
    {
        $primaryUser = User::factory()->create();
        $secondaryUser = User::factory()->create();

        $primaryCompany = CompanyProfile::create([
            'user_id' => $primaryUser->id,
            'company_name' => 'Primary Company',
            'company_email' => 'primary@example.com',
            'is_default' => 1,
        ]);

        $secondaryCompany = CompanyProfile::create([
            'user_id' => $secondaryUser->id,
            'company_name' => 'Secondary Company',
            'company_email' => 'secondary@example.com',
            'is_default' => 1,
        ]);

        return [$primaryCompany, $secondaryCompany];
    }
}