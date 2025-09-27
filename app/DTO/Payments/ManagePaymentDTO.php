<?php

namespace App\DTO\Payments;

use App\Classes\ErrorData;
use Illuminate\Support\Facades\Validator;
use App\DTO\BaseDTOInterface;
use Carbon\Carbon;

class ManagePaymentDTO implements BaseDTOInterface
{
    /**
     * Create a new class instance.
     */
    public function __construct(
        public int $clientId,
        public float $amountPaid,
        public string $paymentDate,
        public ?string $paymentMethod,
        public ?string $notes
    ) {}

    /**
     * Create a DTO from an array of data.
     *
     * @param array $data
     * @return ErrorData|ManagePaymentDTO
     */
    public static function from(array $data): ErrorData|ManagePaymentDTO
    {
        // Validate the data
        $validator = Validator::make($data, [
            'client_id' => 'required|integer|exists:clients,id',
            'amount_paid' => 'required|numeric|min:0.01',
            'payment_date' => 'required|date',
            'payment_method' => 'nullable|string|in:cash,bank_transfer,cash_transfer',
            'notes' => 'nullable|string|max:1000',
        ], [
            'client_id.required' => 'Please select a client.',
            'client_id.exists' => 'The selected client is invalid.',
            'amount_paid.required' => 'Please enter the payment amount.',
            'amount_paid.min' => 'Payment amount must be greater than 0.',
            'payment_date.required' => 'Please select a payment date.',
            'payment_date.date' => 'Please enter a valid payment date.',
            'payment_method.in' => 'Please select a valid payment method.',
            'notes.max' => 'Notes cannot exceed 1000 characters.',
        ]);

        if ($validator->fails()) {
            return new ErrorData($validator->errors()->all());
        }

        // Format date for consistency
        $paymentDate = isset($data['payment_date']) ?
            (is_string($data['payment_date']) ? $data['payment_date'] : $data['payment_date']->format('Y-m-d')) :
            '';

        return new self(
            intval($data['client_id']),
            floatval($data['amount_paid']),
            $paymentDate,
            $data['payment_method'] ?? null,
            $data['notes'] ?? null
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
            'client_id' => 'required|integer|exists:clients,id',
            'amount_paid' => 'required|numeric|min:0.01',
            'payment_date' => 'required|date',
            'payment_method' => 'nullable|string|in:cash,bank_transfer',
            'notes' => 'nullable|string|max:1000',
        ];
    }

    /**
     * Validate the current DTO instance.
     *
     * @return ErrorData|ManagePaymentDTO
     */
    public function validate(): ErrorData|ManagePaymentDTO
    {
        $validator = Validator::make($this->toArray(), $this->rules(), [
            'client_id.required' => 'Please select a client.',
            'client_id.exists' => 'The selected client is invalid.',
            'amount_paid.required' => 'Please enter the payment amount.',
            'amount_paid.min' => 'Payment amount must be greater than 0.',
            'payment_date.required' => 'Please select a payment date.',
            'payment_date.date' => 'Please enter a valid payment date.',
            'payment_method.in' => 'Please select a valid payment method.',
            'notes.max' => 'Notes cannot exceed 1000 characters.',
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
            'client_id' => $this->clientId,
            'amount_paid' => $this->amountPaid,
            'payment_date' => $this->paymentDate,
            'payment_method' => $this->paymentMethod,
            'notes' => $this->notes,
        ];
    }
}
