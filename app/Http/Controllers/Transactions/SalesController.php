<?php

namespace App\Http\Controllers\Transactions;

use App\Http\Controllers\Controller;
use App\DTO\Sales\ManageSaleDTO;
use App\Repositories\ClientsRepository;
use App\Repositories\TransactionRepository;
use App\Classes\ErrorData;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;

class SalesController extends Controller
{
    public function __construct(protected TransactionRepository $transactionRepository, protected ClientsRepository $clientsRepository) {}

    public function index(Request $request) {

        $clients = $this->clientsRepository->getAllClients();

        // Filter by date range
        $dateRange = $request->date_range ?? 'current_month';
        $startDate = null;
        $endDate = null;

        switch ($dateRange) {
            case 'current_month':
                $startDate = Carbon::now()->startOfMonth();
                $endDate = Carbon::now()->endOfMonth();
                break;
            case 'last_month':
                $startDate = Carbon::now()->subMonth()->startOfMonth();
                $endDate = Carbon::now()->subMonth()->endOfMonth();
                break;
            case 'current_quarter':
                $startDate = Carbon::now()->startOfQuarter();
                $endDate = Carbon::now()->endOfQuarter();
                break;
            case 'last_quarter':
                $startDate = Carbon::now()->subQuarter()->startOfQuarter();
                $endDate = Carbon::now()->subQuarter()->endOfQuarter();
                break;
            case 'current_year':
                $startDate = Carbon::now()->startOfYear();
                $endDate = Carbon::now()->endOfYear();
                break;
            case 'last_financial_year':
                // Indian financial year runs from April 1 to March 31
                $currentDate = Carbon::now();
                if ($currentDate->month >= 4) {
                    // If current month is April or later, last FY is previous year April to current year March
                    $startDate = Carbon::create($currentDate->year - 1, 4, 1)->startOfDay();
                    $endDate = Carbon::create($currentDate->year, 3, 31)->endOfDay();
                } else {
                    // If current month is Jan-Mar, last FY is two years ago April to last year March
                    $startDate = Carbon::create($currentDate->year - 2, 4, 1)->startOfDay();
                    $endDate = Carbon::create($currentDate->year - 1, 3, 31)->endOfDay();
                }
                break;
            case 'custom':
                $startDate = $request->has('start_date') ? Carbon::parse($request->start_date) : null;
                $endDate = $request->has('end_date') ? Carbon::parse($request->end_date) : null;
                break;
        }

        // Get sales transactions with filters
        $sales = collect();

        if ($startDate && $endDate) {
            $sales = $this->transactionRepository->getTransactionsByDateRange(
                $startDate->format('Y-m-d'),
                $endDate->format('Y-m-d')
            )->filter(function($transaction) {
                return $transaction->transaction_type === 'sale';
            });
        } else {
            $sales = $this->transactionRepository->getSalesTransactions();
        }

        // Apply additional filters
        if ($request->has('client_id') && !empty($request->client_id)) {
            $sales = $sales->where('client_id', $request->client_id);
        }

        if ($request->has('sale_type') && !empty($request->sale_type)) {
            $sales = $sales->where('sales_type', $request->sale_type);
        }

        if($request->has('payment_status') && $request->payment_status !== '') {
            $paymentStatus = $request->payment_status === '1';
            $sales = $sales->where('paid', $paymentStatus);
        }

        return view('pages.transactions.sales.index', compact('sales', 'clients', 'dateRange', 'startDate', 'endDate'));
    }

    public function create() {
        $clients = $this->clientsRepository->getAllClients();
        return view('pages.transactions.sales.create', compact('clients'));
    }

    /**
     * Store a newly created sale in storage.
     *
     * @param Request $request
     * @return RedirectResponse
     */
    public function store(Request $request): RedirectResponse
    {
        $saleDTO = ManageSaleDTO::from($request->all());
        if ($saleDTO instanceof ErrorData) {
            return redirect()->back()->withErrors($saleDTO->getErrorMessages())->withInput();
        }

        // Convert DTO to transaction data
        $transactionData = [
            'client_id' => $saleDTO->clientId,
            'transaction_type' => 'sale',
            'transaction_date' => $saleDTO->saleDate,
            'sales_type' => $saleDTO->salesType,
            'base_amount' => $saleDTO->baseAmount,
            'tax_amount' => $saleDTO->taxAmount,
            'tax_rate' => $saleDTO->taxRate,
            'tds' => $saleDTO->tds,
            'tds_rate' => $saleDTO->tdsRate,
            'total_amount' => $saleDTO->totalAmount,
            'due_date' => $saleDTO->dueDate,
            'paid' => $saleDTO->paid,
            'payment_id' => $saleDTO->paymentId,
            'notes' => $saleDTO->notes,
        ];

        $response = $this->transactionRepository->storeTransaction($transactionData);
        if ($response instanceof ErrorData) {
            return redirect()->back()->withErrors($response->getErrorMessages())->withInput();
        }

        return redirect()->route('sales.index')->with('success', 'Sale created successfully!');
    }

    /**
     * Show the form for editing the specified sale.
     *
     * @param int $id
     * @return \Illuminate\View\View
     */
    public function edit(int $id)
    {
        $sale = $this->transactionRepository->findTransaction($id);

        if (!$sale || $sale->transaction_type !== 'sale') {
            return redirect()->route('sales.index')->with('error', 'Sale not found.');
        }

        $clients = $this->clientsRepository->getAllClients();

        return view('pages.transactions.sales.create', [
            'sale' => $sale,
            'clients' => $clients,
            'isEditing' => true
        ]);
    }

    /**
     * Update the specified sale in storage.
     *
     * @param Request $request
     * @param int $id
     * @return RedirectResponse
     */
    public function update(Request $request, int $id): RedirectResponse
    {
        $saleDTO = ManageSaleDTO::from($request->all());
        if ($saleDTO instanceof ErrorData) {
            return redirect()->back()->withErrors($saleDTO->getErrorMessages())->withInput();
        }

        // Convert DTO to transaction data
        $transactionData = [
            'client_id' => $saleDTO->clientId,
            'transaction_type' => 'sale',
            'transaction_date' => $saleDTO->saleDate,
            'sales_type' => $saleDTO->salesType,
            'base_amount' => $saleDTO->baseAmount,
            'tax_amount' => $saleDTO->taxAmount,
            'tax_rate' => $saleDTO->taxRate,
            'tds' => $saleDTO->tds,
            'tds_rate' => $saleDTO->tdsRate,
            'total_amount' => $saleDTO->totalAmount,
            'due_date' => $saleDTO->dueDate,
            'paid' => $saleDTO->paid,
            'payment_id' => $saleDTO->paymentId,
            'notes' => $saleDTO->notes,
        ];

        $response = $this->transactionRepository->updateTransaction($transactionData, $id);
        if ($response instanceof ErrorData) {
            return redirect()->back()->withErrors($response->getErrorMessages())->withInput();
        }

        return redirect()->route('sales.index')->with('success', 'Sale updated successfully!');
    }

    /**
     * Remove the specified sale from storage.
     *
     * @param int $id
     * @return RedirectResponse
     */
    public function destroy(int $id): RedirectResponse
    {
        $response = $this->transactionRepository->deleteTransaction($id);

        if ($response instanceof ErrorData) {
            return redirect()->route('sales.index')->with('error', 'Failed to delete sale.');
        }

        return redirect()->route('sales.index')->with('success', 'Sale deleted successfully!');
    }
}
