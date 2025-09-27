<x-layouts.app-layout>
    <div class="py-12">
        <div class="w-full mx-auto sm:px-6 lg:px-8">
            <div class="bg-white/80 backdrop-blur-md overflow-hidden shadow-xl sm:rounded-lg border border-gray-100">
                <div class="p-6 text-gray-900">
                    <div class="flex justify-between items-center mb-6">
                        <h1 class="text-2xl font-bold">Payments Board</h1>
                        <a href="{{route('reports.payments.board.create')}}" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg shadow-sm flex items-center">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                            </svg>
                            Add New Payment Board
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

                    @if(session('error'))
                    <div id="error-alert" class="mb-4 p-4 bg-red-50 border-l-4 border-red-500 text-red-700 flex justify-between items-center">
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

                    <div class="overflow-x-auto bg-white/70 backdrop-blur-sm p-4 rounded-lg shadow-inner border border-white">
                        <table class="min-w-full bg-transparent">
                            <thead>
                                <tr class="bg-gray-100 text-gray-700 uppercase text-sm leading-normal">
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
                            <tbody class="text-gray-600 text-sm">
                                @forelse ($paymentsBoards as $index => $board)
                                    <tr class="border-b border-gray-200 hover:bg-gray-50">
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
                                                <span class="bg-green-100 text-green-800 px-2 py-1 rounded-full text-xs font-medium">
                                                    Paid: ₹{{ number_format($board->total_paid_amount, 2) }}
                                                </span>
                                                <br>
                                                <span class="text-xs text-gray-500 mt-1 block">
                                                    {{ $board->collection_percentage }}% collected
                                                </span>
                                            @else
                                                <span class="text-xs text-gray-500">0</span>
                                            @endif
                                        </td>
                                        <td class="py-3 px-6 text-center">
                                            @if($board->total_unpaid_amount > 0)
                                                <span class="bg-red-100 text-red-800 px-2 py-1 rounded-full text-xs font-medium {{ $board->total_paid_amount > 0 ? 'mt-2 block' : '' }}">
                                                    Pending: ₹{{ number_format($board->total_unpaid_amount, 2) }}
                                                </span>
                                            @else
                                                <span class="text-xs text-gray-500">0</span>
                                            @endif
                                        </td>
                                        <td class="py-3 px-6 text-center">
                                            <div class="flex items-center justify-center space-x-2">
                                                <a href="{{route('reports.payments.board.show', $board->id)}}" class="bg-blue-500 hover:bg-blue-600 text-white px-3 py-1 rounded text-xs">
                                                    View
                                                </a>
                                                <a href="{{route('reports.payments.board.edit', $board->id)}}" class="bg-green-500 hover:bg-green-600 text-white px-3 py-1 rounded text-xs">
                                                    Edit
                                                </a>
                                                <form method="POST" action="{{route('reports.payments.board.delete', $board->id)}}" class="inline-block" onsubmit="return confirm('Are you sure you want to delete this payments board? This action cannot be undone.')">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="bg-red-500 hover:bg-red-600 text-white px-3 py-1 rounded text-xs">
                                                        Delete
                                                    </button>
                                                </form>
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="14" class="py-8 px-6 text-center text-gray-500">
                                            <div class="flex flex-col items-center">
                                                <svg xmlns="http://www.w3.org/2000/svg" class="h-16 w-16 text-gray-300 mb-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                                </svg>
                                                <p class="text-lg font-medium text-gray-400">No payments boards found</p>
                                                <p class="text-sm text-gray-400">Create a new payments board to get started</p>
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
