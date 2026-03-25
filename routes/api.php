<?php
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\{
    Api\LoginController,
    Api\AttendanceController
};

Route::post('login', [LoginController::class, 'login']);
Route::middleware('auth:api')->group(function() {
    Route::post('/logout', [LoginController::class, 'api.logout']);
    Route::get('/user-profile', [LoginController::class, 'getUserProfile'])->name('api.user-profile');
    Route::get('/attendance', [AttendanceController::class, 'index'])->name('api.attendance.index');
    Route::get('/attendance/{id}', [AttendanceController::class, 'show'])->name('api.attendance.show');
    Route::post('/attendance/{recordId}/pay', [AttendanceController::class, 'processSalaryPayment'])->name('attendance.pay');
});
