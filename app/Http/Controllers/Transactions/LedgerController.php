<?php

namespace App\Http\Controllers\Transactions;

use App\Http\Controllers\Controller;
use App\Repositories\TransactionRepository;
use App\Repositories\ClientsRepository;
use Illuminate\Http\Request;
use Carbon\Carbon;

class LedgerController extends Controller
{
    protected TransactionRepository $transactionRepository;
    protected ClientsRepository $clientsRepository;

    public function __construct(TransactionRepository $transactionRepository, ClientsRepository $clientsRepository)
    {
        $this->transactionRepository = $transactionRepository;
        $this->clientsRepository = $clientsRepository;
    }

    /**
     * Display a listing of the ledger.
     */
    public function index(Request $request)
    {
        // Get date range from request or default to current month
        $dateRange = $request->get('date_range', 'current_month');

        // Calculate date range using repository method
        $dateRangeData = $this->transactionRepository->calculateDateRange(
            $dateRange,
            $request->get('start_date'),
            $request->get('end_date')
        );

        $startDate = $dateRangeData['start_date'];
        $endDate = $dateRangeData['end_date'];

        // Get filters
        $clientId = $request->get('client_id');
        $transactionType = $request->get('transaction_type');

        $filters = [
            'client_id' => $clientId,
            'transaction_type' => $transactionType,
        ];

        if ($startDate && $endDate) {
            $filters['start_date'] = $startDate->format('Y-m-d');
            $filters['end_date'] = $endDate->format('Y-m-d');
        }

        $transactions = $this->transactionRepository->getLedgerTransactions($filters);

        // Calculate statistics
        $totalIncome = 0;
        $totalExpenses = 0;
        $totalPayments = 0;

        foreach ($transactions as $transaction) {
            switch ($transaction->transaction_type) {
                case 'sale':
                    $totalIncome += $transaction->total_amount;
                    break;
                case 'payment':
                    $totalPayments += $transaction->total_amount;
                    break;
                case 'expense':
                    $totalExpenses += $transaction->total_amount;
                    break;
            }
        }

        $netBalance = $totalIncome - $totalPayments;

        // Get clients for filter dropdown
        $clients = $this->clientsRepository->getAllClients();

        // Transaction types for filter
        $transactionTypes = [
            'sale' => 'Sales',
            'payment' => 'Payments'
        ];

        // Date range options
        $dateRangeOptions = [
            'current_month' => 'Current Month',
            'last_month' => 'Last Month',
            'current_quarter' => 'Current Quarter',
            'last_quarter' => 'Last Quarter',
            'current_year' => 'Current Year',
            'last_year' => 'Last Year',
            'custom' => 'Custom Range'
        ];

        $statistics = [
            'total_income' => $totalIncome,
            'total_expenses' => $totalExpenses,
            'total_payments' => $totalPayments,
            'net_balance' => $netBalance,
            'transaction_count' => $transactions->count()
        ];

        return view('pages.transactions.ledger.index', compact(
            'transactions',
            'clients',
            'transactionTypes',
            'dateRangeOptions',
            'statistics',
            'dateRange',
            'startDate',
            'endDate',
            'clientId',
            'transactionType'
        ));
    }
}
