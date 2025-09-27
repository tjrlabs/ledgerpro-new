<x-layouts.app-layout>
    <div class="py-12">
        <div class="w-full mx-auto sm:px-6 lg:px-8">
            <div class="bg-white/80 backdrop-blur-md overflow-hidden shadow-xl sm:rounded-lg border border-gray-100">
                <div class="p-6 text-gray-900">
                    <div class="flex justify-between items-center mb-6">
                        <h1 class="text-2xl font-bold">Payment Board Details - {{ $paymentsBoard->formatted_month_year }}</h1>
                        <div class="flex space-x-3">
                            <a href="{{ route('reports.payments.board.edit', $paymentsBoard->id) }}" class="bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-lg shadow-sm flex items-center">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                </svg>
                                Edit Board
                            </a>
                            <a href="{{ route('reports.payments.board') }}" class="bg-gray-600 hover:bg-gray-700 text-white px-4 py-2 rounded-lg shadow-sm flex items-center">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                                </svg>
                                Back to Payments Board
                            </a>
                        </div>
                    </div>

                    <!-- Summary Cards -->
                    <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-6">
                        <div class="bg-blue-50 p-4 rounded-lg border border-blue-200">
                            <div class="text-blue-600 text-sm font-medium">Total Clients</div>
                            <div class="text-2xl font-bold text-blue-800">{{ $clientsPayments->count() }}</div>
                        </div>
                        <div class="bg-green-50 p-4 rounded-lg border border-green-200">
                            <div class="text-green-600 text-sm font-medium">Total Amount</div>
                            <div class="text-2xl font-bold text-green-800">₹{{ number_format($clientsPayments->sum('total_amount'), 2) }}</div>
                        </div>
                        <div class="bg-yellow-50 p-4 rounded-lg border border-yellow-200">
                            <div class="text-yellow-600 text-sm font-medium">Paid Amount</div>
                            <div class="text-2xl font-bold text-yellow-800">₹{{ number_format($clientsPayments->sum('paid_amount'), 2) }}</div>
                        </div>
                        <div class="bg-red-50 p-4 rounded-lg border border-red-200">
                            <div class="text-red-600 text-sm font-medium">Pending Amount</div>
                            <div class="text-2xl font-bold text-red-800">₹{{ number_format($clientsPayments->sum('total_amount') - $clientsPayments->sum('paid_amount'), 2) }}</div>
                        </div>
                    </div>

                    <!-- Client Payments Table -->
                    <div class="bg-white rounded-lg border border-gray-200 overflow-hidden">
                        <div class="px-6 py-4 border-b border-gray-200 bg-gray-50">
                            <h3 class="text-lg font-semibold text-gray-900">Client Payments Data</h3>
                        </div>

                        @if($clientsPayments->isNotEmpty())
                            <div class="overflow-x-auto">
                                <table class="min-w-full divide-y divide-gray-200">
                                    <thead class="bg-gray-50">
                                        <tr>
                                            <th class="px-4 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">S.No</th>
                                            <th class="px-4 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Client</th>
                                            <th class="px-4 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Previous Balance</th>
                                            <th class="px-4 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Cash Sales</th>
                                            <th class="px-4 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Pre-GST Amount</th>
                                            <th class="px-4 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">GST Amount</th>
                                            <th class="px-4 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">TDS</th>
                                            <th class="px-4 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Total Amount</th>
                                            <th class="px-4 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Paid Amount</th>
                                            <th class="px-4 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Balance</th>
                                            <th class="px-4 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Remarks</th>
                                            <th class="px-4 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Re-calculate</th>
                                        </tr>
                                    </thead>
                                    <tbody class="bg-white divide-y divide-gray-200">
                                        @foreach($clientsPayments as $index => $clientPayment)
                                            <tr class="hover:bg-gray-50">
                                                <td class="px-4 py-4 whitespace-nowrap text-center">
                                                    <div class="text-sm text-gray-900">{{ $index + 1 }}</div>
                                                </td>
                                                <td class="px-4 py-4 whitespace-nowrap text-center">
                                                    <div>
                                                        <div class="text-sm font-medium text-gray-900">{{ $clientPayment->client->client_name }}</div>
                                                        <div class="text-sm text-gray-500">{{ $clientPayment->client->email ?? 'No email' }}</div>
                                                    </div>
                                                </td>
                                                <td class="px-4 py-4 whitespace-nowrap text-center">
                                                    <div class="text-sm text-gray-900">₹{{ number_format($clientPayment->previous_balance, 2) }}</div>
                                                </td>
                                                <td class="px-4 py-4 whitespace-nowrap text-center">
                                                    <div class="text-sm text-gray-900">₹{{ number_format($clientPayment->cash_sales, 2) }}</div>
                                                </td>
                                                <td class="px-4 py-4 whitespace-nowrap text-center">
                                                    <div class="text-sm text-gray-900">₹{{ number_format($clientPayment->pre_gst_amount, 2) }}</div>
                                                </td>
                                                <td class="px-4 py-4 whitespace-nowrap text-center">
                                                    <div class="text-sm text-gray-900">₹{{ number_format($clientPayment->gst_amount, 2) }}</div>
                                                </td>
                                                <td class="px-4 py-4 whitespace-nowrap text-center">
                                                    <div class="text-sm text-gray-900">₹{{ number_format($clientPayment->tds, 2) }}</div>
                                                </td>
                                                <td class="px-4 py-4 whitespace-nowrap text-center">
                                                    <div class="text-sm text-gray-900 font-semibold">₹{{ number_format($clientPayment->total_amount, 2) }}</div>
                                                </td>
                                                <td class="px-4 py-4 whitespace-nowrap text-center">
                                                    <div class="text-sm text-gray-900">₹{{ number_format($clientPayment->paid_amount, 2) }}</div>
                                                </td>
                                                <td class="px-4 py-4 whitespace-nowrap text-center">
                                                    @php
                                                        $balance = $clientPayment->total_amount - $clientPayment->paid_amount;
                                                    @endphp
                                                    <div class="text-sm {{ $balance > 0 ? 'text-red-600 font-medium' : 'text-green-600' }}">
                                                        ₹{{ number_format($balance, 2) }}
                                                    </div>
                                                </td>
                                                <td class="px-4 py-4 text-center">
                                                    <div class="flex items-center space-x-2 justify-center">
                                                        <textarea
                                                            id="remarks_{{ $clientPayment->id }}"
                                                            class="flex-1 min-w-[180px] max-w-[220px] px-3 py-2 text-xs border border-gray-300 rounded-md resize-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 focus:outline-none transition-colors duration-200"
                                                            rows="1"
                                                            placeholder="Add remarks..."
                                                        >{{ $clientPayment->remarks ?? '' }}</textarea>
                                                        <button
                                                            type="button"
                                                            onclick="saveRemarks({{ $clientPayment->id }})"
                                                            class="flex-shrink-0 px-2 py-1 bg-blue-600 hover:bg-blue-700 text-white text-xs rounded transition-all duration-200 focus:outline-none focus:ring-1 focus:ring-blue-500">
                                                            Save
                                                        </button>
                                                    </div>
                                                </td>
                                                <td class="px-4 py-4 text-center">
                                                    <button
                                                        type="button"
                                                        onclick="recalculateClientPayment({{ $clientPayment->id }})"
                                                        class="px-3 py-1 bg-blue-500 hover:bg-blue-600 text-white text-xs rounded transition-all duration-200 focus:outline-none focus:ring-1 focus:ring-blue-500">
                                                        Recalculate
                                                    </button>
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                    <tfoot class="bg-gray-50">
                                        <tr class="font-semibold">
                                            <td colspan="2" class="px-4 py-3 text-center text-sm text-gray-900">Total</td>
                                            <td class="px-4 py-3 text-center text-sm text-gray-900">₹{{ number_format($clientsPayments->sum('previous_balance'), 2) }}</td>
                                            <td class="px-4 py-3 text-center text-sm text-gray-900">₹{{ number_format($clientsPayments->sum('cash_sales'), 2) }}</td>
                                            <td class="px-4 py-3 text-center text-sm text-gray-900">₹{{ number_format($clientsPayments->sum('pre_gst_amount'), 2) }}</td>
                                            <td class="px-4 py-3 text-center text-sm text-gray-900">₹{{ number_format($clientsPayments->sum('gst_amount'), 2) }}</td>
                                            <td class="px-4 py-3 text-center text-sm text-gray-900">₹{{ number_format($clientsPayments->sum('tds'), 2) }}</td>
                                            <td class="px-4 py-3 text-center text-sm text-gray-900 font-bold">₹{{ number_format($clientsPayments->sum('total_amount'), 2) }}</td>
                                            <td class="px-4 py-3 text-center text-sm text-gray-900">₹{{ number_format($clientsPayments->sum('paid_amount'), 2) }}</td>
                                            <td class="px-4 py-3 text-center text-sm text-gray-900 font-bold">₹{{ number_format($clientsPayments->sum('total_amount') - $clientsPayments->sum('paid_amount'), 2) }}</td>
                                            <td class="px-4 py-3 text-center text-sm text-gray-900">-</td>
                                            <td class="px-4 py-3 text-center text-sm text-gray-900">-</td>
                                        </tr>
                                    </tfoot>
                                </table>
                            </div>
                        @else
                            <div class="p-6 text-center">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-12 w-12 mx-auto text-gray-400 mb-3" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
                                </svg>
                                <p class="text-gray-500 text-sm">No clients added to this payments board yet.</p>
                                <p class="text-gray-400 text-xs mt-1">Edit the board to add clients and their payment data.</p>
                            </div>
                        @endif
                    </div>

                    <!-- Finalize Button Section -->
                    @if($clientsPayments->isNotEmpty())
                        <div class="mt-6 flex justify-center">
                            <button
                                type="button"
                                onclick="finalizePaymentsBoard()"
                                class="bg-orange-600 hover:bg-orange-700 text-white px-6 py-3 rounded-lg shadow-lg flex items-center font-semibold transition-all duration-200 focus:outline-none focus:ring-2 focus:ring-orange-500">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                                <span id="finalize-btn-text">Finalize PaymentsBoard</span>
                            </button>
                        </div>
                    @endif

                    <!-- Additional Information -->
                    <div class="mt-6 bg-gray-50 p-4 rounded-lg">
                        <h4 class="text-lg font-medium text-gray-900 mb-3">Payment Board Information</h4>
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                            <div>
                                <span class="text-sm font-medium text-gray-500">Period:</span>
                                <div class="text-sm text-gray-900">{{ $paymentsBoard->formatted_month_year }}</div>
                            </div>
                            <div>
                                <span class="text-sm font-medium text-gray-500">Created:</span>
                                <div class="text-sm text-gray-900">{{ $paymentsBoard->created_at->format('M d, Y') }}</div>
                            </div>
                            <div>
                                <span class="text-sm font-medium text-gray-500">Last Updated:</span>
                                <div class="text-sm text-gray-900">{{ $paymentsBoard->updated_at->format('M d, Y H:i') }}</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Success/Error Toast Notification -->
    <div id="toast" class="fixed top-4 right-4 z-50 hidden">
        <div id="toast-content" class="px-4 py-2 rounded-lg shadow-lg text-white text-sm font-medium">
            <span id="toast-message"></span>
        </div>
    </div>

    <script>
        // Function to save remarks for a specific client payment
        function saveRemarks(clientPaymentId) {
            const textarea = document.getElementById(`remarks_${clientPaymentId}`);
            const remarks = textarea.value.trim();
            const button = event.target;

            // Disable button and show loading state
            button.disabled = true;
            button.innerHTML = '<svg class="animate-spin h-3 w-3 inline mr-1" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>Saving...';

            // Send AJAX request to save remarks
            fetch(`/reports/payments-board/client-payment/${clientPaymentId}/remarks`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                },
                body: JSON.stringify({
                    remarks: remarks
                })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    showToast('Remarks saved successfully!', 'success');
                    // Update textarea with saved remarks
                    textarea.value = data.remarks || '';
                } else {
                    showToast('Error saving remarks: ' + (data.message || 'Unknown error'), 'error');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                showToast('Error saving remarks. Please try again.', 'error');
            })
            .finally(() => {
                // Re-enable button and restore original text
                button.disabled = false;
                button.innerHTML = 'Save';
            });
        }

        // Function to show toast notifications
        function showToast(message, type = 'success') {
            const toast = document.getElementById('toast');
            const toastContent = document.getElementById('toast-content');
            const toastMessage = document.getElementById('toast-message');

            // Set message
            toastMessage.textContent = message;

            // Set color based on type
            if (type === 'success') {
                toastContent.className = 'px-4 py-2 rounded-lg shadow-lg text-white text-sm font-medium bg-green-600';
            } else {
                toastContent.className = 'px-4 py-2 rounded-lg shadow-lg text-white text-sm font-medium bg-red-600';
            }

            // Show toast
            toast.classList.remove('hidden');

            // Hide toast after 3 seconds
            setTimeout(() => {
                toast.classList.add('hidden');
            }, 3000);
        }

        // Function to recalculate client payment
        function recalculateClientPayment(clientPaymentId) {
            // Show loading state on button
            const button = event.target;
            button.innerHTML = '<svg class="animate-spin h-4 w-4 inline-block mr-2" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>Recalculating...';

            // Send AJAX request to recalculate payment
            fetch(`/reports/payments-board/client-payment/${clientPaymentId}/recalculate`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                }
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    showToast('Payment recalculated successfully!', 'success');
                    // Optionally, refresh the page or update the table row with new data
                    location.reload();
                } else {
                    showToast('Error recalculating payment: ' + (data.message || 'Unknown error'), 'error');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                showToast('Error recalculating payment. Please try again.', 'error');
            })
            .finally(() => {
                // Restore button text
                button.innerHTML = 'Recalculate';
            });
        }

        // Function to finalize the payments board
        function finalizePaymentsBoard() {
            const button = event.target;
            const originalText = button.innerHTML;

            // Show loading state on button
            button.innerHTML = '<svg class="animate-spin h-4 w-4 inline-block mr-2" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>Finalizing...';

            // Send AJAX request to finalize payments board
            fetch(`/reports/payments-board/{{ $paymentsBoard->id }}/finalize`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                }
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    showToast(data.message, 'success');
                    // Show detailed information about the finalization
                    if (data.data && data.data.clients) {
                        setTimeout(() => {
                            showToast(`Account balances updated for ${data.data.finalized_clients_count} clients for ${data.data.next_period}`, 'success');
                        }, 3500);
                    }
                    // Optionally, redirect to another page or refresh the current page
                    setTimeout(() => {
                        location.reload();
                    }, 6000);
                } else {
                    showToast('Error finalizing Payments Board: ' + (data.message || 'Unknown error'), 'error');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                showToast('Error finalizing Payments Board. Please try again.', 'error');
            })
            .finally(() => {
                // Restore button text
                button.innerHTML = originalText;
            });
        }
    </script>
</x-layouts.app-layout>
