<?php

namespace App\Repositories;

use App\Classes\ResponseData;
use App\Classes\SuccessData;
use App\Classes\ErrorData;
use App\Models\Expense;
use Exception;
use Illuminate\Support\Facades\Log;
use Illuminate\Database\Eloquent\Collection;

class ExpenseRepository
{
    /**
     * Create a new class instance.
     */
    public function __construct(){}

    /**
     * Get all expenses with optional filtering
     *
     * @param array $filters
     * @return Collection
     */
    public function getAllExpenses(array $filters = []): Collection
    {
        $query = Expense::with('companyProfile');

        // Filter by company profile (session-based)
        if (session('company_profile.id')) {
            $query->forCompany(session('company_profile.id'));
        }

        // Filter by expense type
        if (isset($filters['expense_type']) && !empty($filters['expense_type'])) {
            $query->byType($filters['expense_type']);
        }

        // Filter by payment status
        if (isset($filters['paid']) && $filters['paid'] !== '') {
            if ($filters['paid'] == '1') {
                $query->paid();
            } else {
                $query->unpaid();
            }
        }

        // Filter by date range
        if (isset($filters['date_from']) && !empty($filters['date_from'])) {
            $query->whereDate('expense_date', '>=', $filters['date_from']);
        }

        if (isset($filters['date_to']) && !empty($filters['date_to'])) {
            $query->whereDate('expense_date', '<=', $filters['date_to']);
        }

        // Filter by amount range
        if (isset($filters['amount_from']) && !empty($filters['amount_from'])) {
            $query->where('total_amount', '>=', $filters['amount_from']);
        }

        if (isset($filters['amount_to']) && !empty($filters['amount_to'])) {
            $query->where('total_amount', '<=', $filters['amount_to']);
        }

        return $query->orderBy('expense_date', 'desc')->get();
    }

    /**
     * Store a new expense in the database
     *
     * @param array $expenseData
     * @return ResponseData
     */
    public function storeExpense(array $expenseData): ResponseData
    {
        try {
            // Calculate tax amount if not provided
            if (!isset($expenseData['tax_amount']) && isset($expenseData['base_amount'], $expenseData['tax_rate'])) {
                $expenseData['tax_amount'] = ($expenseData['base_amount'] * $expenseData['tax_rate']) / 100;
            }

            // Calculate total amount if not provided
            if (!isset($expenseData['total_amount'])) {
                $expenseData['total_amount'] = $expenseData['base_amount'] + ($expenseData['tax_amount'] ?? 0);
            }

            // Set company profile from session
            $expenseData['company_profile_id'] = session('company_profile.id');

            // Create a new expense
            $expense = Expense::create($expenseData);

            // Return success response with the created expense
            return new SuccessData($expense->toArray());
        } catch (Exception $e) {
            // Log the error
            Log::error('Failed to create expense: ' . $e->getMessage());

            // Return error response
            return new ErrorData(['Failed to create expense: ' . $e->getMessage()]);
        }
    }

    /**
     * Find an expense by ID
     *
     * @param int $id
     * @return Expense|null
     */
    public function findExpense(int $id): ?Expense
    {
        return Expense::with('companyProfile')->find($id);
    }

    /**
     * Update an existing expense
     *
     * @param int $id
     * @param array $expenseData
     * @return ResponseData
     */
    public function updateExpense(int $id, array $expenseData): ResponseData
    {
        try {
            $expense = Expense::find($id);

            if (!$expense) {
                return new ErrorData(['Expense not found']);
            }

            // Calculate tax amount if not provided
            if (!isset($expenseData['tax_amount']) && isset($expenseData['base_amount'], $expenseData['tax_rate'])) {
                $expenseData['tax_amount'] = ($expenseData['base_amount'] * $expenseData['tax_rate']) / 100;
            }

            // Calculate total amount if not provided
            if (!isset($expenseData['total_amount'])) {
                $expenseData['total_amount'] = $expenseData['base_amount'] + ($expenseData['tax_amount'] ?? 0);
            }

            // Update the expense with new data
            $expense->update($expenseData);

            // Return success response with the updated expense
            return new SuccessData($expense->fresh()->toArray());
        } catch (Exception $e) {
            // Log the error
            Log::error('Failed to update expense: ' . $e->getMessage());

            // Return error response
            return new ErrorData(['Failed to update expense: ' . $e->getMessage()]);
        }
    }

    /**
     * Delete an expense
     *
     * @param int $id
     * @return ResponseData
     */
    public function deleteExpense(int $id): ResponseData
    {
        try {
            $expense = Expense::find($id);

            if (!$expense) {
                return new ErrorData(['Expense not found']);
            }

            $expense->delete();

            return new SuccessData(['message' => 'Expense deleted successfully']);
        } catch (Exception $e) {
            // Log the error
            Log::error('Failed to delete expense: ' . $e->getMessage());

            // Return error response
            return new ErrorData(['Failed to delete expense: ' . $e->getMessage()]);
        }
    }

    /**
     * Mark an expense as paid
     *
     * @param int $id
     * @return ResponseData
     */
    public function markAsPaid(int $id): ResponseData
    {
        try {
            $expense = Expense::find($id);

            if (!$expense) {
                return new ErrorData(['Expense not found']);
            }

            $expense->markAsPaid();

            return new SuccessData(['message' => 'Expense marked as paid successfully']);
        } catch (Exception $e) {
            // Log the error
            Log::error('Failed to mark expense as paid: ' . $e->getMessage());

            // Return error response
            return new ErrorData(['Failed to mark expense as paid: ' . $e->getMessage()]);
        }
    }

    /**
     * Mark an expense as unpaid
     *
     * @param int $id
     * @return ResponseData
     */
    public function markAsUnpaid(int $id): ResponseData
    {
        try {
            $expense = Expense::find($id);

            if (!$expense) {
                return new ErrorData(['Expense not found']);
            }

            $expense->markAsUnpaid();

            return new SuccessData(['message' => 'Expense marked as unpaid successfully']);
        } catch (Exception $e) {
            // Log the error
            Log::error('Failed to mark expense as unpaid: ' . $e->getMessage());

            // Return error response
            return new ErrorData(['Failed to mark expense as unpaid: ' . $e->getMessage()]);
        }
    }

    /**
     * Get expenses statistics
     *
     * @param array $filters
     * @return array
     */
    public function getExpenseStatistics(array $filters = []): array
    {
        $query = Expense::query();

        // Filter by company profile (session-based)
        if (session('company_profile.id')) {
            $query->forCompany(session('company_profile.id'));
        }

        // Apply filters
        if (isset($filters['expense_type']) && !empty($filters['expense_type'])) {
            $query->byType($filters['expense_type']);
        }

        if (isset($filters['date_from']) && !empty($filters['date_from'])) {
            $query->whereDate('expense_date', '>=', $filters['date_from']);
        }

        if (isset($filters['date_to']) && !empty($filters['date_to'])) {
            $query->whereDate('expense_date', '<=', $filters['date_to']);
        }

        $paidQuery = clone $query;
        $unpaidQuery = clone $query;

        return [
            'total_expenses' => $query->count(),
            'total_amount' => $query->sum('total_amount'),
            'total_base_amount' => $query->sum('base_amount'),
            'total_tax_amount' => $query->sum('tax_amount'),
            'paid_expenses' => $paidQuery->paid()->count(),
            'unpaid_expenses' => $unpaidQuery->unpaid()->count(),
            'paid_amount' => $paidQuery->paid()->sum('total_amount'),
            'unpaid_amount' => $unpaidQuery->unpaid()->sum('total_amount'),
            'cash_expenses' => $query->byType('cash')->count(),
            'invoice_expenses' => $query->byType('invoice')->count(),
        ];
    }

    /**
     * Get form options for creating/editing expenses
     *
     * @return array
     */
    public function getFormOptions(): array
    {
        return [
            'expense_types' => Expense::EXPENSE_TYPES,
        ];
    }

    /**
     * Get recent expenses
     *
     * @param int $limit
     * @return Collection
     */
    public function getRecentExpenses(int $limit = 10): Collection
    {
        $query = Expense::with('companyProfile');

        // Filter by company profile (session-based)
        if (session('company_profile.id')) {
            $query->forCompany(session('company_profile.id'));
        }

        return $query->orderBy('expense_date', 'desc')
                    ->orderBy('created_at', 'desc')
                    ->limit($limit)
                    ->get();
    }

    /**
     * Get expenses by month for charts/reports
     *
     * @param int $year
     * @return array
     */
    public function getExpensesByMonth(int $year): array
    {
        $query = Expense::query();

        // Filter by company profile (session-based)
        if (session('company_profile.id')) {
            $query->forCompany(session('company_profile.id'));
        }

        $expenses = $query->whereYear('expense_date', $year)
                         ->selectRaw('MONTH(expense_date) as month, SUM(total_amount) as total')
                         ->groupBy('month')
                         ->orderBy('month')
                         ->get()
                         ->pluck('total', 'month')
                         ->toArray();

        // Fill missing months with 0
        $monthlyExpenses = [];
        for ($i = 1; $i <= 12; $i++) {
            $monthlyExpenses[$i] = $expenses[$i] ?? 0;
        }

        return $monthlyExpenses;
    }
}
