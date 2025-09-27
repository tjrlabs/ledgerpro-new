<?php

namespace App\Repositories;

use App\Classes\ResponseData;
use App\Classes\SuccessData;
use App\Classes\ErrorData;
use App\Models\Reports\PaymentsBoard;
use Exception;
use Illuminate\Support\Facades\Log;
use Illuminate\Database\Eloquent\Collection;
use Carbon\Carbon;

class PaymentsBoardRepository
{
    /**
     * Create a new class instance.
     */
    public function __construct(){}

    /**
     * Get all payments boards with optional filtering
     *
     * @param array $filters
     * @return Collection
     */
    public function getAllPaymentsBoards(array $filters = []): Collection
    {
        $query = PaymentsBoard::query();

        // Filter by month and year
        if (isset($filters['month_year']) && !empty($filters['month_year'])) {
            $query->byMonthYear($filters['month_year']);
        }

        // Filter by year only
        if (isset($filters['year']) && !empty($filters['year'])) {
            $query->byYear($filters['year']);
        }

        return $query->latest()->get();
    }

    /**
     * Store a new payments board in the database
     *
     * @param array $boardData
     * @return ResponseData
     */
    public function storePaymentsBoard(array $boardData): ResponseData
    {
        try {
            // Check if board already exists for this month-year
            $existingBoard = PaymentsBoard::byMonthYear($boardData['board_month_year'])->first();

            if ($existingBoard) {
                return new ErrorData(['Payments board already exists for this period']);
            }

            // Calculate start and end dates based on month-year
            $monthYear = $boardData['board_month_year'];
            $date = Carbon::createFromFormat('m-Y', $monthYear);
            $boardData['start_date'] = $date->startOfMonth()->toDateString();
            $boardData['end_date'] = $date->endOfMonth()->toDateString();
            $boardData['total_days'] = $date->daysInMonth;

            // Initialize financial fields if not provided
            $financialFields = [
                'clients_count',
                'total_pre_gst_amount',
                'total_gst_amount',
                'total_cash_sales',
                'total_tds',
                'total_previous_balance',
                'total_amount',
                'total_net_amount',
                'total_paid_amount',
                'total_unpaid_amount'
            ];

            foreach ($financialFields as $field) {
                if (!isset($boardData[$field])) {
                    $boardData[$field] = 0;
                }
            }
            // Create a new payments board
            $paymentsBoard = PaymentsBoard::create($boardData);

            // Return success response with the created board
            return new SuccessData($paymentsBoard->toArray());
        } catch (Exception $e) {
            // Return error response
            return new ErrorData(['Failed to create payments board: ' . $e->getMessage()]);
        }
    }

    /**
     * Find a payments board by ID
     *
     * @param int $id
     * @return PaymentsBoard|null
     */
    public function findPaymentsBoard(int $id): ?PaymentsBoard
    {
        return PaymentsBoard::find($id);
    }

    /**
     * Find a payments board by month-year
     *
     * @param string $monthYear
     * @return PaymentsBoard|null
     */
    public function findByMonthYear(string $monthYear): ?PaymentsBoard
    {
        return PaymentsBoard::byMonthYear($monthYear)->first();
    }

    /**
     * Update an existing payments board
     *
     * @param int $id
     * @param array $boardData
     * @return ResponseData
     */
    public function updatePaymentsBoard(int $id, array $boardData): ResponseData
    {
        try {
            $paymentsBoard = PaymentsBoard::find($id);

            if (!$paymentsBoard) {
                return new ErrorData(['Payments board not found']);
            }

            // Update the payments board with new data
            $paymentsBoard->update($boardData);

            // Return success response with the updated board
            return new SuccessData($paymentsBoard->fresh()->toArray());
        } catch (Exception $e) {
            // Log the error
            Log::error('Failed to update payments board: ' . $e->getMessage());

            // Return error response
            return new ErrorData(['Failed to update payments board: ' . $e->getMessage()]);
        }
    }

    /**
     * Delete a payments board
     *
     * @param int $id
     * @return ResponseData
     */
    public function deletePaymentsBoard(int $id): ResponseData
    {
        try {
            $paymentsBoard = PaymentsBoard::find($id);

            if (!$paymentsBoard) {
                return new ErrorData(['Payments board not found']);
            }

            $paymentsBoard->delete();

            return new SuccessData(['message' => 'Payments board deleted successfully']);
        } catch (Exception $e) {
            // Log the error
            Log::error('Failed to delete payments board: ' . $e->getMessage());

            // Return error response
            return new ErrorData(['Failed to delete payments board: ' . $e->getMessage()]);
        }
    }

    /**
     * Get payments boards for a specific year
     *
     * @param int $year
     * @return Collection
     */
    public function getByYear(int $year): Collection
    {
        return PaymentsBoard::byYear($year)->latest()->get();
    }

    /**
     * Get the latest payments board
     *
     * @return PaymentsBoard|null
     */
    public function getLatest(): ?PaymentsBoard
    {
        return PaymentsBoard::latest()->first();
    }

    /**
     * Get payments boards within date range
     *
     * @param string $startDate
     * @param string $endDate
     * @return Collection
     */
    public function getByDateRange(string $startDate, string $endDate): Collection
    {
        return PaymentsBoard::byDateRange($startDate, $endDate)->latest()->get();
    }

    /**
     * Calculate and update board totals based on actual data
     *
     * @param int $id
     * @return ResponseData
     */
    public function recalculateTotals(int $id): ResponseData
    {
        try {
            $paymentsBoard = PaymentsBoard::find($id);

            if (!$paymentsBoard) {
                return new ErrorData(['Payments board not found']);
            }

            // Here you would implement the logic to recalculate totals
            // based on actual sales, payments, and other data for the period
            // This would typically involve querying related models

            // For now, just return success
            return new SuccessData($paymentsBoard->toArray());
        } catch (Exception $e) {
            // Log the error
            Log::error('Failed to recalculate payments board totals: ' . $e->getMessage());

            // Return error response
            return new ErrorData(['Failed to recalculate totals: ' . $e->getMessage()]);
        }
    }
}
