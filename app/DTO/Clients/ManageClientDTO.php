<?php

namespace App\DTO\Clients;

use App\Classes\ErrorData;
use Illuminate\Support\Facades\Validator;
use App\DTO\BaseDTOInterface;

class ManageClientDTO implements BaseDTOInterface
{
    /**
     * Create a new class instance.
     */
    public function __construct(
        public string $clientName,
        public string $displayName,
        public ?string $clientEmail,
        public ?string $clientPhone,
        public string $clientType,
        public ?string $clientTaxNumber,
        public ?int $billingAddress,
        public ?int $shippingAddress,
        public bool $isActive,
        public bool $addOpeningBalance = false,
        public ?float $accountBalance = null,
        public ?int $applicableMonth = null,
        public ?int $applicableYear = null
    ) {}

    /**
     * Create a DTO from an array of data.
     *
     * @param array $data
     * @return ErrorData|ManageClientDTO
     */
    public static function from(array $data): ErrorData|ManageClientDTO
    {
        $displayName = $data['display_name'] ?? '';
        if(empty($displayName)) {
            $displayName = $data['client_name'] ?? '';
        }

        $addOpeningBalance = !empty($data['add_opening_balance']) && $data['add_opening_balance'] == '1';

        return (new self(
            $data['client_name'] ?? '',
            $displayName,
            $data['client_email'] ?? null,
            $data['client_phone'] ?? null,
            $data['client_type'],
            $data['client_tax_number'] ?? null,
            !empty($data['billing_address']) ? intval($data['billing_address']) : null,
            !empty($data['shipping_address']) ? intval($data['shipping_address']) : null,
            !isset($data['is_active']) ? false : (!empty($data['is_active'])) || boolval($data['is_active']),
            $addOpeningBalance,
            $addOpeningBalance && !empty($data['account_balance']) ? floatval($data['account_balance']) : null,
            $addOpeningBalance && !empty($data['applicable_month']) ? intval($data['applicable_month']) : null,
            $addOpeningBalance && !empty($data['applicable_year']) ? intval($data['applicable_year']) : null
        ))->validate();
    }

    /**
     * Validate the DTO data.
     *
     * @return ErrorData|ManageClientDTO
     */
    public function validate(): ErrorData|ManageClientDTO
    {
        $validator = Validator::make([
            'client_name' => $this->clientName,
            'display_name' => $this->displayName,
            'client_email' => $this->clientEmail,
            'client_phone' => $this->clientPhone,
            'client_type' => $this->clientType,
            'client_tax_number' => $this->clientTaxNumber,
            'billing_address' => $this->billingAddress,
            'shipping_address' => $this->shippingAddress,
            'is_active' => $this->isActive,
            'add_opening_balance' => $this->addOpeningBalance,
            'account_balance' => $this->accountBalance,
            'applicable_month' => $this->applicableMonth,
            'applicable_year' => $this->applicableYear,
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
        $rules = [
            'client_name' => 'required|string|max:255',
            'client_email' => 'nullable|email|max:255',
            'client_phone' => 'nullable|string|max:20',
            'client_type' => 'required|string|max:50',
            'client_tax_number' => 'nullable|string|max:50',
            'billing_address' => 'nullable|integer|exists:addressbook,id',
            'shipping_address' => 'nullable|integer|exists:addressbook,id',
            'is_active' => 'boolean',
            'add_opening_balance' => 'boolean',
        ];

        // Add conditional validation rules for account balance fields
        if ($this->addOpeningBalance) {
            $rules['account_balance'] = 'required|numeric|min:0';
            $rules['applicable_month'] = 'required|integer|between:1,12';
            $rules['applicable_year'] = 'required|integer|min:2020|max:' . (date('Y') + 10);
        }

        return $rules;
    }
}
