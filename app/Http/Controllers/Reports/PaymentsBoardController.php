<?php

namespace App\Http\Controllers\Reports;

use App\Http\Controllers\Controller;
use App\Repositories\PaymentsBoardRepository;
use App\DTO\Reports\PaymentsBoardDTO;
use Illuminate\Http\Request;
use App\Classes\ErrorData;
use App\Classes\SuccessData;

class PaymentsBoardController extends Controller
{
    protected $paymentsBoardRepository;
    protected $clientsPaymentsRepository;
    protected $accountBalanceRepository;

    public function __construct(
        PaymentsBoardRepository $paymentsBoardRepository,
        \App\Repositories\ClientsPaymentsRepository $clientsPaymentsRepository,
        \App\Repositories\AccountBalanceRepository $accountBalanceRepository
    ) {
        $this->paymentsBoardRepository = $paymentsBoardRepository;
        $this->clientsPaymentsRepository = $clientsPaymentsRepository;
        $this->accountBalanceRepository = $accountBalanceRepository;
    }

    public function index()
    {
        // Get all payments boards with optional filtering
        $filters = request()->all();
        $paymentsBoards = $this->paymentsBoardRepository->getAllPaymentsBoards($filters);

        return view('pages.reports.payments_board.index', compact('paymentsBoards'));
    }

    public function create()
    {
        // Get current month and year
        $currentMonth = date('m');
        $currentYear = date('Y');

        // Month options
        $monthOptions = [
            '01' => 'January',
            '02' => 'February',
            '03' => 'March',
            '04' => 'April',
            '05' => 'May',
            '06' => 'June',
            '07' => 'July',
            '08' => 'August',
            '09' => 'September',
            '10' => 'October',
            '11' => 'November',
            '12' => 'December'
        ];

        // Year options (current year and next 5 years)
        $yearOptions = [];
        for ($i = $currentYear; $i <= $currentYear + 5; $i++) {
            $yearOptions[$i] = $i;
        }

        return view('pages.reports.payments_board.create', compact(
            'monthOptions',
            'yearOptions',
            'currentMonth',
            'currentYear'
        ));
    }

    public function store(Request $request)
    {
        // Create DTO from request data
        $dto = PaymentsBoardDTO::from([
            'month' => $request->payments_month,
            'year' => $request->payments_year,
        ]);

        // Check if DTO creation failed
        if ($dto instanceof ErrorData) {
            return redirect()->back()
                ->withErrors($dto->getErrorMessages())
                ->withInput();
        }

        // Validate the DTO
        $validatedDto = $dto->validate();
        if ($validatedDto instanceof ErrorData) {
            return redirect()->back()
                ->withErrors($validatedDto->getErrorMessages())
                ->withInput();
        }

        // Store the payments board using repository
        $result = $this->paymentsBoardRepository->storePaymentsBoard($validatedDto->toArray());

        if ($result instanceof SuccessData) {
            return redirect()->route('reports.payments.board')
                ->with('success', 'Payments board created successfully for ' .
                    $validatedDto->getFormattedMonthYear());
        } else {
            return redirect()->back()
                ->withErrors($result->getErrorMessages())
                ->withInput();
        }
    }

    public function edit($id)
    {
        // Find the payments board by ID
        $paymentsBoard = $this->paymentsBoardRepository->findPaymentsBoard($id);

        if (!$paymentsBoard) {
            return redirect()->route('reports.payments-board')
                ->withErrors(['Payments board not found.']);
        }

        // Get clients already added to this payments board
        $clientsPayments = $this->clientsPaymentsRepository->getPaymentsByBoard($id);

        return view('pages.reports.payments_board.edit', compact(
            'paymentsBoard',
            'clientsPayments'
        ));
    }

    public function show($id)
    {
        // Find the payments board by ID
        $paymentsBoard = $this->paymentsBoardRepository->findPaymentsBoard($id);

        if (!$paymentsBoard) {
            return redirect()->route('reports.payments.board')
                ->withErrors(['Payments board not found.']);
        }

        // Get clients already added to this payments board
        $clientsPayments = $this->clientsPaymentsRepository->getPaymentsByBoard($id);

        return view('pages.reports.payments_board.show', compact(
            'paymentsBoard',
            'clientsPayments'
        ));
    }

    public function update(Request $request, $id)
    {
        try {
            // Find the payments board by ID
            $paymentsBoard = $this->paymentsBoardRepository->findPaymentsBoard($id);

            if (!$paymentsBoard) {
                return redirect()->route('reports.payments.board')
                    ->withErrors(['Payments board not found.']);
            }

            // Validate the incoming request data
            $request->validate([
                'clients' => 'sometimes|array',
                'clients.*.id' => 'required|integer|exists:client_payments,id',
                'clients.*.client_id' => 'required|integer|exists:clients,id',
                'clients.*.cash_sales' => 'required|numeric|min:0',
                'clients.*.pre_gst_amount' => 'required|numeric|min:0',
                'clients.*.gst_amount' => 'required|numeric|min:0',
                'clients.*.tds' => 'required|numeric|min:0',
                'clients.*.total_amount' => 'required|numeric|min:0',
                'clients.*.paid_amount' => 'required|numeric|min:0',
                'clients.*.balance' => 'required|numeric'
            ]);

            // Update client payments if provided
            if ($request->has('clients') && is_array($request->clients)) {
                foreach ($request->clients as $clientData) {
                    $result = $this->clientsPaymentsRepository->updateClientPayment(
                        $clientData['id'],
                        $clientData
                    );

                    if ($result instanceof ErrorData) {
                        return redirect()->back()
                            ->withErrors($result->getErrorMessages())
                            ->withInput();
                    }
                }
            }

            return redirect()->route('reports.payments.board')
                ->with('success', 'Payments board updated successfully.');

        } catch (\Illuminate\Validation\ValidationException $e) {
            return redirect()->back()
                ->withErrors($e->errors())
                ->withInput();
        } catch (\Exception $e) {
            return redirect()->back()
                ->withErrors(['An error occurred while updating the payments board: ' . $e->getMessage()])
                ->withInput();
        }
    }

    public function addClients(Request $request, $id)
    {
        try {
            $request->validate([
                'client_ids' => 'required|array|min:1',
                'client_ids.*' => 'integer|exists:clients,id'
            ]);

            $boardId = $id;
            $clientIds = $request->input('client_ids');

            // Check if the payments board exists
            $paymentsBoard = $this->paymentsBoardRepository->findPaymentsBoard($boardId);
            if (!$paymentsBoard) {
                return response()->json([
                    'success' => false,
                    'message' => 'Payments board not found.'
                ], 404);
            }

            // Get the payments board month and year for account balance lookup
            $boardMonth = (int) $paymentsBoard->month;
            $boardYear = (int) $paymentsBoard->year;
            $companyProfileId = session('company_profile.id');

            // Prepare client data with account balances using repository
            $clientsWithBalances = [];
            foreach ($clientIds as $clientId) {
                // First try to get account balance for the exact board period
                $accountBalance = $this->accountBalanceRepository->getAccountBalanceForPeriod(
                    $clientId,
                    $companyProfileId,
                    $boardMonth,
                    $boardYear
                );

                // If no specific balance found, get the most recent one for this client
                if (!$accountBalance) {
                    $clientBalances = $this->accountBalanceRepository->getClientAccountBalances($clientId, $companyProfileId);
                    $accountBalance = $clientBalances->first();
                }

                // Set previous balance (default to 0.00 if no balance found)
                $previousBalance = $accountBalance ? $accountBalance->opening_balance : 0.00;

                $clientsWithBalances[] = [
                    'client_id' => $clientId,
                    'previous_balance' => $previousBalance
                ];
            }

            // Add clients to the payments board with their account balances
            $result = $this->clientsPaymentsRepository->addClientsToBoard($boardId, $clientsWithBalances);

            if ($result instanceof SuccessData) {
                return response()->json([
                    'success' => true,
                    'message' => 'Clients added to payments board successfully with account balances.',
                    'data' => $result->data
                ]);
            } else {
                return response()->json([
                    'success' => false,
                    'message' => 'Failed to add clients to payments board.',
                    'errors' => $result->getErrorMessages()
                ], 400);
            }

        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed.',
                'errors' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'An error occurred while adding clients: ' . $e->getMessage()
            ], 500);
        }
    }

    public function removeClient(Request $request, $boardId, $clientPaymentId)
    {
        try {
            // Check if the payments board exists
            $paymentsBoard = $this->paymentsBoardRepository->findPaymentsBoard($boardId);
            if (!$paymentsBoard) {
                return response()->json([
                    'success' => false,
                    'message' => 'Payments board not found.'
                ], 404);
            }

            // Find the client payment record
            $clientPayment = $this->clientsPaymentsRepository->findClientPayment($clientPaymentId);
            if (!$clientPayment) {
                return response()->json([
                    'success' => false,
                    'message' => 'Client payment record not found.'
                ], 404);
            }

            // Verify the client payment belongs to this board
            if ($clientPayment->payments_board_id != $boardId) {
                return response()->json([
                    'success' => false,
                    'message' => 'Client payment does not belong to this payments board.'
                ], 400);
            }

            // Remove the client from the payments board
            $result = $this->clientsPaymentsRepository->deleteClientPayment($clientPaymentId);

            if ($result instanceof SuccessData) {
                return response()->json([
                    'success' => true,
                    'message' => 'Client removed from payments board successfully.'
                ]);
            } else {
                return response()->json([
                    'success' => false,
                    'message' => 'Failed to remove client from payments board.',
                    'errors' => $result->getErrorMessages()
                ], 400);
            }

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'An error occurred while removing client: ' . $e->getMessage()
            ], 500);
        }
    }

    public function saveRemarks(Request $request, $id)
    {
        try {
            // Validate the request
            $request->validate([
                'remarks' => 'nullable|string|max:1000'
            ]);

            // Find the client payment record
            $clientPayment = $this->clientsPaymentsRepository->findClientPayment($id);

            if (!$clientPayment) {
                return response()->json([
                    'success' => false,
                    'message' => 'Client payment record not found.'
                ], 404);
            }

            // Update the remarks
            $result = $this->clientsPaymentsRepository->updateClientPayment($id, [
                'remarks' => $request->input('remarks', '')
            ]);

            if ($result instanceof \App\Classes\SuccessData) {
                return response()->json([
                    'success' => true,
                    'message' => 'Remarks saved successfully.',
                    'remarks' => $request->input('remarks', '')
                ]);
            } else {
                return response()->json([
                    'success' => false,
                    'message' => 'Failed to save remarks.',
                    'errors' => $result->getErrorMessages()
                ], 400);
            }

        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed.',
                'errors' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'An error occurred while saving remarks: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Delete a payments board
     */
    public function destroy($id)
    {
        try {
            // Delete the payments board using repository
            $result = $this->paymentsBoardRepository->deletePaymentsBoard($id);

            if ($result instanceof SuccessData) {
                return redirect()->route('reports.payments.board')
                    ->with('success', 'Payments board deleted successfully.');
            } else {
                return redirect()->route('reports.payments.board')
                    ->with('error', 'Failed to delete payments board.');
            }
        } catch (\Exception $e) {
            return redirect()->route('reports.payments.board')
                ->with('error', 'An error occurred while deleting the payments board.');
        }
    }

    /**
     * Recalculate client payment data
     */
    public function recalculateClientPayment(Request $request, $id)
    {
        try {
            // Find the client payment record
            $clientPayment = $this->clientsPaymentsRepository->findClientPayment($id);

            if (!$clientPayment) {
                return response()->json([
                    'success' => false,
                    'message' => 'Client payment record not found.'
                ], 404);
            }

            // Get the payments board to extract date range
            $paymentsBoard = $this->paymentsBoardRepository->findPaymentsBoard($clientPayment->payments_board_id);
            if (!$paymentsBoard) {
                return response()->json([
                    'success' => false,
                    'message' => 'Payments board not found.'
                ], 404);
            }

            $startDate = $paymentsBoard->start_date;
            $endDate = $paymentsBoard->end_date;
            $clientId = $clientPayment->client_id;
            $companyProfileId = session('company_profile.id');

            // Get fresh account balance data
            $boardMonth = (int) $paymentsBoard->month;
            $boardYear = (int) $paymentsBoard->year;

            // First try to get account balance for the exact board period
            $accountBalance = $this->accountBalanceRepository->getAccountBalanceForPeriod(
                $clientId,
                $companyProfileId,
                $boardMonth,
                $boardYear
            );

            // If no specific balance found, get the most recent one for this client
            if (!$accountBalance) {
                $clientBalances = $this->accountBalanceRepository->getClientAccountBalances($clientId, $companyProfileId);
                $accountBalance = $clientBalances->first();
            }

            // Set previous balance (default to 0.00 if no balance found)
            $previousBalance = $accountBalance ? $accountBalance->opening_balance : 0.00;

            // Recalculate transaction amounts using the private method from ClientsPaymentsRepository
            // We need to expose this calculation or create a new method
            $transactionAmounts = $this->calculateClientTransactionAmounts($clientId, $startDate, $endDate);

            // Update the client payment with recalculated data
            $updatedData = [
                'cash_sales' => $transactionAmounts['cash_sales'],
                'pre_gst_amount' => $transactionAmounts['pre_gst_amount'],
                'gst_amount' => $transactionAmounts['gst_amount'],
                'tds' => $transactionAmounts['tds'],
                'subtotal_amount' => $transactionAmounts['subtotal_amount'],
                'previous_balance' => $previousBalance,
                'total_amount' => $transactionAmounts['total_amount'] + $previousBalance,
                'updated_at' => now(),
            ];

            $result = $this->clientsPaymentsRepository->updateClientPayment($id, $updatedData);

            if ($result instanceof \App\Classes\SuccessData) {
                // Update the payments board totals after recalculation
                $this->updatePaymentsBoardTotals($clientPayment->payments_board_id);

                return response()->json([
                    'success' => true,
                    'message' => 'Client payment recalculated successfully.',
                    'data' => $result->data
                ]);
            } else {
                return response()->json([
                    'success' => false,
                    'message' => 'Failed to update client payment.',
                    'errors' => $result->getErrorMessages()
                ], 400);
            }

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'An error occurred while recalculating client payment: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Calculate transaction amounts for a client within a specific date range
     * This method is exposed from the private method in ClientsPaymentsRepository
     */
    private function calculateClientTransactionAmounts(int $clientId, $startDate, $endDate): array
    {
        // Import Transaction model
        $transactionModel = new \App\Models\Transaction();

        // Get all sales transactions for this client in the date range
        $salesTransactions = $transactionModel::where('client_id', $clientId)
            ->where('transaction_type', 'sale')
            ->whereBetween('transaction_date', [$startDate, $endDate])
            ->get();

        // Get all payment transactions for this client in the date range
        $paymentTransactions = $transactionModel::where('client_id', $clientId)
            ->where('transaction_type', 'payment')
            ->whereBetween('transaction_date', [$startDate, $endDate])
            ->get();

        // Calculate cash sales (transactions with sales_type = 'cash')
        $cashSales = $salesTransactions->where('sales_type', 'cash')->sum('total_amount');

        // Calculate invoice sales amounts (transactions with sales_type = 'invoice')
        $invoiceSales = $salesTransactions->where('sales_type', 'invoice');

        // For invoice sales, calculate pre-GST amount, GST amount, and TDS
        $preGstAmount = $invoiceSales->sum('base_amount');
        $gstAmount = $invoiceSales->sum('tax_amount');
        $tdsAmount = $invoiceSales->sum('tds');

        // Calculate subtotal (base amount + tax amount - TDS)
        $subtotalAmount = $preGstAmount + $gstAmount - $tdsAmount;

        // Calculate total amount from all sales
        $totalSalesAmount = $salesTransactions->sum('total_amount');

        // Calculate total payments received
        $totalPaidAmount = $paymentTransactions->sum('total_amount');

        return [
            'cash_sales' => $cashSales,
            'pre_gst_amount' => $preGstAmount,
            'gst_amount' => $gstAmount,
            'tds' => $tdsAmount,
            'subtotal_amount' => $subtotalAmount,
            'total_amount' => $totalSalesAmount,
            'paid_amount' => $totalPaidAmount,
        ];
    }

    /**
     * Update payments board totals based on aggregated client payments data
     */
    private function updatePaymentsBoardTotals(int $boardId): void
    {
        try {
            // Get the payments board
            $paymentsBoard = $this->paymentsBoardRepository->findPaymentsBoard($boardId);
            if (!$paymentsBoard) {
                return;
            }

            // Calculate totals from all client payments for this board
            $payments = $this->clientsPaymentsRepository->getPaymentsByBoard($boardId);

            $totals = [
                'total_cash_sales' => $payments->sum('cash_sales'),
                'total_pre_gst_amount' => $payments->sum('pre_gst_amount'),
                'total_gst_amount' => $payments->sum('gst_amount'),
                'total_tds' => $payments->sum('tds'),
                'total_subtotal_amount' => $payments->sum('subtotal_amount'),
                'total_previous_balance' => $payments->sum('previous_balance'),
                'total_amount' => $payments->sum('total_amount'),
                'total_paid_amount' => $payments->sum('paid_amount'),
                'total_outstanding' => $payments->sum(function ($payment) {
                    return $payment->total_amount - $payment->paid_amount;
                }),
                'clients_count' => $payments->count(),
            ];

            // Update the payments board with calculated totals
            $this->paymentsBoardRepository->updatePaymentsBoard($boardId, $totals);

        } catch (\Exception $e) {
            // Log error but don't fail the operation
        }
    }

    /**
     * Finalize payments board and create/update account balances for the following month
     */
    public function finalize(Request $request, $id)
    {
        try {
            // Find the payments board by ID
            $paymentsBoard = $this->paymentsBoardRepository->findPaymentsBoard($id);
            if (!$paymentsBoard) {
                return response()->json([
                    'success' => false,
                    'message' => 'Payments board not found.'
                ], 404);
            }

            // Get all client payments for this board
            $clientsPayments = $this->clientsPaymentsRepository->getPaymentsByBoard($id);

            if ($clientsPayments->isEmpty()) {
                return response()->json([
                    'success' => false,
                    'message' => 'No client payments found for this board.'
                ], 400);
            }

            $boardMonthYear = explode('-', $paymentsBoard->board_month_year);
            // Calculate next month and year
            $currentMonth = (int) $boardMonthYear[0];
            $currentYear = (int) $boardMonthYear[1];

            $nextMonth = $currentMonth + 1;
            $nextYear = $currentYear;

            if ($nextMonth > 12) {
                $nextMonth = 1;
                $nextYear += 1;
            }

            $companyProfileId = session('company_profile.id');
            $finalizedClients = [];

            // Process each client payment and create/update account balance for next month
            foreach ($clientsPayments as $clientPayment) {
                $clientId = $clientPayment->client_id;
                $balanceAmount = $clientPayment->total_amount - $clientPayment->paid_amount;

                // Use the correct repository method to store or update account balance
                $result = $this->accountBalanceRepository->storeOrUpdateAccountBalance(
                    $companyProfileId,
                    $clientId,
                    $nextMonth,
                    $nextYear,
                    $balanceAmount
                );

                if ($result instanceof \App\Classes\SuccessData) {
                    // Check if it was an update or create by checking if balance already existed
                    $existingBalance = $this->accountBalanceRepository->getAccountBalanceForPeriod(
                        $clientId,
                        $companyProfileId,
                        $nextMonth,
                        $nextYear
                    );

                    $finalizedClients[] = [
                        'client_id' => $clientId,
                        'client_name' => $clientPayment->client->client_name,
                        'balance_amount' => $balanceAmount,
                        'action' => $existingBalance ? 'created/updated' : 'created'
                    ];
                }
            }

            // Mark the payments board as finalized (if there's a status field)
            $this->paymentsBoardRepository->updatePaymentsBoard($id, [
                'is_finalized' => true,
                'finalized_at' => now(),
                'updated_at' => now()
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Payments board finalized successfully. Account balances for ' .
                           $this->getMonthName($nextMonth) . ' ' . $nextYear . ' have been created/updated.',
                'data' => [
                    'finalized_clients_count' => count($finalizedClients),
                    'next_month' => $nextMonth,
                    'next_year' => $nextYear,
                    'next_period' => $this->getMonthName($nextMonth) . ' ' . $nextYear,
                    'clients' => $finalizedClients
                ]
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'An error occurred while finalizing the payments board: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get month name from month number
     */
    private function getMonthName(int $month): string
    {
        $months = [
            1 => 'January', 2 => 'February', 3 => 'March', 4 => 'April',
            5 => 'May', 6 => 'June', 7 => 'July', 8 => 'August',
            9 => 'September', 10 => 'October', 11 => 'November', 12 => 'December'
        ];

        return $months[$month] ?? 'Unknown';
    }
}
