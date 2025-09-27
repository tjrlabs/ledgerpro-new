<?php

namespace App\Repositories;

use App\Classes\ResponseData;
use App\Classes\SuccessData;
use App\Classes\ErrorData;
use App\Models\Reports\ClientsPayments;
use App\Models\Reports\PaymentsBoard;
use App\Models\Client;
use Exception;
use Illuminate\Support\Facades\Log;
use Illuminate\Database\Eloquent\Collection;

class ClientsPaymentsRepository
{
    /**
     * Create a new class instance.
     */
    public function __construct(){}

    /**
     * Get all client payments with optional filtering
     *
     * @param array $filters
     * @return Collection
     */
    public function getAllClientPayments(array $filters = []): Collection
    {
        $query = ClientsPayments::with(['client', 'paymentsBoard']);

        // Filter by payments board
        if (isset($filters['payments_board_id']) && !empty($filters['payments_board_id'])) {
            $query->forBoard($filters['payments_board_id']);
        }

        // Filter by client
        if (isset($filters['client_id']) && !empty($filters['client_id'])) {
            $query->forClient($filters['client_id']);
        }

        // Filter by payment status
        if (isset($filters['payment_status']) && !empty($filters['payment_status'])) {
            switch ($filters['payment_status']) {
                case 'fully_paid':
                    $query->fullyPaid();
                    break;
                case 'partially_paid':
                    $query->partiallyPaid();
                    break;
                case 'unpaid':
                    $query->unpaid();
                    break;
                case 'outstanding':
                    $query->withOutstanding();
                    break;
            }
        }

        // Filter by amount range
        if (isset($filters['min_amount']) && !empty($filters['min_amount'])) {
            $query->where('total_amount', '>=', $filters['min_amount']);
        }

        if (isset($filters['max_amount']) && !empty($filters['max_amount'])) {
            $query->where('total_amount', '<=', $filters['max_amount']);
        }

        return $query->orderBy('created_at', 'desc')->get();
    }

    /**
     * Store a new client payment in the database
     *
     * @param array $paymentData
     * @return ResponseData
     */
    public function storeClientPayment(array $paymentData): ResponseData
    {
        try {
            // Create a new client payment
            $clientPayment = ClientsPayments::create($paymentData);

            // Return success response with the created payment
            return new SuccessData($clientPayment->load(['client', 'paymentsBoard'])->toArray());
        } catch (Exception $e) {
            // Log the error
            Log::error('Failed to create client payment: ' . $e->getMessage());

            // Return error response
            return new ErrorData(['Failed to create client payment: ' . $e->getMessage()]);
        }
    }

    /**
     * Store multiple client payments for a board
     *
     * @param int $boardId
     * @param array $clientsData Array of objects with 'client_id' and 'previous_balance'
     * @return ResponseData
     */
    public function addClientsToBoard(int $boardId, array $clientsData): ResponseData
    {
        try {
            $createdPayments = [];

            // Get the payments board to extract month and year
            $paymentsBoard = PaymentsBoard::find($boardId);
            if (!$paymentsBoard) {
                return new ErrorData(['Payments board not found']);
            }

            // Extract start and end dates for the month
            $startDate = $paymentsBoard->start_date;
            $endDate = $paymentsBoard->end_date;

            foreach ($clientsData as $clientData) {
                $clientId = $clientData['client_id'];
                $previousBalance = $clientData['previous_balance'];

                // Check if client payment already exists for this board
                $existingPayment = ClientsPayments::where('payments_board_id', $boardId)
                    ->where('client_id', $clientId)
                    ->first();

                if (!$existingPayment) {
                    // Calculate transaction amounts for this client for the specific month
                    $transactionAmounts = $this->calculateClientTransactionAmounts($clientId, $startDate, $endDate);

                    $paymentData = [
                        'payments_board_id' => $boardId,
                        'client_id' => $clientId,
                        'cash_sales' => $transactionAmounts['cash_sales'],
                        'pre_gst_amount' => $transactionAmounts['pre_gst_amount'],
                        'gst_amount' => $transactionAmounts['gst_amount'],
                        'tds' => $transactionAmounts['tds'],
                        'subtotal_amount' => $transactionAmounts['subtotal_amount'],
                        'previous_balance' => $previousBalance, // Use the provided previous_balance from account balance
                        'total_amount' => $transactionAmounts['total_amount'] + $previousBalance, // Add previous balance to total
                        'paid_amount' => $transactionAmounts['paid_amount'],
                        'remarks' => null,
                    ];

                    $clientPayment = ClientsPayments::create($paymentData);
                    $createdPayments[] = $clientPayment->load(['client', 'paymentsBoard']);
                }
            }

            // Update the payments board with aggregated totals after adding all clients
            if (!empty($createdPayments)) {
                $this->updatePaymentsBoardTotals($boardId);
            }

            return new SuccessData([
                'message' => count($createdPayments) . ' client(s) added to payments board successfully',
                'payments' => $createdPayments
            ]);
        } catch (Exception $e) {
            // Return error response
            return new ErrorData(['Failed to add clients to payments board: ' . $e->getMessage()]);
        }
    }

    /**
     * Find a client payment by ID
     *
     * @param int $id
     * @return ClientsPayments|null
     */
    public function findClientPayment(int $id): ?ClientsPayments
    {
        return ClientsPayments::with(['client', 'paymentsBoard'])->find($id);
    }

    /**
     * Update an existing client payment
     *
     * @param int $id
     * @param array $paymentData
     * @return ResponseData
     */
    public function updateClientPayment(int $id, array $paymentData): ResponseData
    {
        try {
            $clientPayment = ClientsPayments::find($id);

            if (!$clientPayment) {
                return new ErrorData(['Client payment not found']);
            }

            // Update the client payment with new data
            $clientPayment->update($paymentData);

            // Return success response with the updated payment
            return new SuccessData($clientPayment->fresh()->load(['client', 'paymentsBoard'])->toArray());
        } catch (Exception $e) {
            // Log the error
            Log::error('Failed to update client payment: ' . $e->getMessage());

            // Return error response
            return new ErrorData(['Failed to update client payment: ' . $e->getMessage()]);
        }
    }

    /**
     * Delete a client payment
     *
     * @param int $id
     * @return ResponseData
     */
    public function deleteClientPayment(int $id): ResponseData
    {
        try {
            $clientPayment = ClientsPayments::find($id);

            if (!$clientPayment) {
                return new ErrorData(['Client payment not found']);
            }

            $clientPayment->delete();

            return new SuccessData(['message' => 'Client payment deleted successfully']);
        } catch (Exception $e) {
            // Log the error
            Log::error('Failed to delete client payment: ' . $e->getMessage());

            // Return error response
            return new ErrorData(['Failed to delete client payment: ' . $e->getMessage()]);
        }
    }

    /**
     * Get client payments for a specific board
     *
     * @param int $boardId
     * @return Collection
     */
    public function getPaymentsByBoard(int $boardId): Collection
    {
        return ClientsPayments::with(['client', 'paymentsBoard'])
            ->forBoard($boardId)
            ->orderBy('created_at', 'desc')
            ->get();
    }

    /**
     * Get client payments for a specific client
     *
     * @param int $clientId
     * @return Collection
     */
    public function getPaymentsByClient(int $clientId): Collection
    {
        return ClientsPayments::with(['client', 'paymentsBoard'])
            ->forClient($clientId)
            ->orderBy('created_at', 'desc')
            ->get();
    }

    /**
     * Calculate totals for a payments board
     *
     * @param int $boardId
     * @return array
     */
    public function calculateBoardTotals(int $boardId): array
    {
        $payments = ClientsPayments::forBoard($boardId)->get();

        return [
            'total_cash_sales' => $payments->sum('cash_sales'),
            'total_pre_gst_amount' => $payments->sum('pre_gst_amount'),
            'total_gst_amount' => $payments->sum('gst_amount'),
            'total_tds' => $payments->sum('tds'),
            'total_subtotal_amount' => $payments->sum('subtotal_amount'),
            'total_previous_balance' => $payments->sum('previous_balance'),
            'total_amount' => $payments->sum('total_amount'),
            'total_paid_amount' => $payments->sum('paid_amount'),
            'total_outstanding' => $payments->sum(function ($payment) {
                return $payment->outstanding_amount;
            }),
            'clients_count' => $payments->count(),
        ];
    }

    /**
     * Get payments with outstanding amounts
     *
     * @param int|null $boardId
     * @return Collection
     */
    public function getOutstandingPayments(?int $boardId = null): Collection
    {
        $query = ClientsPayments::with(['client', 'paymentsBoard'])->withOutstanding();

        if ($boardId) {
            $query->forBoard($boardId);
        }

        return $query->orderBy('total_amount', 'desc')->get();
    }

    /**
     * Remove a client from a payments board
     *
     * @param int $boardId
     * @param int $clientId
     * @return ResponseData
     */
    public function removeClientFromBoard(int $boardId, int $clientId): ResponseData
    {
        try {
            $clientPayment = ClientsPayments::where('payments_board_id', $boardId)
                ->where('client_id', $clientId)
                ->first();

            if (!$clientPayment) {
                return new ErrorData(['Client payment not found in this board']);
            }

            $clientPayment->delete();

            return new SuccessData(['message' => 'Client removed from payments board successfully']);
        } catch (Exception $e) {
            // Log the error
            Log::error('Failed to remove client from payments board: ' . $e->getMessage());

            // Return error response
            return new ErrorData(['Failed to remove client from payments board: ' . $e->getMessage()]);
        }
    }

    /**
     * Get available clients for a payments board (excluding clients already added)
     *
     * @param int $boardId
     * @param int $companyProfileId
     * @return Collection
     */
    public function getAvailableClientsForBoard(int $boardId, int $companyProfileId): Collection
    {
        // Get client IDs already added to this payments board
        $clientsAlreadyInBoard = ClientsPayments::where('payments_board_id', $boardId)
            ->pluck('client_id')
            ->toArray();

        // Get all active clients for the company, excluding those already in the board
        return Client::where('company_profile_id', $companyProfileId)
            ->where('is_active', 1)
            ->whereNotIn('id', $clientsAlreadyInBoard)
            ->orderBy('client_name', 'asc')
            ->get();
    }

    /**
     * Calculate transaction amounts for a client within a specific date range
     *
     * @param int $clientId
     * @param string $startDate
     * @param string $endDate
     * @return array
     */
    private function calculateClientTransactionAmounts(int $clientId, $startDate, $endDate): array
    {
        // Import Transaction model
        $transactionModel = new \App\Models\Transaction();

        // Get all sales transactions for this client in the date range
        $salesTransactions = $transactionModel::where('client_id', $clientId)
            ->where('transaction_type', 'sale')
            ->whereBetween('transaction_date', [$startDate, $endDate])
            ->get();

        // Get all payment transactions for this client in the date range
        $paymentTransactions = $transactionModel::where('client_id', $clientId)
            ->where('transaction_type', 'payment')
            ->whereBetween('transaction_date', [$startDate, $endDate])
            ->get();

        // Calculate cash sales (transactions with sales_type = 'cash')
        $cashSales = $salesTransactions->where('sales_type', 'cash')->sum('total_amount');

        // Calculate invoice sales amounts (transactions with sales_type = 'invoice')
        $invoiceSales = $salesTransactions->where('sales_type', 'invoice');

        // For invoice sales, calculate pre-GST amount, GST amount, and TDS
        $preGstAmount = $invoiceSales->sum('base_amount');
        $gstAmount = $invoiceSales->sum('tax_amount');
        $tdsAmount = $invoiceSales->sum('tds');

        // Calculate subtotal (base amount + tax amount - TDS)
        $subtotalAmount = $preGstAmount + $gstAmount - $tdsAmount;

        // Calculate total amount from all sales
        $totalSalesAmount = $salesTransactions->sum('total_amount');

        // Calculate total payments received
        $totalPaidAmount = $paymentTransactions->sum('total_amount');

        // Calculate previous balance (this would need to be calculated from transactions before the start date)
        // For now, we'll calculate unpaid sales from before this period
        $previousBalance = $transactionModel::where('client_id', $clientId)
            ->where('transaction_type', 'sale')
            ->where('transaction_date', '<', $startDate)
            ->where('paid', false)
            ->sum('total_amount');

        // Subtract any payments made before this period for those previous sales
        $previousPayments = $transactionModel::where('client_id', $clientId)
            ->where('transaction_type', 'payment')
            ->where('transaction_date', '<', $startDate)
            ->sum('total_amount');

        $previousBalance = max(0, $previousBalance - $previousPayments);

        // Total amount = current period sales + previous balance
        $totalAmount = $totalSalesAmount + $previousBalance;

        return [
            'cash_sales' => $cashSales,
            'pre_gst_amount' => $preGstAmount,
            'gst_amount' => $gstAmount,
            'tds' => $tdsAmount,
            'subtotal_amount' => $subtotalAmount,
            'previous_balance' => $previousBalance,
            'total_amount' => $totalAmount,
            'paid_amount' => $totalPaidAmount,
        ];
    }

    /**
     * Update payments board totals based on aggregated client payments data
     *
     * @param int $boardId
     * @return void
     */
    private function updatePaymentsBoardTotals(int $boardId): void
    {
        try {
            // Get the payments board
            $paymentsBoard = PaymentsBoard::find($boardId);
            if (!$paymentsBoard) {
                Log::warning("Payments board not found when trying to update totals: {$boardId}");
                return;
            }

            // Calculate totals from all client payments for this board
            $totals = $this->calculateBoardTotals($boardId);

            // Update the payments board with calculated totals
            $paymentsBoard->update([
                'total_cash_sales' => $totals['total_cash_sales'],
                'total_pre_gst_amount' => $totals['total_pre_gst_amount'],
                'total_gst_amount' => $totals['total_gst_amount'],
                'total_tds' => $totals['total_tds'],
                'total_subtotal_amount' => $totals['total_subtotal_amount'],
                'total_previous_balance' => $totals['total_previous_balance'],
                'total_amount' => $totals['total_amount'],
                'total_paid_amount' => $totals['total_paid_amount'],
                'total_outstanding' => $totals['total_outstanding'],
                'clients_count' => $totals['clients_count'],
                'updated_at' => now(),
            ]);

            Log::info("Payments board totals updated successfully", [
                'board_id' => $boardId,
                'clients_count' => $totals['clients_count'],
                'total_amount' => $totals['total_amount']
            ]);

        } catch (Exception $e) {
            Log::error("Failed to update payments board totals", [
                'board_id' => $boardId,
                'error' => $e->getMessage()
            ]);
        }
    }
}
