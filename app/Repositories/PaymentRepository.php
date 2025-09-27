<?php

namespace App\Repositories;

use App\Classes\ResponseData;
use App\Classes\SuccessData;
use App\Classes\ErrorData;
use App\Models\Payment;
use App\Models\Client;
use Exception;
use Illuminate\Support\Facades\Log;
use Illuminate\Database\Eloquent\Collection;

class PaymentRepository
{
    /**
     * Create a new class instance.
     */
    public function __construct(){}

    /**
     * Get all payments with optional filtering
     *
     * @param array $filters
     * @return Collection
     */
    public function getAllPayments(array $filters = []): Collection
    {
        $query = Payment::with('client');

        // Filter by client
        if (isset($filters['client_id']) && !empty($filters['client_id'])) {
            $query->forClient($filters['client_id']);
        }

        // Filter by Company Profile Id
        $companyProfileId = auth()->user()->company_profile_id ?? 1;
        $query->where('company_profile_id', $companyProfileId);

        // Filter by payment method
        if (isset($filters['payment_method']) && !empty($filters['payment_method'])) {
            $query->byPaymentMethod($filters['payment_method']);
        }

        // Filter by date range
        if (isset($filters['date_from']) && !empty($filters['date_from'])) {
            $query->whereDate('payment_date', '>=', $filters['date_from']);
        }

        if (isset($filters['date_to']) && !empty($filters['date_to'])) {
            $query->whereDate('payment_date', '<=', $filters['date_to']);
        }

//        // Filter by amount range
//        if (isset($filters['amount_from']) && !empty($filters['amount_from'])) {
//            $query->where('amount_paid', '>=', $filters['amount_from']);
//        }
//
//        if (isset($filters['amount_to']) && !empty($filters['amount_to'])) {
//            $query->where('amount_paid', '<=', $filters['amount_to']);
//        }

        return $query->orderBy('payment_date', 'desc')->get();
    }

    /**
     * Store a new payment in the database
     *
     * @param array $paymentData
     * @return ResponseData
     */
    public function storePayment(array $paymentData): ResponseData
    {
        try {
            // Create a new payment
            $companyProfileId = auth()->user()->company_profile_id ?? 1;
            $paymentData['company_profile_id'] = $companyProfileId;
            $payment = Payment::create($paymentData);

            // Return success response with the created payment
            return new SuccessData($payment->load('client')->toArray());
        } catch (Exception $e) {
            // Log the error
            Log::error('Failed to create payment: ' . $e->getMessage());

            // Return error response
            return new ErrorData(['Failed to create payment: ' . $e->getMessage()]);
        }
    }

    /**
     * Find a payment by ID
     *
     * @param int $id
     * @return Payment|null
     */
    public function findPayment(int $id): ?Payment
    {
        return Payment::with('client')->find($id);
    }

    /**
     * Update an existing payment
     *
     * @param int $id
     * @param array $paymentData
     * @return ResponseData
     */
    public function updatePayment(int $id, array $paymentData): ResponseData
    {
        try {
            $payment = Payment::find($id);

            if (!$payment) {
                return new ErrorData(['Payment not found']);
            }

            // Update the payment with new data
            $payment->update($paymentData);

            // Return success response with the updated payment
            return new SuccessData($payment->fresh()->load('client')->toArray());
        } catch (Exception $e) {
            // Log the error
            Log::error('Failed to update payment: ' . $e->getMessage());

            // Return error response
            return new ErrorData(['Failed to update payment: ' . $e->getMessage()]);
        }
    }

    /**
     * Delete a payment
     *
     * @param int $id
     * @return ResponseData
     */
    public function deletePayment(int $id): ResponseData
    {
        try {
            $payment = Payment::find($id);

            if (!$payment) {
                return new ErrorData(['Payment not found']);
            }

            $payment->delete();

            return new SuccessData(['message' => 'Payment deleted successfully']);
        } catch (Exception $e) {
            // Log the error
            Log::error('Failed to delete payment: ' . $e->getMessage());

            // Return error response
            return new ErrorData(['Failed to delete payment: ' . $e->getMessage()]);
        }
    }

    /**
     * Get payment statistics
     *
     * @param array $filters
     * @return array
     */
    public function getPaymentStatistics(array $filters = []): array
    {
        $query = Payment::query();

        // Apply same filters as getAllPayments
        if (isset($filters['client_id']) && !empty($filters['client_id'])) {
            $query->forClient($filters['client_id']);
        }

        if (isset($filters['payment_method']) && !empty($filters['payment_method'])) {
            $query->byPaymentMethod($filters['payment_method']);
        }

        if (isset($filters['date_from']) && !empty($filters['date_from'])) {
            $query->whereDate('payment_date', '>=', $filters['date_from']);
        }

        if (isset($filters['date_to']) && !empty($filters['date_to'])) {
            $query->whereDate('payment_date', '<=', $filters['date_to']);
        }

        if (isset($filters['amount_from']) && !empty($filters['amount_from'])) {
            $query->where('amount_paid', '>=', $filters['amount_from']);
        }

        if (isset($filters['amount_to']) && !empty($filters['amount_to'])) {
            $query->where('amount_paid', '<=', $filters['amount_to']);
        }

        return [
            'total_payments' => $query->count(),
            'total_amount' => $query->sum('amount_paid'),
            'average_amount' => $query->avg('amount_paid') ?: 0,
        ];
    }

    /**
     * Get payments for a specific client
     *
     * @param int $clientId
     * @param array $filters
     * @return Collection
     */
    public function getPaymentsByClient(int $clientId, array $filters = []): Collection
    {
        $filters['client_id'] = $clientId;
        return $this->getAllPayments($filters);
    }

    /**
     * Get form options for payment forms
     *
     * @return array
     */
    public function getFormOptions(): array
    {
        return [
            'clients' => Client::orderBy('business_name')->pluck('business_name', 'id')->toArray(),
            'payment_methods' => [
                'cash' => 'Cash',
                'bank_transfer' => 'Bank Transfer',
                'cheque' => 'Cheque',
                'credit_card' => 'Credit Card',
                'debit_card' => 'Debit Card',
                'upi' => 'UPI',
                'net_banking' => 'Net Banking',
                'other' => 'Other',
            ],
        ];
    }

    /**
     * Get payments summary for dashboard
     *
     * @return array
     */
    public function getPaymentsSummary(): array
    {
        $currentMonth = Payment::currentMonth();
        $currentYear = Payment::currentYear();

        return [
            'current_month' => [
                'count' => $currentMonth->count(),
                'amount' => $currentMonth->sum('amount_paid'),
            ],
            'current_year' => [
                'count' => $currentYear->count(),
                'amount' => $currentYear->sum('amount_paid'),
            ],
            'total' => [
                'count' => Payment::count(),
                'amount' => Payment::sum('amount_paid'),
            ],
        ];
    }

    /**
     * Get recent payments
     *
     * @param int $limit
     * @return Collection
     */
    public function getRecentPayments(int $limit = 10): Collection
    {
        return Payment::with('client')
            ->orderBy('payment_date', 'desc')
            ->orderBy('created_at', 'desc')
            ->limit($limit)
            ->get();
    }
}
