<?php

namespace App\Repositories;

use App\Classes\ResponseData;
use App\Classes\SuccessData;
use App\Classes\ErrorData;
use App\DTO\Sales\ManageSaleDTO;
use App\Models\Sales\Sale;
use Exception;
use Illuminate\Database\Eloquent\Collection;

class SalesRepository
{
    /**
     * Create a new class instance.
     */
    public function __construct(){}

    /**
     * Get all sales with optional filtering
     *
     * @param array $filters
     * @return Collection
     */
    public function getAllSales(array $filters = []): Collection
    {
        $query = Sale::with(['client', 'payment']);

        // Filter by client
        if (isset($filters['client_id']) && !empty($filters['client_id'])) {
            $query->where('client_id', $filters['client_id']);
        }

        // Filter by sale type
        if (isset($filters['sale_type']) && !empty($filters['sale_type'])) {
            $query->where('sales_type', $filters['sale_type']);
        }

        // Filter by payment status
        if (isset($filters['payment_status']) && $filters['payment_status'] !== '' && $filters['payment_status'] !== 'all') {
            if ($filters['payment_status'] === 'paid') {
                $query->paid();
            } else {
                $query->unpaid();
            }
        }

        // Filter by date range
        if (isset($filters['date_from']) && !empty($filters['date_from'])) {
            $query->whereDate('sale_date', '>=', $filters['date_from']);
        }

        if (isset($filters['date_to']) && !empty($filters['date_to'])) {
            $query->whereDate('sale_date', '<=', $filters['date_to']);
        }

        // Filter by amount range
        if (isset($filters['amount_from']) && !empty($filters['amount_from'])) {
            $query->where('total_amount', '>=', $filters['amount_from']);
        }

        if (isset($filters['amount_to']) && !empty($filters['amount_to'])) {
            $query->where('total_amount', '<=', $filters['amount_to']);
        }

        return $query->orderBy('sale_date', 'desc')->get();
    }

    /**
     * Store a new sale in the database
     *
     * @param ManageSaleDTO $saleDTO
     * @return ResponseData
     */
    public function storeSale(ManageSaleDTO $saleDTO): ResponseData
    {
        try {
            // Create a new sale
            $sale = new Sale();
            $sale->company_profile_id = session('company_profile.id');
            $sale->client_id = $saleDTO->clientId;
            $sale->sale_date = $saleDTO->saleDate;
            $sale->sales_type = $saleDTO->salesType;
            $sale->base_amount = $saleDTO->baseAmount;
            $sale->tax_amount = $saleDTO->taxAmount;
            $sale->tax_rate = $saleDTO->taxRate;
            $sale->total_amount = $saleDTO->totalAmount;
            $sale->tds = $saleDTO->tds;
            $sale->tds_rate = $saleDTO->tdsRate;
            $sale->due_date = $saleDTO->dueDate;
            $sale->paid = $saleDTO->paid;
            $sale->payment_id = $saleDTO->paymentId;
            $sale->notes = $saleDTO->notes;

            // Save the sale to the database
            $sale->save();

            // Return success response with the created sale
            return new SuccessData($sale->toArray());
        } catch (Exception $e) {
            // Return error response
            return new ErrorData(['Failed to create sale: ' . $e->getMessage()]);
        }
    }

    /**
     * Find a sale by ID
     *
     * @param int $id
     * @return Sale|null
     */
    public function findSale(int $id): ?Sale
    {
        return Sale::with(['client', 'payment'])->find($id);
    }


     /** Update an existing sale
     *
     * @param int $id
     * @param ManageSaleDTO $saleDTO
     * @return ResponseData
     */
    public function updateSale(int $id, ManageSaleDTO $saleDTO): ResponseData
    {
        try {
            $sale = Sale::find($id);

            if (!$sale) {
                return new ErrorData(['Sale not found']);
            }

            // Update the sale with new data from DTO
            $sale->client_id = $saleDTO->clientId;
            $sale->sale_date = $saleDTO->saleDate;
            $sale->sales_type = $saleDTO->salesType;
            $sale->base_amount = $saleDTO->baseAmount;
            $sale->tax_amount = $saleDTO->taxAmount;
            $sale->tax_rate = $saleDTO->taxRate;
            $sale->total_amount = $saleDTO->totalAmount;
            $sale->tds = $saleDTO->tds;
            $sale->tds_rate = $saleDTO->tdsRate;
            $sale->notes = $saleDTO->notes;

            // Save the updated sale
            $sale->due_date = $saleDTO->dueDate;
            $sale->paid = $saleDTO->paid;
            $sale->payment_id = $saleDTO->paymentId;
            $sale->save();

            // Return success response with the updated sale
            return new SuccessData($sale->toArray());
        } catch (Exception $e) {
            // Return error response
            return new ErrorData(['Failed to update sale: ' . $e->getMessage()]);
        }
    }

    /**
     * Delete a sale
     *
     * @param int $id
     * @return ResponseData
     */
    public function deleteSale(int $id): ResponseData
    {
        try {
            $sale = Sale::find($id);

            if (!$sale) {
                return new ErrorData(['Sale not found']);
            }

            $sale->delete();

            return new SuccessData(['message' => 'Sale deleted successfully']);
        } catch (Exception $e) {
            // Return error response
            return new ErrorData(['Failed to delete sale: ' . $e->getMessage()]);
        }
    }

    /**
     * Get sales statistics
     *
     * @param array $filters
     * @return array
     */
    public function getSalesStatistics(array $filters = []): array
    {
        $query = Sale::query();

        // Apply filters
        if (isset($filters['client_id']) && !empty($filters['client_id'])) {
            $query->where('client_id', $filters['client_id']);
        }

        if (isset($filters['date_from']) && !empty($filters['date_from'])) {
            $query->whereDate('sale_date', '>=', $filters['date_from']);
        }

        if (isset($filters['date_to']) && !empty($filters['date_to'])) {
            $query->whereDate('sale_date', '<=', $filters['date_to']);
        }

        // Get paid/unpaid statistics
        $paidQuery = clone $query;
        $unpaidQuery = clone $query;

        return [
            'total_sales' => $query->count(),
            'total_amount' => $query->sum('total_amount'),
            'total_tax' => $query->sum('tax_amount'),
            'total_base_amount' => $query->sum('base_amount'),
            'paid_amount' => $paidQuery->paid()->sum('total_amount'),
            'unpaid_amount' => $unpaidQuery->unpaid()->sum('total_amount'),
            'paid_count' => $paidQuery->paid()->count(),
            'unpaid_count' => $unpaidQuery->unpaid()->count(),
        ];
    }

    /**
     * Mark a sale as paid
     *
     * @param int $id
     * @param int|null $paymentId
     * @return ResponseData
     */
    public function markSaleAsPaid(int $id, ?int $paymentId = null): ResponseData
    {
        try {
            $sale = Sale::find($id);

            if (!$sale) {
                return new ErrorData(['Sale not found']);
            }

            $sale->markAsPaid($paymentId);

            return new SuccessData(['message' => 'Sale marked as paid successfully']);
        } catch (Exception $e) {
            // Return error response
            return new ErrorData(['Failed to mark sale as paid: ' . $e->getMessage()]);
        }
    }

    /**
     * Mark a sale as unpaid
     *
     * @param int $id
     * @return ResponseData
     */
    public function markSaleAsUnpaid(int $id): ResponseData
    {
        try {
            $sale = Sale::find($id);

            if (!$sale) {
                return new ErrorData(['Sale not found']);
            }

            $sale->markAsUnpaid();

            return new SuccessData(['message' => 'Sale marked as unpaid successfully']);
        } catch (Exception $e) {
            // Return error response
            return new ErrorData(['Failed to mark sale as unpaid: ' . $e->getMessage()]);
        }
    }

    /**
     * Get unpaid sales for a specific client
     *
     * @param int $clientId
     * @return Collection
     */
    public function getUnpaidSalesByClient(int $clientId): Collection
    {
        return Sale::with(['client', 'payment'])
            ->where('client_id', $clientId)
            ->unpaid()
            ->orderBy('sale_date', 'asc')
            ->get();
    }

    /**
     * Get overdue sales (unpaid sales past due date)
     *
     * @return Collection
     */
    public function getOverdueSales(): Collection
    {
        return Sale::with(['client', 'payment'])
            ->unpaid()
            ->whereNotNull('due_date')
            ->where('due_date', '<', now()->toDateString())
            ->orderBy('due_date', 'asc')
            ->get();
    }

    /**
     * Get form options for sales forms
     *
     * @return array
     */
    public function getFormOptions(): array
    {
        return [
            'sales_types' => [
                'cash' => 'Cash Sale',
                'invoice' => 'Invoice',
            ],
            'payment_statuses' => [
                '1' => 'Paid',
                '0' => 'Unpaid',
            ],
        ];
    }
}
