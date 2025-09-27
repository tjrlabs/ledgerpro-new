<x-layouts.app-layout>
    <div class="py-12">
        <div class="w-full mx-auto sm:px-6 lg:px-8">
            <div class="bg-white/80 backdrop-blur-md overflow-hidden shadow-xl sm:rounded-lg border border-gray-100">
                <div class="p-6 text-gray-900">
                    <div class="flex justify-between items-center mb-6">
                        <h1 class="text-2xl font-bold">
                            Attendance Details - {{ $attendance->attendance_month_year }}
                        </h1>
                        <a href="{{ route('attendance.index') }}" class="bg-gray-600 hover:bg-gray-700 text-white px-4 py-2 rounded-lg shadow-sm flex items-center">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                            </svg>
                            Back to Attendance
                        </a>
                    </div>

                    <!-- Summary Statistics Cards -->
                    <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-6">
                        <div class="bg-blue-50 p-4 rounded-lg border border-blue-200">
                            <div class="flex items-center">
                                <div class="p-2 bg-blue-100 rounded-full">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-blue-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
                                    </svg>
                                </div>
                                <div class="ml-4">
                                    <p class="text-sm font-medium text-blue-600">Total Employees</p>
                                    <p class="text-2xl font-bold text-blue-800">{{ $attendance->employee_count }}</p>
                                </div>
                            </div>
                        </div>

{{--                        <div class="bg-green-50 p-4 rounded-lg border border-green-200">--}}
{{--                            <div class="flex items-center">--}}
{{--                                <div class="p-2 bg-green-100 rounded-full">--}}
{{--                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-green-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">--}}
{{--                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3a4 4 0 118 0v4m-4 6v6m1-10.586a1 1 0 011.414 0L15 7a1 1 0 01.293.707V8a1 1 0 01-1 1H9a1 1 0 01-1-1v-.293A1 1 0 018.293 7L9 7.586A1 1 0 009 7z" />--}}
{{--                                    </svg>--}}
{{--                                </div>--}}
{{--                                <div class="ml-4">--}}
{{--                                    <p class="text-sm font-medium text-green-600">Working Days</p>--}}
{{--                                    <p class="text-2xl font-bold text-green-800">{{ $attendance->working_days }}</p>--}}
{{--                                </div>--}}
{{--                            </div>--}}
{{--                        </div>--}}

{{--                        <div class="bg-indigo-50 p-4 rounded-lg border border-indigo-200">--}}
{{--                            <div class="flex items-center">--}}
{{--                                <div class="p-2 bg-indigo-100 rounded-full">--}}
{{--                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-indigo-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">--}}
{{--                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />--}}
{{--                                    </svg>--}}
{{--                                </div>--}}
{{--                                <div class="ml-4">--}}
{{--                                    <p class="text-sm font-medium text-indigo-600">Total Salary Paid</p>--}}
{{--                                    <p class="text-2xl font-bold text-indigo-800">₹{{ number_format($attendance->total_salary_paid, 2) }}</p>--}}
{{--                                </div>--}}
{{--                            </div>--}}
{{--                        </div>--}}

{{--                        <div class="bg-yellow-50 p-4 rounded-lg border border-yellow-200">--}}
{{--                            <div class="flex items-center">--}}
{{--                                <div class="p-2 bg-yellow-100 rounded-full">--}}
{{--                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-yellow-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">--}}
{{--                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />--}}
{{--                                    </svg>--}}
{{--                                </div>--}}
{{--                                <div class="ml-4">--}}
{{--                                    <p class="text-sm font-medium text-yellow-600">Total OT Hours</p>--}}
{{--                                    <p class="text-2xl font-bold text-yellow-800">{{ $attendance->total_overtime_hours }}</p>--}}
{{--                                </div>--}}
{{--                            </div>--}}
{{--                        </div>--}}
                    </div>


                    <!-- Employee Attendance Details Table -->
                    @if($employeeAttendance->isNotEmpty())
                        <div class="bg-white rounded-lg border border-gray-200 overflow-hidden">
                            <div class="px-6 py-4 border-b border-gray-200">
                                <h3 class="text-lg font-semibold text-gray-900">Employee Attendance Details</h3>
                            </div>
                            <div class="overflow-x-auto">
                                <table class="min-w-full divide-y divide-gray-200">
                                    <thead class="bg-gray-50">
                                        <tr>
                                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Employee</th>
                                            <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Duty</th>
                                            <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Overtime</th>
{{--                                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Basic Salary</th>--}}
{{--                                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">OT Amount</th>--}}
                                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Bonus</th>
                                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Total Salary</th>
                                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Advance</th>
                                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Previous Balance</th>
                                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Net Pay</th>
                                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Total Paid</th>
                                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Balance C/F</th>
                                            <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                                        </tr>
                                    </thead>
                                    <tbody class="bg-white divide-y divide-gray-200">
                                        @php $sumAdvanceCf = 0; @endphp
                                        @foreach($employeeAttendance as $record)
                                            <tr class="hover:bg-gray-50">
                                                <td class="px-6 py-4 whitespace-nowrap">
                                                    <div>
                                                        <div class="text-sm font-medium text-gray-900">{{ $record->employee->first_name }} {{ $record->employee->last_name }}</div>
                                                        <div class="text-sm text-gray-500">ID: {{ $record->employee->id }}</div>
                                                    </div>
                                                </td>
                                                <td class="px-6 py-4 whitespace-nowrap">
                                                    <div class="flex justify-center flex-wrap gap-1 w-full">
                                                        <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full bg-green-100 text-green-800">
                                                            {{ $record->present_days }}/{{ $attendance->total_days }}
                                                        </span>
                                                        <br>
                                                        <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full bg-blue-100 text-blue-800">
                                                        ₹{{ number_format($record->working_days_salary, 2) }}
                                                        </span>
                                                    </div>
                                                </td>
                                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 ">
                                                    <div class="flex justify-center flex-wrap gap-1 w-full">
                                                        <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full bg-green-100 text-green-800">{{ $record->overtime_hours }} hours
                                                        </span>
                                                        <br>
                                                        <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full bg-blue-100 text-blue-800">
                                                        ₹{{ number_format($record->overtime_amount, 2) }}
                                                        </span>
                                                    </div>
                                                </td>
{{--                                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">₹{{ number_format($record->working_days_salary, 2) }}</td>--}}
{{--                                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">₹{{ number_format($record->overtime_amount, 2) }}</td>--}}
                                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">₹{{ number_format($record->bonus_amount, 2) }}</td>
                                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">₹{{ number_format($record->total_salary, 2) }}</td>
                                                <td class="px-6 py-4 whitespace-nowrap text-sm">
                                                    @if($record->advance_deducted > 0)
                                                        <span class="text-red-600">₹{{ number_format($record->advance_deducted, 2) }}</span>
                                                    @else
                                                        <span class="text-gray-400">-</span>
                                                    @endif
                                                </td>
                                                <td class="px-6 py-4 whitespace-nowrap text-sm">
                                                    @if($record->previous_balance > 0)
                                                        <span class="text-red-600">₹{{ number_format($record->previous_balance, 2) }}</span>
                                                    @else
                                                        <span class="text-gray-400">-</span>
                                                    @endif
                                                </td>
                                                <td class="px-6 py-4 whitespace-nowrap text-sm font-semibold text-gray-900">₹{{ number_format($record->net_salary_after_deductions, 2) }}</td>
                                                <td class="px-6 py-4 whitespace-nowrap text-sm font-semibold text-gray-900">₹{{ number_format($record->paid_amount, 2) }}</td>
                                                <td class="px-6 py-4 whitespace-nowrap text-sm">
                                                    @if($record->balance_carry_forward != 0)
                                                        <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full {{ $record->balance_carry_forward > 0 ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                                                            ₹{{ number_format($record->balance_carry_forward, 2) }}
                                                        </span>
                                                    @else
                                                        <span class="text-gray-400">-</span>
                                                    @endif
                                                </td>
                                                <td class="px-6 py-4 whitespace-nowrap text-center">
                                                    <div class="flex flex-col items-center space-y-2">
                                                        @if($record->paid_amount >= $record->net_salary_after_deductions)
                                                            <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full bg-green-100 text-green-800">Paid</span>
                                                        @else
                                                            <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full bg-yellow-100 text-yellow-800">Pending</span>
                                                            <button type="button"
                                                                    class="pay-button bg-blue-600 hover:bg-blue-700 text-white font-semibold text-xs border-none cursor-pointer px-3 py-1 rounded transition-colors"
                                                                    data-record-id="{{ $record->id }}"
                                                                    data-employee-name="{{ $record->employee->first_name }} {{ $record->employee->last_name }}"
                                                                    data-net-salary="{{ $record->net_salary_after_deductions }}"
                                                                    data-paid-amount="{{ $record->paid_amount }}"
                                                                    data-remaining-amount="{{ $record->net_salary_after_deductions - $record->paid_amount }}"
                                                                    data-advance-due="{{ $record->employee->advance_due }}"
                                                                    data-advance-deducted="{{ $record->advance_deducted }}"
                                                                    data-balance-cf="{{ $record->balance_carry_forward }}">
                                                                Pay
                                                            </button>
                                                        @endif
                                                    </div>
                                                </td>
                                            </tr>
                                            @if($record->remarks)
                                                <tr class="bg-gray-50">
                                                    <td colspan="13" class="px-6 py-3">
                                                        <div class="text-sm text-gray-600">
                                                            <span class="font-medium">Remarks:</span> {{ $record->remarks }}
                                                        </div>
                                                    </td>
                                                </tr>
                                            @endif
                                        @endforeach
                                    </tbody>
                                    <tfoot class="bg-gray-100">
                                        <tr>
                                            <th class="px-6 py-4 text-left text-sm font-semibold text-gray-900">Totals</th>
                                            <th class="px-6 py-4 text-center text-sm font-semibold text-gray-900">{{ $employeeAttendance->sum('present_days') }} days <br> (₹{{ number_format($employeeAttendance->sum('working_days_salary'), 2) }})</th>
                                            <th class="px-6 py-4 text-center text-sm font-semibold text-gray-900">{{ $employeeAttendance->sum('overtime_hours') }} hours <br> ₹{{ number_format($employeeAttendance->sum('overtime_amount'), 2) }}</th>
                                            <th class="px-6 py-4 text-left text-sm font-semibold text-gray-900">₹{{ number_format($employeeAttendance->sum('bonus_amount'), 2) }}</th>
                                            <th class="px-6 py-4 text-left text-sm font-semibold text-gray-900">₹{{ number_format($employeeAttendance->sum('total_salary'), 2) }}</th>
                                            <th class="px-6 py-4 text-left text-sm font-semibold text-gray-900">₹{{ number_format($employeeAttendance->sum('advance_deducted'), 2) }}</th>
                                            <th class="px-6 py-4 text-left text-sm font-semibold text-gray-900">₹{{ number_format($employeeAttendance->sum('previous_balance'), 2) }}</th>
                                            <th class="px-6 py-4 text-left text-sm font-semibold text-gray-900">₹{{ number_format($employeeAttendance->sum('net_salary_after_deductions'), 2) }}</th>
                                            <th class="px-6 py-4 text-left text-sm font-semibold text-gray-900">₹{{ number_format($employeeAttendance->sum('paid_amount'), 2) }}</th>
                                            <th class="px-6 py-4 text-left text-sm font-semibold text-gray-900">₹{{ number_format($employeeAttendance->sum('balance_carry_forward'), 2) }}</th>
                                            <th class="px-6 py-4 text-left text-sm font-semibold text-gray-900">-</th>
                                        </tr>
                                    </tfoot>
                                </table>
                            </div>
                        </div>
                    @else
                        <div class="bg-blue-50 border border-blue-200 rounded-lg p-6 text-center">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-12 w-12 text-blue-400 mx-auto mb-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                            </svg>
                            <h3 class="text-lg font-medium text-blue-900 mb-2">No Employee Records Found</h3>
                            <p class="text-blue-700">No employee attendance data found for this period.</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <!-- Payment Modal -->
    <div id="payment-modal" class="hidden fixed inset-0 bg-gray-500 bg-opacity-75 flex items-center justify-center z-50">
        <div class="bg-white rounded-lg shadow-xl p-6 w-full max-w-md mx-4 transform transition-all">
            <!-- Modal Header -->
            <div class="flex justify-between items-center mb-6">
                <h2 class="text-xl font-semibold text-gray-900">Process Payment</h2>
                <button type="button" id="close-payment-modal" class="text-gray-400 hover:text-gray-600 transition-colors">
                    <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                </button>
            </div>

            <!-- Employee Information -->
            <div class="mb-6 p-4 bg-gradient-to-r from-blue-50 to-indigo-50 rounded-lg border border-blue-200">
                <div class="flex items-center mb-3">
                    <div class="w-10 h-10 bg-blue-100 rounded-full flex items-center justify-center mr-3">
                        <span id="modal-employee-initials" class="text-blue-600 font-semibold text-sm"></span>
                    </div>
                    <div>
                        <h3 id="modal-employee-name" class="font-medium text-gray-900"></h3>
                        <p class="text-sm text-gray-600">Employee Payment</p>
                    </div>
                </div>

                <div class="grid grid-cols-3 gap-4 text-sm mb-4">
                    <div>
                        <p class="text-gray-500">Net Salary</p>
                        <p id="modal-net-salary" class="font-medium text-gray-900"></p>
                    </div>
                    <div>
                        <p class="text-gray-500">Paid Already</p>
                        <p id="modal-paid-amount" class="font-medium text-green-600"></p>
                    </div>
                    <div>
                        <p class="text-gray-500">Remaining</p>
                        <p id="modal-remaining-amount" class="font-medium text-red-600"></p>
                    </div>
                </div>

                <!-- New Carry Forward Section -->
                <div class="border-t border-gray-200 pt-4">
                    <h4 class="text-sm font-medium text-gray-700 mb-3">After Payment Projections</h4>
                    <div class="text-sm">
                        <div>
                            <p class="text-gray-500">C/F Balance</p>
                            <p id="modal-cf-balance" class="font-medium text-blue-600">₹0.00</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Payment Form -->
            <form id="payment-form" method="POST">
                @csrf
                <input type="hidden" id="record-id" name="record_id">

                <!-- Alert Messages Container -->
                <div id="payment-alerts" class="mb-4 hidden">
                    <div id="payment-success" class="hidden p-4 bg-green-50 border-l-4 border-green-500 text-green-700 rounded-md">
                        <div class="flex items-center">
                            <svg class="h-5 w-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                            <span id="payment-success-text"></span>
                        </div>
                    </div>

                    <div id="payment-error" class="hidden p-4 bg-red-50 border-l-4 border-red-500 text-red-700 rounded-md">
                        <div class="flex items-center">
                            <svg class="h-5 w-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                            <span id="payment-error-text"></span>
                        </div>
                    </div>
                </div>

                <!-- Amount Paid Field -->
                <div class="mb-6">
                    <label for="amount-paid" class="block text-sm font-medium text-gray-700 mb-2">
                        Amount Paid (₹) <span class="text-red-500">*</span>
                    </label>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <span class="text-gray-500 text-sm">₹</span>
                        </div>
                        <input type="number"
                               id="amount-paid"
                               name="amount_paid"
                               step="0.01"
                               min="0.01"
                               placeholder="0.00"
                               class="w-full pl-8 pr-3 py-3 border border-gray-300 rounded-lg shadow-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors"
                               required>
                    </div>
                    <p class="mt-1 text-xs text-gray-500">Enter the amount being paid to the employee</p>
                </div>

                <!-- Form Actions -->
                <div class="flex justify-end space-x-3">
                    <button type="button"
                            id="cancel-payment"
                            class="px-6 py-2 text-sm font-medium text-gray-700 bg-gray-100 border border-gray-300 rounded-lg hover:bg-gray-200 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-gray-500 transition-colors">
                        Cancel
                    </button>
                    <button type="submit"
                            id="save-payment"
                            class="px-6 py-2 text-sm font-medium text-white bg-gradient-to-r from-blue-500 to-blue-600 hover:from-blue-600 hover:to-blue-700 border border-transparent rounded-lg focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 shadow-sm transition-all transform hover:scale-105">
                        <svg class="h-4 w-4 mr-2 inline" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                        </svg>
                        Save Payment
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- JavaScript for Modal -->
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const modal = document.getElementById('payment-modal');
            const payButtons = document.querySelectorAll('.pay-button');
            const closeButtons = document.querySelectorAll('#close-payment-modal, #cancel-payment');
            const form = document.getElementById('payment-form');

            // Show alert function
            function showAlert(type, message) {
                document.getElementById('payment-alerts').classList.remove('hidden');
                document.getElementById('payment-success').classList.add('hidden');
                document.getElementById('payment-error').classList.add('hidden');

                if (type === 'success') {
                    document.getElementById('payment-success-text').textContent = message;
                    document.getElementById('payment-success').classList.remove('hidden');
                } else if (type === 'error') {
                    document.getElementById('payment-error-text').textContent = message;
                    document.getElementById('payment-error').classList.remove('hidden');
                }
            }

            // Hide alerts function
            function hideAlerts() {
                document.getElementById('payment-alerts').classList.add('hidden');
                document.getElementById('payment-success').classList.add('hidden');
                document.getElementById('payment-error').classList.add('hidden');
            }

            // Reset modal function
            function resetModal() {
                form.reset();
                hideAlerts();
                document.getElementById('record-id').value = '';
            }

            // Open modal
            payButtons.forEach(button => {
                button.addEventListener('click', function() {
                    const recordId = this.dataset.recordId;
                    const employeeName = this.dataset.employeeName;
                    const netSalary = parseFloat(this.dataset.netSalary);
                    const paidAmount = parseFloat(this.dataset.paidAmount);
                    const remainingAmount = parseFloat(this.dataset.remainingAmount);
                    const advanceDue = parseFloat(this.dataset.advanceDue);
                    const advanceDeducted = parseFloat(this.dataset.advanceDeducted);
                    const currentBalanceCf = parseFloat(this.dataset.balanceCf);

                    // Store these values globally for calculations
                    window.modalData = {
                        netSalary,
                        originalPaidAmount: paidAmount,
                        advanceDue,
                        advanceDeducted,
                        currentBalanceCf
                    };

                    // Populate modal data
                    document.getElementById('record-id').value = recordId;
                    document.getElementById('modal-employee-name').textContent = employeeName;
                    document.getElementById('modal-employee-initials').textContent =
                        employeeName.split(' ').map(n => n[0]).join('').toUpperCase();
                    document.getElementById('modal-net-salary').textContent = '₹' + netSalary.toLocaleString('en-IN', {minimumFractionDigits: 2});
                    document.getElementById('modal-paid-amount').textContent = '₹' + paidAmount.toLocaleString('en-IN', {minimumFractionDigits: 2});
                    document.getElementById('modal-remaining-amount').textContent = '₹' + remainingAmount.toLocaleString('en-IN', {minimumFractionDigits: 2});

                    // Set max amount and default value
                    const amountInput = document.getElementById('amount-paid');
                    //amountInput.max = remainingAmount;
                    amountInput.value = remainingAmount.toFixed(2);

                    // Calculate and display carry forward projections
                    calculateProjections();

                    // Show modal
                    modal.classList.remove('hidden');
                });
            });

            // Close modal
            closeButtons.forEach(button => {
                button.addEventListener('click', function() {
                    resetModal();
                    modal.classList.add('hidden');
                });
            });

            // Close modal on backdrop click
            modal.addEventListener('click', function(e) {
                if (e.target === modal) {
                    resetModal();
                    modal.classList.add('hidden');
                }
            });

            // Handle form submission
            form.addEventListener('submit', function(e) {
                e.preventDefault();

                const submitButton = document.getElementById('save-payment');
                const originalText = submitButton.innerHTML;
                const recordId = document.getElementById('record-id').value;
                const amountPaid = document.getElementById('amount-paid').value;

                // Show loading state
                submitButton.disabled = true;
                submitButton.innerHTML = '<svg class="animate-spin h-4 w-4 mr-2 inline" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path></svg>Processing...';

                // Hide previous alerts
                hideAlerts();

                // Submit via fetch
                fetch(`/attendance/${recordId}/pay`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                        'Accept': 'application/json'
                    },
                    body: JSON.stringify({
                        amount_paid: parseFloat(amountPaid),
                        record_id: recordId
                    })
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        showAlert('success', data.message || 'Payment processed successfully!');

                        // Reload page after 2 seconds
                        setTimeout(() => {
                            window.location.reload();
                        }, 2000);
                    } else {
                        showAlert('error', data.message || 'An error occurred while processing the payment.');
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    showAlert('error', 'An unexpected error occurred. Please try again.');
                })
                .finally(() => {
                    // Reset button state
                    submitButton.disabled = false;
                    submitButton.innerHTML = originalText;
                });
            });

            // Validate amount input
            document.getElementById('amount-paid').addEventListener('input', function() {
                const value = parseFloat(this.value);
                const max = parseFloat(this.max);

                if (value > max) {
                    this.setCustomValidity(`Amount cannot exceed ₹${max.toFixed(2)}`);
                } else if (value <= 0) {
                    this.setCustomValidity('Amount must be greater than 0');
                } else {
                    this.setCustomValidity('');
                }

                // Update remaining amount display
                const netSalary = window.modalData.netSalary;
                const newPaidAmount = window.modalData.originalPaidAmount + parseFloat(this.value || 0);
                const remainingAmount = netSalary - newPaidAmount;
                document.getElementById('modal-remaining-amount').textContent = '₹' + Math.max(0, remainingAmount).toLocaleString('en-IN', {minimumFractionDigits: 2});

                // Recalculate projections
                calculateProjections();
            });

            // Calculate and display carry forward projections
            function calculateProjections() {
                const cfBalanceElement = document.getElementById('modal-cf-balance');

                if (!window.modalData) return;

                const { netSalary, originalPaidAmount } = window.modalData;
                const currentPaymentAmount = parseFloat(document.getElementById('amount-paid').value) || 0;
                const totalPaidAmount = originalPaidAmount + currentPaymentAmount;

                // Calculate new balance carry forward based on payment
                let newBalanceCf = 0;

                if (totalPaidAmount > netSalary) {
                    // If overpayment, it becomes a balance carry forward (credit to employee)
                    newBalanceCf = totalPaidAmount - netSalary;
                }

                // Update the carry forward balance field with color coding
                cfBalanceElement.textContent = '₹' + newBalanceCf.toLocaleString('en-IN', {minimumFractionDigits: 2});

                // Update colors based on amount
                cfBalanceElement.className = `font-medium ${newBalanceCf > 0 ? 'text-green-600' : 'text-gray-400'}`;
            }
        });
    </script>
</x-layouts.app-layout>
