<x-layouts.app-layout>
    <div class="py-12">
        <div class="mx-auto sm:px-6 lg:px-8">
            <div class="bg-white/80 backdrop-blur-md overflow-hidden shadow-xl sm:rounded-lg border border-gray-100">
                <div class="p-6">
                    <div class="mb-6">
                        <a href="{{ route('clients.index') }}" class="px-4 py-2 bg-gray-100 hover:bg-gray-200 text-gray-800 rounded-lg inline-flex items-center mb-4">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-1" viewBox="0 0 20 20" fill="currentColor">
                                <path fill-rule="evenodd" d="M9.707 16.707a1 1 0 01-1.414 0l-6-6a1 1 0 010-1.414l6-6a1 1 0 011.414 1.414L5.414 9H17a1 1 0 110 2H5.414l4.293 4.293a1 1 0 010 1.414z" clip-rule="evenodd" />
                            </svg>
                            Back to Clients
                        </a>
                        <h1 class="text-2xl font-bold text-gray-900">{{ isset($isEditing) && $isEditing ? 'Edit Client' : 'Create New Client' }}</h1>
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
                        <form action="{{ route('clients.update', $client->id) }}" method="POST" class="space-y-6 p-5 bg-white/70 backdrop-blur-sm rounded-lg shadow-inner border border-white" id="clientForm">
                        @method('PUT')
                    @else
                        <form action="{{ route('clients.store') }}" method="POST" class="space-y-6 p-5 bg-white/70 backdrop-blur-sm rounded-lg shadow-inner border border-white" id="clientForm">
                    @endif
                        @csrf

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div>
                                <x-forms.label for="client_name" value="Client Name" required="true" />
                                <x-forms.input type="text" name="client_name" id="client_name" value="{{ old('client_name', isset($client) ? $client->client_name : '') }}" placeholder="Enter Client Name" required>
                                    <x-slot name="afterIcon">
                                        <i class="fas fa-building text-gray-500"></i>
                                    </x-slot>
                                </x-forms.input>
                                @error('client_name')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <div>
                                <x-forms.label for="display_name" value="Display Name" />
                                <x-forms.input type="text" name="display_name" id="display_name" value="{{ old('display_name', isset($client) ? $client->display_name : '') }}" placeholder="Enter Display Name">
                                    <x-slot name="afterIcon">
                                        <i class="fas fa-id-card text-gray-500"></i>
                                    </x-slot>
                                </x-forms.input>
                                @error('display_name')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div>
                                <x-forms.label for="client_email" value="Email" />
                                <x-forms.input type="email" name="client_email" id="client_email" value="{{ old('client_email', isset($client) ? $client->client_email : '') }}" placeholder="Enter Client Email">
                                    <x-slot name="afterIcon">
                                        <i class="fas fa-envelope text-gray-500"></i>
                                    </x-slot>
                                </x-forms.input>
                                @error('client_email')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <div>
                                <x-forms.label for="client_phone" value="Phone Number" />
                                <x-forms.input type="text" name="client_phone" id="client_phone" value="{{ old('client_phone', isset($client) ? $client->client_phone : '') }}" placeholder="Enter Phone Number">
                                    <x-slot name="afterIcon">
                                        <i class="fas fa-phone text-gray-500"></i>
                                    </x-slot>
                                </x-forms.input>
                                @error('client_phone')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div>
                                <x-forms.label for="client_type" value="Client Type" required="true" />
                                <x-forms.select name="client_type" id="client_type" required>
                                    <option value="">Select Client Type</option>
                                    <option value="Individual" {{ old('client_type', isset($client) ? $client->client_type : '') == 'Individual' ? 'selected' : '' }}>Individual</option>
                                    <option value="Business" {{ old('client_type', isset($client) ? $client->client_type : '') == 'Business' ? 'selected' : '' }}>Business</option>
                                    <option value="Government" {{ old('client_type', isset($client) ? $client->client_type : '') == 'Government' ? 'selected' : '' }}>Government</option>
                                    <option value="Non-Profit" {{ old('client_type', isset($client) ? $client->client_type : '') == 'Non-Profit' ? 'selected' : '' }}>Non-Profit</option>
                                    <x-slot name="afterIcon">
                                        <i class="fas fa-users text-gray-500"></i>
                                    </x-slot>
                                </x-forms.select>
                                @error('client_type')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <div>
                                <x-forms.label for="client_tax_number" value="Tax Number" />
                                <x-forms.input type="text" name="client_tax_number" id="client_tax_number" value="{{ old('client_tax_number', isset($client) ? $client->client_tax_number : '') }}" placeholder="Enter Tax Number (GST/VAT/TIN)">
                                    <x-slot name="afterIcon">
                                        <i class="fas fa-file-invoice-dollar text-gray-500"></i>
                                    </x-slot>
                                </x-forms.input>
                                @error('client_tax_number')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div>
                                <x-forms.label for="billing_address" value="Billing Address" />
                                <x-forms.select name="billing_address" id="billing_address">
                                    <option value="">Select Billing Address</option>
                                    <!-- Address options will be populated dynamically when implemented -->
                                    <x-slot name="afterIcon">
                                        <i class="fas fa-file-invoice text-gray-500"></i>
                                    </x-slot>
                                </x-forms.select>
                                @error('billing_address')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <div>
                                <x-forms.label for="shipping_address" value="Shipping Address" />
                                <x-forms.select name="shipping_address" id="shipping_address">
                                    <option value="">Select Shipping Address</option>
                                    <!-- Address options will be populated dynamically when implemented -->
                                    <x-slot name="afterIcon">
                                        <i class="fas fa-shipping-fast text-gray-500"></i>
                                    </x-slot>
                                </x-forms.select>
                                @error('shipping_address')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>

                        <div>
                            <div class="flex items-center">
                                <input type="checkbox" name="is_active" id="is_active" value="1" {{ old('is_active', isset($client) && $client->is_active ? 'checked' : '') }} class="rounded border-gray-300 text-blue-600 shadow-sm focus:border-blue-300 focus:ring focus:ring-blue-200 focus:ring-opacity-50">
                                <label for="is_active" class="ml-2 block text-sm text-gray-900">Active Client</label>
                            </div>
                            @error('is_active')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <div class="flex items-center">
                                @php
                                    $hasAccountBalance = isset($accountBalance) && $accountBalance;
                                    $checkboxChecked = old('add_opening_balance') ? true : $hasAccountBalance;
                                @endphp
                                <input type="checkbox" name="add_opening_balance" id="add_opening_balance" value="1"
                                       {{ $checkboxChecked ? 'checked' : '' }}
                                       class="rounded border-gray-300 text-blue-600 shadow-sm focus:border-blue-300 focus:ring focus:ring-blue-200 focus:ring-opacity-50"
                                       onchange="toggleOpeningBalanceFields()">
                                <label for="add_opening_balance" class="ml-2 block text-sm text-gray-900">Add opening account balance. (By default, it will be set to 0 starting current month).</label>
                            </div>
                            @error('add_opening_balance')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div id="opening_balance_fields" class="space-y-6" style="display: {{ old('add_opening_balance') || $hasAccountBalance ? 'block' : 'none' }};">
                            <div>
                                <x-forms.label for="account_balance" value="Account Balance (INR)" required="true" />
                                <x-forms.input type="number" name="account_balance" id="account_balance"
                                               value="{{ old('account_balance', isset($accountBalance) ? $accountBalance->opening_balance : '') }}"
                                               placeholder="Enter Account Balance" step="0.01" min="0">
                                    <x-slot name="afterIcon">
                                        <i class="fas fa-rupee-sign text-gray-500"></i>
                                    </x-slot>
                                </x-forms.input>
                                @error('account_balance')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <div>
                                    <x-forms.label for="applicable_month" value="Applicable Month" required="true" />
                                    @if(isset($isEditing) && $isEditing && $hasAccountBalance)
                                        <x-forms.input type="text" name="applicable_month_display" id="applicable_month_display"
                                                       value="{{ isset($accountBalance) ? $accountBalance->month_name : '' }}"
                                                       readonly class="bg-gray-100 cursor-not-allowed">
                                            <x-slot name="afterIcon">
                                                <i class="fas fa-calendar text-gray-400"></i>
                                            </x-slot>
                                        </x-forms.input>
                                        <input type="hidden" name="applicable_month" value="{{ isset($accountBalance) ? $accountBalance->month : '' }}">
                                        <p class="mt-1 text-xs text-gray-500">Month cannot be changed when editing existing balance</p>
                                    @else
                                        <x-forms.select name="applicable_month" id="applicable_month">
                                            <option value="">Select Month</option>
                                            @php
                                                $selectedMonth = old('applicable_month', isset($accountBalance) ? $accountBalance->month : '');
                                            @endphp
                                            <option value="1" {{ $selectedMonth == '1' ? 'selected' : '' }}>January</option>
                                            <option value="2" {{ $selectedMonth == '2' ? 'selected' : '' }}>February</option>
                                            <option value="3" {{ $selectedMonth == '3' ? 'selected' : '' }}>March</option>
                                            <option value="4" {{ $selectedMonth == '4' ? 'selected' : '' }}>April</option>
                                            <option value="5" {{ $selectedMonth == '5' ? 'selected' : '' }}>May</option>
                                            <option value="6" {{ $selectedMonth == '6' ? 'selected' : '' }}>June</option>
                                            <option value="7" {{ $selectedMonth == '7' ? 'selected' : '' }}>July</option>
                                            <option value="8" {{ $selectedMonth == '8' ? 'selected' : '' }}>August</option>
                                            <option value="9" {{ $selectedMonth == '9' ? 'selected' : '' }}>September</option>
                                            <option value="10" {{ $selectedMonth == '10' ? 'selected' : '' }}>October</option>
                                            <option value="11" {{ $selectedMonth == '11' ? 'selected' : '' }}>November</option>
                                            <option value="12" {{ $selectedMonth == '12' ? 'selected' : '' }}>December</option>
                                            <x-slot name="afterIcon">
                                                <i class="fas fa-calendar text-gray-500"></i>
                                            </x-slot>
                                        </x-forms.select>
                                    @endif
                                    @error('applicable_month')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                    @enderror
                                </div>

                                <div>
                                    <x-forms.label for="applicable_year" value="Applicable Year" required="true" />
                                    @if(isset($isEditing) && $isEditing && $hasAccountBalance)
                                        <x-forms.input type="text" name="applicable_year_display" id="applicable_year_display"
                                                       value="{{ isset($accountBalance) ? $accountBalance->year : '' }}"
                                                       readonly class="bg-gray-100 cursor-not-allowed">
                                            <x-slot name="afterIcon">
                                                <i class="fas fa-calendar-alt text-gray-400"></i>
                                            </x-slot>
                                        </x-forms.input>
                                        <input type="hidden" name="applicable_year" value="{{ isset($accountBalance) ? $accountBalance->year : '' }}">
                                        <p class="mt-1 text-xs text-gray-500">Year cannot be changed when editing existing balance</p>
                                    @else
                                        <x-forms.select name="applicable_year" id="applicable_year">
                                            <option value="">Select Year</option>
                                            @php
                                                $currentYear = date('Y');
                                                $startYear = $currentYear - 5;
                                                $endYear = $currentYear + 5;
                                                $selectedYear = old('applicable_year', isset($accountBalance) ? $accountBalance->year : $currentYear);
                                            @endphp
                                            @for($year = $startYear; $year <= $endYear; $year++)
                                                <option value="{{ $year }}" {{ $selectedYear == $year ? 'selected' : '' }}>{{ $year }}</option>
                                            @endfor
                                            <x-slot name="afterIcon">
                                                <i class="fas fa-calendar-alt text-gray-500"></i>
                                            </x-slot>
                                        </x-forms.select>
                                    @endif
                                    @error('applicable_year')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="flex justify-end pt-4">
                            <x-forms.button type="submit" class="bg-blue-600 hover:bg-blue-700">
                                {{ isset($isEditing) && $isEditing ? 'Update Client' : 'Create Client' }}
                            </x-forms.button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script>
        function toggleOpeningBalanceFields() {
            const checkbox = document.getElementById('add_opening_balance');
            const fields = document.getElementById('opening_balance_fields');
            const accountBalanceInput = document.getElementById('account_balance');
            const monthSelect = document.getElementById('applicable_month');
            const yearSelect = document.getElementById('applicable_year');

            if (checkbox.checked) {
                fields.style.display = 'block';
                accountBalanceInput.setAttribute('required', 'required');
                monthSelect.setAttribute('required', 'required');
                yearSelect.setAttribute('required', 'required');
            } else {
                fields.style.display = 'none';
                accountBalanceInput.removeAttribute('required');
                monthSelect.removeAttribute('required');
                yearSelect.removeAttribute('required');
                // Clear the values when hiding
                accountBalanceInput.value = '';
                monthSelect.value = '';
                yearSelect.value = '';
            }
        }

        // Set default month and year when checkbox is checked
        document.getElementById('add_opening_balance').addEventListener('change', function() {
            if (this.checked) {
                const currentMonth = new Date().getMonth() + 1;
                const currentYear = new Date().getFullYear();

                if (!document.getElementById('applicable_month').value) {
                    document.getElementById('applicable_month').value = currentMonth;
                }
                if (!document.getElementById('applicable_year').value) {
                    document.getElementById('applicable_year').value = currentYear;
                }
            }
        });
    </script>
</x-layouts.app-layout>
