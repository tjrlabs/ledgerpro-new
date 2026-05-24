<x-layouts.app-layout>
    <div class="py-12">
        <div class="w-full mx-auto sm:px-6 lg:px-8">
            <div class="page-shell">
                <div class="page-inner">
                    <div class="page-header">
                        <div>
                            <h1 class="page-title">P/L - {{ $profitLossReport->formatted_month_year }}</h1>
                            <p class="page-subtitle">Calculated from the finalized payments board for {{ $profitLossReport->paymentsBoard->formatted_month_year }} and that month's expenses.</p>
                        </div>
                        <div class="flex items-center gap-3">
                            <a href="{{ route('reports.payments.board.show', $profitLossReport->payments_board_id) }}" class="btn-secondary">View Payments Board</a>
                            <a href="{{ route('reports.profit-loss') }}" class="btn-secondary">Back to P/L</a>
                        </div>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-6">
                        <div class="stat-card">
                            <div class="text-sm text-violet-200/70">Total Income</div>
                            <div class="mt-2 text-2xl font-black text-green-500">₹{{ number_format($profitLossReport->total_income, 2) }}</div>
                        </div>
                        <div class="stat-card">
                            <div class="text-sm text-violet-200/70">Total Expenses</div>
                            <div class="mt-2 text-2xl font-black text-rose-300">₹{{ number_format($profitLossReport->total_expenses, 2) }}</div>
                        </div>
                        <div class="stat-card">
                            <div class="text-sm text-violet-200/70">GST</div>
                            <div class="mt-2 text-2xl font-black">₹{{ number_format($profitLossReport->total_gst, 2) }}</div>
                        </div>
                        <div class="stat-card">
                            <div class="text-sm text-violet-200/70">P/L</div>
                            <div class="mt-2 text-2xl font-black {{ $profitLossReport->is_profit ? 'text-green-500' : 'text-rose-300' }}">₹{{ number_format($profitLossReport->profit_loss, 2) }}</div>
                        </div>
                    </div>

                    <div class="surface-muted">
                        <h3 class="mb-4 text-lg font-semibold">Calculation</h3>
                        <div class="app-table-wrap">
                            <table class="app-table">
                                <thead>
                                    <tr>
                                        <th>Description</th>
                                        <th class="text-right">Amount</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        <td>Cash Sales</td>
                                        <td class="text-right">₹{{ number_format($profitLossReport->total_cash_sales, 2) }}</td>
                                    </tr>
                                    <tr>
                                        <td>Pre-GST Sales</td>
                                        <td class="text-right">₹{{ number_format($profitLossReport->total_pre_gst_sales, 2) }}</td>
                                    </tr>
                                    <tr>
                                        <td>GST</td>
                                        <td class="text-right">₹{{ number_format($profitLossReport->total_gst, 2) }}</td>
                                    </tr>
                                    <tr>
                                        <td>Total Income</td>
                                        <td class="text-right">₹{{ number_format($profitLossReport->total_income, 2) }}</td>
                                    </tr>
                                    <tr>
                                        <td>Total Expenses</td>
                                        <td class="text-right">₹{{ number_format($profitLossReport->total_expenses, 2) }}</td>
                                    </tr>
                                    <tr>
                                        <td>P/L = Total Income - Total Expenses - GST</td>
                                        <td class="text-right font-semibold {{ $profitLossReport->is_profit ? 'text-green-500' : 'text-rose-300' }}">₹{{ number_format($profitLossReport->profit_loss, 2) }}</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-layouts.app-layout>
