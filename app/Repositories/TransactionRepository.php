<?php

namespace App\Repositories;

use App\Classes\ResponseData;
use App\Classes\SuccessData;
use App\Classes\ErrorData;
use App\Models\Transaction;
use Exception;
use Illuminate\Support\Facades\Log;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Pagination\LengthAwarePaginator;

class TransactionRepository
{
    /**
     * Create a new class instance.
     */
    public function __construct(){}

    /**
     * Get all transactions ordered by date
     *
     * @return Collection
     */
    public function getAllTransactions(): Collection
    {
        return Transaction::with(['client', 'companyProfile'])
                         ->orderBy('transaction_date', 'desc')
                         ->get();
    }

    /**
     * Get paginated transactions
     *
     * @param int $perPage
     * @return LengthAwarePaginator
     */
    public function getPaginatedTransactions(int $perPage = 15): LengthAwarePaginator
    {
        return Transaction::with(['client', 'companyProfile'])
                         ->orderBy('transaction_date', 'desc')
                         ->paginate($perPage);
    }

    /**
     * Get sales transactions
     *
     * @return Collection
     */
    public function getSalesTransactions(): Collection
    {
        return Transaction::sales()
                         ->with(['client', 'companyProfile'])
                         ->orderBy('transaction_date', 'desc')
                         ->get();
    }

    /**
     * Get payment transactions
     *
     * @return Collection
     */
    public function getPaymentTransactions(): Collection
    {
        return Transaction::payments()
                         ->with(['client', 'companyProfile'])
                         ->orderBy('transaction_date', 'desc')
                         ->get();
    }

    /**
     * Get unpaid sales
     *
     * @return Collection
     */
    public function getUnpaidSales(): Collection
    {
        return Transaction::unpaidSales()
                         ->with(['client', 'companyProfile'])
                         ->orderBy('transaction_date', 'desc')
                         ->get();
    }

    /**
     * Get transactions by client
     *
     * @param int $clientId
     * @return Collection
     */
    public function getTransactionsByClient(int $clientId): Collection
    {
        return Transaction::where('client_id', $clientId)
                         ->with(['client', 'companyProfile'])
                         ->orderBy('transaction_date', 'desc')
                         ->get();
    }

    /**
     * Get transactions by date range
     *
     * @param string $startDate
     * @param string $endDate
     * @return Collection
     */
    public function getTransactionsByDateRange(string $startDate, string $endDate): Collection
    {
        return Transaction::whereBetween('transaction_date', [$startDate, $endDate])
                         ->with(['client', 'companyProfile'])
                         ->orderBy('transaction_date', 'desc')
                         ->get();
    }

    /**
     * Get filtered expense transactions
     *
     * @param array $filters
     * @return Collection
     */
    public function getFilteredExpenses(array $filters = []): Collection
    {
        $query = Transaction::where('transaction_type', 'expense')
                           ->with(['client', 'companyProfile'])
                           ->orderBy('transaction_date', 'desc');

        // Apply date range filter
        if (!empty($filters['start_date']) && !empty($filters['end_date'])) {
            $query->whereBetween('transaction_date', [$filters['start_date'], $filters['end_date']]);
        }

        // Apply expense type filter
        if (!empty($filters['expense_type'])) {
            $query->where('sales_type', $filters['expense_type']);
        }

        // Apply payment status filter
        if (isset($filters['paid']) && $filters['paid'] !== '') {
            $query->where('paid', (bool) $filters['paid']);
        }

        // Apply amount range filters
        if (!empty($filters['amount_from'])) {
            $query->where('total_amount', '>=', $filters['amount_from']);
        }

        if (!empty($filters['amount_to'])) {
            $query->where('total_amount', '<=', $filters['amount_to']);
        }

        return $query->get();
    }

    /**
     * Calculate date range based on type
     *
     * @param string $dateRange
     * @param string|null $customStartDate
     * @param string|null $customEndDate
     * @return array
     */
    public function calculateDateRange(string $dateRange, ?string $customStartDate = null, ?string $customEndDate = null): array
    {
        $startDate = null;
        $endDate = null;

        switch ($dateRange) {
            case 'current_month':
                $startDate = \Carbon\Carbon::now()->startOfMonth();
                $endDate = \Carbon\Carbon::now()->endOfMonth();
                break;
            case 'last_month':
                $startDate = \Carbon\Carbon::now()->subMonth()->startOfMonth();
                $endDate = \Carbon\Carbon::now()->subMonth()->endOfMonth();
                break;
            case 'current_quarter':
                $startDate = \Carbon\Carbon::now()->startOfQuarter();
                $endDate = \Carbon\Carbon::now()->endOfQuarter();
                break;
            case 'last_quarter':
                $startDate = \Carbon\Carbon::now()->subQuarter()->startOfQuarter();
                $endDate = \Carbon\Carbon::now()->subQuarter()->endOfQuarter();
                break;
            case 'current_year':
                $startDate = \Carbon\Carbon::now()->startOfYear();
                $endDate = \Carbon\Carbon::now()->endOfYear();
                break;
            case 'last_year':
                $startDate = \Carbon\Carbon::now()->subYear()->startOfYear();
                $endDate = \Carbon\Carbon::now()->subYear()->endOfYear();
                break;
            case 'last_financial_year':
                // Indian financial year runs from April 1 to March 31
                $currentDate = \Carbon\Carbon::now();
                if ($currentDate->month >= 4) {
                    // If current month is April or later, last FY is previous year April to current year March
                    $startDate = \Carbon\Carbon::create($currentDate->year - 1, 4, 1)->startOfDay();
                    $endDate = \Carbon\Carbon::create($currentDate->year, 3, 31)->endOfDay();
                } else {
                    // If current month is Jan-Mar, last FY is two years ago April to last year March
                    $startDate = \Carbon\Carbon::create($currentDate->year - 2, 4, 1)->startOfDay();
                    $endDate = \Carbon\Carbon::create($currentDate->year - 1, 3, 31)->endOfDay();
                }
                break;
            case 'custom':
                $startDate = $customStartDate ? \Carbon\Carbon::parse($customStartDate) : null;
                $endDate = $customEndDate ? \Carbon\Carbon::parse($customEndDate) : null;
                break;
        }

        return [
            'start_date' => $startDate,
            'end_date' => $endDate
        ];
    }

    /**
     * Store a new transaction in the database
     *
     * @param array $transactionData
     * @return ResponseData
     */
    public function storeTransaction(array $transactionData): ResponseData
    {
        try {
            // Create a new transaction
            $transaction = new Transaction();
            $transaction->company_profile_id = session('company_profile.id');
            $transaction->fill($transactionData);

            // Save the transaction to the database
            $transaction->save();

            // Load relationships for response
            $transaction->load(['client', 'companyProfile']);

            // Return success response with the created transaction
            return new SuccessData($transaction->toArray());
        } catch (Exception $e) {
            // Log the error
            Log::error('Failed to create transaction: ' . $e->getMessage());

            // Return error response
            return new ErrorData(['Failed to create transaction: ' . $e->getMessage()]);
        }
    }

    /**
     * Update an existing transaction in the database
     *
     * @param array $transactionData
     * @param int $id
     * @return ResponseData
     */
    public function updateTransaction(array $transactionData, int $id): ResponseData
    {
        try {
            // Find the transaction to update
            $transaction = Transaction::findOrFail($id);

            // Update the transaction
            $transaction->fill($transactionData);

            // Save the updated transaction to the database
            $transaction->save();

            // Load relationships for response
            $transaction->load(['client', 'companyProfile']);

            // Return success response with the updated transaction
            return new SuccessData($transaction->toArray());
        } catch (Exception $e) {
            // Log the error
            Log::error('Failed to update transaction: ' . $e->getMessage());

            // Return error response
            return new ErrorData(['Failed to update transaction: ' . $e->getMessage()]);
        }
    }

    /**
     * Find a transaction by ID
     *
     * @param int $id
     * @return Transaction|null
     */
    public function findTransaction(int $id): ?Transaction
    {
        return Transaction::with(['client', 'companyProfile', 'payment', 'salesTransactions'])
                         ->find($id);
    }

    /**
     * Find a transaction by UUID
     *
     * @param string $uuid
     * @return Transaction|null
     */
    public function findTransactionByUuid(string $uuid): ?Transaction
    {
        return Transaction::with(['client', 'companyProfile', 'payment', 'salesTransactions'])
                         ->where('uuid', $uuid)
                         ->first();
    }

    /**
     * Delete a transaction
     *
     * @param int $id
     * @return ResponseData
     */
    public function deleteTransaction(int $id): ResponseData
    {
        try {
            // Find the transaction to delete
            $transaction = Transaction::findOrFail($id);

            // Delete the transaction
            $transaction->delete();

            // Return success response
            return new SuccessData(['message' => 'Transaction deleted successfully']);
        } catch (Exception $e) {
            // Log the error
            Log::error('Failed to delete transaction: ' . $e->getMessage());

            // Return error response
            return new ErrorData(['Failed to delete transaction: ' . $e->getMessage()]);
        }
    }

    /**
     * Mark a sales transaction as paid
     *
     * @param int $salesTransactionId
     * @param int $paymentTransactionId
     * @return ResponseData
     */
    public function markSalesAsPaid(int $salesTransactionId, int $paymentTransactionId): ResponseData
    {
        try {
            // Find the sales transaction
            $salesTransaction = Transaction::findOrFail($salesTransactionId);

            // Update payment status
            $salesTransaction->paid = true;
            $salesTransaction->payment_id = $paymentTransactionId;
            $salesTransaction->save();

            // Return success response
            return new SuccessData($salesTransaction->toArray());
        } catch (Exception $e) {
            // Log the error
            Log::error('Failed to mark sales as paid: ' . $e->getMessage());

            // Return error response
            return new ErrorData(['Failed to mark sales as paid: ' . $e->getMessage()]);
        }
    }

    /**
     * Get total sales amount for a date range
     *
     * @param string $startDate
     * @param string $endDate
     * @return float
     */
    public function getTotalSalesAmount(string $startDate, string $endDate): float
    {
        return Transaction::sales()
                         ->whereBetween('transaction_date', [$startDate, $endDate])
                         ->sum('total_amount') ?? 0;
    }

    /**
     * Get total payments amount for a date range
     *
     * @param string $startDate
     * @param string $endDate
     * @return float
     */
    public function getTotalPaymentsAmount(string $startDate, string $endDate): float
    {
        return Transaction::payments()
                         ->whereBetween('transaction_date', [$startDate, $endDate])
                         ->sum('total_amount') ?? 0;
    }

    /**
     * Get outstanding amount (unpaid sales)
     *
     * @return float
     */
    public function getOutstandingAmount(): float
    {
        return Transaction::unpaidSales()
                         ->sum('total_amount') ?? 0;
    }

    /**
     * Get filtered payment transactions
     *
     * @param array $filters
     * @return Collection
     */
    public function getFilteredPayments(array $filters = []): Collection
    {
        $query = Transaction::where('transaction_type', 'payment')
                           ->with(['client', 'companyProfile'])
                           ->orderBy('transaction_date', 'desc');

        // Filter by client
        if (!empty($filters['client_id'])) {
            $query->where('client_id', $filters['client_id']);
        }

        // Filter by payment method (stored in sales_type for transactions)
        if (!empty($filters['payment_method'])) {
            $query->where('payment_method', $filters['payment_method']);
        }

        // Filter by date range
        if (!empty($filters['date_from'])) {
            $query->whereDate('transaction_date', '>=', $filters['date_from']);
        }

        if (!empty($filters['date_to'])) {
            $query->whereDate('transaction_date', '<=', $filters['date_to']);
        }

        return $query->get();
    }

    /**
     * Store a new payment transaction
     *
     * @param array $paymentData
     * @return ResponseData
     */
    public function storePaymentTransaction(array $paymentData): ResponseData
    {
        try {
            // Create a new transaction for payment
            $transaction = new Transaction();
            $transaction->company_profile_id = session('company_profile.id');
            $transaction->transaction_type = 'payment';
            $transaction->fill($paymentData);

            // Save the transaction to the database
            $transaction->save();

            // Load relationships for response
            $transaction->load(['client', 'companyProfile']);

            // Return success response with the created transaction
            return new SuccessData($transaction->toArray());
        } catch (Exception $e) {
            // Log the error
            Log::error('Failed to create payment transaction: ' . $e->getMessage());

            // Return error response
            return new ErrorData(['Failed to create payment transaction: ' . $e->getMessage()]);
        }
    }

    /**
     * Update an existing payment transaction
     *
     * @param int $id
     * @param array $paymentData
     * @return ResponseData
     */
    public function updatePaymentTransaction(int $id, array $paymentData): ResponseData
    {
        try {
            // Find the payment transaction to update
            $transaction = Transaction::where('id', $id)
                                   ->where('transaction_type', 'payment')
                                   ->firstOrFail();

            // Update the transaction
            $transaction->fill($paymentData);
            $transaction->save();

            // Load relationships for response
            $transaction->load(['client', 'companyProfile']);

            // Return success response with the updated transaction
            return new SuccessData($transaction->toArray());
        } catch (Exception $e) {
            // Log the error
            Log::error('Failed to update payment transaction: ' . $e->getMessage());

            // Return error response
            return new ErrorData(['Failed to update payment transaction: ' . $e->getMessage()]);
        }
    }

    /**
     * Find a payment transaction by ID
     *
     * @param int $id
     * @return Transaction|null
     */
    public function findPaymentTransaction(int $id): ?Transaction
    {
        return Transaction::with(['client', 'companyProfile'])
                         ->where('transaction_type', 'payment')
                         ->find($id);
    }

    /**
     * Delete a payment transaction
     *
     * @param int $id
     * @return ResponseData
     */
    public function deletePaymentTransaction(int $id): ResponseData
    {
        try {
            // Find the payment transaction to delete
            $transaction = Transaction::where('id', $id)
                                   ->where('transaction_type', 'payment')
                                   ->firstOrFail();

            // Delete the transaction
            $transaction->delete();

            // Return success response
            return new SuccessData(['message' => 'Payment transaction deleted successfully']);
        } catch (Exception $e) {
            // Log the error
            Log::error('Failed to delete payment transaction: ' . $e->getMessage());

            // Return error response
            return new ErrorData(['Failed to delete payment transaction: ' . $e->getMessage()]);
        }
    }
}
