<div x-data="{ open: false }" class="sticky top-0 z-40 border-b border-violet-500/20 bg-black/72 backdrop-blur-xl">
    <div class="mx-auto max-w-[1800px] px-4 sm:px-6 lg:px-8">
        <div class="flex min-h-16 items-center justify-between gap-4 py-3">
            <div class="flex items-center gap-4">
                <button @click="toggleSidebar()" type="button" class="hidden lg:inline-flex items-center justify-center rounded-lg border border-violet-500/25 bg-violet-950/20 p-3 text-violet-100 shadow-sm shadow-black/40 transition hover:-translate-y-0.5 hover:border-violet-400/60 hover:text-white hover:shadow-md focus:outline-hidden" :aria-expanded="(!sidebarCollapsed).toString()" aria-controls="app-sidebar" :title="sidebarCollapsed ? 'Open sidebar' : 'Collapse sidebar'">
                    <svg x-cloak x-show="!sidebarCollapsed" xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.75" d="M15 19l-7-7 7-7" />
                    </svg>
                    <svg x-cloak x-show="sidebarCollapsed" xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.75" d="M9 5l7 7-7 7" />
                    </svg>
                </button>

                <a href="{{ route('dashboard') }}" class="flex items-center gap-3 rounded-lg border border-violet-500/25 bg-violet-950/20 px-3 py-2 shadow-sm shadow-black/40 transition hover:-translate-y-0.5 hover:border-violet-400/60 hover:bg-violet-500/10 hover:shadow-md">
                    <span class="flex h-10 w-10 items-center justify-center rounded-lg bg-violet-600 text-sm font-black uppercase tracking-[0.2em] text-white shadow-sm shadow-violet-950/70">
                        LP
                    </span>
                    <span>
                        <span class="block text-[11px] font-semibold uppercase tracking-[0.2em] text-violet-200/60">Workspace</span>
                        <span class="block text-lg font-black tracking-tight text-white">LedgerPro</span>
                    </span>
                </a>

                <div class="hidden rounded-lg border border-violet-500/20 bg-black/35 px-4 py-2 shadow-sm shadow-black/30 lg:block">
                    <p class="text-[11px] font-semibold uppercase tracking-[0.18em] text-violet-200/60">Active Company</p>
                    <p class="mt-1 text-sm font-semibold text-violet-50">{{ optional(session('company_profile'))->company_name ?? 'Select Company' }}</p>
                </div>
            </div>

            <!-- User Dropdown -->
            <div class="hidden sm:flex sm:items-center">
                <x-dropdown align="right" width="48">
                    <x-slot name="trigger">
                        <button class="inline-flex items-center gap-3 rounded-lg border border-violet-500/25 bg-violet-950/20 px-3 py-2 text-sm font-medium leading-4 text-violet-100 shadow-sm shadow-black/40 transition hover:-translate-y-0.5 hover:border-violet-400/60 hover:text-white hover:shadow-md focus:outline-hidden">
                            <div class="flex h-9 w-9 items-center justify-center rounded-lg bg-gradient-to-br from-violet-500 to-fuchsia-500 text-xs font-bold text-white">
                                {{ strtoupper(substr(Auth::user()->name ?? 'G', 0, 1)) }}
                            </div>

                            <div class="text-left">
                                <div class="font-semibold text-white">{{ Auth::user()->name ?? 'Guest' }}</div>
                                <div class="text-xs uppercase tracking-wide text-violet-200/60">Account</div>
                            </div>

                            <div class="ms-1">
                                <svg class="fill-current h-4 w-4" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd" />
                                </svg>
                            </div>
                        </button>
                    </x-slot>

                    <x-slot name="content">
                        <x-dropdown-link :href="route('profile.edit')">
                            {{ __('Profile') }}
                        </x-dropdown-link>

                        <!-- Authentication -->
                        <form method="POST" action="{{ route('logout') }}">
                            @csrf

                            <x-dropdown-link :href="route('logout')"
                                    onclick="event.preventDefault();
                                              this.closest('form').submit();">
                                {{ __('Log Out') }}
                            </x-dropdown-link>
                        </form>
                    </x-slot>
                </x-dropdown>
            </div>

            <!-- Mobile menu button -->
            <div class="-me-2 flex items-center sm:hidden">
                <button @click="open = ! open" class="inline-flex items-center justify-center rounded-lg border border-violet-500/25 bg-violet-950/20 p-2 text-violet-100 shadow-sm transition hover:border-violet-400/60 hover:text-white focus:outline-hidden">
                    <svg class="h-6 w-6" stroke="currentColor" fill="none" viewBox="0 0 24 24">
                        <path :class="{'hidden': open, 'inline-flex': ! open }" class="inline-flex" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                        <path :class="{'hidden': ! open, 'inline-flex': open }" class="hidden" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>
        </div>
    </div>

    <!-- Mobile menu -->
    <div :class="{'block': open, 'hidden': ! open}" class="hidden border-t border-violet-500/20 bg-black/95 sm:hidden">
        <div class="grid grid-cols-1 gap-2 px-4 pb-4 pt-3">
            <a href="{{ route('dashboard') }}" class="btn-secondary justify-start">Dashboard</a>
            <a href="{{ route('clients.index') }}" class="btn-secondary justify-start">Clients</a>
            <a href="{{ route('items.index') }}" class="btn-secondary justify-start">Items</a>
            <a href="{{ route('employees.index') }}" class="btn-secondary justify-start">Employees</a>
            <a href="{{ route('attendance.index') }}" class="btn-secondary justify-start">Attendance</a>
            <a href="{{ route('sales.index') }}" class="btn-secondary justify-start">Sales</a>
            <a href="{{ route('payments.index') }}" class="btn-secondary justify-start">Payments</a>
            <a href="{{ route('expenses.index') }}" class="btn-secondary justify-start">Expenses</a>
            <a href="{{ route('ledger.index') }}" class="btn-secondary justify-start">Ledger</a>
            <a href="{{ route('reports.payments.board') }}" class="btn-secondary justify-start">Payments Board</a>
        </div>

        <!-- Responsive Settings Options -->
        <div class="border-t border-violet-500/20 pb-4 pt-4">
            <div class="px-4">
                <div class="font-medium text-base text-white">{{ Auth::user()->name ?? 'Guest' }}</div>
                @auth
                    <div class="font-medium text-sm text-violet-200/60">{{ Auth::user()->email }}</div>
                @endauth
            </div>

            <div class="mt-3 space-y-1">
                <x-responsive-nav-link :href="route('profile.edit')">
                    {{ __('Profile') }}
                </x-responsive-nav-link>

                <!-- Authentication -->
                <form method="POST" action="{{ route('logout') }}">
                    @csrf

                    <x-responsive-nav-link :href="route('logout')"
                            onclick="event.preventDefault();
                                        this.closest('form').submit();">
                        {{ __('Log Out') }}
                    </x-responsive-nav-link>
                </form>
            </div>
        </div>
    </div>
</div>
