<?php

namespace App\Http\Controllers\Transactions;

use App\DTO\Expenses\ManageExpenseDTO;
use App\Http\Controllers\Controller;
use App\Repositories\TransactionRepository;
use App\Classes\ErrorData;
use Illuminate\Http\Request;
use Carbon\Carbon;

class ExpensesController extends Controller
{
    protected TransactionRepository $transactionRepository;

    public function __construct(TransactionRepository $transactionRepository)
    {
        $this->transactionRepository = $transactionRepository;
    }

    /**
     * Display a listing of expenses.
     */
    public function index(Request $request)
    {
        // Get date range from request or default to current month
        $dateRange = $request->date_range ?? 'current_month';

        // Calculate date range using repository method
        $dateRangeData = $this->transactionRepository->calculateDateRange(
            $dateRange,
            $request->start_date,
            $request->end_date
        );

        $startDate = $dateRangeData['start_date'];
        $endDate = $dateRangeData['end_date'];

        // Prepare filters for repository
        $filters = [
            'expense_type' => $request->expense_type,
            'paid' => $request->paid,
            'amount_from' => $request->amount_from,
            'amount_to' => $request->amount_to,
        ];

        // Add date range to filters if available
        if ($startDate && $endDate) {
            $filters['start_date'] = $startDate->format('Y-m-d');
            $filters['end_date'] = $endDate->format('Y-m-d');
        }

        // Get filtered expenses from repository
        $expenses = $this->transactionRepository->getFilteredExpenses($filters);

        // Calculate statistics
        $statistics = [
            'total_expenses' => $expenses->sum('total_amount'),
            'paid_expenses' => $expenses->where('paid', true)->sum('total_amount'),
            'unpaid_expenses' => $expenses->where('paid', false)->sum('total_amount'),
            'count' => $expenses->count()
        ];

        // Get form options (expense types)
        $formOptions = [
            'expense_types' => [
                'cash' => 'Cash',
                'invoice'  => 'Invoice',
            ]
        ];

        return view('pages.transactions.expenses.index', compact(
            'expenses',
            'statistics',
            'formOptions',
            'dateRange',
            'startDate',
            'endDate'
        ));
    }

    /**
     * Show the form for creating a new expense.
     */
    public function create()
    {
        // Get form options for expense creation
        $formOptions = [
            'expense_types' => [
                'cash' => 'Cash',
                'invoice'  => 'Invoice',
            ]
        ];

        // Set default date to current date
        $defaultDate = Carbon::now()->format('Y-m-d');

        return view('pages.transactions.expenses.create', compact(
            'formOptions',
            'defaultDate'
        ));
    }

    /**
     * Store a newly created expense in storage.
     */
    public function store(Request $request)
    {
        // Create a DTO from the request data
        $expenseDTO = ManageExpenseDTO::from($request->all());

        // Check if validation failed
        if ($expenseDTO instanceof ErrorData) {
            return back()->withInput()
                ->withErrors($expenseDTO->getErrorMessages());
        }

        // Convert DTO to transaction data
        $transactionData = [
            'transaction_type' => 'expense',
            'transaction_date' => $expenseDTO->expenseDate,
            'sales_type' => $expenseDTO->expenseType,
            'base_amount' => $expenseDTO->baseAmount,
            'tax_amount' => $expenseDTO->taxAmount,
            'tax_rate' => $expenseDTO->taxRate,
            'total_amount' => $expenseDTO->totalAmount,
            'paid' => $expenseDTO->paid,
            'notes' => $expenseDTO->notes,
        ];

        $response = $this->transactionRepository->storeTransaction($transactionData);
        if ($response instanceof ErrorData) {
            return redirect()->back()->withErrors($response->getErrorMessages())->withInput();
        }

        return redirect()->route('expenses.index')->with('success', 'Expense created successfully!');
    }

    /**
     * Show the form for editing the specified expense.
     */
    public function edit($id)
    {
        // Get the expense by ID
        $expense = $this->transactionRepository->findTransaction($id);

        if (!$expense || $expense->transaction_type !== 'expense') {
            return redirect()->route('expenses.index')
                ->with('error', 'Expense not found.');
        }

        // Get form options for expense editing
        $formOptions = [
            'expense_types' => [
                'cash' => 'Cash',
                'invoice'  => 'Invoice',
            ]
        ];

        // Set default date to current date
        $defaultDate = Carbon::now()->format('Y-m-d');

        // Set edit mode flag
        $isEditing = true;

        return view('pages.transactions.expenses.create', compact(
            'expense',
            'formOptions',
            'defaultDate',
            'isEditing'
        ));
    }

    /**
     * Update the specified expense in storage.
     */
    public function update(Request $request, $id)
    {
        // Create a DTO from the request data
        $expenseDTO = ManageExpenseDTO::from($request->all());

        // Check if validation failed
        if ($expenseDTO instanceof ErrorData) {
            return back()->withInput()
                ->withErrors($expenseDTO->getErrorMessages());
        }

        // Convert DTO to transaction data
        $transactionData = [
            'transaction_type' => 'expense',
            'transaction_date' => $expenseDTO->expenseDate,
            'sales_type' => $expenseDTO->expenseType,
            'base_amount' => $expenseDTO->baseAmount,
            'tax_amount' => $expenseDTO->taxAmount,
            'tax_rate' => $expenseDTO->taxRate,
            'total_amount' => $expenseDTO->totalAmount,
            'paid' => $expenseDTO->paid,
            'notes' => $expenseDTO->notes,
        ];

        $response = $this->transactionRepository->updateTransaction($transactionData, $id);
        if ($response instanceof ErrorData) {
            return redirect()->back()->withErrors($response->getErrorMessages())->withInput();
        }

        return redirect()->route('expenses.index')->with('success', 'Expense updated successfully!');
    }

    /**
     * Remove the specified expense from storage.
     */
    public function destroy($id)
    {
        $response = $this->transactionRepository->deleteTransaction($id);

        if ($response instanceof ErrorData) {
            return redirect()->route('expenses.index')->with('error', 'Failed to delete expense.');
        }

        return redirect()->route('expenses.index')->with('success', 'Expense deleted successfully!');
    }
}
