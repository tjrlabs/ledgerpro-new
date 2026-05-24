<x-layouts.app-layout>
    <div class="py-12">
        <div class="w-full mx-auto sm:px-6 lg:px-8">
            <div class="page-shell">
                <div class="p-6">
                    <div class="flex justify-between items-center mb-6">
                        <h1 class="text-2xl font-bold">Create Attendance Board</h1>
                        <a href="{{ route('attendance.index') }}" class="btn-secondary flex items-center">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                            </svg>
                            Back to Attendance
                        </a>
                    </div>

                    @if($errors->any())
                        <div class="alert-error mb-6">
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

                    <form method="POST" action="{{ route('attendance.store') }}" id="attendanceForm">
                        @csrf

                        <!-- Period and Days Configuration -->
                        <div class="surface-muted mb-6">
                            <h3 class="mb-4 text-lg font-semibold">Attendance Period Configuration</h3>

                            <div class="grid grid-cols-2 md:grid-cols-2 gap-6">
                                <div>
                                    <label for="attendance_month" class="mb-2 block text-sm font-medium text-violet-100/80">Month</label>
                                    <select name="attendance_month" id="attendance_month" class="w-full rounded-lg border border-violet-500/25 bg-black/35 px-3 py-2 text-violet-50 shadow-sm shadow-black/30 focus:border-violet-400 focus:outline-hidden focus:ring-4 focus:ring-violet-500/20" required>
                                        @foreach($monthOptions as $value => $label)
                                            <option value="{{ $value }}" {{ old('attendance_month', $currentMonth) == $value ? 'selected' : '' }}>
                                                {{ $label }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>

                                <div>
                                    <label for="attendance_year" class="mb-2 block text-sm font-medium text-violet-100/80">Year</label>
                                    <select name="attendance_year" id="attendance_year" class="w-full rounded-lg border border-violet-500/25 bg-black/35 px-3 py-2 text-violet-50 shadow-sm shadow-black/30 focus:border-violet-400 focus:outline-hidden focus:ring-4 focus:ring-violet-500/20" required>
                                        @foreach($yearOptions as $value => $label)
                                            <option value="{{ $value }}" {{ old('attendance_year', $currentYear) == $value ? 'selected' : '' }}>
                                                {{ $label }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>

                            <!-- Hidden field to combine month and year -->
                            <input type="hidden" name="attendance_month_year" id="attendance_month_year" value="{{ old('attendance_month_year', $currentMonth . '-' . $currentYear) }}">
                        </div>

                        <!-- Submit Buttons -->
                        <div class="flex justify-end space-x-4 mt-6">
                            <a href="{{ route('attendance.index') }}" class="btn-secondary">
                                Cancel
                            </a>
                            <button type="submit" class="btn-primary">
                                Create Attendance Board
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
    <script>
    document.addEventListener('DOMContentLoaded', function() {
        const workingDaysInput = document.getElementById('working_days');
        const fillDefaultsBtn = document.getElementById('fillDefaults');
        const monthSelect = document.getElementById('attendance_month');
        const yearSelect = document.getElementById('attendance_year');
        const totalDaysInput = document.getElementById('total_days');
        const hiddenMonthYearInput = document.getElementById('attendance_month_year');

        // Function to update the hidden month-year field
        function updateMonthYear() {
            const month = monthSelect.value;
            const year = yearSelect.value;
            hiddenMonthYearInput.value = month + '-' + year;
        }

        // Function to calculate days in month
        function updateTotalDays() {
            const month = parseInt(monthSelect.value);
            const year = parseInt(yearSelect.value);
            const daysInMonth = new Date(year, month, 0).getDate();
            totalDaysInput.value = daysInMonth;
        }

        // Update hidden field and total days when month or year changes
        monthSelect.addEventListener('change', function() {
            updateMonthYear();
            updateTotalDays();
        });

        yearSelect.addEventListener('change', function() {
            updateMonthYear();
            updateTotalDays();
        });

        // Initialize on page load
        updateMonthYear();
        updateTotalDays();

        // Fill default values for all employees
        fillDefaultsBtn.addEventListener('click', function() {
            const workingDays = parseInt(workingDaysInput.value) || 26;

            // Fill present days with working days value for all employees
            document.querySelectorAll('input[name*="[present_days]"]').forEach(input => {
                if (!input.value) {
                    input.value = workingDays;
                }
            });

            // Set default values for other fields if empty
            document.querySelectorAll('input[name*="[overtime_hours]"]').forEach(input => {
                if (!input.value) {
                    input.value = '0';
                }
            });

            document.querySelectorAll('input[name*="[bonus_amount]"]').forEach(input => {
                if (!input.value) {
                    input.value = '0';
                }
            });

            document.querySelectorAll('input[name*="[advance_taken]"]').forEach(input => {
                if (!input.value) {
                    input.value = '0';
                }
            });

            document.querySelectorAll('input[name*="[previous_balance]"]').forEach(input => {
                if (!input.value) {
                    input.value = '0';
                }
            });
        });
    });
    </script>
    @endpush
</x-layouts.app-layout>
