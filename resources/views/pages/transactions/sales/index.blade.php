<x-layouts.app-layout>
    <div class="py-12">
        <div class="w-full mx-auto sm:px-6 lg:px-8">
            <div class="bg-white/80 backdrop-blur-md overflow-hidden shadow-xl sm:rounded-lg border border-gray-100">
                <div class="p-6 text-gray-900">
                    <div class="flex justify-between items-center mb-6">
                        <h1 class="text-2xl font-bold">Sales Transactions</h1>
                        <a href="{{ route('sales.create') }}" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg shadow-sm flex items-center">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                            </svg>
                            Add New
                        </a>
                    </div>

                    <!-- Filters Section -->
                    <div class="mb-6 bg-gray-50 p-4 rounded-lg border border-gray-200">
                        <form action="{{ route('sales.index') }}" method="GET" class="grid grid-cols-1 md:grid-cols-5 gap-4">
                            <div>
                                <label for="client_id" class="block text-sm font-medium text-gray-700 mb-1">Client</label>
                                <select name="client_id" id="client_id" class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-200 focus:ring-opacity-50">
                                    <option value="">All Clients</option>
                                    @foreach($clients as $client)

                                        <option value="{{ $client->id }}" {{ request('client_id') == $client->id ? 'selected' : '' }}>
                                            {{ $client->client_name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            <div>
                                <label for="sale_type" class="block text-sm font-medium text-gray-700 mb-1">Sale Type</label>
                                <select name="sale_type" id="sale_type" class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-200 focus:ring-opacity-50">
                                    <option value="">All</option>
                                    <option value="invoice" {{ request('sale_type') == 'invoice' ? 'selected' : '' }}>Invoice</option>
                                    <option value="cash" {{ request('sale_type') == 'cash' ? 'selected' : '' }}>Cash</option>
                                </select>
                            </div>

                            <div>
                                <label for="date_range" class="block text-sm font-medium text-gray-700 mb-1">Duration</label>
                                <select name="date_range" id="date_range" class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-200 focus:ring-opacity-50">
                                    <option value="current_month" {{ $dateRange == 'current_month' ? 'selected' : '' }}>Current Month</option>
                                    <option value="last_month" {{ $dateRange == 'last_month' ? 'selected' : '' }}>Last Month</option>
                                    <option value="current_quarter" {{ $dateRange == 'current_quarter' ? 'selected' : '' }}>Current Quarter</option>
                                    <option value="last_quarter" {{ $dateRange == 'last_quarter' ? 'selected' : '' }}>Last Quarter</option>
                                    <option value="current_year" {{ $dateRange == 'current_year' ? 'selected' : '' }}>Current Year</option>
                                    <option value="last_financial_year" {{ $dateRange == 'last_financial_year' ? 'selected' : '' }}>Last Financial Year</option>
                                    <option value="custom" {{ $dateRange == 'custom' ? 'selected' : '' }}>Custom</option>
                                </select>
                            </div>

                            <div>
                                <label for="payment_status" class="block text-sm font-medium text-gray-700 mb-1">Payment Status</label>
                                <select name="payment_status" id="payment_status" class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-200 focus:ring-opacity-50">
                                    <option value="">All</option>
                                    <option value="paid" {{ request('payment_status') == 'paid' ? 'selected' : '' }}>Paid</option>
                                    <option value="unpaid" {{ request('payment_status') == 'unpaid' ? 'selected' : '' }}>Unpaid</option>
                                </select>
                            </div>

                            <div>
                                <button type="submit" class="h-9 mt-6 px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2">
                                    Apply Filters
                                </button>
                                <a href="{{ route('sales.index') }}" class="h-9 mt-6 px-4 py-2 bg-gray-200 text-gray-700 rounded-md hover:bg-gray-300 focus:outline-none focus:ring-2 focus:ring-gray-500 focus:ring-offset-2 ml-2 inline-flex items-center">
                                    Reset
                                </a>
                            </div>

                            <!-- Custom Date Range Fields (Hidden by default) -->
                            <div id="custom-date-range" class="md:col-span-4 grid grid-cols-1 md:grid-cols-2 gap-4 {{ $dateRange == 'custom' ? '' : 'hidden' }}">
                                <div>
                                    <label for="start_date" class="block text-sm font-medium text-gray-700 mb-1">Start Date</label>
                                    <input type="text" name="start_date" id="start_date" class="datepicker w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-200 focus:ring-opacity-50"
                                        value="{{ $dateRange == 'custom' && $startDate ? $startDate->format('Y-m-d') : '' }}">
                                </div>
                                <div>
                                    <label for="end_date" class="block text-sm font-medium text-gray-700 mb-1">End Date</label>
                                    <input type="text" name="end_date" id="end_date" class="datepicker w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-200 focus:ring-opacity-50"
                                        value="{{ $dateRange == 'custom' && $endDate ? $endDate->format('Y-m-d') : '' }}">
                                </div>
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

                    <div class="overflow-x-auto bg-white/70 backdrop-blur-sm p-4 rounded-lg shadow-inner border border-white">
                        <table class="min-w-full bg-transparent">
                            <thead>
                                <tr class="bg-gray-100 text-gray-700 uppercase text-sm leading-normal">
                                    <th class="py-3 px-6 text-center">S.NO</th>
                                    <th class="py-3 px-6 text-center">Sale Date</th>
                                    <th class="py-3 px-6 text-center">Client Name</th>
                                    <th class="py-3 px-6 text-center">Sale Type</th>
                                    <th class="py-3 px-6 text-center">Base Amount (INR)</th>
                                    <th class="py-3 px-6 text-center">Tax (INR)</th>
                                    <th class="py-3 px-6 text-center">TDS Deducted (INR)</th>
                                    <th class="py-3 px-6 text-center">Total Amount (INR)</th>
                                    <th class="py-3 px-6 text-center">Payment Status</th>
                                    <th class="py-3 px-6 text-center">Actions</th>
                                </tr>
                            </thead>
                            <tbody class="text-gray-600 text-sm">
                                @forelse ($sales as $index => $sale)
                                    <tr class="border-b border-gray-200 hover:bg-gray-50">
                                        <td class="py-3 px-6 text-center">{{ $index + 1 }}</td>
                                        <td class="py-3 px-6 text-center">{{ $sale->transaction_date->format('d-m-Y') }}</td>
                                        <td class="py-3 px-6 text-center">{{ $sale->client->client_name ?? 'N/A' }}</td>
                                        <td class="py-3 px-6 text-center">{{ ucfirst($sale->sales_type) }}</td>
                                        <td class="py-3 px-6 text-center">{{ number_format($sale->base_amount, 2) }}</td>
                                        <td class="py-3 px-6 text-center">{{ number_format($sale->tax_amount, 2) }}<br>({{ $sale->tax_rate }}%)</td>
                                        <td class="py-3 px-6 text-center">{{ number_format($sale->tds, 2) }} <br> ({{$sale->tds_rate}}%)</td>
                                        <td class="py-3 px-6 text-center">{{ number_format($sale->total_amount, 2) }}</td>
                                        <td class="py-3 px-6 text-center">
                                            @if($sale->paid == 1)
                                                <span class="bg-green-100 text-green-800 px-2 py-1 rounded-full text-xs font-medium">
                                                    Paid
                                                </span>
                                            <br>
                                            @if($sale->payment)
                                                <span class="bg-green-100 text-green-800 px-2 py-1 rounded-lg text-xs font-medium mt-2 inline-block">
                                                    ID: {{ $sale->payment_id }} | {{ $sale->payment->transaction_date->format('d M Y')}}
                                                </span>
                                            @endif
                                            @else
                                                <span class="bg-red-100 mt-2 inline-block text-red-800 px-2 py-1 rounded-full text-xs font-medium">
                                                Due on: <br> {{ $sale->due_date ? $sale->due_date->format('d-m-Y') : 'N/A' }}
                                                </span>

                                            @endif
                                        </td>
                                        <td class="py-3 px-6 text-center">
                                            <div class="flex item-center justify-center space-x-2">
                                                <a href="{{ route('sales.edit', $sale->id) }}" class="bg-blue-100 text-blue-600 hover:bg-blue-200 px-3 py-1 rounded-md inline-flex items-center">
                                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                                    </svg>
                                                </a>
                                                <form method="POST" action="{{ route('sales.destroy', $sale->id) }}" class="inline-block" onsubmit="return confirm('Are you sure you want to delete this sale? This action cannot be undone.');">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="bg-red-100 text-red-600 hover:bg-red-200 px-3 py-1 rounded-md inline-flex items-center">
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
                                        <td colspan="10" class="py-8 text-center text-gray-500">
                                            <div class="flex flex-col items-center">
                                                <svg xmlns="http://www.w3.org/2000/svg" class="h-12 w-12 text-gray-400 mb-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                                </svg>
                                                <p class="text-lg">No sales found</p>
                                                <p class="text-sm">Create your first sale to get started</p>
                                            </div>
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

    <!-- jQuery UI Datepicker CDN - Load before custom script -->
    <link rel="stylesheet" href="https://code.jquery.com/ui/1.13.2/themes/base/jquery-ui.css">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://code.jquery.com/ui/1.13.2/jquery-ui.min.js"></script>

    <script>
        // Ensure jQuery is loaded and then initialize datepicker
        jQuery(document).ready(function($) {
            // Check if datepicker is available
            if (typeof $.fn.datepicker === 'function') {
                // Initialize start date picker
                $("#start_date").datepicker({
                    dateFormat: 'yy-mm-dd',
                    changeMonth: true,
                    changeYear: true,
                    maxDate: 0, // Disable future dates (0 = today)
                    onSelect: function(selectedDate) {
                        // Set minimum date for end date picker to the selected start date
                        $("#end_date").datepicker("option", "minDate", selectedDate);
                    }
                });

                // Initialize end date picker
                $("#end_date").datepicker({
                    dateFormat: 'yy-mm-dd',
                    changeMonth: true,
                    changeYear: true,
                    maxDate: 0, // Disable future dates (0 = today)
                    minDate: null // Will be set dynamically when start date is selected
                });

                // If start date already has a value, set the minimum date for end date
                var startDateValue = $("#start_date").val();
                if (startDateValue) {
                    $("#end_date").datepicker("option", "minDate", startDateValue);
                }
            } else {
                console.error('jQuery UI datepicker is not loaded');
            }

            // Show/hide custom date range fields based on selection
            $('#date_range').on('change', function() {
                if ($(this).val() === 'custom') {
                    $('#custom-date-range').removeClass('hidden');
                } else {
                    $('#custom-date-range').addClass('hidden');
                }
            });
        });
    </script>
</x-layouts.app-layout>
