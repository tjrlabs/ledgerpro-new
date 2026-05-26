<x-layouts.app-layout>
    <div class="py-12">
        <div class="w-full mx-auto sm:px-6 lg:px-8">
            <div class="page-shell">
                <div class="page-inner">
                    <div class="page-header">
                        <div>
                            <h1 class="page-title">Payments Board</h1>
                            <p class="page-subtitle">Manage monthly payment boards, client balances, collections, and unpaid amounts.</p>
                        </div>
                        <a href="{{route('reports.payments.board.create')}}" class="btn-primary">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                            </svg>
                            Add New Payment Board
                        </a>
                    </div>

                    @if(session('success'))
                    <div id="success-alert" class="alert-success mb-4">
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

                    @if(session('error'))
                    <div id="error-alert" class="alert-error mb-4">
                        <div class="flex items-center">
                            <svg class="h-5 w-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                            {{ session('error') }}
                        </div>
                        <button type="button" onclick="document.getElementById('error-alert').style.display='none'" class="text-red-700 hover:text-red-900">
                            <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                            </svg>
                        </button>
                    </div>
                    @endif

                    <div class="app-table-wrap">
                        <table class="app-table">
                            <thead>
                                <tr>
                                    <th class="py-3 px-6 text-center">S.NO</th>
                                    <th class="py-3 px-6 text-center">Duration</th>
                                    <th class="py-3 px-6 text-center">Clients Count</th>
                                    <th class="py-3 px-6 text-center">Cash (INR)</th>
                                    <th class="py-3 px-6 text-center">Pre-GST (INR)</th>
                                    <th class="py-3 px-6 text-center">GST (INR)</th>
                                    <th class="py-3 px-6 text-center">TDS (INR)</th>
                                    <th class="py-3 px-6 text-center">Total Amount</th>
                                    <th class="py-3 px-6 text-center">Prev Bal (INR)</th>
                                    <th class="py-3 px-6 text-center">Net Amount (INR)</th>
                                    <th class="py-3 px-6 text-center">Paid (INR)</th>
                                    <th class="py-3 px-6 text-center">Unpaid (INR)</th>
                                    <th class="py-3 px-6 text-center">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($paymentsBoards as $index => $board)
                                    <tr>
                                        <td class="py-3 px-6 text-center">{{ $index + 1 }}</td>
                                        <td class="py-3 px-6 text-center">{{ $board->formatted_month_year }}</td>
                                        <td class="py-3 px-6 text-center">{{ $board->clients_count ?? 0 }}</td>
                                        <td class="py-3 px-6 text-center">{{ number_format($board->total_cash_sales ?? 0, 2) }}</td>
                                        <td class="py-3 px-6 text-center">{{ number_format($board->total_pre_gst_amount ?? 0, 2) }}</td>
                                        <td class="py-3 px-6 text-center">{{ number_format($board->total_gst_amount ?? 0, 2) }}</td>
                                        <td class="py-3 px-6 text-center">{{ number_format($board->total_tds ?? 0, 2) }}</td>
                                        <td class="py-3 px-6 text-center">{{ number_format($board->total_amount ?? 0, 2) }}</td>
                                        <td class="py-3 px-6 text-center">{{ number_format($board->total_previous_balance ?? 0, 2) }}</td>
                                        <td class="py-3 px-6 text-center">{{ number_format($board->total_net_amount ?? 0, 2) }}</td>
                                        <td class="py-3 px-6 text-center">
                                            @if($board->total_paid_amount > 0)
                                                <span class="badge badge-success">
                                                    Paid: ₹{{ number_format($board->total_paid_amount, 2) }}
                                                </span>
                                                <br>
                                                <span class="mt-1 block text-xs text-violet-300/65">
                                                    {{ $board->collection_percentage }}% collected
                                                </span>
                                            @else
                                                <span class="text-xs text-violet-300/65">0</span>
                                            @endif
                                        </td>
                                        <td class="py-3 px-6 text-center">
                                            @if($board->total_unpaid_amount > 0)
                                                <span class="badge badge-danger {{ $board->total_paid_amount > 0 ? 'mt-2 block' : '' }}">
                                                    Pending: ₹{{ number_format($board->total_unpaid_amount, 2) }}
                                                </span>
                                            @else
                                                <span class="text-xs text-violet-300/65">0</span>
                                            @endif
                                        </td>
                                        <td class="py-3 px-6 text-center">
                                            <div class="flex items-center justify-center space-x-2">
                                                <a href="{{route('reports.payments.board.show', $board->id)}}" class="btn-soft">
                                                    View
                                                </a>
                                                <a href="{{route('reports.payments.board.edit', $board->id)}}" class="btn-soft">
                                                    Edit
                                                </a>
                                                <form method="POST" action="{{route('reports.payments.board.delete', $board->id)}}" class="inline-block" onsubmit="return confirm('Are you sure you want to delete this payments board? This action cannot be undone.')">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="btn-soft-danger">
                                                        Delete
                                                    </button>
                                                </form>
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="14" class="px-6 py-8 text-center text-violet-300/60">
                                            <div class="flex flex-col items-center">
                                                <svg xmlns="http://www.w3.org/2000/svg" class="mb-4 h-16 w-16 text-violet-300/40" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                                </svg>
                                                <p class="text-lg font-medium text-violet-200/70">No payments boards found</p>
                                                <p class="text-sm text-violet-300/55">Create a new payments board to get started</p>
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

    <script>
        // Toggle custom date range fields
        document.getElementById('date_range').addEventListener('change', function() {
            const customDateRange = document.getElementById('custom-date-range');
            if (this.value === 'custom') {
                customDateRange.classList.remove('hidden');
            } else {
                customDateRange.classList.add('hidden');
            }
        });
    </script>
</x-layouts.app-layout>
