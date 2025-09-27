<x-layouts.app-layout>
    <div class="py-12">
        <div class="mx-auto sm:px-6 lg:px-8">
            <div class="bg-white/80 backdrop-blur-md overflow-hidden shadow-xl sm:rounded-lg border border-gray-100">
                <div class="p-6">
                    <!-- Header -->
                    <div class="mb-6">
                        <h1 class="text-2xl font-bold text-gray-900 mb-2">General Ledger</h1>
                        <p class="text-gray-600">Complete transaction history and running balance</p>
                    </div>

                    <!-- Statistics Cards -->
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
                        <div class="bg-gradient-to-r from-blue-500 to-blue-600 rounded-lg p-4 text-white">
                            <div class="flex items-center justify-between">
                                <div>
                                    <p class="text-green-100 text-sm">Total Sales</p>
                                    <p class="text-2xl font-bold">₹{{ number_format($statistics['total_income'], 2) }}</p>
                                </div>
                                <div class="bg-blue-400 rounded-full p-2">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" />
                                    </svg>
                                </div>
                            </div>
                        </div>

                        <div class="bg-gradient-to-r from-green-500 to-green-600 rounded-lg p-4 text-white">
                            <div class="flex items-center justify-between">
                                <div>
                                    <p class="text-blue-100 text-sm">Total Payments</p>
                                    <p class="text-2xl font-bold">₹{{ number_format($statistics['total_payments'], 2) }}</p>
                                </div>
                                <div class="bg-green-400 rounded-full p-2">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                    </svg>
                                </div>
                            </div>
                        </div>

                        <div class="bg-gradient-to-r from-orange-500 to-orange-600 rounded-lg p-4 text-white">
                            <div class="flex items-center justify-between">
                                <div>
                                    <p class="text-orange-100 text-sm">Outstanding Balance</p>
                                    <p class="text-2xl font-bold">₹{{ number_format($statistics['total_income'] - $statistics['total_payments'], 2) }}</p>
                                </div>
                                <div class="bg-orange-400 rounded-full p-2">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v3m0 0v3m0-3h3m-3 0H9m12 0a9 9 0 11-18 0 9 9 0 0118 0z" />
                                    </svg>
                                </div>
                            </div>
                        </div>

                        <div class="bg-gradient-to-r from-gray-500 to-gray-600 rounded-lg p-4 text-white">
                            <div class="flex items-center justify-between">
                                <div>
                                    <p class="text-gray-100 text-sm">Total Transactions</p>
                                    <p class="text-2xl font-bold">{{ $statistics['transaction_count'] }}</p>
                                </div>
                                <div class="bg-gray-400 rounded-full p-2">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                    </svg>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Filters -->
                    <div class="bg-gray-50 rounded-lg p-4 mb-6">
                        <form method="GET" action="{{ route('ledger.index') }}" class="space-y-4">
                            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
                                <!-- Date Range -->
                                <div>
                                    <label for="date_range" class="block text-sm font-medium text-gray-700 mb-1">Date Range</label>
                                    <select name="date_range" id="date_range" class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-200 focus:ring-opacity-50">
                                        @foreach($dateRangeOptions as $value => $label)
                                            <option value="{{ $value }}" {{ $dateRange == $value ? 'selected' : '' }}>{{ $label }}</option>
                                        @endforeach
                                    </select>
                                </div>

                                <!-- Client Filter -->
                                <div>
                                    <label for="client_id" class="block text-sm font-medium text-gray-700 mb-1">Client</label>
                                    <select name="client_id" id="client_id" class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-200 focus:ring-opacity-50">
                                        <option value="">All Clients</option>
                                        @foreach($clients as $client)
                                            <option value="{{ $client->id }}" {{ $clientId == $client->id ? 'selected' : '' }}>{{ $client->client_name }}</option>
                                        @endforeach
                                    </select>
                                </div>

                                <!-- Transaction Type Filter -->
                                <div>
                                    <label for="transaction_type" class="block text-sm font-medium text-gray-700 mb-1">Transaction Type</label>
                                    <select name="transaction_type" id="transaction_type" class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-200 focus:ring-opacity-50">
                                        <option value="">All Types</option>
                                        @foreach($transactionTypes as $value => $label)
                                            <option value="{{ $value }}" {{ $transactionType == $value ? 'selected' : '' }}>{{ $label }}</option>
                                        @endforeach
                                    </select>
                                </div>

                                <!-- Filter Button -->
                                <div class="flex items-end">
                                    <button type="submit" class="w-full bg-blue-600 hover:bg-blue-700 text-white font-medium py-2 px-4 rounded-md transition duration-150">
                                        Apply Filters
                                    </button>
                                </div>
                            </div>

                            <!-- Custom Date Range -->
                            <div id="custom-date-range" class="grid grid-cols-1 md:grid-cols-2 gap-4 {{ $dateRange == 'custom' ? '' : 'hidden' }}">
                                <div>
                                    <label for="start_date" class="block text-sm font-medium text-gray-700 mb-1">Start Date</label>
                                    <input type="date" name="start_date" id="start_date" value="{{ request('start_date') }}" class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-200 focus:ring-opacity-50">
                                </div>
                                <div>
                                    <label for="end_date" class="block text-sm font-medium text-gray-700 mb-1">End Date</label>
                                    <input type="date" name="end_date" id="end_date" value="{{ request('end_date') }}" class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-200 focus:ring-opacity-50">
                                </div>
                            </div>
                        </form>
                    </div>

                    <!-- Transactions Table -->
                    <div class="bg-white rounded-lg shadow overflow-hidden">
                        <div class="overflow-x-auto">
                            <table class="min-w-full divide-y divide-gray-200">
                                <thead class="bg-gray-50">
                                    <tr>
                                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Date</th>
                                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Type</th>
                                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Client</th>
                                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Description</th>
                                        <th scope="col" class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Debit</th>
                                        <th scope="col" class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Credit</th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white divide-y divide-gray-200">
                                    @forelse($transactions as $transaction)
                                        <tr class="hover:bg-gray-50">
                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                                {{ $transaction->transaction_date->format('M d, Y') }}
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap">
                                                @php
                                                    $typeColors = [
                                                        'sale' => 'bg-green-100 text-green-800',
                                                        'payment' => 'bg-blue-100 text-blue-800',
                                                        'expense' => 'bg-red-100 text-red-800'
                                                    ];
                                                @endphp
                                                <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full {{ $typeColors[$transaction->transaction_type] ?? 'bg-gray-100 text-gray-800' }}">
                                                    {{ ucfirst($transaction->transaction_type) }}
                                                </span>
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                                {{ $transaction->client->client_name ?? 'N/A' }}
                                            </td>
                                            <td class="px-6 py-4 text-sm text-gray-900">
                                                <div class="max-w-xs truncate">
                                                    @if($transaction->transaction_type == 'sale')
                                                        Sale - {{ $transaction->sales_type }}
                                                    @elseif($transaction->transaction_type == 'payment')
                                                        Payment - {{ $transaction->sales_type }}
                                                    @else
                                                        Expense - {{ $transaction->sales_type }}
                                                    @endif
                                                    @if($transaction->notes)
                                                        <br><span class="text-gray-500 text-xs">{{ $transaction->notes }}</span>
                                                    @endif
                                                </div>
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-right text-gray-900">
                                                @if($transaction->transaction_type == 'sale')
                                                    <span class="text-red-600 font-medium">₹{{ number_format($transaction->total_amount, 2) }}</span>
                                                @else
                                                    <span class="text-gray-400">—</span>
                                                @endif
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-right text-gray-900">
                                                @if($transaction->transaction_type == 'payment')
                                                    <span class="text-green-600 font-medium">₹{{ number_format($transaction->total_amount, 2) }}</span>
                                                @else
                                                    <span class="text-gray-400">—</span>
                                                @endif
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="6" class="px-6 py-12 text-center text-gray-500">
                                                <div class="flex flex-col items-center">
                                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-12 w-12 text-gray-400 mb-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                                    </svg>
                                                    <h3 class="text-lg font-medium text-gray-900 mb-1">No transactions found</h3>
                                                    <p class="text-gray-500">Try adjusting your filters or date range to see transactions.</p>
                                                </div>
                                            </td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>

                    <!-- Date Range Display -->
                    @if($startDate && $endDate)
                        <div class="mt-4 text-center text-sm text-gray-600">
                            Showing transactions from {{ $startDate->format('M d, Y') }} to {{ $endDate->format('M d, Y') }}
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const dateRangeSelect = document.getElementById('date_range');
            const customDateRange = document.getElementById('custom-date-range');

            function toggleCustomDateRange() {
                if (dateRangeSelect.value === 'custom') {
                    customDateRange.classList.remove('hidden');
                } else {
                    customDateRange.classList.add('hidden');
                }
            }

            dateRangeSelect.addEventListener('change', toggleCustomDateRange);
            toggleCustomDateRange(); // Initial check
        });
    </script>
</x-layouts.app-layout>
