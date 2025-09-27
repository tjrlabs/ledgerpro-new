<?php

namespace App\DTO\Items;
use App\Classes\ErrorData;
use Illuminate\Support\Facades\Validator;

class ManageItemDTO implements \App\DTO\BaseDTOInterface
{
    public function __construct(public string $itemName, public string $itemType, public int $itemHsnCode, public string $itemDescription, public float $itemPrice, public string $itemSKU, public int $clientId){}

    public static function from(array $data): ErrorData|ManageItemDTO {

        return (new self(
            $data['item_name'] ?? '',
            $data['item_type'] ?? '',
            intval($data['item_hsn_code'] ?? 0),
            $data['item_description'] ?? '',
            floatval($data['item_price'] ?? 0.0),
            $data['item_sku'] ?? '',
            intval($data['client_id'] ?? 0)
        ))->validate();
    }

    public function validate(): ErrorData|ManageItemDTO {
        $validator = Validator::make([
            'item_name' => $this->itemName,
            'item_type' => $this->itemType,
            'item_hsn_code' => $this->itemHsnCode,
            'item_description' => $this->itemDescription,
            'item_price' => $this->itemPrice,
            'item_sku' => $this->itemSKU
        ], $this->rules());

        if ($validator->fails()) {
            return new ErrorData($validator->errors()->all());
        }

        return $this;
    }

    public function rules(): array {
        return [
            'item_name' => 'required|string|max:255',
            'item_type' => 'required|string|max:50',
            'item_hsn_code' => 'required|integer',
            'item_description' => 'nullable|string|max:500',
            'item_price' => 'required|numeric|min:0',
            'item_sku' => 'nullable|string|max:100',
        ];
    }
}
