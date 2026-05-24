@php
    $transactionActive = request()->routeIs('sales.*') || request()->routeIs('expenses.*') || request()->routeIs('payments.*') || request()->routeIs('ledger.*');
    $employeeActive = request()->routeIs('employees.*') || request()->routeIs('attendance.*');
    $reportsActive = request()->routeIs('reports.*');

    $navClass = fn (bool $active) => 'app-nav-link ' . ($active ? 'app-nav-link-active' : 'app-nav-link-idle');
    $subNavClass = fn (bool $active) => 'flex items-center rounded-md px-3 py-2 text-sm transition ' . ($active ? 'bg-violet-500/15 font-semibold text-white ring-1 ring-inset ring-violet-400/25' : 'text-violet-100/65 hover:bg-violet-500/10 hover:text-white');
@endphp

<aside id="app-sidebar" :class="sidebarCollapsed ? 'lg:w-0 lg:-translate-x-6 lg:opacity-0' : 'lg:w-72 lg:translate-x-0 lg:opacity-100'" class="hidden shrink-0 overflow-hidden transition-all duration-300 lg:block">
    <div class="sticky top-24 flex max-h-[calc(100vh-7rem)] w-72 flex-col overflow-y-auto rounded-lg border border-violet-500/20 bg-black/62 p-3 shadow-[0_24px_80px_-46px_rgba(88,28,135,0.72)] backdrop-blur-xl" :class="sidebarCollapsed ? 'pointer-events-none' : ''">
        <div class="mb-4 rounded-lg border border-violet-400/25 bg-violet-950/45 p-4 text-white shadow-lg shadow-black/50">
            <p class="text-[11px] font-semibold uppercase tracking-[0.2em] text-violet-200/70">Command Center</p>
            <a href="{{ route('dashboard') }}" class="mt-2 block text-2xl font-black tracking-tight">LedgerPro</a>
            <p class="mt-2 text-sm leading-5 text-violet-100/70">Records, transactions, payroll, and reports in one focused workspace.</p>
        </div>

        @if(session('company_profile'))
            <div class="mb-4 flex items-start gap-3 rounded-lg border border-violet-500/20 bg-violet-950/20 px-3 py-3 text-sm">
                <span class="mt-0.5 flex h-9 w-9 shrink-0 items-center justify-center rounded-lg bg-black/45 text-violet-200 shadow-sm">
                    <i class="fa-solid fa-building"></i>
                </span>
                <div class="min-w-0">
                    <p class="text-[11px] font-semibold uppercase tracking-[0.18em] text-violet-200/60">Active Company</p>
                    <p class="mt-1 truncate font-semibold text-violet-50">{{ optional(session('company_profile'))->company_name }}</p>
                </div>
            </div>
        @endif

        <nav class="space-y-5">
            <div>
                <p class="mb-2 px-3 text-[11px] font-semibold uppercase tracking-[0.18em] text-violet-200/45">Overview</p>
                <a href="{{ route('dashboard') }}" class="{{ $navClass(request()->routeIs('dashboard')) }}">
                    <i class="fa-solid fa-chart-line mr-3 w-5 text-center"></i>
                    Dashboard
                </a>
            </div>

            <div>
                <div class="mb-2 flex items-center justify-between px-3">
                    <p class="text-[11px] font-semibold uppercase tracking-[0.18em] text-violet-200/45">Operations</p>
                    <span class="badge badge-info">6</span>
                </div>
                <ul class="space-y-1">
                    <li>
                        <button onclick="toggleTransactionSubmenu()" class="{{ $navClass($transactionActive) }} w-full justify-between">
                            <span class="flex items-center">
                                <i class="fa-solid fa-wallet mr-3 w-5 text-center"></i>
                                Transactions
                            </span>
                            <i id="transaction-arrow" class="fa-solid fa-chevron-right text-xs transition-transform {{ $transactionActive ? 'rotate-90' : '' }}"></i>
                        </button>
                        <ul id="transaction-submenu" class="ml-4 mt-2 space-y-1 border-l border-violet-500/20 pl-3 {{ $transactionActive ? '' : 'hidden' }}">
                            <li><a href="{{ route('sales.index') }}" class="{{ $subNavClass(request()->routeIs('sales.*')) }}"><i class="fa-solid fa-arrow-trend-up mr-2 w-4 text-center"></i>Sales</a></li>
                            <li><a href="{{ route('payments.index') }}" class="{{ $subNavClass(request()->routeIs('payments.*')) }}"><i class="fa-solid fa-circle-dollar-to-slot mr-2 w-4 text-center"></i>Payments</a></li>
                            <li><a href="{{ route('expenses.index') }}" class="{{ $subNavClass(request()->routeIs('expenses.*')) }}"><i class="fa-solid fa-receipt mr-2 w-4 text-center"></i>Expenses</a></li>
                            <li><a href="{{ route('ledger.index') }}" class="{{ $subNavClass(request()->routeIs('ledger.*')) }}"><i class="fa-solid fa-book-open mr-2 w-4 text-center"></i>Ledger</a></li>
                        </ul>
                    </li>
                    <li>
                        <a href="{{ route('clients.index') }}" class="{{ $navClass(request()->routeIs('clients.*')) }}">
                            <i class="fa-solid fa-address-book mr-3 w-5 text-center"></i>
                            Clients
                        </a>
                    </li>
                    <li>
                        <button onclick="toggleEmployeeSubmenu()" class="{{ $navClass($employeeActive) }} w-full justify-between">
                            <span class="flex items-center">
                                <i class="fa-solid fa-users mr-3 w-5 text-center"></i>
                                Employees
                            </span>
                            <i id="employee-arrow" class="fa-solid fa-chevron-right text-xs transition-transform {{ $employeeActive ? 'rotate-90' : '' }}"></i>
                        </button>
                        <ul id="employee-submenu" class="ml-4 mt-2 space-y-1 border-l border-violet-500/20 pl-3 {{ $employeeActive ? '' : 'hidden' }}">
                            <li><a href="{{ route('employees.index') }}" class="{{ $subNavClass(request()->routeIs('employees.*')) }}"><i class="fa-solid fa-id-card mr-2 w-4 text-center"></i>Manage Employees</a></li>
                            <li><a href="{{ route('attendance.index') }}" class="{{ $subNavClass(request()->routeIs('attendance.*')) }}"><i class="fa-solid fa-calendar-check mr-2 w-4 text-center"></i>Manage Attendance</a></li>
                        </ul>
                    </li>
                    <li>
                        <a href="{{ route('items.index') }}" class="{{ $navClass(request()->routeIs('items.*')) }}">
                            <i class="fa-solid fa-boxes-stacked mr-3 w-5 text-center"></i>
                            Items
                        </a>
                    </li>
                </ul>
            </div>

            <div>
                <div class="mb-2 flex items-center justify-between px-3">
                    <p class="text-[11px] font-semibold uppercase tracking-[0.18em] text-violet-200/45">Reporting</p>
                    <span class="badge badge-warning">1</span>
                </div>
                <button onclick="toggleReportsSubmenu()" class="{{ $navClass($reportsActive) }} w-full justify-between">
                    <span class="flex items-center">
                        <i class="fa-solid fa-chart-pie mr-3 w-5 text-center"></i>
                        Reports
                    </span>
                    <i id="reports-arrow" class="fa-solid fa-chevron-right text-xs transition-transform {{ $reportsActive ? 'rotate-90' : '' }}"></i>
                </button>
                <ul id="reports-submenu" class="ml-4 mt-2 space-y-1 border-l border-violet-500/20 pl-3 {{ $reportsActive ? '' : 'hidden' }}">
                    <li><a href="{{ route('reports.payments.board') }}" class="{{ $subNavClass(request()->routeIs('reports.payments.board*')) }}"><i class="fa-solid fa-table-list mr-2 w-4 text-center"></i>Payments Board</a></li>
                </ul>
            </div>
        </nav>

        <div class="mt-auto border-t border-violet-500/20 pt-4">
            <a href="{{ route('profile.edit') }}" class="flex items-center rounded-lg bg-violet-950/20 px-3 py-3 text-sm font-semibold text-violet-100/75 transition hover:bg-violet-500/10 hover:text-white">
                <span class="mr-3 flex h-9 w-9 items-center justify-center rounded-lg bg-black/45 text-violet-200 shadow-sm">
                    <i class="fa-solid fa-user-gear"></i>
                </span>
                Profile Settings
            </a>
        </div>
    </div>
</aside>

<script>
function toggleTransactionSubmenu() {
    const submenu = document.getElementById('transaction-submenu');
    const arrow = document.getElementById('transaction-arrow');

    submenu.classList.toggle('hidden');
    arrow.classList.toggle('rotate-90');
}

function toggleEmployeeSubmenu() {
    const submenu = document.getElementById('employee-submenu');
    const arrow = document.getElementById('employee-arrow');

    submenu.classList.toggle('hidden');
    arrow.classList.toggle('rotate-90');
}

function toggleReportsSubmenu() {
    const submenu = document.getElementById('reports-submenu');
    const arrow = document.getElementById('reports-arrow');

    submenu.classList.toggle('hidden');
    arrow.classList.toggle('rotate-90');
}
</script>
