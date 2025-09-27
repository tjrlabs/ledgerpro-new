<x-layouts.app-layout>
    <div class="py-12">
        <div class="mx-auto sm:px-6 lg:px-8">
            <div class="bg-white/80 backdrop-blur-md overflow-hidden shadow-xl sm:rounded-lg border border-gray-100">
                <div class="p-6">
                    <div class="mb-6">
                        <a href="{{ route('expenses.index') }}" class="px-4 py-2 bg-gray-100 hover:bg-gray-200 text-gray-800 rounded-lg inline-flex items-center mb-4">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-1" viewBox="0 0 20 20" fill="currentColor">
                                <path fill-rule="evenodd" d="M9.707 16.707a1 1 0 01-1.414 0l-6-6a1 1 0 010-1.414l6-6a1 1 0 011.414 1.414L5.414 9H17a1 1 0 110 2H5.414l4.293 4.293a1 1 0 010 1.414z" clip-rule="evenodd" />
                            </svg>
                            Back to Expenses
                        </a>
                        <h1 class="text-2xl font-bold text-gray-900">{{ isset($isEditing) && $isEditing ? 'Edit Expense' : 'Create New Expense' }}</h1>
                    </div>

                    @if ($errors->any())
                        <div class="mb-4 p-4 bg-red-50 border-l-4 border-red-500 text-red-700">
                            <h4 class="text-lg font-medium mb-2">Please correct the following errors:</h4>
                            <ul class="list-disc pl-5">
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    @if(isset($isEditing) && $isEditing)
                        <form action="{{ route('expenses.update', $expense->id) }}" method="POST" class="space-y-6 p-5 bg-white/70 backdrop-blur-sm rounded-lg shadow-inner border border-white" id="expenseForm">
                        @method('PUT')
                    @else
                        <form action="{{ route('expenses.store') }}" method="POST" class="space-y-6 p-5 bg-white/70 backdrop-blur-sm rounded-lg shadow-inner border border-white" id="expenseForm">
                    @endif
                        @csrf

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <!-- Expense Type -->
                            <div>
                                <x-forms.label for="expense_type" value="Expense Type" required="true" />
                                <x-forms.select name="expense_type" id="expense_type" required>
                                    <option value="">Select Expense Type</option>
                                    @foreach($formOptions['expense_types'] as $key => $value)
                                        <option value="{{ $key }}" {{ (old('expense_type') == $key || (isset($expense) && $expense->sales_type == $key)) ? 'selected' : '' }}>
                                            {{ $value }}
                                        </option>
                                    @endforeach
                                    <x-slot name="afterIcon">
                                        <i class="fas fa-tags text-gray-500"></i>
                                    </x-slot>
                                </x-forms.select>
                                @error('expense_type')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <!-- Expense Date -->
                            <div>
                                <x-forms.label for="expense_date" value="Expense Date" required="true" />
                                <x-forms.input type="text" name="expense_date" id="expense_date" class="datepicker" value="{{ old('expense_date', isset($expense) ? $expense->transaction_date->format('Y-m-d') : $defaultDate) }}" placeholder="Select Expense Date" required>
                                    <x-slot name="afterIcon">
                                        <i class="fas fa-calendar text-gray-500"></i>
                                    </x-slot>
                                </x-forms.input>
                                @error('expense_date')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <!-- Base Amount -->
                            <div>
                                <x-forms.label for="base_amount" value="Base Amount" required="true" />
                                <x-forms.input type="number" name="base_amount" id="base_amount" step="0.01" min="0" value="{{ old('base_amount', isset($expense) ? $expense->base_amount : '') }}" placeholder="0.00" required>
                                    <x-slot name="afterIcon">
                                        <i class="fas fa-rupee-sign text-gray-500"></i>
                                    </x-slot>
                                </x-forms.input>
                                @error('base_amount')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <!-- Tax Rate -->
                            <div>
                                <x-forms.label for="tax_rate" value="Tax Percentage" />
                                <x-forms.select name="tax_rate" id="tax_rate">
                                    <option value="0" {{ (old('tax_rate') == '0' || (isset($expense) && $expense->tax_rate == '0')) ? 'selected' : '' }}>0%</option>
                                    <option value="3" {{ (old('tax_rate') == '3' || (isset($expense) && $expense->tax_rate == '3')) ? 'selected' : '' }}>3%</option>
                                    <option value="5" {{ (old('tax_rate') == '5' || (isset($expense) && $expense->tax_rate == '5')) ? 'selected' : '' }}>5%</option>
                                    <option value="12" {{ (old('tax_rate') == '12' || (isset($expense) && $expense->tax_rate == '12')) ? 'selected' : '' }}>12%</option>
                                    <option value="18" {{ (old('tax_rate') == '18' || (isset($expense) && $expense->tax_rate == '18')) ? 'selected' : '' }}>18%</option>
                                    <option value="28" {{ (old('tax_rate') == '28' || (isset($expense) && $expense->tax_rate == '28')) ? 'selected' : '' }}>28%</option>
                                    <x-slot name="afterIcon">
                                        <i class="fas fa-percentage text-gray-500"></i>
                                    </x-slot>
                                </x-forms.select>
                                @error('tax_rate')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>

                        <!-- Tax Amount (Auto-calculated) -->
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div>
                                <x-forms.label for="tax_amount" value="Tax Amount" />
                                <x-forms.input type="number" name="tax_amount" id="tax_amount" step="0.01" min="0" value="{{ old('tax_amount', isset($expense) ? $expense->tax_amount : '0.00') }}" placeholder="0.00" readonly>
                                    <x-slot name="afterIcon">
                                        <i class="fas fa-calculator text-gray-500"></i>
                                    </x-slot>
                                </x-forms.input>
                                @error('tax_amount')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <!-- Total Amount (Auto-calculated) -->
                            <div>
                                <x-forms.label for="total_amount" value="Total Amount" />
                                <x-forms.input type="number" name="total_amount" id="total_amount" step="0.01" min="0" value="{{ old('total_amount', isset($expense) ? $expense->total_amount : '0.00') }}" placeholder="0.00" readonly>
                                    <x-slot name="afterIcon">
                                        <i class="fas fa-money-bill text-gray-500"></i>
                                    </x-slot>
                                </x-forms.input>
                                @error('total_amount')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>

                        <!-- Payment Status -->
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div>
                                <x-forms.label for="paid" value="Payment Status" />
                                <x-forms.select name="paid" id="paid">
                                    <option value="0" {{ (old('paid') == '0' || (isset($expense) && !$expense->paid)) ? 'selected' : '' }}>Unpaid</option>
                                    <option value="1" {{ (old('paid') == '1' || (isset($expense) && $expense->paid)) ? 'selected' : '' }}>Paid</option>
                                    <x-slot name="afterIcon">
                                        <i class="fas fa-credit-card text-gray-500"></i>
                                    </x-slot>
                                </x-forms.select>
                                @error('paid')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>

                        <!-- Notes -->
                        <div>
                            <x-forms.label for="notes" value="Notes (Optional)" />
                            <x-forms.textarea name="notes" id="notes" rows="3" placeholder="Add any additional notes about this expense...">{{ old('notes', isset($expense) ? $expense->notes : '') }}
                                <x-slot name="afterIcon">
                                    <i class="fas fa-sticky-note text-gray-500"></i>
                                </x-slot>
                            </x-forms.textarea>
                            @error('notes')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="flex justify-end pt-4">
                            <x-forms.button type="submit" class="bg-blue-600 hover:bg-blue-700">
                                {{ isset($isEditing) && $isEditing ? 'Update Expense' : 'Create Expense' }}
                            </x-forms.button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- jQuery UI Datepicker CDN -->
    <link rel="stylesheet" href="https://code.jquery.com/ui/1.13.2/themes/base/jquery-ui.css">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://code.jquery.com/ui/1.13.2/jquery-ui.min.js"></script>

    <script>
        document.addEventListener("DOMContentLoaded", function() {
            // Make sure jQuery and jQuery UI are loaded
            if (typeof jQuery != 'undefined') {
                // Use jQuery's document ready to ensure the DOM is fully loaded
                jQuery(function($) {
                    // Check if datepicker is available
                    if ($.fn.datepicker) {
                        // Initialize datepickers
                        $(".datepicker").datepicker({
                            dateFormat: 'yy-mm-dd',
                            changeMonth: true,
                            changeYear: true,
                            maxDate: 0, // Disable future dates
                        });

                        // Calculate tax amount and total when base amount or tax rate changes
                        $("#base_amount, #tax_rate").on("change keyup", function() {
                            calculateAmounts();
                        });

                        // Handle expense type changes
                        $("#expense_type").on("change", function() {
                            handleExpenseTypeChange();
                        });

                        // Initial setup
                        handleExpenseTypeChange();
                        calculateAmounts();

                        // Function to handle expense type change
                        function handleExpenseTypeChange() {
                            const expenseType = $('#expense_type').val();
                            if (expenseType === 'cash') {
                                // Disable and clear tax-related fields for cash expenses
                                $('#tax_rate').prop('disabled', true).val('0');
                                $('#tax_amount').val('0.00');
                            } else if (expenseType === 'invoice') {
                                // Enable tax-related fields for invoice expenses
                                $('#tax_rate').prop('disabled', false);
                            } else {
                                // For empty selection, disable tax fields
                                $('#tax_rate').prop('disabled', true);
                            }

                            // Recalculate amounts after field changes
                            calculateAmounts();
                        }

                        // Function to calculate tax and total amounts
                        function calculateAmounts() {
                            var baseAmount = parseFloat($("#base_amount").val()) || 0;
                            var taxRate = parseFloat($("#tax_rate").val()) || 0;

                            var taxAmount = baseAmount * (taxRate / 100);
                            var totalAmount = baseAmount + taxAmount;

                            $("#tax_amount").val(taxAmount.toFixed(2));
                            $("#total_amount").val(totalAmount.toFixed(2));
                        }
                    } else {
                        console.error("jQuery UI Datepicker is not loaded properly");
                    }
                });
            } else {
                console.error("jQuery is not loaded properly");
            }
        });
    </script>
</x-layouts.app-layout>
