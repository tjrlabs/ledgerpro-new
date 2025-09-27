<x-layouts.app-layout>
    <div class="py-12">
        <div class="mx-auto sm:px-6 lg:px-8">
            <div class="bg-white/80 backdrop-blur-md overflow-hidden shadow-xl sm:rounded-lg border border-gray-100">
                <div class="p-6">
                    <div class="mb-6">
                        <a href="{{ route('sales.index') }}" class="px-4 py-2 bg-gray-100 hover:bg-gray-200 text-gray-800 rounded-lg inline-flex items-center mb-4">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-1" viewBox="0 0 20 20" fill="currentColor">
                                <path fill-rule="evenodd" d="M9.707 16.707a1 1 0 01-1.414 0l-6-6a1 1 0 010-1.414l6-6a1 1 0 011.414 1.414L5.414 9H17a1 1 0 110 2H5.414l4.293 4.293a1 1 0 010 1.414z" clip-rule="evenodd" />
                            </svg>
                            Back to Sales
                        </a>
                        <h1 class="text-2xl font-bold text-gray-900">{{ isset($isEditing) && $isEditing ? 'Edit Sale' : 'Create New Sale' }}</h1>
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
                        <form action="{{ route('sales.update', $sale->id) }}" method="POST" class="space-y-6 p-5 bg-white/70 backdrop-blur-sm rounded-lg shadow-inner border border-white" id="salesForm">
                        @method('PUT')
                    @else
                        <form action="{{ route('sales.store') }}" method="POST" class="space-y-6 p-5 bg-white/70 backdrop-blur-sm rounded-lg shadow-inner border border-white" id="salesForm">
                    @endif
                        @csrf

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <!-- Client Dropdown -->
                            <div>
                                <x-forms.label for="client_id" value="Client" required="true" />
                                <x-forms.select name="client_id" id="client_id" required>
                                    <option value="">Select Client</option>
                                    @foreach($clients as $client)
                                        <option value="{{ $client->id }}" {{ (old('client_id') == $client->id || (isset($sale) && $sale->client_id == $client->id)) ? 'selected' : '' }}>
                                            {{ $client->client_name }}
                                        </option>
                                    @endforeach
                                    <x-slot name="afterIcon">
                                        <i class="fas fa-user text-gray-500"></i>
                                    </x-slot>
                                </x-forms.select>
                                @error('client_id')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <!-- Sale Date -->
                            <div>
                                <x-forms.label for="sale_date" value="Sale Date" required="true" />
                                <x-forms.input type="text" name="sale_date" id="sale_date" class="datepicker" value="{{ old('sale_date', isset($sale) ? $sale->transaction_date->format('Y-m-d') : '') }}" placeholder="Select Sale Date" required>
                                    <x-slot name="afterIcon">
                                        <i class="fas fa-calendar text-gray-500"></i>
                                    </x-slot>
                                </x-forms.input>
                                @error('sale_date')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <!-- Sale Type -->
                            <div>
                                <x-forms.label for="sales_type" value="Sale Type" required="true" />
                                <x-forms.select name="sales_type" id="sales_type" required>
                                    <option value="">Select Sale Type</option>
                                    <option value="invoice" {{ (old('sales_type') == 'invoice' || (isset($sale) && $sale->sales_type == 'invoice')) ? 'selected' : '' }}>Invoice</option>
                                    <option value="cash" {{ (old('sales_type') == 'cash' || (isset($sale) && $sale->sales_type == 'cash')) ? 'selected' : '' }}>Cash</option>
                                    <x-slot name="afterIcon">
                                        <i class="fas fa-receipt text-gray-500"></i>
                                    </x-slot>
                                </x-forms.select>
                                @error('sales_type')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <!-- Due Date -->
                            <div>
                                <x-forms.label for="due_date" value="Due Date" />
                                <x-forms.input type="text" name="due_date" id="due_date" class="datepicker" value="{{ old('due_date', isset($sale) && isset($sale->due_date) ? $sale->due_date->format('Y-m-d') : '') }}" placeholder="Select Due Date">
                                    <x-slot name="afterIcon">
                                        <i class="fas fa-calendar-alt text-gray-500"></i>
                                    </x-slot>
                                </x-forms.input>
                                @error('due_date')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <!-- Base Amount -->
                            <div>
                                <x-forms.label for="base_amount" value="Base Amount" required="true" />
                                <x-forms.input type="number" name="base_amount" id="base_amount" step="0.01" min="0" value="{{ old('base_amount', isset($sale) ? $sale->base_amount : '') }}" placeholder="0.00" required>
                                    <x-slot name="afterIcon">
                                        <i class="fas fa-dollar-sign text-gray-500"></i>
                                    </x-slot>
                                </x-forms.input>
                                @error('base_amount')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <!-- Tax Percentage -->
                            <div>
                                <x-forms.label for="tax_rate" value="Tax Percentage" />
                                <x-forms.select name="tax_rate" id="tax_rate">
                                    <option value="">Select Tax Rate</option>
                                    <option value="0" {{ (old('tax_rate') == '0' || (isset($sale) && $sale->tax_rate == '0')) ? 'selected' : '' }}>0%</option>
                                    <option value="3" {{ (old('tax_rate') == '3' || (isset($sale) && $sale->tax_rate == '3')) ? 'selected' : '' }}>3%</option>
                                    <option value="6" {{ (old('tax_rate') == '6' || (isset($sale) && $sale->tax_rate == '6')) ? 'selected' : '' }}>6%</option>
                                    <option value="9" {{ (old('tax_rate') == '9' || (isset($sale) && $sale->tax_rate == '9')) ? 'selected' : '' }}>9%</option>
                                    <option value="12" {{ (old('tax_rate') == '12' || (isset($sale) && $sale->tax_rate == '12')) ? 'selected' : '' }}>12%</option>
                                    <option value="18" {{ (old('tax_rate') == '18' || (isset($sale) && $sale->tax_rate == '18')) ? 'selected' : '' }}>18%</option>
                                    <option value="25" {{ (old('tax_rate') == '25' || (isset($sale) && $sale->tax_rate == '25')) ? 'selected' : '' }}>25%</option>
                                    <x-slot name="afterIcon">
                                        <i class="fas fa-percentage text-gray-500"></i>
                                    </x-slot>
                                </x-forms.select>
                                @error('tax_rate')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <!-- Tax Amount (Read-only, calculated automatically) -->
                            <div>
                                <x-forms.label for="tax_amount" value="Tax Amount" />
                                <x-forms.input type="number" name="tax_amount" id="tax_amount" step="0.01" value="{{ old('tax_amount', isset($sale) ? $sale->tax_amount : '0.00') }}" readonly class="bg-gray-50">
                                    <x-slot name="afterIcon">
                                        <i class="fas fa-calculator text-gray-500"></i>
                                    </x-slot>
                                </x-forms.input>
                                @error('tax_amount')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <!-- TDS Rate -->
                            <div>
                                <x-forms.label for="tds_rate" value="TDS Rate %" />
                                <x-forms.select name="tds_rate" id="tds_rate">
                                    <option value="">Select TDS Rate</option>
                                    <option value="0" {{ (old('tds_rate') == '0' || (isset($sale) && $sale->tds_rate == '0')) ? 'selected' : '' }}>0%</option>
                                    <option value="1" {{ (old('tds_rate') == '1' || (isset($sale) && $sale->tds_rate == '1')) ? 'selected' : '' }}>1%</option>
                                    <option value="2" {{ (old('tds_rate') == '2' || (isset($sale) && $sale->tds_rate == '2')) ? 'selected' : '' }}>2%</option>
                                    <option value="10" {{ (old('tds_rate') == '10' || (isset($sale) && $sale->tds_rate == '10')) ? 'selected' : '' }}>10%</option>
                                    <x-slot name="afterIcon">
                                        <i class="fas fa-percentage text-gray-500"></i>
                                    </x-slot>
                                </x-forms.select>
                                @error('tds_rate')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <!-- TDS Amount (Read-only, calculated automatically) -->
                            <div>
                                <x-forms.label for="tds" value="TDS Amount" />
                                <x-forms.input type="number" name="tds" id="tds" step="0.01" value="{{ old('tds', isset($sale) ? $sale->tds : '0.00') }}" readonly class="bg-gray-50">
                                    <x-slot name="afterIcon">
                                        <i class="fas fa-calculator text-gray-500"></i>
                                    </x-slot>
                                </x-forms.input>
                                @error('tds')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <!-- Total Amount (Read-only, calculated automatically) -->
                            <div>
                                <x-forms.label for="total_amount" value="Total Amount" />
                                <x-forms.input type="number" name="total_amount" id="total_amount" step="0.01" value="{{ old('total_amount', isset($sale) ? $sale->total_amount : '0.00') }}" readonly class="bg-gray-50 font-semibold">
                                    <x-slot name="afterIcon">
                                        <i class="fas fa-money-bill text-gray-500"></i>
                                    </x-slot>
                                </x-forms.input>
                                @error('total_amount')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <!-- Payment Status -->
                            <div>
                                <x-forms.label for="paid" value="Payment Status" />
                                <x-forms.select name="paid" id="paid">
                                    <option value="0" {{ (old('paid') == '0' || (isset($sale) && !$sale->paid)) ? 'selected' : '' }}>Unpaid</option>
                                    <option value="1" {{ (old('paid') == '1' || (isset($sale) && $sale->paid)) ? 'selected' : '' }}>Paid</option>
                                    <x-slot name="afterIcon">
                                        <i class="fas fa-credit-card text-gray-500"></i>
                                    </x-slot>
                                </x-forms.select>
                                @error('paid')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <!-- Payment ID (optional, for linking to payment transaction) -->
                            <div>
                                <x-forms.label for="payment_id" value="Payment ID (Optional)" />
                                <x-forms.input type="number" name="payment_id" id="payment_id" value="{{ old('payment_id', isset($sale) ? $sale->payment_id : '') }}" placeholder="Enter Payment Transaction ID">
                                    <x-slot name="afterIcon">
                                        <i class="fas fa-link text-gray-500"></i>
                                    </x-slot>
                                </x-forms.input>
                                @error('payment_id')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>

                        <!-- Notes -->
                        <div>
                            <x-forms.label for="notes" value="Notes (Optional)" />
                            <x-forms.textarea name="notes" id="notes" rows="3" placeholder="Add any additional notes about this sale...">{{ old('notes', isset($sale) ? $sale->notes : '') }}</x-forms.textarea>
                            @error('notes')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Submit Button -->
                        <div class="flex items-center justify-end space-x-3">
                            <a href="{{ route('sales.index') }}" class="px-4 py-2 bg-gray-300 hover:bg-gray-400 text-gray-800 rounded-lg">
                                Cancel
                            </a>
                            <button type="submit" class="px-6 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-lg flex items-center">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                                </svg>
                                {{ isset($isEditing) && $isEditing ? 'Update Sale' : 'Create Sale' }}
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Include jQuery and Datepicker libraries -->
    <link rel="stylesheet" href="https://code.jquery.com/ui/1.13.2/themes/base/jquery-ui.css">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://code.jquery.com/ui/1.13.2/jquery-ui.min.js"></script>

    <script>
        jQuery(document).ready(function($) {
            // Initialize datepickers
            if (typeof $.fn.datepicker === 'function') {
                $('.datepicker').datepicker({
                    dateFormat: 'yy-mm-dd',
                    changeMonth: true,
                    changeYear: true,
                    maxDate: 0 // Disable future dates
                });
            }

            // Function to handle sale type changes
            function handleSaleTypeChange() {
                const salesType = $('#sales_type').val();
                const taxFields = ['#tax_rate', '#tax_amount'];
                const tdsFields = ['#tds_rate', '#tds'];

                if (salesType === 'cash') {
                    // Disable and reset tax and TDS fields for cash sales
                    [...taxFields, ...tdsFields].forEach(field => {
                        $(field).prop('disabled', true).addClass('bg-gray-100 cursor-not-allowed');
                        if (field === '#tax_rate' || field === '#tds_rate') {
                            $(field).val('0');
                        } else {
                            $(field).val('0.00');
                        }
                    });
                } else if (salesType === 'invoice') {
                    // Enable tax and TDS fields for invoice sales
                    [...taxFields, ...tdsFields].forEach(field => {
                        $(field).prop('disabled', false).removeClass('bg-gray-100 cursor-not-allowed');
                    });
                } else {
                    // Default state - enable all fields
                    [...taxFields, ...tdsFields].forEach(field => {
                        $(field).prop('disabled', false).removeClass('bg-gray-100 cursor-not-allowed');
                    });
                }

                // Recalculate amounts after changing field states
                calculateAmounts();
            }

            // Auto-calculate tax, TDS, and total amounts
            function calculateAmounts() {
                const baseAmount = parseFloat($('#base_amount').val()) || 0;
                const taxRate = parseFloat($('#tax_rate').val()) || 0;
                const tdsRate = parseFloat($('#tds_rate').val()) || 0;

                const taxAmount = (baseAmount * taxRate) / 100;
                const tdsAmount = (baseAmount * tdsRate) / 100;
                const totalAmount = baseAmount + taxAmount - tdsAmount;

                $('#tax_amount').val(taxAmount.toFixed(2));
                $('#tds').val(tdsAmount.toFixed(2));
                $('#total_amount').val(totalAmount.toFixed(2));
            }

            // Bind events
            $('#sales_type').on('change', handleSaleTypeChange);
            $('#base_amount, #tax_rate, #tds_rate').on('input change', calculateAmounts);

            // Initialize on page load
            handleSaleTypeChange();
            calculateAmounts();
        });
    </script>
</x-layouts.app-layout>
