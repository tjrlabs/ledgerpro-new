<x-layouts.app-layout>
    <div class="py-12">
        <div class="mx-auto sm:px-6 lg:px-8">
            <div class="page-shell">
                <div class="page-inner">
                    <div class="mb-6">
                        <a href="{{ route('employees.index') }}" class="btn-secondary mb-4 inline-flex items-center">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-1" viewBox="0 0 20 20" fill="currentColor">
                                <path fill-rule="evenodd" d="M9.707 16.707a1 1 0 01-1.414 0l-6-6a1 1 0 010-1.414l6-6a1 1 0 011.414 1.414L5.414 9H17a1 1 0 110 2H5.414l4.293 4.293a1 1 0 010 1.414z" clip-rule="evenodd" />
                            </svg>
                            Back to Employees
                        </a>
                        <h1 class="page-title">Salary Management - {{ $employee->first_name }} {{ $employee->last_name }}</h1>
                        <p class="page-subtitle">{{ $employee->department }} • {{ ucfirst($employee->status) }}</p>
                    </div>

                    @if(session('success'))
                    <div id="success-alert" class="alert-success mb-4">
                        <div class="flex items-center">
                            <svg class="h-5 w-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                            {{ session('success') }}
                        </div>
                        <button type="button" onclick="document.getElementById('success-alert').style.display='none'" class="text-green-700 hover:text-green-900">
                            <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                            </svg>
                        </button>
                    </div>
                    @endif

                    @if ($errors->any())
                    <div class="alert-error mb-4">
                        <h4 class="text-lg font-medium mb-2">Please correct the following errors:</h4>
                        <ul class="list-disc pl-5">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                    @endif

                    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                        <!-- Current Salary Information -->
                        <div class="surface-muted p-6">
                            <h2 class="mb-4 text-xl font-semibold text-white">Current Salary Information</h2>

                            <div class="space-y-4">
                                <div class="flex justify-between items-center p-4 bg-blue-50 rounded-lg">
                                    <div>
                                        <span class="text-sm font-medium text-blue-600">Current Monthly Salary</span>
                                        <div class="text-2xl font-bold text-blue-800">₹{{ number_format($employee->salary) }}</div>
                                    </div>
                                    <div class="p-3 bg-blue-100 rounded-full">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-blue-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1" />
                                        </svg>
                                    </div>
                                </div>

                                <div class="grid grid-cols-2 gap-4">
                                    <div class="rounded-lg border border-violet-500/20 bg-black/35 p-4">
                                        <span class="text-sm font-medium text-violet-200/75">Working Hours/Day</span>
                                        <div class="text-lg font-semibold text-white">{{ $employee->salary_hours }} hours</div>
                                    </div>
                                    <div class="rounded-lg border border-violet-500/20 bg-black/35 p-4">
                                        <span class="text-sm font-medium text-violet-200/75">Hourly Rate</span>
                                        <div class="text-lg font-semibold text-white">₹{{ number_format($employee->salary / 30 / $employee->salary_hours, 2) }}</div>
                                    </div>
                                </div>

                                <div class="rounded-lg border border-violet-500/20 bg-black/35 p-4">
                                    <span class="text-sm font-medium text-violet-200/75">Joining Date</span>
                                    <div class="text-lg font-semibold text-white">{{ \Carbon\Carbon::parse($employee->joining_date)->format('d M, Y') }}</div>
                                </div>
                            </div>
                        </div>

                        <!-- Update Salary Form -->
                        <div class="surface-muted p-6">
                            <h2 class="mb-4 text-xl font-semibold text-white">Update Salary</h2>

                            <form action="{{ route('employees.salary.update', $employee->id) }}" method="POST" class="space-y-4">
                                @csrf
                                @method('PUT')

                                <div>
                                    <x-forms.label for="new_salary" value="New Monthly Salary (₹)" required="true" />
                                    <x-forms.input type="number" name="new_salary" id="new_salary" step="1" min="0" value="{{ old('new_salary') }}" placeholder="Enter new salary amount" required>
                                        <x-slot name="afterIcon">
                                            <i class="fas fa-rupee-sign text-gray-500"></i>
                                        </x-slot>
                                    </x-forms.input>
                                    @error('new_salary')
                                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                    @enderror
                                </div>

                                <div>
                                    <x-forms.label for="effective_date" value="Effective Date" required="true" />
                                    <x-forms.input type="text" name="effective_date" id="effective_date" class="datepicker" value="{{ old('effective_date', date('Y-m-d')) }}" placeholder="Select Effective Date" required>
                                        <x-slot name="afterIcon">
                                            <i class="fas fa-calendar text-gray-500"></i>
                                        </x-slot>
                                    </x-forms.input>
                                    @error('effective_date')
                                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                    @enderror
                                </div>

                                <div class="flex justify-end pt-4">
                                    <x-forms.button type="submit">
                                        Update Salary
                                    </x-forms.button>
                                </div>
                            </form>
                        </div>
                    </div>

                    <!-- Salary History -->
                    <div class="surface-muted mt-8 p-6">
                        <h2 class="mb-4 text-xl font-semibold text-white">Salary History</h2>

                        @if($salaryHistory->count() > 0)
                            <div class="overflow-x-auto">
                                <table class="app-table">
                                    <thead>
                                        <tr>
                                            <th class="py-3 px-6 text-left">Effective Date</th>
                                            <th class="py-3 px-6 text-center">Salary Amount</th>
                                            <th class="py-3 px-6 text-center">Updated On</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($salaryHistory as $salary)
                                            <tr>
                                                <td class="py-3 px-6 text-left">
                                                    <span class="font-medium">{{ \Carbon\Carbon::parse($salary->effective_date)->format('d M, Y') }}</span>
                                                </td>
                                                <td class="py-3 px-6 text-center">
                                                    <span class="px-3 py-1 bg-green-100 text-green-800 rounded-full font-medium">
                                                        ₹{{ number_format($salary->salary) }}
                                                    </span>
                                                </td>
                                                <td class="py-3 px-6 text-center text-violet-300/65">
                                                    {{ \Carbon\Carbon::parse($salary->created_at)->format('d M, Y H:i') }}
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        @else
                            <div class="py-8 text-center text-violet-300/60">
                                <svg xmlns="http://www.w3.org/2000/svg" class="mx-auto mb-4 h-12 w-12 text-violet-300/40" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                </svg>
                                <p class="text-lg">No salary history found</p>
                                <p class="text-sm">Salary changes will appear here once recorded</p>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- jQuery UI Datepicker CDN -->
    <link rel="stylesheet" href="https://code.jquery.com/ui/1.13.2/themes/base/jquery-ui.css">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://code.jquery.com/ui/1.13.2/jquery-ui.min.js"></script>

    <script>
        jQuery(document).ready(function($) {
            // Initialize datepicker for effective date
            if (typeof $.fn.datepicker === 'function') {
                $("#effective_date").datepicker({
                    dateFormat: 'yy-mm-dd',
                    changeMonth: true,
                    changeYear: true,
                    // maxDate: 0, // Disable future dates
                    yearRange: "2000:c"
                });
            }
        });
    </script>
</x-layouts.app-layout>
