<x-layouts.app-layout>
    @php
        $companyName = optional(session('company_profile'))->company_name ?? 'Active Company';

        $moduleGroups = [
            [
                'title' => 'Core Records',
                'description' => 'Maintain the master data that powers your daily workflows.',
                'cards' => [
                    [
                        'title' => 'Clients',
                        'description' => 'View, add, and manage customer accounts and contact details.',
                        'route' => route('clients.index'),
                        'tag' => 'Records',
                        'accent' => 'from-violet-500 to-fuchsia-400',
                    ],
                    [
                        'title' => 'Items',
                        'description' => 'Manage products and services used in sales and billing.',
                        'route' => route('items.index'),
                        'tag' => 'Inventory',
                        'accent' => 'from-purple-500 to-violet-300',
                    ],
                    [
                        'title' => 'Employees',
                        'description' => 'Maintain employee profiles, salary settings, and advances.',
                        'route' => route('employees.index'),
                        'tag' => 'People',
                        'accent' => 'from-fuchsia-500 to-purple-400',
                    ],
                    [
                        'title' => 'Attendance',
                        'description' => 'Track boards, attendance rows, and salary payout records.',
                        'route' => route('attendance.index'),
                        'tag' => 'Operations',
                        'accent' => 'from-violet-600 to-indigo-400',
                    ],
                ],
            ],
            [
                'title' => 'Transactions',
                'description' => 'Handle revenue, collections, spending, and account movements.',
                'cards' => [
                    [
                        'title' => 'Sales',
                        'description' => 'Create and manage invoices, due dates, and receivables.',
                        'route' => route('sales.index'),
                        'tag' => 'Revenue',
                        'accent' => 'from-purple-600 to-fuchsia-500',
                    ],
                    [
                        'title' => 'Payments',
                        'description' => 'Capture collections received from clients and reconcile balances.',
                        'route' => route('payments.index'),
                        'tag' => 'Collections',
                        'accent' => 'from-violet-500 to-purple-300',
                    ],
                    [
                        'title' => 'Expenses',
                        'description' => 'Record outgoing costs and track business spending.',
                        'route' => route('expenses.index'),
                        'tag' => 'Spend',
                        'accent' => 'from-fuchsia-500 to-violet-400',
                    ],
                    [
                        'title' => 'Ledger',
                        'description' => 'Review sales and payment movement with filterable ledger views.',
                        'route' => route('ledger.index'),
                        'tag' => 'Analysis',
                        'accent' => 'from-purple-900 to-violet-500',
                    ],
                ],
            ],
            [
                'title' => 'Reporting',
                'description' => 'Monitor period-based settlements and carry balances forward cleanly.',
                'cards' => [
                    [
                        'title' => 'Payments Board',
                        'description' => 'Build monthly boards, recalculate client totals, and finalize balances.',
                        'route' => route('reports.payments.board'),
                        'tag' => 'Reports',
                        'accent' => 'from-violet-500 to-indigo-400',
                    ],
                ],
            ],
        ];
    @endphp

    <div class="min-h-full px-4 py-6 sm:px-6 lg:px-10">
        <div class="mx-auto max-w-7xl space-y-8">
            <section class="overflow-hidden rounded-lg border border-violet-500/25 bg-[#08030f] text-white shadow-xl shadow-violet-950/60">
                <div class="grid gap-6 px-6 py-8 sm:px-8 lg:grid-cols-[1.4fr_0.8fr] lg:px-10 lg:py-10">
                    <div class="space-y-4">
                        <span class="inline-flex items-center rounded-full bg-violet-500/15 px-3 py-1 text-xs font-semibold uppercase tracking-[0.2em] text-violet-100 ring-1 ring-inset ring-violet-300/20">
                            Dashboard
                        </span>
                        <div class="space-y-3">
                            <h1 class="text-3xl font-black tracking-tight sm:text-4xl">
                                Open the modules you use every day from one place.
                            </h1>
                            <p class="max-w-2xl text-sm leading-6 text-violet-100/70 sm:text-base">
                                Jump straight into records, transactions, payroll operations, and monthly reporting without digging through nested menus.
                            </p>
                        </div>
                    </div>

                    <div class="rounded-lg border border-violet-500/20 bg-violet-950/20 p-5 backdrop-blur-sm">
                        <p class="text-xs font-semibold uppercase tracking-[0.2em] text-violet-200/70">Active Company</p>
                        <p class="mt-3 text-2xl font-bold text-white">{{ $companyName }}</p>
                        <div class="mt-6 grid grid-cols-2 gap-3 text-sm text-violet-100/80">
                            <div class="rounded-lg border border-violet-500/20 bg-black/35 p-3">
                                <p class="text-xs uppercase tracking-wide text-violet-200/55">Modules</p>
                                <p class="mt-1 text-lg font-semibold">9</p>
                            </div>
                            <div class="rounded-lg border border-violet-500/20 bg-black/35 p-3">
                                <p class="text-xs uppercase tracking-wide text-violet-200/55">Focus</p>
                                <p class="mt-1 text-lg font-semibold">Daily Ops</p>
                            </div>
                        </div>
                    </div>
                </div>
            </section>

            @foreach ($moduleGroups as $group)
                <section class="space-y-4">
                    <div class="flex flex-col gap-2 sm:flex-row sm:items-end sm:justify-between">
                        <div>
                            <h2 class="text-2xl font-bold tracking-tight text-white">{{ $group['title'] }}</h2>
                            <p class="text-sm text-violet-100/65">{{ $group['description'] }}</p>
                        </div>
                    </div>

                    <div class="grid gap-4 md:grid-cols-2 xl:grid-cols-4">
                        @foreach ($group['cards'] as $card)
                            <a
                                href="{{ $card['route'] }}"
                                class="group relative overflow-hidden rounded-lg border border-violet-500/20 bg-black/45 p-5 shadow-sm shadow-black/40 transition duration-200 hover:-translate-y-1 hover:border-violet-400/60 hover:bg-violet-950/25 hover:shadow-xl hover:shadow-violet-950/60"
                            >
                                <div class="absolute inset-x-0 top-0 h-1 bg-gradient-to-r {{ $card['accent'] }}"></div>
                                <div class="flex h-full flex-col justify-between gap-6">
                                    <div class="space-y-4">
                                        <div class="flex items-start justify-between gap-3">
                                            <span class="inline-flex rounded-full bg-violet-500/10 px-3 py-1 text-xs font-semibold uppercase tracking-wide text-violet-100/80 ring-1 ring-inset ring-violet-400/25">
                                                {{ $card['tag'] }}
                                            </span>
                                            <span class="text-violet-300/45 transition group-hover:text-violet-100">&rarr;</span>
                                        </div>

                                        <div>
                                            <h3 class="text-xl font-bold text-white">{{ $card['title'] }}</h3>
                                            <p class="mt-2 text-sm leading-6 text-violet-100/65">{{ $card['description'] }}</p>
                                        </div>
                                    </div>

                                    <div class="inline-flex items-center text-sm font-semibold text-violet-100">
                                        Open module
                                    </div>
                                </div>
                            </a>
                        @endforeach
                    </div>
                </section>
            @endforeach
        </div>
    </div>
</x-layouts.app-layout>
