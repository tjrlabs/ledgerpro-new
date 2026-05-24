<x-layouts.app-layout>
    <div class="py-12">
        <div class="w-full mx-auto sm:px-6 lg:px-8">
            <div class="page-shell">
                <div class="page-inner">
                    <div class="page-header">
                        <div>
                            <h1 class="page-title">Create P/L Report</h1>
                            <p class="page-subtitle">P/L can only be created after the matching payments board has been finalized.</p>
                        </div>
                        <a href="{{ route('reports.profit-loss') }}" class="btn-secondary">Back to P/L</a>
                    </div>

                    @if($errors->any())
                        <div class="alert-error mb-6">
                            <ul class="list-disc list-inside">
                                @foreach($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    <form method="POST" action="{{ route('reports.profit-loss.store') }}">
                        @csrf

                        <div class="surface-muted mb-6">
                            <h3 class="mb-4 text-lg font-semibold">P/L Period Configuration</h3>

                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <div>
                                    <label for="profit_loss_month" class="mb-2 block text-sm font-medium text-violet-100/80">Month</label>
                                    <select name="profit_loss_month" id="profit_loss_month" class="w-full rounded-lg border border-violet-500/25 bg-black/35 px-3 py-2 text-violet-50 shadow-sm shadow-black/30 focus:border-violet-400 focus:outline-hidden focus:ring-4 focus:ring-violet-500/20" required>
                                        @foreach($monthOptions as $value => $label)
                                            <option value="{{ $value }}" {{ old('profit_loss_month', $currentMonth) == $value ? 'selected' : '' }}>{{ $label }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div>
                                    <label for="profit_loss_year" class="mb-2 block text-sm font-medium text-violet-100/80">Year</label>
                                    <select name="profit_loss_year" id="profit_loss_year" class="w-full rounded-lg border border-violet-500/25 bg-black/35 px-3 py-2 text-violet-50 shadow-sm shadow-black/30 focus:border-violet-400 focus:outline-hidden focus:ring-4 focus:ring-violet-500/20" required>
                                        @foreach($yearOptions as $value => $label)
                                            <option value="{{ $value }}" {{ old('profit_loss_year', $currentYear) == $value ? 'selected' : '' }}>{{ $label }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                        </div>

                        <div class="flex justify-end gap-4">
                            <a href="{{ route('reports.profit-loss') }}" class="btn-secondary">Cancel</a>
                            <button type="submit" class="btn-primary">Create P/L Report</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-layouts.app-layout>
