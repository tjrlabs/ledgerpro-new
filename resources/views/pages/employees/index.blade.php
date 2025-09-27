<x-layouts.app-layout>
    <div class="py-12">
        <div class="w-full mx-auto sm:px-6 lg:px-8">
            <div class="bg-white/80 backdrop-blur-md overflow-hidden shadow-xl sm:rounded-lg border border-gray-100">
                <div class="p-6 text-gray-900">
                    <div class="flex justify-between items-center mb-6">
                        <h1 class="text-2xl font-bold">Employee Management</h1>
                        <a href="{{ route('employees.create') }}" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg shadow-sm flex items-center">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                            </svg>
                            Add New Employee
                        </a>
                    </div>

                    <!-- Statistics Cards -->
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
                                    <p class="text-2xl font-bold text-blue-800">{{ $totalEmployees }}</p>
                                </div>
                            </div>
                        </div>

                        <div class="bg-green-50 p-4 rounded-lg border border-green-200">
                            <div class="flex items-center">
                                <div class="p-2 bg-green-100 rounded-full">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-green-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                                    </svg>
                                </div>
                                <div class="ml-4">
                                    <p class="text-sm font-medium text-green-600">Active Employees</p>
                                    <p class="text-2xl font-bold text-green-800">{{ $activeEmployees }}</p>
                                </div>
                            </div>
                        </div>

                        <div class="bg-red-50 p-4 rounded-lg border border-red-200">
                            <div class="flex items-center">
                                <div class="p-2 bg-red-100 rounded-full">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-red-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z" />
                                    </svg>
                                </div>
                                <div class="ml-4">
                                    <p class="text-sm font-medium text-red-600">Inactive Employees</p>
                                    <p class="text-2xl font-bold text-red-800">{{ $inactiveEmployees }}</p>
                                </div>
                            </div>
                        </div>

                        <div class="bg-yellow-50 p-4 rounded-lg border border-yellow-200">
                            <div class="flex items-center">
                                <div class="p-2 bg-yellow-100 rounded-full">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-yellow-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1" />
                                    </svg>
                                </div>
                                <div class="ml-4">
                                    <p class="text-sm font-medium text-yellow-600">Monthly Payroll</p>
                                    <p class="text-2xl font-bold text-yellow-800">₹{{ number_format($totalMonthlySalary) }}</p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Filters Section -->
                    <div class="mb-6 bg-gray-50 p-4 rounded-lg border border-gray-200">
                        <form action="{{ route('employees.index') }}" method="GET" class="grid grid-cols-1 md:grid-cols-6 gap-4">
                            <!-- Search -->
                            <div>
                                <label for="search" class="block text-sm font-medium text-gray-700 mb-1">Search</label>
                                <input type="text" name="search" id="search" placeholder="Name or Mobile"
                                       value="{{ $currentFilters['search'] ?? '' }}"
                                       class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-200 focus:ring-opacity-50">
                            </div>

                            <!-- Status Filter -->
                            <div>
                                <label for="status" class="block text-sm font-medium text-gray-700 mb-1">Status</label>
                                <select name="status" id="status" class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-200 focus:ring-opacity-50">
                                    @foreach($statuses as $status)
                                        <option value="{{ $status }}" {{ ($currentFilters['status'] ?? 'active') == $status ? 'selected' : '' }}>
                                            {{ ucfirst($status) }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            <!-- Department Filter -->
                            <div>
                                <label for="department" class="block text-sm font-medium text-gray-700 mb-1">Department</label>
                                <select name="department" id="department" class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-200 focus:ring-opacity-50">
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
                                <label for="gender" class="block text-sm font-medium text-gray-700 mb-1">Gender</label>
                                <select name="gender" id="gender" class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-200 focus:ring-opacity-50">
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
                                <label for="joining_from" class="block text-sm font-medium text-gray-700 mb-1">Joined From</label>
                                <input type="text" name="joining_from" id="joining_from" placeholder="YYYY-MM-DD"
                                       value="{{ $currentFilters['joining_from'] ?? '' }}"
                                       class="datepicker w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-200 focus:ring-opacity-50">
                            </div>

                            <!-- Filter Buttons -->
                            <div class="flex items-end space-x-2">
                                <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2">
                                    Apply Filters
                                </button>
                                <a href="{{ route('employees.index') }}" class="px-4 py-2 bg-gray-200 text-gray-700 rounded-md hover:bg-gray-300 focus:outline-none focus:ring-2 focus:ring-gray-500 focus:ring-offset-2">
                                    Reset
                                </a>
                            </div>
                        </form>
                    </div>

                    @if(session('success'))
                    <div id="success-alert" class="mb-4 p-4 bg-green-50 border-l-4 border-green-500 text-green-700 flex justify-between items-center">
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
                    <div id="error-alert" class="mb-4 p-4 bg-red-50 border-l-4 border-red-500 text-red-700 flex justify-between items-center">
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
                    <div class="overflow-x-auto bg-white/70 backdrop-blur-sm p-4 rounded-lg shadow-inner border border-white">
                        <table class="min-w-full bg-transparent">
                            <thead>
                                <tr class="bg-gray-100 text-gray-700 uppercase text-sm leading-normal">
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
                            <tbody class="text-gray-600 text-sm">
                                @forelse ($employees as $index => $employee)
                                    <tr class="border-b border-gray-200 hover:bg-gray-50">
                                        <td class="py-3 px-6 text-center">{{ $index + 1 }}</td>
                                        <td class="py-3 px-6 text-left">
                                            <div class="flex items-center">
                                                <div class="mr-3">
                                                    <div class="w-8 h-8 bg-blue-100 rounded-full flex items-center justify-center">
                                                        <span class="text-blue-600 font-semibold text-sm">
                                                            {{ strtoupper(substr($employee->first_name, 0, 1)) }}{{ strtoupper(substr($employee->last_name, 0, 1)) }}
                                                        </span>
                                                    </div>
                                                </div>
                                                <div>
                                                    <div class="font-medium text-gray-900">{{ $employee->first_name }} {{ $employee->last_name }}</div>
                                                </div>
                                            </div>
                                        </td>
                                        <td class="py-3 px-6 text-center">
                                            <span class="px-2 py-1 text-xs rounded-full {{ $employee->gender == 'male' ? 'bg-blue-100 text-blue-800' : 'bg-pink-100 text-pink-800' }}">
                                                {{ ucfirst($employee->gender) }}
                                            </span>
                                        </td>
                                        <td class="py-3 px-6 text-center">
                                            <span class="px-2 py-1 text-xs rounded-full bg-gray-100 text-gray-800">{{ $employee->department }}</span>
                                        </td>
                                        <td class="py-3 px-6 text-center">₹{{ number_format($employee->salary) }} <br/> ({{$employee->salary_hours}} hours)</td>
                                        <td class="py-3 px-6 text-center">₹{{ number_format($employee->advance_due) }}</td>
                                        <td class="py-3 px-6 text-center">{{ \Carbon\Carbon::parse($employee->joining_date)->format('d-m-Y') }}</td>
                                        <td class="py-3 px-6 text-center">
                                            <span class="px-2 py-1 text-xs rounded-full {{ $employee->status == 'active' ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                                                {{ ucfirst($employee->status) }}
                                            </span>
                                        </td>
                                        <td class="py-3 px-6 text-center">
                                            <div class="flex item-center justify-center space-x-2">
                                                <a href="{{ route('employees.edit', $employee->id) }}" title="Edit Employee" class="bg-blue-100 text-blue-600 hover:bg-blue-200 px-3 py-1 rounded-md inline-flex items-center">
                                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                                    </svg>
                                                </a>

                                                <a href="{{ route('employees.salary', $employee->id) }}" title="Manage Salary" class="bg-green-100 text-green-600 hover:bg-green-200 px-3 py-1 rounded-md inline-flex items-center">
                                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1" />
                                                </a>
                                                <button type="button" title="Pay Advance Amount" class="pay-advance-btn bg-yellow-100 text-yellow-600 hover:bg-yellow-200 px-3 py-1 rounded-md inline-flex items-center" data-val="{{$employee->advance_due}}">
                                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z" />
                                                    </svg>
                                                </button>
                                                <form method="POST" action="{{ route('employees.destroy', $employee->id) }}" class="inline-block" onsubmit="return confirm('Are you sure you want to delete this employee? This action cannot be undone.');">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" title="Delete Employee" class="bg-red-100 text-red-600 hover:bg-red-200 px-3 py-1 rounded-md inline-flex items-center">
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
                                        <td colspan="10" class="py-6 text-center text-gray-400 text-base">
                                            No employees found
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- Modal -->
    <div id="pay-advance-modal" class="hidden fixed inset-0 bg-gray-500 bg-opacity-75 flex items-center justify-center z-50">
        <div class="bg-white rounded-lg shadow-lg p-6 w-full max-w-md mx-4">
            <!-- Modal Header -->
            <div class="flex justify-between items-center mb-6">
                <h2 class="text-xl font-semibold text-gray-900">Pay Advance Amount</h2>
                <button type="button" id="close-modal" class="text-gray-400 hover:text-gray-600">
                    <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                </button>
            </div>
            <!-- Employee Information -->
            <div class="mb-6 p-4 bg-gray-50 rounded-lg">
                <div class="flex items-center mb-3">
                    <div class="w-10 h-10 bg-blue-100 rounded-full flex items-center justify-center mr-3">
                        <span id="employee-initials" class="text-blue-600 font-semibold text-sm"></span>
                    </div>
                    <div>
                        <h3 id="employee-name" class="font-medium text-gray-900"></h3>
                        <p class="text-sm text-gray-500">Employee</p>
                    </div>
                </div>

                <div class="grid grid-cols-2 gap-4 text-sm">
                    <div>
                        <p class="text-gray-500">Current Salary</p>
                        <p id="employee-salary" class="font-medium text-gray-900"></p>
                    </div>
                    <div>
                        <p class="text-gray-500">Advance Due</p>
                        <p id="employee-advance-due" class="font-medium text-red-600">₹0</p>
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
                    <div id="success-message" class="hidden p-4 bg-green-50 border-l-4 border-green-500 text-green-700">
                        <div class="flex items-center">
                            <svg class="h-5 w-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                            <span id="success-text"></span>
                        </div>
                    </div>

                    <!-- Error Alert -->
                    <div id="error-message" class="hidden p-4 bg-red-50 border-l-4 border-red-500 text-red-700">
                        <div class="flex items-center">
                            <svg class="h-5 w-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                            <span id="error-text"></span>
                        </div>
                    </div>
                </div>

                <div class="mb-4">
                    <label for="advance-amount" class="block text-sm font-medium text-gray-700 mb-2">
                        Advance Amount (₹)
                    </label>
                    <input type="number"
                           id="advance-amount"
                           name="advance_amount"
                           step="0.01"
                           min="1"
                           max="50000"
                           placeholder="Enter amount"
                           class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-200 focus:ring-opacity-50"
                           required>
                    <p class="mt-1 text-xs text-gray-500">Enter the advance amount to be paid to the employee</p>
                </div>

                <div class="mb-6">
                    <label for="advance-reason" class="block text-sm font-medium text-gray-700 mb-2">
                        Reason (Optional)
                    </label>
                    <textarea id="advance-reason"
                              name="reason"
                              rows="3"
                              placeholder="Reason for advance payment..."
                              class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-200 focus:ring-opacity-50"></textarea>
                </div>

                <!-- Form Actions -->
                <div class="flex justify-end space-x-3">
                    <button type="button"
                            id="cancel-advance"
                            class="px-4 py-2 text-sm font-medium text-gray-700 bg-gray-200 border border-gray-300 rounded-md hover:bg-gray-300 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-gray-500">
                        Cancel
                    </button>
                    <button type="submit"
                            class="px-4 py-2 text-sm font-medium text-white bg-yellow-600 border border-transparent rounded-md hover:bg-yellow-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-yellow-500">
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
