<?php

namespace App\Repositories;

use App\Classes\ResponseData;
use App\Classes\SuccessData;
use App\Classes\ErrorData;
use App\Models\EmployeeBalance;
use App\Models\Employee;
use Exception;
use Illuminate\Support\Facades\Log;
use Illuminate\Database\Eloquent\Collection;

class EmployeeBalanceRepository
{
    /**
     * Create a new class instance.
     */
    protected ActionLogRepository $actionLogRepository;

    public function __construct()
    {
        $this->actionLogRepository = new ActionLogRepository();
    }

    /**
     * Get all employee balances for a specific company
     *
     * @param int $companyProfileId
     * @return Collection
     */
    public function getAllEmployeeBalances(int $companyProfileId): Collection
    {
        return EmployeeBalance::with(['employee', 'companyProfile'])
            ->where('company_profile_id', $companyProfileId)
            ->orderBy('year', 'desc')
            ->orderBy('month', 'desc')
            ->get();
    }

    /**
     * Get employee balances for a specific employee
     *
     * @param int $employeeId
     * @param int $companyProfileId
     * @return Collection
     */
    public function getEmployeeBalances(int $employeeId, int $companyProfileId): Collection
    {
        return EmployeeBalance::with(['employee', 'companyProfile'])
            ->where('employee_id', $employeeId)
            ->where('company_profile_id', $companyProfileId)
            ->orderBy('year', 'desc')
            ->orderBy('month', 'desc')
            ->get();
    }

    /**
     * Get employee balance for a specific period
     *
     * @param int $employeeId
     * @param int $companyProfileId
     * @param int $month
     * @param int $year
     * @return EmployeeBalance|null
     */
    public function getEmployeeBalanceForPeriod(int $employeeId, int $companyProfileId, int $month, int $year): ?EmployeeBalance
    {
        return EmployeeBalance::where('employee_id', $employeeId)
            ->where('company_profile_id', $companyProfileId)
            ->forPeriod($month, $year)
            ->first();
    }

    /**
     * Get all employee balances for a specific year
     *
     * @param int $companyProfileId
     * @param int $year
     * @return Collection
     */
    public function getEmployeeBalancesForYear(int $companyProfileId, int $year): Collection
    {
        return EmployeeBalance::with(['employee', 'companyProfile'])
            ->where('company_profile_id', $companyProfileId)
            ->forYear($year)
            ->orderBy('month', 'desc')
            ->get();
    }

    /**
     * Store or update employee balance
     *
     * @param int $companyProfileId
     * @param int $employeeId
     * @param int $month
     * @param int $year
     * @param float $openingAdvanceBalance
     * @param float $openingAmountBalance
     * @return ResponseData
     */
    public function storeOrUpdateEmployeeBalance(
        int $companyProfileId,
        int $employeeId,
        int $month,
        int $year,
        float $openingAdvanceBalance,
        float $openingAmountBalance
    ): ResponseData {
        try {
            // Check if employee exists
            $employee = Employee::find($employeeId);
            if (!$employee) {
                return new ErrorData(['message' => 'Employee not found!']);
            }

            // Find existing balance or create new one
            $employeeBalance = EmployeeBalance::where('employee_id', $employeeId)
                ->where('company_profile_id', $companyProfileId)
                ->forPeriod($month, $year)
                ->first();

            $isUpdate = !is_null($employeeBalance);

            if (!$employeeBalance) {
                $employeeBalance = new EmployeeBalance();
            }

            $employeeBalance->fill([
                'company_profile_id' => $companyProfileId,
                'employee_id' => $employeeId,
                'month' => $month,
                'year' => $year,
                'opening_advance_balance' => $openingAdvanceBalance,
                'opening_amount_balance' => $openingAmountBalance,
            ]);

            $employeeBalance->save();

            // Log the action
            $actionType = $isUpdate ? 'updated' : 'created';
            $this->actionLogRepository->logAction(
                action: "Employee balance {$actionType}",
                description: "Employee balance {$actionType} for {$employee->first_name} {$employee->last_name} for {$month}/{$year}",
                model_type: EmployeeBalance::class,
                model_id: $employeeBalance->id
            );

            return new SuccessData(['message' => 'Employee balance saved successfully.']);

        } catch (Exception $e) {
            Log::error('Error storing/updating employee balance: ' . $e->getMessage());

            return new ErrorData(['message' => 'Failed to save employee balance. Please try again.']);
        }
    }

    /**
     * Delete employee balance
     *
     * @param int $employeeBalanceId
     * @return ResponseData
     */
    public function deleteEmployeeBalance(int $employeeBalanceId): ResponseData
    {
        try {
            $employeeBalance = EmployeeBalance::with(['employee'])->find($employeeBalanceId);

            if (!$employeeBalance) {
                return new ErrorData(['message' => 'Employee Balance Not found']);
            }

            $employeeName = $employeeBalance->employee->first_name . ' ' . $employeeBalance->employee->last_name;
            $period = $employeeBalance->month . '/' . $employeeBalance->year;

            $employeeBalance->delete();

            // Log the action
            $this->actionLogRepository->logAction(
                action: "Employee balance deleted",
                description: "Employee balance deleted for {$employeeName} for {$period}",
                model_type: EmployeeBalance::class,
                model_id: $employeeBalanceId
            );

            return new SuccessData(['message' => 'Employee balance deleted successfully.']);

        } catch (Exception $e) {
            Log::error('Error deleting employee balance: ' . $e->getMessage());

            return new ErrorData(['message' => 'Failed to delete employee balance. Please try again.']);
        }
    }

    /**
     * Get employee balances summary for a company
     *
     * @param int $companyProfileId
     * @param int|null $month
     * @param int|null $year
     * @return array
     */
    public function getEmployeeBalancesSummary(int $companyProfileId, ?int $month = null, ?int $year = null): array
    {
        $query = EmployeeBalance::with(['employee'])
            ->where('company_profile_id', $companyProfileId);

        if ($month && $year) {
            $query->forPeriod($month, $year);
        } elseif ($year) {
            $query->forYear($year);
        }

        $balances = $query->get();

        return [
            'total_employees' => $balances->count(),
            'total_advance_balance' => $balances->sum('opening_advance_balance'),
            'total_amount_balance' => $balances->sum('opening_amount_balance'),
            'total_balance' => $balances->sum(function($balance) {
                return $balance->opening_advance_balance + $balance->opening_amount_balance;
            }),
            'balances' => $balances
        ];
    }
}
