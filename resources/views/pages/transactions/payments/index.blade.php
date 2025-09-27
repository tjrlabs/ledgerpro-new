<x-layouts.app-layout>
    <div class="py-12">
        <div class="w-full mx-auto sm:px-6 lg:px-8">
            <div class="bg-white/80 backdrop-blur-md overflow-hidden shadow-xl sm:rounded-lg border border-gray-100">
                <div class="p-6 text-gray-900">
                    <div class="flex justify-between items-center mb-6">
                        <h1 class="text-2xl font-bold">Payments</h1>
                        <a href="{{ route('payments.create') }}" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg shadow-sm flex items-center">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                            </svg>
                            Add Payment
                        </a>
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

                    <!-- Search and Filters Section -->
                    <div class="bg-gray-50/70 backdrop-blur-sm p-4 rounded-lg shadow-inner border border-white mb-6">
                        <form method="GET" action="{{ route('payments.index') }}" class="space-y-4">
                            <!-- Advanced Filters -->
                            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-3 gap-4">
                                <!-- Client Filter -->
                                <div>
                                    <label for="client_id" class="block text-sm font-medium text-gray-700 mb-1">Client</label>
                                    <select id="client_id" name="client_id" class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                                        <option value="">All Clients</option>
                                        @foreach($clients as $client)
                                            <option value="{{ $client->id }}" {{ $filters['client_id'] == $client->id ? 'selected' : '' }}>
                                                {{ $client->display_name ?: $client->client_name }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>

                                <!-- Payment Method Filter -->
                                <div>
                                    <label for="payment_method" class="block text-sm font-medium text-gray-700 mb-1">Payment Method</label>
                                    <select id="payment_method" name="payment_method" class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                                        <option value="">All Methods</option>
                                        @foreach($paymentMethods as $key => $method)
                                            <option value="{{ $key }}" {{ $filters['payment_method'] == $key ? 'selected' : '' }}>
                                                {{ $method }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>

                                <!-- Duration Filter -->
                                <div>
                                    <label for="date_range" class="block text-sm font-medium text-gray-700 mb-1">Duration</label>
                                    <select id="date_range" name="date_range" class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                                        <option value="current_month" {{ ($filters['date_range'] ?? 'current_month') == 'current_month' ? 'selected' : '' }}>Current Month</option>
                                        <option value="last_month" {{ ($filters['date_range'] ?? '') == 'last_month' ? 'selected' : '' }}>Last Month</option>
                                        <option value="current_quarter" {{ ($filters['date_range'] ?? '') == 'current_quarter' ? 'selected' : '' }}>Current Quarter</option>
                                        <option value="last_quarter" {{ ($filters['date_range'] ?? '') == 'last_quarter' ? 'selected' : '' }}>Last Quarter</option>
                                        <option value="current_year" {{ ($filters['date_range'] ?? '') == 'current_year' ? 'selected' : '' }}>Current Year</option>
                                        <option value="last_financial_year" {{ ($filters['date_range'] ?? '') == 'last_financial_year' ? 'selected' : '' }}>Last Financial Year</option>
                                        <option value="custom" {{ ($filters['date_range'] ?? '') == 'custom' ? 'selected' : '' }}>Custom</option>
                                    </select>
                                </div>
                            </div>

                            <!-- Custom Date Range Fields (Hidden by default) -->
                            <div id="custom-date-range" class="grid grid-cols-1 md:grid-cols-2 gap-4 mt-4 {{ ($filters['date_range'] ?? '') == 'custom' ? '' : 'hidden' }}">
                                <div>
                                    <label for="date_from" class="block text-sm font-medium text-gray-700 mb-1">Start Date</label>
                                    <input type="date"
                                           id="date_from"
                                           name="date_from"
                                           value="{{ $filters['date_from'] ?? '' }}"
                                           class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                                </div>
                                <div>
                                    <label for="date_to" class="block text-sm font-medium text-gray-700 mb-1">End Date</label>
                                    <input type="date"
                                           id="date_to"
                                           name="date_to"
                                           value="{{ $filters['date_to'] ?? '' }}"
                                           class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                                </div>
                            </div>

                            <!-- Filter Actions -->
                            <div class="flex flex-wrap gap-2">
                                <button type="submit" class="bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-md shadow-sm flex items-center">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.414A1 1 0 013 6.707V4z" />
                                    </svg>
                                    Apply Filters
                                </button>
                                <a href="{{ route('payments.index') }}" class="bg-gray-600 hover:bg-gray-700 text-white px-4 py-2 rounded-md shadow-sm flex items-center">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
                                    </svg>
                                    Clear All
                                </a>
                            </div>
                        </form>
                    </div>

                    <!-- Payments Table -->
                    <div class="overflow-x-auto bg-white/70 backdrop-blur-sm p-4 rounded-lg shadow-inner border border-white">
                        <table class="min-w-full bg-transparent">
                            <thead>
                                <tr class="bg-gray-100 text-gray-700 uppercase text-sm leading-normal">
                                    <th class="py-3 px-6 text-center">Date</th>
                                    <th class="py-3 px-6 text-center">Client</th>
                                    <th class="py-3 px-6 text-center">Amount (INR)</th>
                                    <th class="py-3 px-6 text-center">Payment Method</th>
                                    <th class="py-3 px-6 text-center">Notes</th>
                                    <th class="py-3 px-6 text-center">Actions</th>
                                </tr>
                            </thead>
                            <tbody class="text-gray-600 text-sm">
                                @forelse ($pagedPayments as $payment)
                                    <tr class="border-b border-gray-200 hover:bg-gray-50">
                                        <td class="py-3 px-6 text-center">
                                            {{ \Carbon\Carbon::parse($payment->payment_date)->format('M d, Y') }}
                                        </td>
                                        <td class="py-3 px-6 font-medium text-center">
                                            {{ $payment->client->client_name ?? 'N/A' }}
                                        </td>
                                        <td class="py-3 px-6 font-semibold text-green-600 text-center">
                                            {{ number_format($payment->total_amount, 2) }}
                                        </td>
                                        <td class="py-3 px-6 text-center">
                                            <span class="bg-blue-100 text-blue-800 text-xs font-medium px-2.5 py-0.5 rounded-full">
                                                {{ $paymentMethods[$payment->payment_method] ?? ucfirst($payment->payment_method) }}
                                            </span>
                                        </td>
                                        <td class="py-3 px-6 text-center">
                                            <div class="max-w-xs truncate" title="{{ $payment->notes }}">
                                                {{ $payment->notes ?? 'N/A' }}
                                            </div>
                                        </td>
                                        <td class="py-3 px-6 text-center">
                                            <div class="flex item-center justify-center gap-2">
                                                <a href="{{ route('payments.edit', $payment->id) }}" class="bg-blue-100 text-blue-600 hover:bg-blue-200 px-3 py-1 rounded-md inline-flex items-center">
                                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                                    </svg>
                                                    Edit
                                                </a>
                                                <form action="{{ route('payments.destroy', $payment->id) }}" method="POST" class="inline" onsubmit="return confirm('Are you sure you want to delete this payment? This action cannot be undone.')">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="bg-red-100 text-red-600 hover:bg-red-200 px-3 py-1 rounded-md inline-flex items-center">
                                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                                        </svg>
                                                        Delete
                                                    </button>
                                                </form>
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="7" class="py-8 text-center text-gray-400 text-base">
                                            <div class="flex flex-col items-center">
                                                <svg xmlns="http://www.w3.org/2000/svg" class="h-12 w-12 mb-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                                </svg>
                                                <p>No payments found</p>
                                                @if(array_filter($filters))
                                                    <p class="text-sm mt-1">Try adjusting your search or filters</p>
                                                @endif
                                            </div>
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    <!-- Pagination would go here if using Laravel's built-in pagination -->
                    @if($pagedPayments->count() > 0)
                        <div class="mt-6 flex justify-between items-center">
                            <div class="text-sm text-gray-700">
                                Showing {{ $pagedPayments->count() }} results
                            </div>
                            <!-- Custom pagination controls would be implemented here based on your pagination logic -->
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</x-layouts.app-layout>
