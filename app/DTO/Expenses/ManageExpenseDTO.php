<?php

namespace App\DTO\Expenses;

use App\Classes\ErrorData;
use Illuminate\Support\Facades\Validator;
use App\DTO\BaseDTOInterface;
use Carbon\Carbon;

class ManageExpenseDTO implements BaseDTOInterface
{
    /**
     * Create a new class instance.
     */
    public function __construct(
        public string $expenseType,
        public string $expenseDate,
        public float $baseAmount,
        public float $taxAmount,
        public float $taxRate,
        public float $totalAmount,
        public bool $paid,
        public ?string $notes
    ) {}

    /**
     * Create a DTO from an array of data.
     *
     * @param array $data
     * @return ErrorData|ManageExpenseDTO
     */
    public static function from(array $data): ErrorData|ManageExpenseDTO
    {
        // Format dates for consistency
        $expenseDate = isset($data['expense_date']) ?
            (is_string($data['expense_date']) ? $data['expense_date'] : $data['expense_date']->format('Y-m-d')) :
            '';

        return (new self(
            $data['expense_type'] ?? '',
            $expenseDate,
            floatval($data['base_amount'] ?? 0),
            floatval($data['tax_amount'] ?? 0),
            floatval($data['tax_rate'] ?? 0),
            floatval($data['total_amount'] ?? 0),
            boolval($data['paid'] ?? false),
            $data['notes'] ?? null
        ))->validate();
    }

    /**
     * Validate the DTO data.
     *
     * @return ErrorData|ManageExpenseDTO
     */
    public function validate(): ErrorData|ManageExpenseDTO
    {
        $validator = Validator::make([
            'expense_type' => $this->expenseType,
            'expense_date' => $this->expenseDate,
            'base_amount' => $this->baseAmount,
            'tax_amount' => $this->taxAmount,
            'tax_rate' => $this->taxRate,
            'total_amount' => $this->totalAmount,
            'paid' => $this->paid,
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
            'expense_type' => 'required|string|max:50',
            'expense_date' => 'required|date',
            'base_amount' => 'required|numeric|min:0',
            'tax_amount' => 'nullable|numeric|min:0',
            'tax_rate' => 'nullable|numeric|min:0|max:100',
            'total_amount' => 'required|numeric|min:0',
            'paid' => 'required|boolean',
            'notes' => 'nullable|string|max:1000',
        ];
    }

    /**
     * Convert DTO to array for storage.
     *
     * @return array
     */
    public function toArray(): array
    {
        return [
            'expense_type' => $this->expenseType,
            'expense_date' => $this->expenseDate,
            'base_amount' => $this->baseAmount,
            'tax_amount' => $this->taxAmount,
            'tax_rate' => $this->taxRate,
            'total_amount' => $this->totalAmount,
            'paid' => $this->paid,
            'notes' => $this->notes,
        ];
    }
}
