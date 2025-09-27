<?php

namespace App\DTO\Sales;

use App\Classes\ErrorData;
use Illuminate\Support\Facades\Validator;
use App\DTO\BaseDTOInterface;

class ManageSaleDTO implements BaseDTOInterface
{
    /**
     * Create a new class instance.
     */
    public function __construct(
        public int $clientId,
        public string $saleDate,
        public string $salesType,
        public float $baseAmount,
        public float $taxAmount,
        public float $taxRate,
        public float $totalAmount,
        public float $tds,
        public float $tdsRate,
        public ?string $dueDate,
        public bool $paid,
        public ?int $paymentId,
        public ?string $notes
    ) {}

    /**
     * Create a DTO from an array of data.
     *
     * @param array $data
     * @return ErrorData|ManageSaleDTO
     */
    public static function from(array $data): ErrorData|ManageSaleDTO
    {
        // Format due date for consistency
        $dueDate = null;
        if (!empty($data['due_date'])) {
            $dueDate = is_string($data['due_date']) ?
                $data['due_date'] :
                $data['due_date']->format('Y-m-d');
        }

        return (new self(
            intval($data['client_id'] ?? 0),
            $data['sale_date'] ?? '',
            $data['sales_type'] ?? '',
            floatval($data['base_amount'] ?? 0),
            floatval($data['tax_amount'] ?? 0),
            floatval($data['tax_rate'] ?? 0),
            floatval($data['total_amount'] ?? 0),
            floatval($data['tds'] ?? 0),
            floatval($data['tds_rate'] ?? 0),
            $dueDate,
            boolval($data['paid'] ?? false),
            !empty($data['payment_id']) ? intval($data['payment_id']) : null,
            $data['notes'] ?? null
        ))->validate();
    }

    /**
     * Validate the DTO data.
     *
     * @return ErrorData|ManageSaleDTO
     */
    public function validate(): ErrorData|ManageSaleDTO
    {
        $validator = Validator::make([
            'client_id' => $this->clientId,
            'sale_date' => $this->saleDate,
            'sales_type' => $this->salesType,
            'base_amount' => $this->baseAmount,
            'tax_amount' => $this->taxAmount,
            'tax_rate' => $this->taxRate,
            'total_amount' => $this->totalAmount,
            'tds' => $this->tds,
            'tds_rate' => $this->tdsRate,
            'due_date' => $this->dueDate,
            'paid' => $this->paid,
            'payment_id' => $this->paymentId,
            'notes' => $this->notes,
        ], $this->rules());

        if ($validator->fails()) {
            return new ErrorData($validator->errors()->all());
        }

        return $this;
    }

    /**
     * Get validation rules for the DTO.
     *
     * @return array
     */
    public function rules(): array
    {
        return [
            'client_id' => 'required|integer|exists:clients,id',
            'sale_date' => 'required|date',
            'sales_type' => 'required|string|max:50',
            'base_amount' => 'required|numeric|min:0',
            'tax_amount' => 'nullable|numeric|min:0',
            'tax_rate' => 'nullable|numeric|min:0|max:100',
            'total_amount' => 'required|numeric|min:0',
            'tds' => 'nullable|numeric|min:0',
            'tds_rate' => 'nullable|numeric|min:0|max:100',
            'due_date' => 'nullable|date|after_or_equal:sale_date',
            'paid' => 'required|boolean',
            'payment_id' => 'nullable|integer|exists:payments,id',
            'notes' => 'nullable|string|max:1000',
        ];
    }

    /**
     * Convert DTO to array format for database operations.
     *
     * @return array
     */
    public function toArray(): array
    {
        return [
            'client_id' => $this->clientId,
            'sale_date' => $this->saleDate,
            'sales_type' => $this->salesType,
            'base_amount' => $this->baseAmount,
            'tax_amount' => $this->taxAmount,
            'tax_rate' => $this->taxRate,
            'total_amount' => $this->totalAmount,
            'tds' => $this->tds,
            'tds_rate' => $this->tdsRate,
            'due_date' => $this->dueDate,
            'paid' => $this->paid,
            'payment_id' => $this->paymentId,
            'notes' => $this->notes,
        ];
    }
}
