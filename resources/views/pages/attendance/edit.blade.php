<x-layouts.app-layout>
    <div class="py-12">
        <div class="w-full mx-auto sm:px-6 lg:px-8">
            <div class="bg-white/80 backdrop-blur-md overflow-hidden shadow-xl sm:rounded-lg border border-gray-100">
                <div class="p-6 text-gray-900">
                    <div class="flex justify-between items-center mb-6">
                        <h1 class="text-2xl font-bold">
                            Edit Attendance Board - {{ $attendance->attendance_month_year }}
                        </h1>
                        <a href="{{ route('attendance.index') }}" class="bg-gray-600 hover:bg-gray-700 text-white px-4 py-2 rounded-lg shadow-sm flex items-center">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                            </svg>
                            Back to Attendance
                        </a>
                    </div>

                    @if($errors->any())
                        <div class="bg-red-50 border border-red-200 rounded-lg p-4 mb-6">
                            <div class="flex">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-red-400 mr-2" viewBox="0 0 20 20" fill="currentColor">
                                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd" />
                                </svg>
                                <div>
                                    <h3 class="text-sm font-medium text-red-800">Please fix the following errors:</h3>
                                    <ul class="mt-2 text-sm text-red-700 list-disc list-inside">
                                        @foreach($errors->all() as $error)
                                            <li>{{ $error }}</li>
                                        @endforeach
                                    </ul>
                                </div>
                            </div>
                        </div>
                    @endif

                    @if(session('success'))
                        <div class="bg-green-50 border border-green-200 rounded-lg p-4 mb-6">
                            <div class="flex">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-green-400 mr-2" viewBox="0 0 20 20" fill="currentColor">
                                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                                </svg>
                                <div>
                                    <h3 class="text-sm font-medium text-green-800">Success!</h3>
                                    <p class="mt-1 text-sm text-green-700">{{ session('success') }}</p>
                                </div>
                            </div>
                        </div>
                    @endif

                    @if(session('warning'))
                        <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-4 mb-6">
                            <div class="flex">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-yellow-400 mr-2" viewBox="0 0 20 20" fill="currentColor">
                                    <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd" />
                                </svg>
                                <div>
                                    <h3 class="text-sm font-medium text-yellow-800">Warning!</h3>
                                    <p class="mt-1 text-sm text-yellow-700">{{ session('warning') }}</p>
                                </div>
                            </div>
                        </div>
                    @endif

                    @if(session('error'))
                        <div class="bg-red-50 border border-red-200 rounded-lg p-4 mb-6">
                            <div class="flex">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-red-400 mr-2" viewBox="0 0 20 20" fill="currentColor">
                                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd" />
                                </svg>
                                <div>
                                    <h3 class="text-sm font-medium text-red-800">Error!</h3>
                                    <p class="mt-1 text-sm text-red-700">{{ session('error') }}</p>
                                </div>
                            </div>
                        </div>
                    @endif

                    <form method="POST" action="{{ route('attendance.update', $attendance->id) }}" id="attendanceEditForm">
                        @csrf
                        @method('PUT')

                        <!-- Employee Attendance Data -->
                        <div class="bg-white rounded-lg border border-gray-200 overflow-hidden">
                            <div class="px-6 py-4 border-b border-gray-200 flex justify-between items-center">
                                <h3 class="text-lg font-semibold text-gray-900">Edit Employee Attendance Data</h3>
                                <button type="button" id="addEmployees" class="bg-blue-600 hover:bg-blue-700 text-white px-3 py-1 rounded text-sm flex items-center">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                                    </svg>
                                    Add Employees
                                </button>
                            </div>

                            @if($employeeAttendance->isNotEmpty())
                                <div class="overflow-x-auto">
                                    <table class="min-w-full divide-y divide-gray-200">
                                        <thead class="bg-gray-50">
                                            <tr>
                                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Employee</th>
                                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Monthly Salary</th>
                                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Present Days</th>
                                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">OT Hours</th>
                                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Bonus Amount</th>
                                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Advance Deducted</th>
{{--                                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Previous Balance</th>--}}
{{--                                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Remarks</th>--}}
                                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                                            </tr>
                                        </thead>
                                        <tbody class="bg-white divide-y divide-gray-200">
                                            @foreach($employeeAttendance as $index => $record)
                                                <tr class="hover:bg-gray-50">
                                                    <td class="px-4 py-4 whitespace-nowrap">
                                                        <div>
                                                            <div class="text-sm font-medium text-gray-900">{{ $record->employee->first_name }} {{ $record->employee->last_name }}</div>
                                                            <div class="text-sm text-gray-500">{{ $record->employee->department ?? 'N/A' }} - {{ $record->employee->designation ?? 'N/A' }}</div>
                                                        </div>
                                                        <input type="hidden" name="employees[{{ $index }}][id]" value="{{ $record->id }}">
                                                        <input type="hidden" name="employees[{{ $index }}][employee_id]" value="{{ $record->employee_id }}">
                                                    </td>
                                                    <td class="px-4 py-4 whitespace-nowrap text-sm text-gray-900">
                                                        ₹{{ number_format($record->employee->salary, 2) }}
                                                    </td>
                                                    <td class="px-4 py-4 whitespace-nowrap">
                                                        <input type="number" name="employees[{{ $index }}][present_days]" min="0" max="31" value="{{ old('employees.'.$index.'.present_days', $record->present_days) }}" class="border border-gray-300 rounded px-2 py-1 w-20 text-sm focus:border-blue-500 focus:ring-blue-500" required>
                                                    </td>
                                                    <td class="px-4 py-4 whitespace-nowrap">
                                                        <input type="number" name="employees[{{ $index }}][overtime_hours]" step="0.5" value="{{ old('employees.'.$index.'.overtime_hours', $record->overtime_hours) }}" class="border border-gray-300 rounded px-2 py-1 w-20 text-sm focus:border-blue-500 focus:ring-blue-500">
                                                    </td>
                                                    <td class="px-4 py-4 whitespace-nowrap">
                                                        <input type="number" name="employees[{{ $index }}][bonus_amount]" min="0" step="0.01" value="{{ old('employees.'.$index.'.bonus_amount', $record->bonus_amount) }}" class="border border-gray-300 rounded px-2 py-1 w-24 text-sm focus:border-blue-500 focus:ring-blue-500">
                                                    </td>
                                                    <td class="px-4 py-4 whitespace-nowrap">
                                                        <input type="number" name="employees[{{ $index }}][advance_deducted]" min="0" step="0.01" value="{{ old('employees.'.$index.'.advance_deducted', $record->advance_deducted) }}" class="border border-gray-300 rounded px-2 py-1 w-24 text-sm focus:border-blue-500 focus:ring-blue-500">
                                                        <br>
                                                        <span class="text-xs text-gray-400">Current Due: ₹{{ number_format($record->employee->advance_due, 2) }} </span>
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
                                                                    class="delete-employee-btn text-red-600 hover:text-red-800 p-1 rounded hover:bg-red-50"
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
                                    <p class="text-gray-500">No employee attendance records found for this period.</p>
                                </div>
                            @endif
                        </div>

                        <!-- Submit Buttons -->
                        <div class="flex justify-end space-x-4 mt-6">
                            <a href="{{ route('attendance.index') }}" class="bg-gray-300 hover:bg-gray-400 text-gray-700 px-6 py-2 rounded-lg">
                                Cancel
                            </a>
                            <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-2 rounded-lg shadow-sm">
                                Update Attendance Board
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Add Employees Modal -->
    <div id="addEmployeesModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full hidden z-50">
        <div class="relative top-10 mx-auto p-5 border w-4/5 max-w-4xl shadow-lg rounded-md bg-white">
            <div class="mt-3">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="text-lg leading-6 font-medium text-gray-900">Add Employees to Attendance</h3>
                    <button id="closeModal" class="text-gray-400 hover:text-gray-600">
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

                            <div class="flex justify-between items-center mt-6 pt-4 border-t border-gray-200">
                                <div class="text-sm text-gray-600">
                                    <span id="selectedCount">0</span> employee(s) selected
                                </div>
                                <div class="flex space-x-3">
                                    <button type="button" id="cancelAddEmployees" class="px-4 py-2 bg-gray-300 text-gray-700 text-sm font-medium rounded-md hover:bg-gray-400 focus:outline-none focus:ring-2 focus:ring-gray-300">
                                        Cancel
                                    </button>
                                    <button type="submit" class="px-4 py-2 bg-blue-600 text-white text-sm font-medium rounded-md hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500">
                                        Add Selected Employees
                                    </button>
                                </div>
                            </div>
                        </form>
                    @else
                        <div class="text-center py-8">
                            <p class="text-gray-500">No active employees found to add.</p>
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
                    modalBody.innerHTML = '<div class="text-center py-8"><p class="text-gray-500">No active employees found to add.</p></div>';
                    return;
                }

                employees.forEach(employee => {
                    const employeeDiv = document.createElement('div');
                    employeeDiv.className = `p-4 border rounded-lg ${employee.is_in_board ? 'bg-gray-100 border-gray-300' : 'bg-white border-gray-200 hover:border-blue-300'}`;

                    let checkboxHtml = '';
                    if (!employee.is_in_board) {
                        checkboxHtml = `
                            <input type="checkbox"
                                   name="selected_employees[]"
                                   value="${employee.id}"
                                   class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded">
                        `;
                    }

                    employeeDiv.innerHTML = `
                        <div class="flex items-center space-x-3">
                            <div class="flex-shrink-0">
                                ${checkboxHtml}
                            </div>
                            <div class="flex-1 min-w-0">
                                <div class="flex justify-between items-start">
                                    <div>
                                        <p class="text-sm font-medium text-gray-900 ${employee.is_in_board ? 'opacity-60' : ''}">
                                            ${employee.employee_name}
                                            ${employee.is_in_board ? '<span class="text-xs text-gray-500 ml-2">(Already in board)</span>' : ''}
                                        </p>
                                        <p class="text-xs text-gray-500">
                                            Salary: ₹${employee.salary ? employee.salary.toLocaleString() : 'N/A'} |
                                            Working Hours: ${employee.working_hours || 'N/A'}
                                        </p>
                                        ${employee.joining_date ? `<p class="text-xs text-gray-400">Joined: ${new Date(employee.joining_date).toLocaleDateString()}</p>` : ''}
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
