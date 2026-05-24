<x-layouts.app-layout>
    <div class="py-12">
        <div class="w-full mx-auto sm:px-6 lg:px-8">
            <div class="page-shell">
                <div class="page-inner">
                    <div class="page-header">
                        <div>
                            <h1 class="page-title">P/L Reports</h1>
                            <p class="page-subtitle">Create month-year P/L statements from finalized payments boards and the same month expenses.</p>
                        </div>
                        <a href="{{ route('reports.profit-loss.create') }}" class="btn-primary">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                            </svg>
                            Create P/L
                        </a>
                    </div>

                    @if(session('success'))
                        <div class="alert-success mb-6">{{ session('success') }}</div>
                    @endif

                    @if(session('error'))
                        <div class="alert-error mb-6">{{ session('error') }}</div>
                    @endif

                    <div class="app-table-wrap">
                        <table class="app-table">
                            <thead>
                                <tr>
                                    <th class="text-center">Period</th>
                                    <th class="text-center">Payments Board</th>
                                    <th class="text-center">Total Income</th>
                                    <th class="text-center">Total Expenses</th>
                                    <th class="text-center">GST</th>
                                    <th class="text-center">P/L</th>
                                    <th class="text-center">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($profitLossReports as $report)
                                    <tr>
                                        <td class="text-center">{{ $report->formatted_month_year }}</td>
                                        <td class="text-center">{{ $report->paymentsBoard->formatted_month_year }}</td>
                                        <td class="text-center text-green-600 font-semibold">₹{{ number_format($report->total_income, 2) }}</td>
                                        <td class="text-center text-rose-200">₹{{ number_format($report->total_expenses, 2) }}</td>
                                        <td class="text-center">₹{{ number_format($report->total_gst, 2) }}</td>
                                        <td class="text-center {{ $report->is_profit ? 'text-green-600' : 'text-rose-300' }} font-semibold">₹{{ number_format($report->profit_loss, 2) }}</td>
                                        <td class="text-center">
                                            <div class="flex items-center justify-center gap-2">
                                                <a href="{{ route('reports.profit-loss.show', $report->id) }}" class="btn-soft">View</a>
                                                <form method="POST" action="{{ route('reports.profit-loss.delete', $report->id) }}" onsubmit="return confirm('Are you sure you want to delete this P/L report?');">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="btn-soft-danger">Delete</button>
                                                </form>
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="7" class="px-5 py-10 text-center text-violet-200/60">No P/L reports found.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-layouts.app-layout>
