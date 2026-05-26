<x-layouts.app-layout>
    <div class="py-12">
        <div class="w-full mx-auto sm:px-6 lg:px-8">
            <div class="page-shell">
                <div class="page-inner">
                    <div class="page-header">
                        <div>
                            <h1 class="page-title">Edit Attendance Board - {{ $attendance->attendance_month_year }}</h1>
                            <p class="page-subtitle">Update present days, overtime, bonus, and advance deductions for each employee on this board.</p>
                        </div>
                        <a href="{{ route('attendance.index') }}" class="btn-secondary">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                            </svg>
                            Back to Attendance
                        </a>
                    </div>

                    @if($errors->any())
                        <div class="alert-error mb-6">
                            <div class="flex">
                                <svg xmlns="http://www.w3.org/2000/svg" class="mr-2 h-5 w-5 text-rose-300" viewBox="0 0 20 20" fill="currentColor">
                                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd" />
                                </svg>
                                <div>
                                    <h3 class="text-sm font-medium text-rose-100">Please fix the following errors:</h3>
                                    <ul class="mt-2 list-inside list-disc text-sm text-rose-200">
                                        @foreach($errors->all() as $error)
                                            <li>{{ $error }}</li>
                                        @endforeach
                                    </ul>
                                </div>
                            </div>
                        </div>
                    @endif

                    @if(session('success'))
                        <div class="alert-success mb-6">
                            <div class="flex">
                                <svg xmlns="http://www.w3.org/2000/svg" class="mr-2 h-5 w-5 text-purple-200" viewBox="0 0 20 20" fill="currentColor">
                                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                                </svg>
                                <div>
                                    <h3 class="text-sm font-medium text-purple-100">Success!</h3>
                                    <p class="mt-1 text-sm text-purple-200">{{ session('success') }}</p>
                                </div>
                            </div>
                        </div>
                    @endif

                    @if(session('warning'))
                        <div class="mb-6 rounded-lg border border-fuchsia-400/25 bg-fuchsia-500/10 p-4 text-fuchsia-100 shadow-sm shadow-black/30">
                            <div class="flex">
                                <svg xmlns="http://www.w3.org/2000/svg" class="mr-2 h-5 w-5 text-fuchsia-200" viewBox="0 0 20 20" fill="currentColor">
                                    <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd" />
                                </svg>
                                <div>
                                    <h3 class="text-sm font-medium text-fuchsia-100">Warning!</h3>
                                    <p class="mt-1 text-sm text-fuchsia-200">{{ session('warning') }}</p>
                                </div>
                            </div>
                        </div>
                    @endif

                    @if(session('error'))
                        <div class="alert-error mb-6">
                            <div class="flex">
                                <svg xmlns="http://www.w3.org/2000/svg" class="mr-2 h-5 w-5 text-rose-300" viewBox="0 0 20 20" fill="currentColor">
                                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd" />
                                </svg>
                                <div>
                                    <h3 class="text-sm font-medium text-rose-100">Error!</h3>
                                    <p class="mt-1 text-sm text-rose-200">{{ session('error') }}</p>
                                </div>
                            </div>
                        </div>
                    @endif

                    <form method="POST" action="{{ route('attendance.update', $attendance->id) }}" id="attendanceEditForm">
                        @csrf
                        @method('PUT')

                        <!-- Employee Attendance Data -->
                        <div class="overflow-hidden rounded-lg border border-violet-500/20 bg-black/35 shadow-sm shadow-black/50">
                            <div class="flex items-center justify-between border-b border-violet-500/20 px-6 py-4">
                                <h3 class="text-lg font-semibold text-white">Edit Employee Attendance Data</h3>
                                <button type="button" id="addEmployees" class="btn-primary !px-3 !py-1.5 text-sm">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                                    </svg>
                                    Add Employees
                                </button>
                            </div>

                            @if($employeeAttendance->isNotEmpty())
                                <div class="overflow-x-auto">
                                    <table class="app-table">
                                        <thead>
                                            <tr>
                                                <th class="px-4 py-3 text-left text-xs font-medium uppercase tracking-wider">Employee</th>
                                                <th class="px-4 py-3 text-left text-xs font-medium uppercase tracking-wider">Monthly Salary</th>
                                                <th class="px-4 py-3 text-left text-xs font-medium uppercase tracking-wider">Present Days</th>
                                                <th class="px-4 py-3 text-left text-xs font-medium uppercase tracking-wider">OT Hours</th>
                                                <th class="px-4 py-3 text-left text-xs font-medium uppercase tracking-wider">Bonus Amount</th>
                                                <th class="px-4 py-3 text-left text-xs font-medium uppercase tracking-wider">Advance Deducted</th>
{{--                                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Previous Balance</th>--}}
{{--                                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Remarks</th>--}}
                                                <th class="px-4 py-3 text-left text-xs font-medium uppercase tracking-wider">Actions</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach($employeeAttendance as $index => $record)
                                                <tr>
                                                    <td class="px-4 py-4 whitespace-nowrap">
                                                        <div>
                                                            <div class="text-sm font-medium text-white">{{ $record->employee->first_name }} {{ $record->employee->last_name }}</div>
                                                            <div class="text-sm text-violet-300/65">{{ $record->employee->department ?? 'N/A' }} - {{ $record->employee->designation ?? 'N/A' }}</div>
                                                        </div>
                                                        <input type="hidden" name="employees[{{ $index }}][id]" value="{{ $record->id }}">
                                                        <input type="hidden" name="employees[{{ $index }}][employee_id]" value="{{ $record->employee_id }}">
                                                    </td>
                                                    <td class="px-4 py-4 whitespace-nowrap text-sm text-violet-100">
                                                        ₹{{ number_format($record->employee->salary, 2) }}
                                                    </td>
                                                    <td class="px-4 py-4 whitespace-nowrap">
                                                        <input type="number" name="employees[{{ $index }}][present_days]" min="0" max="31" value="{{ old('employees.'.$index.'.present_days', $record->present_days) }}" class="w-20 rounded-lg border border-violet-500/25 bg-black/45 px-2 py-1.5 text-sm text-violet-50 shadow-sm shadow-black/30 focus:border-violet-400 focus:outline-hidden focus:ring-4 focus:ring-violet-500/20" required>
                                                    </td>
                                                    <td class="px-4 py-4 whitespace-nowrap">
                                                        <input type="number" name="employees[{{ $index }}][overtime_hours]" step="0.5" value="{{ old('employees.'.$index.'.overtime_hours', $record->overtime_hours) }}" class="w-20 rounded-lg border border-violet-500/25 bg-black/45 px-2 py-1.5 text-sm text-violet-50 shadow-sm shadow-black/30 focus:border-violet-400 focus:outline-hidden focus:ring-4 focus:ring-violet-500/20">
                                                    </td>
                                                    <td class="px-4 py-4 whitespace-nowrap">
                                                        <input type="number" name="employees[{{ $index }}][bonus_amount]" min="0" step="0.01" value="{{ old('employees.'.$index.'.bonus_amount', $record->bonus_amount) }}" class="w-24 rounded-lg border border-violet-500/25 bg-black/45 px-2 py-1.5 text-sm text-violet-50 shadow-sm shadow-black/30 focus:border-violet-400 focus:outline-hidden focus:ring-4 focus:ring-violet-500/20">
                                                    </td>
                                                    <td class="px-4 py-4 whitespace-nowrap">
                                                        <input type="number" name="employees[{{ $index }}][advance_deducted]" min="0" step="0.01" value="{{ old('employees.'.$index.'.advance_deducted', $record->advance_deducted) }}" class="w-24 rounded-lg border border-violet-500/25 bg-black/45 px-2 py-1.5 text-sm text-violet-50 shadow-sm shadow-black/30 focus:border-violet-400 focus:outline-hidden focus:ring-4 focus:ring-violet-500/20">
                                                        <br>
                                                        <span class="text-xs text-violet-300/60">Current Due: ₹{{ number_format($record->employee->advance_due, 2) }} </span>
                                                    </td>
{{--                                                    <td class="px-4 py-4 whitespace-nowrap">--}}
{{--                                                        <input type="number" name="employees[{{ $index }}][previous_balance]" step="0.01" value="{{ old('employees.'.$index.'.previous_balance', $record->previous_balance) }}" class="border border-gray-300 rounded px-2 py-1 w-24 text-sm focus:border-blue-500 focus:ring-blue-500">--}}
{{--                                                    </td>--}}
{{--                                                    <td class="px-4 py-4 whitespace-nowrap">--}}
{{--                                                        <textarea name="employees[{{ $index }}][remarks]" maxlength="500" class="border border-gray-300 rounded px-2 py-1 w-32 text-sm focus:border-blue-500 focus:ring-blue-500" placeholder="Optional">{{ old('employees.'.$index.'.remarks', $record->remarks) }}</textarea>--}}
{{--                                                    </td>--}}
                                                    <td class="px-4 py-4 whitespace-nowrap text-sm">
                                                        <!-- Action buttons (Edit, Delete) -->
                                                        <div class="flex space-x-2">
                                                                <button type="button"
                                                                    class="delete-employee-btn rounded p-1 text-rose-300 transition hover:bg-rose-500/10 hover:text-rose-100"
                                                                    data-employee-attendance-id="{{ $record->id }}"
                                                                    data-employee-name="{{ $record->employee->first_name }} {{ $record->employee->last_name }}"
                                                                    title="Remove employee from attendance board">
                                                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                                                </svg>
                                                            </button>
                                                        </div>
                                                    </td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            @else
                                <div class="p-6 text-center">
                                    <p class="text-violet-300/60">No employee attendance records found for this period.</p>
                                </div>
                            @endif
                        </div>

                        <!-- Submit Buttons -->
                        <div class="flex justify-end space-x-4 mt-6">
                            <a href="{{ route('attendance.index') }}" class="btn-secondary">
                                Cancel
                            </a>
                            <button type="submit" class="btn-primary">
                                Update Attendance Board
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Add Employees Modal -->
    <div id="addEmployeesModal" class="fixed inset-0 z-50 hidden h-full w-full overflow-y-auto bg-black/70 backdrop-blur-sm">
        <div class="relative top-10 mx-auto w-4/5 max-w-4xl rounded-2xl border border-violet-500/25 bg-[#08030f]/95 p-5 shadow-[0_28px_90px_-44px_rgba(88,28,135,0.8)]">
            <div class="mt-3">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="text-lg font-medium leading-6 text-white">Add Employees to Attendance</h3>
                    <button id="closeModal" class="text-violet-300/70 hover:text-white">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>

                <div class="max-h-96 overflow-y-auto">
                    @if(isset($activeEmployees) && $activeEmployees->count() > 0)
                        <form id="addEmployeesForm">
                            <div class="space-y-3">
                            </div>

                            <div class="mt-6 flex items-center justify-between border-t border-violet-500/20 pt-4">
                                <div class="text-sm text-violet-300/70">
                                    <span id="selectedCount">0</span> employee(s) selected
                                </div>
                                <div class="flex space-x-3">
                                    <button type="button" id="cancelAddEmployees" class="btn-secondary">
                                        Cancel
                                    </button>
                                    <button type="submit" class="btn-primary">
                                        Add Selected Employees
                                    </button>
                                </div>
                            </div>
                        </form>
                    @else
                        <div class="text-center py-8">
                            <p class="text-violet-300/60">No active employees found to add.</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <script type="text/javascript">
    document.addEventListener('DOMContentLoaded', function() {


        // Add Employees Modal
        const addEmployeesBtn = document.getElementById('addEmployees');
        const addEmployeesModal = document.getElementById('addEmployeesModal');
        const closeModalBtn = document.getElementById('closeModal');
        const cancelBtn = document.getElementById('cancelAddEmployees');
        const addEmployeesForm = document.getElementById('addEmployeesForm');
        const selectedCountSpan = document.getElementById('selectedCount');


        // Handle delete employee buttons
        document.addEventListener('click', function(e) {
            if (e.target.closest('.delete-employee-btn')) {
                e.preventDefault();
                const deleteBtn = e.target.closest('.delete-employee-btn');
                const employeeAttendanceId = deleteBtn.getAttribute('data-employee-attendance-id');
                const employeeName = deleteBtn.getAttribute('data-employee-name');
                const attendanceId = {{ $attendance->id }};

                if (confirm(`Are you sure you want to remove ${employeeName} from this attendance board?`)) {
                    // Send AJAX request to delete employee from attendance
                    fetch(`/attendance/${attendanceId}/employee/${employeeAttendanceId}`, {
                        method: 'DELETE',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                        }
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            // Success: reload the page to show updated employee list
                            location.reload();
                        } else {
                            // Failed: show error message
                            alert('Error: ' + data.message);
                        }
                    })
                    .catch(error => {
                        console.error('AJAX Error:', error);
                        alert('Error removing employee. Please try again.');
                    });
                }
            }
        });

        if (addEmployeesBtn && addEmployeesModal) {
            addEmployeesBtn.addEventListener('click', function(e) {
                e.preventDefault();
                addEmployeesModal.classList.remove('hidden');

                // Send AJAX request to get employees for attendance
                const attendanceId = {{ $attendance->id }};
                fetch(`/attendance/${attendanceId}/getemployees`, {
                    method: 'GET',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                    }
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        populateEmployeesModal(data.data);
                    } else {
                        console.error('Error fetching employees:', data.message);
                        alert('Error loading employees: ' + data.message);
                    }
                })
                .catch(error => {
                    console.error('AJAX Error:', error);
                    alert('Error loading employees. Please try again.');
                });

                updateSelectedCount();
            });

            if (closeModalBtn) {
                closeModalBtn.addEventListener('click', function(e) {
                    e.preventDefault();
                    addEmployeesModal.classList.add('hidden');
                });
            }

            if (cancelBtn) {
                cancelBtn.addEventListener('click', function(e) {
                    e.preventDefault();
                    addEmployeesModal.classList.add('hidden');
                });
            }

            // Close modal when clicking outside
            addEmployeesModal.addEventListener('click', function(e) {
                if (e.target === addEmployeesModal) {
                    addEmployeesModal.classList.add('hidden');
                }
            });

            // Handle checkbox selection count
            function updateSelectedCount() {
                const checkboxes = document.querySelectorAll('input[name="selected_employees[]"]:checked');
                if (selectedCountSpan) {
                    selectedCountSpan.textContent = checkboxes.length;
                }
            }

            // Add event listeners to checkboxes
            const checkboxes = document.querySelectorAll('input[name="selected_employees[]"]');
            checkboxes.forEach(checkbox => {
                checkbox.addEventListener('change', updateSelectedCount);
            });

            // Handle form submission
            if (addEmployeesForm) {
                addEmployeesForm.addEventListener('submit', function(e) {
                    e.preventDefault();
                    const selectedEmployees = document.querySelectorAll('input[name="selected_employees[]"]:checked');

                    if (selectedEmployees.length === 0) {
                        alert('Please select at least one employee to add.');
                        return;
                    }

                    // Get the employee IDs from checked checkboxes
                    const employeeIds = Array.from(selectedEmployees).map(cb => parseInt(cb.value));
                    const attendanceId = {{ $attendance->id }};

                    // Send AJAX request to add employees
                    fetch(`/attendance/${attendanceId}/addemployees`, {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                        },
                        body: JSON.stringify({
                            employees: employeeIds
                        })
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            // Success: reload the page to show updated employee list
                            location.reload();
                        } else {
                            // Failed: show error message
                            alert('Error: ' + data.message);
                        }
                    })
                    .catch(error => {
                        console.error('AJAX Error:', error);
                        alert('Error adding employees. Please try again.');
                    });
                });
            }

            // Function to populate the modal with employees
            function populateEmployeesModal(employees) {
                const modalBody = document.querySelector('#addEmployeesModal .space-y-3');
                if (!modalBody) return;

                // Clear existing content
                modalBody.innerHTML = '';

                if (employees.length === 0) {
                    modalBody.innerHTML = '<div class="text-center py-8"><p class="text-violet-300/60">No active employees found to add.</p></div>';
                    return;
                }

                employees.forEach(employee => {
                    const employeeDiv = document.createElement('div');
                    employeeDiv.className = `rounded-lg border p-4 ${employee.is_in_board ? 'border-violet-500/15 bg-violet-950/20 opacity-75' : 'border-violet-500/25 bg-black/40 hover:border-violet-400/45'}`;

                    let checkboxHtml = '';
                    if (!employee.is_in_board) {
                        checkboxHtml = `
                            <input type="checkbox"
                                   name="selected_employees[]"
                                   value="${employee.id}"
                                   class="h-4 w-4 rounded border-violet-400/40 bg-black/50 text-violet-500 focus:ring-violet-500">
                        `;
                    }

                    employeeDiv.innerHTML = `
                        <div class="flex items-center space-x-3">
                            <div class="shrink-0">
                                ${checkboxHtml}
                            </div>
                            <div class="flex-1 min-w-0">
                                <div class="flex justify-between items-start">
                                    <div>
                                        <p class="text-sm font-medium text-white ${employee.is_in_board ? 'opacity-60' : ''}">
                                            ${employee.employee_name}
                                            ${employee.is_in_board ? '<span class="ml-2 text-xs text-violet-300/60">(Already in board)</span>' : ''}
                                        </p>
                                        <p class="text-xs text-violet-300/65">
                                            Salary: ₹${employee.salary ? employee.salary.toLocaleString() : 'N/A'} |
                                            Working Hours: ${employee.working_hours || 'N/A'}
                                        </p>
                                        ${employee.joining_date ? `<p class="text-xs text-violet-300/50">Joined: ${new Date(employee.joining_date).toLocaleDateString()}</p>` : ''}
                                    </div>
                                </div>
                            </div>
                        </div>
                    `;

                    modalBody.appendChild(employeeDiv);
                });

                // Re-attach event listeners to new checkboxes
                attachCheckboxListeners();
            }

            // Function to attach event listeners to checkboxes
            function attachCheckboxListeners() {
                const checkboxes = document.querySelectorAll('input[name="selected_employees[]"]');
                checkboxes.forEach(checkbox => {
                    checkbox.addEventListener('change', updateSelectedCount);
                });
            }
        } else {
            console.error("One or more elements not found!");
        }
    });
    </script>
</x-layouts.app-layout>
