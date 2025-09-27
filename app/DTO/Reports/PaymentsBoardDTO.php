<?php

namespace App\DTO\Reports;

use App\Classes\ErrorData;
use Illuminate\Support\Facades\Validator;
use App\DTO\BaseDTOInterface;
use Carbon\Carbon;

class PaymentsBoardDTO implements BaseDTOInterface
{
    /**
     * Create a new class instance.
     */
    public function __construct(
        public string $month,
        public string $year,
        public ?string $boardMonthYear = null,
        public ?string $startDate = null,
        public ?string $endDate = null,
        public ?int $totalDays = null,
        public ?int $clientsCount = null,
        public ?float $totalPreGstAmount = null,
        public ?float $totalGstAmount = null,
        public ?float $totalCashSales = null,
        public ?float $totalTds = null,
        public ?float $totalPreviousBalance = null,
        public ?float $totalAmount = null,
        public ?float $totalNetAmount = null,
        public ?float $totalPaidAmount = null,
        public ?float $totalUnpaidAmount = null
    ) {
        // Auto-generate board_month_year if not provided
        if (!$this->boardMonthYear) {
            $this->boardMonthYear = sprintf('%02d-%s', (int)$this->month, $this->year);
        }
    }

    /**
     * Create a DTO from an array of data.
     *
     * @param array $data
     * @return ErrorData|PaymentsBoardDTO
     */
    public static function from(array $data): ErrorData|PaymentsBoardDTO
    {
        // Validate the data
        $validator = Validator::make($data, [
            'month' => 'required|numeric|min:1|max:12',
            'year' => 'required|numeric|min:2020|max:2030',
            'start_date' => 'nullable|date',
            'end_date' => 'nullable|date|after_or_equal:start_date',
            'total_days' => 'nullable|integer|min:1|max:31',
            'clients_count' => 'nullable|integer|min:0',
            'total_pre_gst_amount' => 'nullable|numeric|min:0',
            'total_gst_amount' => 'nullable|numeric|min:0',
            'total_cash_sales' => 'nullable|numeric|min:0',
            'total_tds' => 'nullable|numeric|min:0',
            'total_previous_balance' => 'nullable|numeric',
            'total_amount' => 'nullable|numeric|min:0',
            'total_net_amount' => 'nullable|numeric|min:0',
            'total_paid_amount' => 'nullable|numeric|min:0',
            'total_unpaid_amount' => 'nullable|numeric|min:0',
        ], [
            'month.required' => 'Please select a month.',
            'month.min' => 'Month must be between 1 and 12.',
            'month.max' => 'Month must be between 1 and 12.',
            'year.required' => 'Please select a year.',
            'year.min' => 'Year must be between 2020 and 2030.',
            'year.max' => 'Year must be between 2020 and 2030.',
            'start_date.date' => 'Please enter a valid start date.',
            'end_date.date' => 'Please enter a valid end date.',
            'end_date.after_or_equal' => 'End date must be after or equal to start date.',
            'total_days.min' => 'Total days must be at least 1.',
            'total_days.max' => 'Total days cannot exceed 31.',
            'clients_count.min' => 'Clients count cannot be negative.',
            'total_pre_gst_amount.min' => 'Pre-GST amount cannot be negative.',
            'total_gst_amount.min' => 'GST amount cannot be negative.',
            'total_cash_sales.min' => 'Cash sales amount cannot be negative.',
            'total_tds.min' => 'TDS amount cannot be negative.',
            'total_amount.min' => 'Total amount cannot be negative.',
            'total_net_amount.min' => 'Net amount cannot be negative.',
            'total_paid_amount.min' => 'Paid amount cannot be negative.',
            'total_unpaid_amount.min' => 'Unpaid amount cannot be negative.',
        ]);

        if ($validator->fails()) {
            return new ErrorData($validator->errors()->all());
        }

        // Generate board_month_year from month and year
        $boardMonthYear = sprintf('%02d-%s', (int)$data['month'], $data['year']);

        // Calculate dates and total days if not provided
        $startDate = $data['start_date'] ?? null;
        $endDate = $data['end_date'] ?? null;
        $totalDays = $data['total_days'] ?? null;

        if (!$startDate || !$endDate || !$totalDays) {
            try {
                $date = Carbon::createFromFormat('m-Y', $boardMonthYear);
                $startDate = $startDate ?? $date->startOfMonth()->toDateString();
                $endDate = $endDate ?? $date->endOfMonth()->toDateString();
                $totalDays = $totalDays ?? $date->daysInMonth;
            } catch (\Exception $e) {
                return new ErrorData(['Invalid month-year combination provided.']);
            }
        }

        return new self(
            $data['month'],
            $data['year'],
            $boardMonthYear,
            $startDate,
            $endDate,
            $totalDays,
            isset($data['clients_count']) ? intval($data['clients_count']) : null,
            isset($data['total_pre_gst_amount']) ? floatval($data['total_pre_gst_amount']) : null,
            isset($data['total_gst_amount']) ? floatval($data['total_gst_amount']) : null,
            isset($data['total_cash_sales']) ? floatval($data['total_cash_sales']) : null,
            isset($data['total_tds']) ? floatval($data['total_tds']) : null,
            isset($data['total_previous_balance']) ? floatval($data['total_previous_balance']) : null,
            isset($data['total_amount']) ? floatval($data['total_amount']) : null,
            isset($data['total_net_amount']) ? floatval($data['total_net_amount']) : null,
            isset($data['total_paid_amount']) ? floatval($data['total_paid_amount']) : null,
            isset($data['total_unpaid_amount']) ? floatval($data['total_unpaid_amount']) : null
        );
    }

    /**
     * Get validation rules for this DTO.
     *
     * @return array
     */
    public function rules(): array
    {
        return [
            'month' => 'required|numeric|min:1|max:12',
            'year' => 'required|numeric|min:2020|max:2030',
            'start_date' => 'nullable|date',
            'end_date' => 'nullable|date|after_or_equal:start_date',
            'total_days' => 'nullable|integer|min:1|max:31',
            'clients_count' => 'nullable|integer|min:0',
            'total_pre_gst_amount' => 'nullable|numeric|min:0',
            'total_gst_amount' => 'nullable|numeric|min:0',
            'total_cash_sales' => 'nullable|numeric|min:0',
            'total_tds' => 'nullable|numeric|min:0',
            'total_previous_balance' => 'nullable|numeric',
            'total_net_amount' => 'nullable|numeric|min:0',
            'total_paid_amount' => 'nullable|numeric|min:0',
            'total_unpaid_amount' => 'nullable|numeric|min:0',
        ];
    }

    /**
     * Validate the current DTO instance.
     *
     * @return ErrorData|PaymentsBoardDTO
     */
    public function validate(): ErrorData|PaymentsBoardDTO
    {
        // Create validation data array including month and year
        $validationData = array_merge($this->toArray(), [
            'month' => $this->month,
            'year' => $this->year
        ]);

        $validator = Validator::make($validationData, $this->rules(), [
            'month.required' => 'Please select a month.',
            'month.min' => 'Month must be between 1 and 12.',
            'month.max' => 'Month must be between 1 and 12.',
            'year.required' => 'Please select a year.',
            'year.min' => 'Year must be between 2020 and 2030.',
            'year.max' => 'Year must be between 2020 and 2030.',
            'start_date.date' => 'Please enter a valid start date.',
            'end_date.date' => 'Please enter a valid end date.',
            'end_date.after_or_equal' => 'End date must be after or equal to start date.',
            'total_days.min' => 'Total days must be at least 1.',
            'total_days.max' => 'Total days cannot exceed 31.',
            'clients_count.min' => 'Clients count cannot be negative.',
            'total_pre_gst_amount.min' => 'Pre-GST amount cannot be negative.',
            'total_gst_amount.min' => 'GST amount cannot be negative.',
            'total_cash_sales.min' => 'Cash sales amount cannot be negative.',
            'total_tds.min' => 'TDS amount cannot be negative.',
            'total_amount.min' => 'Total amount cannot be negative.',
            'total_net_amount.min' => 'Net amount cannot be negative.',
            'total_paid_amount.min' => 'Paid amount cannot be negative.',
            'total_unpaid_amount.min' => 'Unpaid amount cannot be negative.',
        ]);

        if ($validator->fails()) {
            return new ErrorData($validator->errors()->all());
        }

        return $this;
    }

    /**
     * Convert the DTO to an array.
     *
     * @return array
     */
    public function toArray(): array
    {
        return [
            'board_month_year' => $this->boardMonthYear,
            'start_date' => $this->startDate,
            'end_date' => $this->endDate,
            'total_days' => $this->totalDays,
            'clients_count' => $this->clientsCount,
            'total_pre_gst_amount' => $this->totalPreGstAmount,
            'total_gst_amount' => $this->totalGstAmount,
            'total_cash_sales' => $this->totalCashSales,
            'total_tds' => $this->totalTds,
            'total_previous_balance' => $this->totalPreviousBalance,
            'total_amount' => $this->totalAmount,
            'total_net_amount' => $this->totalNetAmount,
            'total_paid_amount' => $this->totalPaidAmount,
            'total_unpaid_amount' => $this->totalUnpaidAmount,
        ];
    }

    /**
     * Get formatted month year for display.
     *
     * @return string
     */
    public function getFormattedMonthYear(): string
    {
        try {
            return Carbon::createFromFormat('m-Y', $this->boardMonthYear)->format('F Y');
        } catch (\Exception $e) {
            return $this->boardMonthYear;
        }
    }

    /**
     * Calculate collection percentage.
     *
     * @return float
     */
    public function getCollectionPercentage(): float
    {
        if ($this->totalAmount && $this->totalAmount > 0) {
            return round(($this->totalPaidAmount / $this->totalAmount) * 100, 2);
        }
        return 0.0;
    }
}
