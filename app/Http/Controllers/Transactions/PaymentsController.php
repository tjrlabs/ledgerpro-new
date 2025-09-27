<?php

namespace App\Http\Controllers\Transactions;

use App\Http\Controllers\Controller;
use App\Repositories\TransactionRepository;
use App\Repositories\ClientsRepository;
use App\DTO\Payments\ManagePaymentDTO;
use Illuminate\Http\Request;
use Carbon\Carbon;

class PaymentsController extends Controller
{
    protected $transactionRepository;
    protected $clientsRepository;

    public function __construct(TransactionRepository $transactionRepository, ClientsRepository $clientsRepository)
    {
        $this->transactionRepository = $transactionRepository;
        $this->clientsRepository = $clientsRepository;
    }

    /**
     * Display a listing of the payments.
     */
    public function index(Request $request)
    {
        // Get date range from request or default to current month
        $dateRange = $request->get('date_range', 'current_month');

        // Calculate date range using repository method
        $dateRangeData = $this->transactionRepository->calculateDateRange(
            $dateRange,
            $request->get('date_from'),
            $request->get('date_to')
        );

        $startDate = $dateRangeData['start_date'];
        $endDate = $dateRangeData['end_date'];

        // Prepare filters for repository
        $filters = [
            'client_id' => $request->get('client_id'),
            'payment_method' => $request->get('payment_method'),
            'date_range' => $dateRange
        ];

        // Add date range to filters if available
        if ($startDate && $endDate) {
            $filters['date_from'] = $startDate->format('Y-m-d');
            $filters['date_to'] = $endDate->format('Y-m-d');
        }

        // Get filtered payments from repository
        $payments = $this->transactionRepository->getFilteredPayments($filters);

        // Paginate results (convert collection to paginator)
        $perPage = 15;
        $currentPage = $request->get('page', 1);
        $pagedPayments = $payments->forPage($currentPage, $perPage);

        // Get clients for filter dropdown
        $clients = $this->clientsRepository->getAllClients();

        // Payment methods for filter dropdown
        $paymentMethods = [
            'cash' => 'Cash',
            'bank_transfer' => 'Bank Transfer',
            'cash_transfer' => 'Cash Transfer'
        ];

        return view('pages.transactions.payments.index', compact(
            'pagedPayments',
            'clients',
            'paymentMethods',
            'filters'
        ));
    }

    /**
     * Show the form for creating a new payment.
     */
    public function create()
    {
        // Get clients for the dropdown
        $clients = $this->clientsRepository->getAllClients();

        // Payment methods for dropdown
        $paymentMethods = [
            'cash' => 'Cash',
            'bank_transfer' => 'Bank Transfer',
            'cash_transfer' => 'Cash Transfer'
        ];

        // Set default date to current date
        $defaultDate = Carbon::now()->format('Y-m-d');

        return view('pages.transactions.payments.create', compact(
            'clients',
            'paymentMethods',
            'defaultDate'
        ));
    }

    /**
     * Store a newly created payment in storage.
     */
    public function store(Request $request)
    {
        // Create a DTO from the request data
        $paymentDTO = ManagePaymentDTO::from($request->all());

        // Check if validation failed
        if ($paymentDTO instanceof \App\Classes\ErrorData) {
            return back()->withInput()
                ->withErrors($paymentDTO->getErrorMessages());
        }

        // Convert DTO to transaction data
        $transactionData = [
            'transaction_type' => 'payment',
            'transaction_date' => $paymentDTO->paymentDate,
            'client_id' => $paymentDTO->clientId,
            'payment_method' => $paymentDTO->paymentMethod, // Store payment method in sales_type
            'total_amount' => $paymentDTO->amountPaid,
            'base_amount' => $paymentDTO->amountPaid,
            'notes' => $paymentDTO->notes ?? null,
            'paid' => true, // Payments are always paid
        ];

        // Create payment using the transaction repository
        $paymentResponse = $this->transactionRepository->storePaymentTransaction($transactionData);

        if ($paymentResponse instanceof \App\Classes\SuccessData) {
            return redirect()->route('payments.index')
                ->with('success', 'Payment created successfully.');
        } else {
            return back()->withInput()
                ->withErrors($paymentResponse->getErrorMessages());
        }
    }

    /**
     * Show the form for editing the specified payment.
     */
    public function edit($id)
    {
        // Find the payment transaction
        $payment = $this->transactionRepository->findPaymentTransaction($id);

        if (!$payment) {
            return redirect()->route('payments.index')
                ->with('error', 'Payment not found.');
        }

        // Get clients for the dropdown
        $clients = $this->clientsRepository->getAllClients();

        // Payment methods for dropdown
        $paymentMethods = [
            'cash' => 'Cash',
            'bank_transfer' => 'Bank Transfer',
            'cash_transfer' => 'Cash Transfer'
        ];

        // Set default date to current date
        $defaultDate = Carbon::now()->format('Y-m-d');

        return view('pages.transactions.payments.create', compact(
            'payment',
            'clients',
            'paymentMethods',
            'defaultDate'
        ))->with('isEditing', true);
    }

    /**
     * Update the specified payment in storage.
     */
    public function update(Request $request, $id)
    {
        // Create a DTO from the request data
        $paymentDTO = ManagePaymentDTO::from($request->all());

        // Check if validation failed
        if ($paymentDTO instanceof \App\Classes\ErrorData) {
            return back()->withInput()
                ->withErrors($paymentDTO->getErrorMessages());
        }

        // Convert DTO to transaction data
        $transactionData = [
            'transaction_date' => $paymentDTO->paymentDate,
            'client_id' => $paymentDTO->clientId,
            'payment_method' => $paymentDTO->paymentMethod, // Store payment method in sales_type
            'total_amount' => $paymentDTO->amountPaid,
            'base_amount' => $paymentDTO->amountPaid,
            'notes' => $paymentDTO->notes ?? null,
            'paid' => true, // Payments are always paid
        ];

        // Update payment using the transaction repository
        $paymentResponse = $this->transactionRepository->updatePaymentTransaction($id, $transactionData);

        if ($paymentResponse instanceof \App\Classes\SuccessData) {
            return redirect()->route('payments.index')
                ->with('success', 'Payment updated successfully.');
        } else {
            return back()->withInput()
                ->withErrors($paymentResponse->getErrorMessages());
        }
    }

    /**
     * Remove the specified payment from storage.
     */
    public function destroy($id)
    {
        // Delete payment using the transaction repository
        $paymentResponse = $this->transactionRepository->deletePaymentTransaction($id);

        if (!$paymentResponse instanceof \App\Classes\ErrorData) {
            return redirect()->route('payments.index')
                ->with('success', 'Payment deleted successfully.');
        } else {
            return redirect()->route('payments.index')
                ->with('error', 'There was a problem deleting the payment: ' .
                    (count($paymentResponse->getErrorMessages()) > 0 ? $paymentResponse->getErrorMessages()[0] : 'Please try again.'));
        }
    }
}
