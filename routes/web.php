<?php

use App\Http\Controllers\{Clients\ClientsController,
    ProfileController,
    Inventory\ItemsController,
    Transactions\PaymentsController,
    Transactions\SalesController,
    Transactions\ExpensesController,
    Transactions\LedgerController,
    Employee\EmployeeController,
    Employee\AttendanceController as AttendanceController,
    Reports\PaymentsBoardController
};
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
    Route::get('/dashboard', function () { return view('dashboard'); })->name('dashboard');
    Route::get('/products', [ItemsController::class, 'index'])->name('items.index');
    // Items management routes
    Route::get('/items/create', [ItemsController::class, 'create'])->name('items.create');
    Route::post('/items/store', [ItemsController::class, 'store'])->name('items.store');
    Route::get('/items/edit/{id}', [ItemsController::class, 'edit'])->name('items.edit');
    Route::put('/items/update/{id}', [ItemsController::class, 'update'])->name('items.update');
    // Client management Routes
    Route::get('/clients', [ClientsController::class, 'index'])->name('clients.index');
    Route::get('/clients/create', [ClientsController::class, 'create'])->name('clients.create');
    Route::post('/clients/store', [ClientsController::class, 'store'])->name('clients.store');
    Route::get('/clients/edit/{id}', [ClientsController::class, 'edit'])->name('clients.edit');
    Route::put('/clients/update/{id}', [ClientsController::class, 'update'])->name('clients.update');
    Route::delete('/clients/{id}', [ClientsController::class, 'destroy'])->name('clients.destroy');
    Route::post('/clients/fetch-for-board', [ClientsController::class, 'fetchForBoard'])->name('clients.fetch-for-board');

    // Sales
    Route::get('/sales', [SalesController::class, 'index'])->name('sales.index');
    Route::get('/sales/create', [SalesController::class, 'create'])->name('sales.create');
    Route::post('/sales/store', [SalesController::class, 'store'])->name('sales.store');
    Route::get('/sales/edit/{id}', [SalesController::class, 'edit'])->name('sales.edit');
    Route::put('/sales/update/{id}', [SalesController::class, 'update'])->name('sales.update');
    Route::delete('/sales/delete/{id}', [SalesController::class, 'destroy'])->name('sales.destroy');

    // Expenses
    Route::get('/expenses', [ExpensesController::class, 'index'])->name('expenses.index');
    Route::get('/expenses/create', [ExpensesController::class, 'create'])->name('expenses.create');
    Route::post('/expenses/store', [ExpensesController::class, 'store'])->name('expenses.store');
    Route::get('/expenses/edit/{id}', [ExpensesController::class, 'edit'])->name('expenses.edit');
    Route::put('/expenses/update/{id}', [ExpensesController::class, 'update'])->name('expenses.update');
    Route::delete('/expenses/delete/{id}', [ExpensesController::class, 'destroy'])->name('expenses.destroy');
    Route::post('/expenses/duplicate/{id}', [ExpensesController::class, 'duplicate'])->name('expenses.duplicate');

    // Payments
    Route::get('/payments', [PaymentsController::class, 'index'])->name('payments.index');
    Route::get('/payments/create', [PaymentsController::class, 'create'])->name('payments.create');
    Route::post('/payments/store', [PaymentsController::class, 'store'])->name('payments.store');
    Route::get('/payments/edit/{id}', [PaymentsController::class, 'edit'])->name('payments.edit');
    Route::put('/payments/update/{id}', [PaymentsController::class, 'update'])->name('payments.update');
    Route::delete('/payments/delete/{id}', [PaymentsController::class, 'destroy'])->name('payments.destroy');
    Route::get('/payments/details/{id}', [PaymentsController::class, 'details'])->name('payments.show');

    // Ledgers
    Route::get('/ledger', [LedgerController::class, 'index'])->name('ledger.index');

    // Employees
    Route::get('/employees', [EmployeeController::class, 'index'])->name('employees.index');
    Route::get('/employees/create', [EmployeeController::class, 'create'])->name('employees.create');
    Route::post('/employees/store', [EmployeeController::class, 'store'])->name('employees.store');
    Route::get('/employees/edit/{id}', [EmployeeController::class, 'edit'])->name('employees.edit');
    Route::put('/employees/update/{id}', [EmployeeController::class, 'update'])->name('employees.update');
    Route::delete('/employees/delete/{id}', [EmployeeController::class, 'destroy'])->name('employees.destroy');
    Route::get('/employees/{id}/salary', [EmployeeController::class, 'salary'])->name('employees.salary');
    Route::put('/employees/{id}/salary', [EmployeeController::class, 'updateSalary'])->name('employees.salary.update');
    Route::post('/employees/{id}/pay-advance', [EmployeeController::class, 'payAdvance'])->name('employees.pay-advance');

    // Attendance
    Route::get('/attendance', [AttendanceController::class, 'index'])->name('attendance.index');
    Route::get('/attendance/create', [AttendanceController::class, 'create'])->name('attendance.create');
    Route::post('/attendance/store', [AttendanceController::class, 'store'])->name('attendance.store');
    Route::get('/attendance/{id}', [AttendanceController::class, 'show'])->name('attendance.show');
    Route::get('/attendance/{id}/edit', [AttendanceController::class, 'edit'])->name('attendance.edit');
    Route::get('/attendance/{attendance}/getemployees', [AttendanceController::class, 'getEmployeesForAttendance'])->name('attendance.getemployees');
    Route::post('/attendance/{attendance}/addemployees', [AttendanceController::class, 'addEmployeesToAttendance'])->name('attendance.addemployees');
    Route::delete('/attendance/{attendance}/employee/{employeeAttendanceId}', [AttendanceController::class, 'removeEmployeeFromAttendance'])->name('attendance.removeemployee');
    Route::put('/attendance/{id}/update', [AttendanceController::class, 'update'])->name('attendance.update');
    Route::delete('/attendance/{id}', [AttendanceController::class, 'destroy'])->name('attendance.destroy');
    Route::post('/attendance/{recordId}/pay', [AttendanceController::class, 'processSalaryPayment'])->name('attendance.pay');

    // Reports
    Route::get('/reports/payments-board', [PaymentsBoardController::class, 'index'])->name('reports.payments.board');
    Route::get('/reports/payments-board/create', [PaymentsBoardController::class, 'create'])->name('reports.payments.board.create');
    Route::post('/reports/payments-board/store', [PaymentsBoardController::class, 'store'])->name('reports.payments.board.store');
    Route::get('/reports/payments-board/{id}', [PaymentsBoardController::class, 'show'])->name('reports.payments.board.show');
    Route::get('/reports/payments-board/{id}/edit', [PaymentsBoardController::class, 'edit'])->name('reports.payments.board.edit');
    Route::post('/reports/payments-board/client-payment/{id}/remarks', [PaymentsBoardController::class, 'saveRemarks'])->name('reports.payments.board.save-remarks');
    Route::post('/reports/payments-board/{id}/add-clients', [PaymentsBoardController::class, 'addClients'])->name('reports.payments.board.add-clients');
    Route::delete('/reports/payments-board/{boardId}/remove-client/{clientPaymentId}', [PaymentsBoardController::class, 'removeClient'])->name('reports.payments.board.remove-client');
    Route::put('/reports/payments-board/{id}/update', [PaymentsBoardController::class, 'update'])->name('reports.payments.board.update');
    Route::delete('/reports/payments-board/{id}/delete', [PaymentsBoardController::class, 'destroy'])->name('reports.payments.board.delete');
    Route::post('/reports/payments-board/client-payment/{id}/recalculate', [PaymentsBoardController::class, 'recalculateClientPayment'])->name('reports.payments.board.recalculate-client');
    Route::post('/reports/payments-board/{id}/finalize', [PaymentsBoardController::class, 'finalize'])->name('reports.payments.board.finalize');
});

require __DIR__.'/auth.php';
