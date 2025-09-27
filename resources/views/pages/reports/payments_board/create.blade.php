<x-layouts.app-layout>
    <div class="py-12">
        <div class="w-full mx-auto sm:px-6 lg:px-8">
            <div class="bg-white/80 backdrop-blur-md overflow-hidden shadow-xl sm:rounded-lg border border-gray-100">
                <div class="p-6 text-gray-900">
                    <div class="flex justify-between items-center mb-6">
                        <h1 class="text-2xl font-bold">Create Payments Board</h1>
                        <a href="{{ route('reports.payments.board') }}" class="bg-gray-600 hover:bg-gray-700 text-white px-4 py-2 rounded-lg shadow-sm flex items-center">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                            </svg>
                            Back to Payments Board
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

                    <form method="POST" action="{{ route('reports.payments.board.store') }}" id="paymentsForm">
                        @csrf

                        <!-- Period Configuration -->
                        <div class="bg-gray-50 p-6 rounded-lg border border-gray-200 mb-6">
                            <h3 class="text-lg font-semibold text-gray-900 mb-4">Payments Period Configuration</h3>

                            <div class="grid grid-cols-2 md:grid-cols-2 gap-6">
                                <div>
                                    <label for="payments_month" class="block text-sm font-medium text-gray-700 mb-2">Month</label>
                                    <select name="payments_month" id="payments_month" class="border border-gray-300 rounded-lg px-3 py-2 w-full bg-white shadow-sm focus:border-blue-500 focus:ring-blue-500" required>
                                        @foreach($monthOptions as $value => $label)
                                            <option value="{{ $value }}" {{ old('payments_month', $currentMonth) == $value ? 'selected' : '' }}>
                                                {{ $label }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>

                                <div>
                                    <label for="payments_year" class="block text-sm font-medium text-gray-700 mb-2">Year</label>
                                    <select name="payments_year" id="payments_year" class="border border-gray-300 rounded-lg px-3 py-2 w-full bg-white shadow-sm focus:border-blue-500 focus:ring-blue-500" required>
                                        @foreach($yearOptions as $value => $label)
                                            <option value="{{ $value }}" {{ old('payments_year', $currentYear) == $value ? 'selected' : '' }}>
                                                {{ $label }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>

                            <!-- Hidden field to combine month and year -->
                            <input type="hidden" name="payments_month_year" id="payments_month_year" value="{{ old('payments_month_year', $currentMonth . '-' . $currentYear) }}">
                        </div>

                        <!-- Submit Buttons -->
                        <div class="flex justify-end space-x-4 mt-6">
                            <a href="{{ route('reports.payments.board') }}" class="bg-gray-300 hover:bg-gray-400 text-gray-700 px-6 py-2 rounded-lg">
                                Cancel
                            </a>
                            <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-2 rounded-lg shadow-sm">
                                Create Payments Board
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
        const monthSelect = document.getElementById('payments_month');
        const yearSelect = document.getElementById('payments_year');
        const hiddenMonthYearInput = document.getElementById('payments_month_year');

        // Function to update the hidden month-year field
        function updateMonthYear() {
            const month = monthSelect.value;
            const year = yearSelect.value;
            hiddenMonthYearInput.value = month + '-' + year;
        }

        // Update hidden field when month or year changes
        monthSelect.addEventListener('change', updateMonthYear);
        yearSelect.addEventListener('change', updateMonthYear);
    });
    </script>
    @endpush
</x-layouts.app-layout>
