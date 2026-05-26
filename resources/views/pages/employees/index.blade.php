<x-layouts.app-layout>
    <div class="py-12">
        <div class="w-full mx-auto sm:px-6 lg:px-8">
            <div class="page-shell">
                <div class="page-inner">
                    <div class="page-header">
                        <div>
                            <h1 class="page-title">Employee Management</h1>
                            <p class="page-subtitle">Manage employee records, salary details, and advance balances in one place.</p>
                        </div>
                        <a href="{{ route('employees.create') }}" class="btn-primary">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                            </svg>
                            Add New Employee
                        </a>
                    </div>

                    <!-- Statistics Cards -->
                    <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-6">
                        <div class="stat-card">
                            <div class="flex items-center">
                                <div class="stat-icon">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
                                    </svg>
                                </div>
                                <div class="ml-4">
                                    <p class="text-sm font-medium text-violet-200/80">Total Employees</p>
                                    <p class="text-2xl font-bold text-white">{{ $totalEmployees }}</p>
                                </div>
                            </div>
                        </div>

                        <div class="stat-card">
                            <div class="flex items-center">
                                <div class="stat-icon">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-purple-100" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                                    </svg>
                                </div>
                                <div class="ml-4">
                                    <p class="text-sm font-medium text-violet-200/80">Active Employees</p>
                                    <p class="text-2xl font-bold text-purple-100">{{ $activeEmployees }}</p>
                                </div>
                            </div>
                        </div>

                        <div class="stat-card">
                            <div class="flex items-center">
                                <div class="stat-icon !bg-rose-600">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z" />
                                    </svg>
                                </div>
                                <div class="ml-4">
                                    <p class="text-sm font-medium text-violet-200/80">Inactive Employees</p>
                                    <p class="text-2xl font-bold text-rose-200">{{ $inactiveEmployees }}</p>
                                </div>
                            </div>
                        </div>

                        <div class="stat-card">
                            <div class="flex items-center">
                                <div class="stat-icon !bg-fuchsia-600">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1" />
                                    </svg>
                                </div>
                                <div class="ml-4">
                                    <p class="text-sm font-medium text-violet-200/80">Monthly Payroll</p>
                                    <p class="text-2xl font-bold text-fuchsia-100">₹{{ number_format($totalMonthlySalary) }}</p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Filters Section -->
                    <div class="surface-muted mb-6">
                        <form action="{{ route('employees.index') }}" method="GET" class="grid grid-cols-1 md:grid-cols-6 gap-4">
                            <!-- Search -->
                            <div>
                                    <label for="search" class="mb-1 block text-sm font-medium text-violet-100">Search</label>
                                <input type="text" name="search" id="search" placeholder="Name or Mobile"
                                       value="{{ $currentFilters['search'] ?? '' }}"
                                        class="w-full rounded-lg border border-violet-500/25 bg-black/45 px-3 py-2.5 text-sm text-violet-50 placeholder:text-violet-300/45 shadow-sm shadow-black/30 focus:border-violet-400 focus:outline-hidden focus:ring-4 focus:ring-violet-500/20">
                            </div>

                            <!-- Status Filter -->
                            <div>
                                <label for="status" class="mb-1 block text-sm font-medium text-violet-100">Status</label>
                                <select name="status" id="status" class="w-full rounded-lg border border-violet-500/25 bg-black/45 px-3 py-2.5 text-sm text-violet-50 shadow-sm shadow-black/30 focus:border-violet-400 focus:outline-hidden focus:ring-4 focus:ring-violet-500/20">
                                    @foreach($statuses as $status)
                                        <option value="{{ $status }}" {{ ($currentFilters['status'] ?? 'active') == $status ? 'selected' : '' }}>
                                            {{ ucfirst($status) }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            <!-- Department Filter -->
                            <div>
                                <label for="department" class="mb-1 block text-sm font-medium text-violet-100">Department</label>
                                <select name="department" id="department" class="w-full rounded-lg border border-violet-500/25 bg-black/45 px-3 py-2.5 text-sm text-violet-50 shadow-sm shadow-black/30 focus:border-violet-400 focus:outline-hidden focus:ring-4 focus:ring-violet-500/20">
                                    <option value="">All Departments</option>
                                    @foreach($departments as $department)
                                        <option value="{{ $department }}" {{ ($currentFilters['department'] ?? '') == $department ? 'selected' : '' }}>
                                            {{ $department }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            <!-- Gender Filter -->
                            <div>
                                <label for="gender" class="mb-1 block text-sm font-medium text-violet-100">Gender</label>
                                <select name="gender" id="gender" class="w-full rounded-lg border border-violet-500/25 bg-black/45 px-3 py-2.5 text-sm text-violet-50 shadow-sm shadow-black/30 focus:border-violet-400 focus:outline-hidden focus:ring-4 focus:ring-violet-500/20">
                                    <option value="">All Genders</option>
                                    @foreach($genders as $gender)
                                        <option value="{{ $gender }}" {{ ($currentFilters['gender'] ?? '') == $gender ? 'selected' : '' }}>
                                            {{ ucfirst($gender) }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            <!-- Joining Date From -->
                            <div>
                                    <label for="joining_from" class="mb-1 block text-sm font-medium text-violet-100">Joined From</label>
                                <input type="text" name="joining_from" id="joining_from" placeholder="YYYY-MM-DD"
                                       value="{{ $currentFilters['joining_from'] ?? '' }}"
                                        class="datepicker w-full rounded-lg border border-violet-500/25 bg-black/45 px-3 py-2.5 text-sm text-violet-50 placeholder:text-violet-300/45 shadow-sm shadow-black/30 focus:border-violet-400 focus:outline-hidden focus:ring-4 focus:ring-violet-500/20">
                            </div>

                            <!-- Filter Buttons -->
                            <div class="flex items-end space-x-2">
                                <button type="submit" class="btn-primary">
                                    Apply Filters
                                </button>
                                <a href="{{ route('employees.index') }}" class="btn-secondary">
                                    Reset
                                </a>
                            </div>
                        </form>
                    </div>

                    @if(session('success'))
                    <div id="success-alert" class="alert-success mb-4">
                        <div class="flex items-center">
                            <svg class="h-5 w-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                            {{ session('success') }}
                        </div>
                        <button type="button" onclick="document.getElementById('success-alert').style.display='none'" class="text-green-700 hover:text-green-900">
                            <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                            </svg>
                        </button>
                    </div>
                    @endif

                    @if(session('error'))
                    <div id="error-alert" class="alert-error mb-4 flex justify-between items-center">
                        <div class="flex items-center">
                            <svg class="h-5 w-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                            {{ session('error') }}
                        </div>
                        <button type="button" onclick="document.getElementById('error-alert').style.display='none'" class="text-red-700 hover:text-red-900">
                            <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                            </svg>
                        </button>
                    </div>
                    @endif

                    <!-- Employees Table -->
                    <div class="app-table-wrap">
                        @php
                            $displayedTotalSalary = $employees->sum('salary');
                            $displayedTotalAdvanceDue = $employees->sum('advance_due');
                        @endphp
                        <table class="app-table">
                            <thead>
                                <tr>
                                    <th class="py-3 px-6 text-center">S.NO</th>
                                    <th class="py-3 px-6 text-left">Employee Name</th>
                                    <th class="py-3 px-6 text-center">Gender</th>
                                    <th class="py-3 px-6 text-center">Department</th>
                                    <th class="py-3 px-6 text-center">Salary (₹)</th>
                                    <th class="py-3 px-6 text-center">Advance Due (₹)</th>
                                    <th class="py-3 px-6 text-center">Joining Date</th>
                                    <th class="py-3 px-6 text-center">Status</th>
                                    <th class="py-3 px-6 text-center">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($employees as $index => $employee)
                                    <tr>
                                        <td class="py-3 px-6 text-center">{{ $index + 1 }}</td>
                                        <td class="py-3 px-6 text-left">
                                            <div class="flex items-center">
                                                <div class="mr-3">
                                                    <div class="flex h-8 w-8 items-center justify-center rounded-full bg-violet-500/15 ring-1 ring-violet-400/25">
                                                        <span class="text-sm font-semibold text-violet-100">
                                                            {{ strtoupper(substr($employee->first_name, 0, 1)) }}{{ strtoupper(substr($employee->last_name, 0, 1)) }}
                                                        </span>
                                                    </div>
                                                </div>
                                                <div>
                                                    <div class="font-medium text-white">{{ $employee->first_name }} {{ $employee->last_name }}</div>
                                                </div>
                                            </div>
                                        </td>
                                        <td class="py-3 px-6 text-center">
                                            <span class="badge {{ $employee->gender == 'male' ? 'badge-info' : 'badge-warning' }}">
                                                {{ ucfirst($employee->gender) }}
                                            </span>
                                        </td>
                                        <td class="py-3 px-6 text-center">
                                            <span class="badge badge-neutral">{{ $employee->department }}</span>
                                        </td>
                                        <td class="py-3 px-6 text-center">₹{{ number_format($employee->salary) }} <br/> ({{$employee->salary_hours}} hours)</td>
                                        <td class="py-3 px-6 text-center">₹{{ number_format($employee->advance_due) }}</td>
                                        <td class="py-3 px-6 text-center">{{ \Carbon\Carbon::parse($employee->joining_date)->format('d-m-Y') }}</td>
                                        <td class="py-3 px-6 text-center">
                                            <span class="badge {{ $employee->status == 'active' ? 'badge-success' : 'badge-danger' }}">
                                                {{ ucfirst($employee->status) }}
                                            </span>
                                        </td>
                                        <td class="py-3 px-6 text-center">
                                            <div class="flex item-center justify-center space-x-2">
                                                <a href="{{ route('employees.edit', $employee->id) }}" title="Edit Employee" class="btn-soft">
                                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                                    </svg>
                                                </a>

                                                <a href="{{ route('employees.salary', $employee->id) }}" title="Manage Salary" class="btn-soft">
                                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1" />
                                                </a>
                                                <button type="button" title="Pay Advance Amount" class="pay-advance-btn btn-soft" data-val="{{$employee->advance_due}}">
                                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z" />
                                                    </svg>
                                                </button>
                                                <form method="POST" action="{{ route('employees.destroy', $employee->id) }}" class="inline-block" onsubmit="return confirm('Are you sure you want to delete this employee? This action cannot be undone.');">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" title="Delete Employee" class="btn-soft-danger">
                                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                                        </svg>
                                                    </button>
                                                </form>
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="10" class="py-6 text-center text-base text-violet-300/60">
                                            No employees found
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                            @if($employees->isNotEmpty())
                                <tfoot>
                                    <tr class="bg-violet-950/60">
                                        <td colspan="4" class="px-5 py-4 text-right text-sm font-semibold uppercase tracking-[0.12em] text-violet-200">
                                            Totals
                                        </td>
                                        <td class="px-5 py-4 text-center text-sm font-bold text-fuchsia-100">
                                            ₹{{ number_format($displayedTotalSalary) }}
                                        </td>
                                        <td class="px-5 py-4 text-center text-sm font-bold text-rose-200">
                                            ₹{{ number_format($displayedTotalAdvanceDue, 2) }}
                                        </td>
                                        <td colspan="3" class="px-5 py-4"></td>
                                    </tr>
                                </tfoot>
                            @endif
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- Modal -->
    <div id="pay-advance-modal" class="fixed inset-0 z-50 hidden items-center justify-center bg-black/70 px-4 backdrop-blur-sm">
        <div class="w-full max-w-md rounded-2xl border border-violet-500/25 bg-[#08030f]/95 p-6 shadow-[0_28px_90px_-44px_rgba(88,28,135,0.8)]">
            <!-- Modal Header -->
            <div class="flex justify-between items-center mb-6">
                <h2 class="text-xl font-semibold text-white">Pay Advance Amount</h2>
                <button type="button" id="close-modal" class="text-violet-300/70 hover:text-white">
                    <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                </button>
            </div>
            <!-- Employee Information -->
            <div class="surface-muted mb-6 p-4">
                <div class="flex items-center mb-3">
                    <div class="mr-3 flex h-10 w-10 items-center justify-center rounded-full bg-violet-500/15 ring-1 ring-violet-400/25">
                        <span id="employee-initials" class="text-sm font-semibold text-violet-100"></span>
                    </div>
                    <div>
                        <h3 id="employee-name" class="font-medium text-white"></h3>
                        <p class="text-sm text-violet-300/65">Employee</p>
                    </div>
                </div>

                <div class="grid grid-cols-2 gap-4 text-sm">
                    <div>
                        <p class="text-violet-300/65">Current Salary</p>
                        <p id="employee-salary" class="font-medium text-white"></p>
                    </div>
                    <div>
                        <p class="text-violet-300/65">Advance Due</p>
                        <p id="employee-advance-due" class="font-medium text-rose-200">₹0</p>
                    </div>
                </div>
            </div>

            <!-- Advance Payment Form -->
            <form id="advance-payment-form" method="POST">
                @csrf
                <!-- Hidden field for employee ID -->
                <input type="hidden" id="employee-id" name="employee_id" value="">

                <!-- Alert Messages Container -->
                <div id="form-alerts" class="mb-4 hidden">
                    <!-- Success Alert -->
                    <div id="success-message" class="alert-success hidden">
                        <div class="flex items-center">
                            <svg class="h-5 w-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                            <span id="success-text"></span>
                        </div>
                    </div>

                    <!-- Error Alert -->
                    <div id="error-message" class="alert-error hidden">
                        <div class="flex items-center">
                            <svg class="h-5 w-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                            <span id="error-text"></span>
                        </div>
                    </div>
                </div>

                <div class="mb-4">
                    <label for="advance-amount" class="mb-2 block text-sm font-medium text-violet-100">
                        Advance Amount (₹)
                    </label>
                    <input type="number"
                           id="advance-amount"
                           name="advance_amount"
                           step="0.01"
                           min="1"
                           max="50000"
                           placeholder="Enter amount"
                           class="w-full rounded-lg border border-violet-500/25 bg-black/45 px-3 py-2.5 text-sm text-violet-50 placeholder:text-violet-300/45 shadow-sm shadow-black/30 focus:border-violet-400 focus:outline-hidden focus:ring-4 focus:ring-violet-500/20"
                           required>
                    <p class="mt-1 text-xs text-violet-300/60">Enter the advance amount to be paid to the employee</p>
                </div>

                <div class="mb-6">
                    <label for="advance-reason" class="mb-2 block text-sm font-medium text-violet-100">
                        Reason (Optional)
                    </label>
                    <textarea id="advance-reason"
                              name="reason"
                              rows="3"
                              placeholder="Reason for advance payment..."
                              class="w-full rounded-lg border border-violet-500/25 bg-black/45 px-3 py-2.5 text-sm text-violet-50 placeholder:text-violet-300/45 shadow-sm shadow-black/30 focus:border-violet-400 focus:outline-hidden focus:ring-4 focus:ring-violet-500/20"></textarea>
                </div>

                <!-- Form Actions -->
                <div class="flex justify-end space-x-3">
                    <button type="button"
                            id="cancel-advance"
                            class="btn-secondary">
                        Cancel
                    </button>
                    <button type="submit"
                            class="btn-accent">
                        Pay Advance
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- jQuery UI Datepicker CDN -->
    <link rel="stylesheet" href="https://code.jquery.com/ui/1.13.2/themes/base/jquery-ui.css">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://code.jquery.com/ui/1.13.2/jquery-ui.min.js"></script>

    <script>
        jQuery(document).ready(function($) {
            // Initialize datepicker
            if (typeof $.fn.datepicker === 'function') {
                $("#joining_from").datepicker({
                    dateFormat: 'yy-mm-dd',
                    changeMonth: true,
                    changeYear: true,
                    maxDate: 0, // Disable future dates
                    yearRange: "2000:c"
                });
            }

            const modal = document.getElementById('pay-advance-modal');
            const openButtons = document.querySelectorAll('.pay-advance-btn');
            const closeButton = document.getElementById('close-modal');

            // Function to show alert messages
            function showAlert(type, message) {
                // Show alerts container and hide individual alerts first
                $('#form-alerts').removeClass('hidden');
                $('#success-message, #error-message').addClass('hidden');

                if (type === 'success') {
                    $('#success-text').text(message);
                    $('#success-message').removeClass('hidden');
                } else if (type === 'error') {
                    $('#error-text').text(message);
                    $('#error-message').removeClass('hidden');
                }
            }

            function hideAllAlerts() {
                $('#form-alerts').addClass('hidden');
                $('#success-message, #error-message').addClass('hidden');
            }

            // Function to reset modal form
            function resetModalForm() {
                // Reset the form
                document.getElementById('advance-payment-form').reset();

                // Clear employee details
                document.getElementById('employee-name').innerText = '';
                document.getElementById('employee-salary').innerText = '';
                document.getElementById('employee-initials').innerText = '';
                document.getElementById('employee-advance-due').innerText = '₹0';

                // Reset the hidden employee ID field
                document.getElementById('employee-id').value = '';

                // Reset form action
                document.getElementById('advance-payment-form').action = '';

                // Hide all alert messages
                hideAllAlerts();
            }

            openButtons.forEach(button => {
                button.addEventListener('click', () => {
                    // Get employee details
                    const employeeRow = button.closest('tr');
                    const employeeName = employeeRow.querySelector('td:nth-child(2) div div').innerText;
                    const employeeSalary = employeeRow.querySelector('td:nth-child(5)').innerText;
                    const employeeId = employeeRow.querySelector('form').action.split('/').pop();

                    // Set employee details in modal
                    document.getElementById('employee-name').innerText = employeeName;
                    document.getElementById('employee-salary').innerText = employeeSalary;
                    document.getElementById('employee-initials').innerText = employeeName.split(' ').map(n => n[0]).join('');

                    // Set the hidden employee ID field
                    document.getElementById('employee-id').value = employeeId;

                    // Update advance due amount
                    const advanceDue = button.getAttribute('data-val') || '0';
                    document.getElementById('employee-advance-due').innerText = '₹' + advanceDue;

                    // Set form action for advance payment
                    const form = document.getElementById('advance-payment-form');
                    form.action = '/employees/' + employeeId + '/pay-advance';

                    modal.classList.remove('hidden');
                });
            });

            closeButton.addEventListener('click', () => {
                resetModalForm();
                modal.classList.add('hidden');
            });

            // Cancel button in modal
            document.getElementById('cancel-advance').addEventListener('click', () => {
                resetModalForm();
                modal.classList.add('hidden');
            });

            // Handle advance payment form submission via AJAX
            $('#advance-payment-form').on('submit', function(e) {
                e.preventDefault(); // Prevent default form submission

                // Show loading state
                const submitButton = $('button[type="submit"]', this);
                const originalText = submitButton.text();

                submitButton.prop('disabled', true).text('Submitting...');

                // Hide previous alert messages
                hideAllAlerts();

                // Get form data
                const formData = $(this).serialize();
                const actionUrl = $(this).attr('action');

                // Submit the form via AJAX
                $.ajax({
                    url: actionUrl,
                    method: 'POST',
                    data: formData,
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content'),
                        'Accept': 'application/json',
                        'Content-Type': 'application/x-www-form-urlencoded'
                    },
                    success: function(response) {
                        if (response.success) {
                            // Show success message
                            showAlert('success', response.message);

                            // Reset form inputs but keep the success message visible
                            $('#advance-amount').val('');
                            $('#advance-reason').val('');

                            // Reload page after 2 seconds
                            setTimeout(function() {
                                window.location.reload();
                            }, 2000);
                        } else {
                            // Handle unexpected response format
                            showAlert('error', response.message || 'An unexpected error occurred.');
                        }
                    },
                    error: function(xhr) {
                        let errorMessage = 'An error occurred while processing your request.';

                        if (xhr.responseJSON) {
                            if (xhr.responseJSON.message) {
                                errorMessage = xhr.responseJSON.message;
                            } else if (xhr.responseJSON.errors) {
                                // Handle validation errors
                                const errors = xhr.responseJSON.errors;
                                const errorMessages = Object.values(errors).flat();
                                errorMessage = errorMessages.join(', ');
                            }
                        } else if (xhr.status === 422) {
                            errorMessage = 'Please check your input and try again.';
                        } else if (xhr.status === 500) {
                            errorMessage = 'Server error. Please try again later.';
                        }

                        showAlert('error', errorMessage);
                    },
                    complete: function() {
                        // Reset submit button state
                        submitButton.prop('disabled', false).text(originalText);
                    }
                });
            });
        });
    </script>
</x-layouts.app-layout>
