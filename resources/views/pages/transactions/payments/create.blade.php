<x-layouts.app-layout>
    <div class="py-12">
        <div class="mx-auto sm:px-6 lg:px-8">
            <div class="bg-white/80 backdrop-blur-md overflow-hidden shadow-xl sm:rounded-lg border border-gray-100">
                <div class="p-6">
                    <div class="mb-6">
                        <a href="{{ route('payments.index') }}" class="px-4 py-2 bg-gray-100 hover:bg-gray-200 text-gray-800 rounded-lg inline-flex items-center mb-4">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-1" viewBox="0 0 20 20" fill="currentColor">
                                <path fill-rule="evenodd" d="M9.707 16.707a1 1 0 01-1.414 0l-6-6a1 1 0 010-1.414l6-6a1 1 0 011.414 1.414L5.414 9H17a1 1 0 110 2H5.414l4.293 4.293a1 1 0 010 1.414z" clip-rule="evenodd" />
                            </svg>
                            Back to Payments
                        </a>
                        <h1 class="text-2xl font-bold text-gray-900">{{ isset($isEditing) && $isEditing ? 'Edit Payment' : 'Create New Payment' }}</h1>
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
                        <form action="{{ route('payments.update', $payment->id) }}" method="POST" class="space-y-6 p-5 bg-white/70 backdrop-blur-sm rounded-lg shadow-inner border border-white" id="paymentForm">
                        @method('PUT')
                    @else
                        <form action="{{ route('payments.store') }}" method="POST" class="space-y-6 p-5 bg-white/70 backdrop-blur-sm rounded-lg shadow-inner border border-white" id="paymentForm">
                    @endif
                        @csrf

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <!-- Client Selection -->
                            <div>
                                <x-forms.label for="client_id" value="Client" required="true" />
                                <x-forms.select name="client_id" id="client_id" required>
                                    <option value="">Select Client</option>
                                    @foreach($clients as $client)
                                        <option value="{{ $client->id }}" {{ (old('client_id') == $client->id || (isset($payment) && $payment->client_id == $client->id)) ? 'selected' : '' }}>
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

                            <!-- Payment Date -->
                            <div>
                                <x-forms.label for="payment_date" value="Payment Date" required="true" />
                                <x-forms.input type="text" name="payment_date" id="payment_date" class="datepicker" value="{{ old('payment_date', isset($payment) ? $payment->transaction_date->format('Y-m-d') : $defaultDate) }}" placeholder="Select Payment Date" required>
                                    <x-slot name="afterIcon">
                                        <i class="fas fa-calendar text-gray-500"></i>
                                    </x-slot>
                                </x-forms.input>
                                @error('payment_date')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <!-- Amount Paid -->
                            <div>
                                <x-forms.label for="amount_paid" value="Amount Paid" required="true" />
                                <x-forms.input type="number" name="amount_paid" id="amount_paid" step="0.01" min="0.01" value="{{ old('amount_paid', isset($payment) ? $payment->total_amount : '') }}" placeholder="0.00" required>
                                    <x-slot name="afterIcon">
                                        <i class="fas fa-rupee-sign text-gray-500"></i>
                                    </x-slot>
                                </x-forms.input>
                                @error('amount_paid')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <!-- Payment Method -->
                            <div>
                                <x-forms.label for="payment_method" value="Payment Method" />
                                <x-forms.select name="payment_method" id="payment_method">
                                    <option value="">Select Payment Method</option>
                                    @foreach($paymentMethods as $key => $value)
                                        <option value="{{ $key }}" {{ (old('payment_method') == $key || (isset($payment) && $payment->payment_method == $key)) ? 'selected' : '' }}>
                                            {{ $value }}
                                        </option>
                                    @endforeach
                                    <x-slot name="afterIcon">
                                        <i class="fas fa-credit-card text-gray-500"></i>
                                    </x-slot>
                                </x-forms.select>
                                @error('payment_method')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>

                        <!-- Notes -->
                        <div>
                            <x-forms.label for="notes" value="Notes" />
                            <textarea name="notes" id="notes" rows="4" class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-200 focus:ring-opacity-50" placeholder="Enter any additional notes about this payment...">{{ old('notes', isset($payment) ? $payment->notes : '') }}</textarea>
                            @error('notes')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="flex justify-end pt-4">
                            <x-forms.button type="submit" class="bg-blue-600 hover:bg-blue-700">
                                {{ isset($isEditing) && $isEditing ? 'Update Payment' : 'Create Payment' }}
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
                    } else {
                        console.warn('jQuery UI Datepicker is not available');
                    }
                });
            } else {
                console.warn('jQuery is not available');
            }
        });
    </script>
</x-layouts.app-layout>
