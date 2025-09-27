<?php

namespace App\Repositories;

use App\Classes\ResponseData;
use App\Classes\SuccessData;
use App\Classes\ErrorData;
use App\DTO\Items\ManageItemDTO;
use App\Models\Inventory\Items;
use Exception;
use Illuminate\Support\Facades\Log;

class ItemsRepository
{
    /**
     * Create a new class instance.
     */
    public function __construct(){}

    /**
     * Store a new item in the database
     *
     * @param ManageItemDTO $itemDTO
     * @return ResponseData
     */
    public function storeItem(ManageItemDTO $itemDTO): ResponseData
    {
        try {
            // Create a new item using the DTO data
            $item = new Items();
            $item->company_profile_id = session('company_profile.id');
            if($itemDTO->clientId > 0) {
                $item->client_id = $itemDTO->clientId;
            }
            $item->item_name = $itemDTO->itemName;
            $item->item_type = $itemDTO->itemType;
            $item->item_hsn_code = $itemDTO->itemHsnCode;
            $item->item_description = $itemDTO->itemDescription;
            $item->item_price = $itemDTO->itemPrice;
            $item->item_sku = $itemDTO->itemSKU;
            $item->item_unit = 'pcs';
            // Save the item to the database
            $item->save();

            // Return success response with the created item
            return new SuccessData($item->toArray());
        } catch (Exception $e) {
            // Return error response
            return new ErrorData(['Failed to create item: ' . $e->getMessage()]);
        }
    }

    /**
     * Update an existing item in the database
     *
     * @param ManageItemDTO $itemDTO
     * @param int $id
     * @return ResponseData
     */
    public function updateItem(ManageItemDTO $itemDTO, int $id): ResponseData
    {
        try {
            // Find the item to update
            $item = Items::findOrFail($id);

            // Update the item using the DTO data
            $item->item_name = $itemDTO->itemName;
            $item->item_type = $itemDTO->itemType;
            $item->item_hsn_code = $itemDTO->itemHsnCode;
            $item->item_description = $itemDTO->itemDescription;
            $item->item_price = $itemDTO->itemPrice;
            $item->item_sku = $itemDTO->itemSKU;

            // Save the updated item to the database
            $item->save();

            // Return success response with the updated item
            return new SuccessData($item->toArray());
        } catch (Exception $e) {
            // Return error response
            return new ErrorData(['Failed to update item: ' . $e->getMessage()]);
        }
    }
}
