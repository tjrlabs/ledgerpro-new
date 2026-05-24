<x-layouts.app-layout>
    <div class="py-12">
        <div class="w-full mx-auto sm:px-6 lg:px-8">
            <div class="bg-white/80 backdrop-blur-md overflow-hidden shadow-xl sm:rounded-lg border border-gray-100">
                <div class="p-6 text-gray-900">
                    <div class="flex justify-between items-center mb-6">
                        <h1 class="text-2xl font-bold">Edit Payments Board</h1>
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
                        <div class="bg-gray-50 p-6 rounded-lg border border-gray-200 mb-6">
                            <div class="flex justify-between items-center mb-4">
                                <div>
                                    <h3 class="text-lg font-semibold text-gray-900">Payments Board Configuration</h3>
                                    <p class="text-sm text-gray-600 mt-1">
                                        <span class="font-medium">Period:</span>
                                        {{$paymentsBoard->month . ' ' . $paymentsBoard->year}}
                                    </p>
                                </div>
                                <div class="flex items-center gap-3">
                                    <button type="button" id="recalculateBoard" class="bg-blue-600 hover:bg-blue-700 text-white px-3 py-1 rounded text-sm flex items-center">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
                                        </svg>
                                        <span id="recalculate-board-btn-text">Recalculate Board</span>
                                    </button>
                                    <button type="button" id="addClients" class="bg-blue-600 hover:bg-blue-700 text-white px-3 py-1 rounded text-sm flex items-center">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                                        </svg>
                                        Add Client
                                    </button>
                                </div>
                            </div>
                        </div>

                        <!-- Clients Payments Section -->
                        <div class="bg-white rounded-lg border border-gray-200 overflow-hidden mb-6">
                            <div class="px-6 py-4 border-b border-gray-200">
                                <h3 class="text-lg font-semibold text-gray-900">Client Payments Data</h3>
                            </div>

                            @if($clientsPayments->isNotEmpty())
                                <div class="payments-board-table-wrap overflow-auto max-h-[72vh]">
                                    <table class="payments-board-table min-w-full divide-y divide-gray-200">
                                        <thead class="bg-gray-50">
                                            <tr>
                                                <th class="px-4 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Client</th>
                                                <th class="px-4 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Cash Sales</th>
                                                <th class="px-4 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">PRE-GST Sales</th>
                                                <th class="px-4 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">GST Amount</th>
                                                <th class="px-4 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">TDS</th>
                                                <th class="px-4 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Previous Balance</th>
                                                <th class="px-4 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Total Amount</th>
                                                <th class="px-4 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Paid Amount</th>
                                                <th class="px-4 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Balance</th>
                                                <th class="px-4 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Remarks</th>
                                                <th class="px-4 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                                            </tr>
                                        </thead>
                                        <tbody class="bg-white divide-y divide-gray-200">
                                            @foreach($clientsPayments as $index => $clientPayment)
                                                <tr class="hover:bg-gray-50">
                                                    <td class="px-4 py-4 whitespace-nowrap text-center">
                                                        <div>
                                                            <div class="text-sm font-medium text-gray-900">{{ $clientPayment->client->client_name }}</div>
                                                        </div>
                                                        <input type="hidden" name="clients[{{ $index }}][id]" value="{{ $clientPayment->id }}">
                                                        <input type="hidden" name="clients[{{ $index }}][client_id]" value="{{ $clientPayment->client_id }}">
                                                    </td>
                                                    <td class="px-4 py-4 whitespace-nowrap text-center">
                                                        <div class="text-sm text-gray-900">{{ number_format($clientPayment->cash_sales, 2) }}</div>
                                                        <input type="hidden" name="clients[{{ $index }}][cash_sales]" value="{{ $clientPayment->cash_sales }}">
                                                    </td>
                                                    <td class="px-4 py-4 whitespace-nowrap text-center">
                                                        <div class="text-sm text-gray-900">{{ number_format($clientPayment->pre_gst_amount, 2) }}</div>
                                                        <input type="hidden" name="clients[{{ $index }}][pre_gst_amount]" value="{{ $clientPayment->pre_gst_amount }}">
                                                    </td>
                                                    <td class="px-4 py-4 whitespace-nowrap text-center">
                                                        <div class="text-sm text-gray-900">{{ number_format($clientPayment->gst_amount, 2) }}</div>
                                                        <input type="hidden" name="clients[{{ $index }}][gst_amount]" value="{{ $clientPayment->gst_amount }}">
                                                    </td>
                                                    <td class="px-4 py-4 whitespace-nowrap text-center">
                                                        <div class="text-sm text-gray-900">{{ number_format($clientPayment->tds, 2) }}</div>
                                                        <input type="hidden" name="clients[{{ $index }}][tds]" value="{{ $clientPayment->tds }}">
                                                    </td>
                                                    <td class="px-4 py-4 whitespace-nowrap text-center">
                                                        <div class="text-sm text-gray-900">{{ number_format($clientPayment->previous_balance, 2) }}</div>
                                                        <input type="hidden" name="clients[{{ $index }}][previous_balance]" value="{{ $clientPayment->previous_balance }}">
                                                    </td>
                                                    <td class="px-4 py-4 whitespace-nowrap text-center">
                                                        <div class="text-sm text-gray-900 font-semibold">{{ number_format($clientPayment->total_amount, 2) }}</div>
                                                        <input type="hidden" name="clients[{{ $index }}][total_amount]" value="{{ $clientPayment->total_amount }}">
                                                    </td>
                                                    <td class="px-4 py-4 whitespace-nowrap text-center">
                                                        <div class="text-sm text-green-600 font-medium">{{ number_format($clientPayment->paid_amount, 2) }}</div>
                                                        <input type="hidden" name="clients[{{ $index }}][paid_amount]" value="{{ $clientPayment->paid_amount }}">
                                                    </td>
                                                    <td class="px-4 py-4 whitespace-nowrap text-center">
                                                        <div class="text-sm {{ ($clientPayment->total_amount - $clientPayment->paid_amount) > 0 ? 'text-red-600 font-medium' : 'text-green-600' }}">
                                                            {{ number_format(($clientPayment->total_amount - $clientPayment->paid_amount), 2) }}
                                                        </div>
                                                        <input type="hidden" name="clients[{{ $index }}][balance]" value="{{ ($clientPayment->total_amount - $clientPayment->paid_amount) }}">
                                                    </td>
                                                    <td class="px-4 py-4 text-center">
                                                        <div class="flex items-center space-x-2 justify-center">
                                                            <textarea
                                                                id="remarks_{{ $clientPayment->id }}"
                                                                class="flex-1 min-w-[180px] max-w-[220px] px-3 py-2 text-xs border border-gray-300 rounded-md resize-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 focus:outline-hidden transition-colors duration-200"
                                                                rows="1"
                                                                placeholder="Add remarks..."
                                                            >{{ $clientPayment->remarks ?? '' }}</textarea>
                                                            <button
                                                                type="button"
                                                                class="save-remarks-btn shrink-0 px-2 py-1 bg-blue-600 hover:bg-blue-700 text-white text-xs rounded transition-all duration-200 focus:outline-hidden focus:ring-1 focus:ring-blue-500"
                                                                data-client-payment-id="{{ $clientPayment->id }}">
                                                                Save
                                                            </button>
                                                        </div>
                                                    </td>
                                                    <td class="px-4 py-4 whitespace-nowrap text-center">
                                                        <div class="flex justify-center space-x-2">
                                                            <button type="button"
                                                                    class="recalculate-client-btn text-blue-600 hover:text-blue-800 p-1 rounded hover:bg-blue-50"
                                                                    data-client-payment-id="{{ $clientPayment->id }}"
                                                                    title="Recalculate client on payments board">
                                                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
                                                                </svg>
                                                            </button>
                                                            <button type="button"
                                                                    class="remove-client-btn text-red-600 hover:text-red-800 p-1 rounded hover:bg-red-50"
                                                                    data-client-payment-id="{{ $clientPayment->id }}"
                                                                    data-client-name="{{ $clientPayment->client->client_name }}"
                                                                    title="Remove client from payments board">
                                                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                                                </svg>
                                                            </button>
                                                        </div>
                                                    </td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            @else
                                <div class="p-6 text-center">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-12 w-12 mx-auto text-gray-400 mb-3" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
                                    </svg>
                                    <p class="text-gray-500 text-sm">No clients added to this payments board yet.</p>
                                    <p class="text-gray-400 text-xs mt-1">Click "Add Client" to start adding clients.</p>
                                </div>
                            @endif
                        </div>

                        <!-- Submit Buttons -->
                        <div class="flex justify-end space-x-4 mt-6">
                            <a href="{{ route('reports.payments.board.show', $paymentsBoard->id) }}" class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-2 rounded-lg shadow-sm">
                                View Payments Board
                            </a>
                        </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Add Clients Modal -->
    <div id="addClientsModal" class="fixed inset-0 bg-gray-600/50 overflow-y-auto h-full w-full hidden z-50">
        <div class="relative top-10 mx-auto p-5 border w-4/5 max-w-4xl shadow-lg rounded-md bg-white">
            <div class="mt-3">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="text-lg leading-6 font-medium text-gray-900">Add Clients to Payments Board</h3>
                    <button id="closeModal" class="text-gray-400 hover:text-gray-600">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>

                <div class="max-h-96 overflow-y-auto">
                    <form id="addClientsForm">
                        <div class="space-y-3" id="clientsList">
                            <!-- Clients will be populated here via AJAX -->
                        </div>

                        <div class="flex justify-between items-center mt-6 pt-4 border-t border-gray-200">
                            <div class="text-sm text-gray-600">
                                <span id="selectedCount">0</span> client(s) selected
                            </div>
                            <div class="flex space-x-3">
                                <button type="button" id="cancelAddClients" class="px-4 py-2 bg-gray-300 text-gray-700 text-sm font-medium rounded-md hover:bg-gray-400 focus:outline-hidden focus:ring-2 focus:ring-gray-300">
                                    Cancel
                                </button>
                                <button type="submit" class="px-4 py-2 bg-blue-600 text-white text-sm font-medium rounded-md hover:bg-blue-700 focus:outline-hidden focus:ring-2 focus:ring-blue-500">
                                    Add Selected Clients
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <div id="toast" class="fixed top-4 right-4 z-50 hidden">
        <div id="toast-content" class="px-4 py-2 rounded-lg shadow-lg text-white text-sm font-medium">
            <span id="toast-message"></span>
        </div>
    </div>

    @push('scripts')
        <script>
            document.addEventListener('DOMContentLoaded', function() {

                // Add Clients Modal functionality
                const addClientsBtn = document.getElementById('addClients');
                const addClientsModal = document.getElementById('addClientsModal');
                const closeModalBtn = document.getElementById('closeModal');
                const cancelBtn = document.getElementById('cancelAddClients');
                const addClientsForm = document.getElementById('addClientsForm');
                const recalculateBoardBtn = document.getElementById('recalculateBoard');
                const selectedCountSpan = document.getElementById('selectedCount');

                function showToast(message, type = 'success') {
                    const toast = document.getElementById('toast');
                    const toastContent = document.getElementById('toast-content');
                    const toastMessage = document.getElementById('toast-message');

                    toastMessage.textContent = message;
                    toastContent.className = type === 'success'
                        ? 'px-4 py-2 rounded-lg shadow-lg text-white text-sm font-medium bg-green-600'
                        : 'px-4 py-2 rounded-lg shadow-lg text-white text-sm font-medium bg-red-600';

                    toast.classList.remove('hidden');

                    setTimeout(() => {
                        toast.classList.add('hidden');
                    }, 3000);
                }

                // Handle remove client buttons
                document.addEventListener('click', function(e) {
                    if (e.target.closest('.save-remarks-btn')) {
                        e.preventDefault();
                        const saveBtn = e.target.closest('.save-remarks-btn');
                        const clientPaymentId = saveBtn.getAttribute('data-client-payment-id');
                        const textarea = document.getElementById(`remarks_${clientPaymentId}`);
                        const originalText = saveBtn.innerHTML;

                        saveBtn.disabled = true;
                        saveBtn.innerHTML = 'Saving...';

                        fetch(`/reports/payments-board/client-payment/${clientPaymentId}/remarks`, {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                            },
                            body: JSON.stringify({
                                remarks: textarea.value.trim()
                            })
                        })
                        .then(response => response.json())
                        .then(data => {
                            if (data.success) {
                                textarea.value = data.remarks || '';
                                showToast('Remarks saved successfully!', 'success');
                            } else {
                                showToast('Error saving remarks: ' + (data.message || 'Unknown error'), 'error');
                            }
                        })
                        .catch(error => {
                            console.error('AJAX Error:', error);
                            showToast('Error saving remarks. Please try again.', 'error');
                        })
                        .finally(() => {
                            saveBtn.disabled = false;
                            saveBtn.innerHTML = originalText;
                        });

                        return;
                    }

                    if (e.target.closest('.recalculate-client-btn')) {
                        e.preventDefault();
                        const recalculateBtn = e.target.closest('.recalculate-client-btn');
                        const clientPaymentId = recalculateBtn.getAttribute('data-client-payment-id');
                        const originalContent = recalculateBtn.innerHTML;

                        recalculateBtn.disabled = true;
                        recalculateBtn.innerHTML = '<svg class="animate-spin h-5 w-5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>';

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
                                location.reload();
                            } else {
                                alert('Error recalculating payment: ' + (data.message || 'Unknown error'));
                            }
                        })
                        .catch(error => {
                            console.error('AJAX Error:', error);
                            alert('Error recalculating payment. Please try again.');
                        })
                        .finally(() => {
                            recalculateBtn.disabled = false;
                            recalculateBtn.innerHTML = originalContent;
                        });

                        return;
                    }

                    if (e.target.closest('.remove-client-btn')) {
                        e.preventDefault();
                        const removeBtn = e.target.closest('.remove-client-btn');
                        const clientPaymentId = removeBtn.getAttribute('data-client-payment-id');
                        const clientName = removeBtn.getAttribute('data-client-name');
                        const boardId = {{ $paymentsBoard->id }};

                        if (confirm(`Are you sure you want to remove ${clientName} from this payments board?`)) {
                            // Send AJAX request to remove client from board
                            fetch(`/reports/payments-board/${boardId}/remove-client/${clientPaymentId}`, {
                                method: 'DELETE',
                                headers: {
                                    'Content-Type': 'application/json',
                                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                                }
                            })
                            .then(response => response.json())
                            .then(data => {
                                if (data.success) {
                                    // Success: reload the page to show updated client list
                                    location.reload();
                                } else {
                                    // Failed: show error message
                                    alert('Error: ' + data.message);
                                }
                            })
                            .catch(error => {
                                console.error('AJAX Error:', error);
                                alert('Error removing client. Please try again.');
                            });
                        }
                    }
                });

                if (recalculateBoardBtn) {
                    recalculateBoardBtn.addEventListener('click', function(e) {
                        e.preventDefault();
                        const text = document.getElementById('recalculate-board-btn-text');
                        const originalText = text.innerHTML;

                        recalculateBoardBtn.disabled = true;
                        text.innerHTML = 'Recalculating...';

                        fetch(`/reports/payments-board/{{ $paymentsBoard->id }}/recalculate`, {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                            }
                        })
                        .then(response => response.json())
                        .then(data => {
                            if (data.success) {
                                location.reload();
                            } else {
                                alert('Error recalculating board: ' + (data.message || 'Unknown error'));
                            }
                        })
                        .catch(error => {
                            console.error('AJAX Error:', error);
                            alert('Error recalculating board. Please try again.');
                        })
                        .finally(() => {
                            recalculateBoardBtn.disabled = false;
                            text.innerHTML = originalText;
                        });
                    });
                }

                if (addClientsBtn && addClientsModal) {
                    addClientsBtn.addEventListener('click', function(e) {
                        e.preventDefault();
                        addClientsModal.classList.remove('hidden');

                        // Fetch available clients via AJAX
                        fetch('/clients/fetch-for-board', {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                            },
                            body: JSON.stringify({
                                board_id: {{ $paymentsBoard->id }}
                            })
                        })
                        .then(response => response.json())
                        .then(data => {
                            if (data.success) {
                                populateClientsModal(data.clients);
                            } else {
                                console.error('Error fetching clients:', data.message);
                                alert('Error loading clients: ' + data.message);
                            }
                        })
                        .catch(error => {
                            console.error('AJAX Error:', error);
                            alert('Error loading clients. Please try again.');
                        });
                    });

                    // Close modal functionality
                    [closeModalBtn, cancelBtn].forEach(btn => {
                        if (btn) {
                            btn.addEventListener('click', function(e) {
                                e.preventDefault();
                                addClientsModal.classList.add('hidden');
                            });
                        }
                    });

                    // Handle form submission
                    if (addClientsForm) {
                        addClientsForm.addEventListener('submit', function(e) {
                            e.preventDefault();
                            const selectedClients = Array.from(document.querySelectorAll('input[name="selected_clients[]"]:checked')).map(input => input.value);

                            if (selectedClients.length === 0) {
                                alert('Please select at least one client.');
                                return;
                            }

                            // Add clients to payments board via AJAX
                            fetch(`/reports/payments-board/{{ $paymentsBoard->id }}/add-clients`, {
                                method: 'POST',
                                headers: {
                                    'Content-Type': 'application/json',
                                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                                },
                                body: JSON.stringify({
                                    client_ids: selectedClients
                                })
                            })
                            .then(response => response.json())
                            .then(data => {
                                if (data.success) {
                                    addClientsModal.classList.add('hidden');
                                    location.reload(); // Reload to show updated data
                                } else {
                                    alert('Error: ' + data.message);
                                }
                            })
                            .catch(error => {
                                console.error('AJAX Error:', error);
                                alert('Error adding clients. Please try again.');
                            });
                        });
                    }
                }

                function populateClientsModal(clients) {
                    const clientsList = document.getElementById('clientsList');
                    clientsList.innerHTML = '';

                    if (clients.length === 0) {
                        clientsList.innerHTML = '<div class="text-center py-8"><p class="text-gray-500">No clients available to add.</p></div>';
                        return;
                    }

                    clients.forEach(client => {
                        const clientDiv = document.createElement('div');
                        clientDiv.className = 'flex items-center p-3 border border-gray-200 rounded-lg hover:bg-gray-50';
                        clientDiv.innerHTML = `
                            <input type="checkbox" name="selected_clients[]" value="${client.id}"
                                   class="mr-3 h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded client-checkbox">
                            <div class="flex-1">
                                <div class="text-sm font-medium text-gray-900">${client.client_name}</div>
                                <div class="text-sm text-gray-500">${client.email || 'No email'} • ${client.phone || 'No phone'}</div>
                            </div>
                        `;
                        clientsList.appendChild(clientDiv);
                    });

                    // Update selected count when checkboxes change
                    document.querySelectorAll('.client-checkbox').forEach(checkbox => {
                        checkbox.addEventListener('change', updateSelectedCount);
                    });
                }

                function updateSelectedCount() {
                    const selectedCount = document.querySelectorAll('input[name="selected_clients[]"]:checked').length;
                    selectedCountSpan.textContent = selectedCount;
                }
            });
        </script>
    @endpush
</x-layouts.app-layout>
