<div class="bg-black text-white h-screen w-64 overflow-y-auto">
    <div class="p-4">
        <!-- Logo -->
        <div class="text-xl font-bold mb-6 mt-2">
            <a href="{{ route('dashboard') }}">LedgerPro</a>
        </div>

        <!-- Company Profile Name -->
        @if(session('company_profile'))
            <div class="text-sm font-semibold mb-4 px-3 py-2 bg-gray-700 rounded flex items-center border-l-4 border-blue-500">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2 text-blue-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                </svg>
                {{ session('company_profile')->company_name }}
            </div>
        @endif

        <!-- Navigation -->
        <nav>
            <ul>
                <li class="mb-2">
                    <a href="{{ route('dashboard') }}" class="flex items-center p-2 rounded hover:bg-gray-700 {{ request()->routeIs('dashboard') ? 'bg-gray-700' : '' }}">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6" />
                        </svg>
                        Dashboard
                    </a>
                </li>
                <!-- Transactions with sub-menu -->
                <li class="mb-2">
                    <div>
                        <button onclick="toggleTransactionSubmenu()" class="flex items-center justify-between w-full p-2 rounded hover:bg-gray-700 {{ request()->routeIs('sales.*') || request()->routeIs('expenses.*') || request()->routeIs('payments.*') || request()->routeIs('ledger.*') ? 'bg-gray-700' : '' }}">
                            <div class="flex items-center">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                                Transactions
                            </div>
                            <svg id="transaction-arrow" xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 transform transition-transform {{ request()->routeIs('sales.*') || request()->routeIs('expenses.*') || request()->routeIs('payments.*') || request()->routeIs('ledger.*') ? 'rotate-90' : '' }}" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                            </svg>
                        </button>
                        <ul id="transaction-submenu" class="ml-6 mt-2 space-y-1 {{ request()->routeIs('sales.*') || request()->routeIs('expenses.*') || request()->routeIs('payments.*') || request()->routeIs('ledger.*') ? '' : 'hidden' }}">
                            <li>
                                <a href="{{route('sales.index')}}" class="flex items-center p-2 rounded hover:bg-gray-600 text-sm {{request()->routeIs('sales.*') ? 'bg-gray-600 text-blue-400' : ''}}">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" />
                                    </svg>
                                    Sales
                                </a>
                            </li>
                            <li>
                                <a href="{{route('expenses.index')}}" class="flex items-center p-2 rounded hover:bg-gray-600 text-sm {{request()->routeIs('expenses.*') ? 'bg-gray-600 text-blue-400' : ''}}">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z" />
                                    </svg>
                                    Expenses
                                </a>
                            </li>
                            <li>
                                <a href="{{route('payments.index')}}" class="flex items-center p-2 rounded hover:bg-gray-600 text-sm {{request()->routeIs('payments.*') ? 'bg-gray-600 text-blue-400' : ''}}">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                    </svg>
                                    Payments
                                </a>
                            </li>
                            <li>
                                <a href="{{route('ledger.index')}}" class="flex items-center p-2 rounded hover:bg-gray-600 text-sm {{request()->routeIs('ledger.*') ? 'bg-gray-600 text-blue-400' : ''}}">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                    </svg>
                                    Ledgers
                                </a>
                            </li>
                        </ul>
                    </div>
                </li>
                <li class="mb-2">
                    <a href="{{route('clients.index')}}" class="flex items-center p-2 rounded hover:bg-gray-700 {{request()->routeIs('clients.index') ? 'bg-gray-700' : ''}}">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
                        </svg>
                        Clients
                    </a>
                </li>
                <!-- Employees with sub-menu -->
                <li class="mb-2">
                    <div>
                        <button onclick="toggleEmployeeSubmenu()" class="flex items-center justify-between w-full p-2 rounded hover:bg-gray-700 {{ request()->routeIs('employees.*') || request()->routeIs('attendance.*') ? 'bg-gray-700' : '' }}">
                            <div class="flex items-center">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                </svg>
                                Employees
                            </div>
                            <svg id="employee-arrow" xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 transform transition-transform {{ request()->routeIs('employees.*') || request()->routeIs('attendance.*') ? 'rotate-90' : '' }}" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                            </svg>
                        </button>
                        <ul id="employee-submenu" class="ml-6 mt-2 space-y-1 {{ request()->routeIs('employees.*') || request()->routeIs('attendance.*') ? '' : 'hidden' }}">
                            <li>
                                <a href="{{route('employees.index')}}" class="flex items-center p-2 rounded hover:bg-gray-600 text-sm {{request()->routeIs('employees.*') ? 'bg-gray-600 text-blue-400' : ''}}">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197m13.5-9a2.5 2.5 0 11-5 0 2.5 2.5 0 015 0z" />
                                    </svg>
                                    Manage Employees
                                </a>
                            </li>
                            <li>
                                <a href="{{route('attendance.index')}}" class="flex items-center p-2 rounded hover:bg-gray-600 text-sm {{request()->routeIs('attendance.*') ? 'bg-gray-600 text-blue-400' : ''}}">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v10a2 2 0 002 2h8a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4" />
                                    </svg>
                                    Manage Attendance
                                </a>
                            </li>
                        </ul>
                    </div>
                </li>
                <li class="mb-2">
                    <a href="{{route('items.index')}}" class="flex items-center p-2 rounded hover:bg-gray-700 {{ request()->routeIs('items.index') ? 'bg-gray-700' : '' }}">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0v10a2 2 0 01-2 2H6a2 2 0 01-2-2V7m16 0l-8 4m0 0L4 7" />
                        </svg>
                        Items
                    </a>
                </li>
                <!-- Reports with sub-menu -->
                <li class="mb-2">
                    <div>
                        <button onclick="toggleReportsSubmenu()" class="flex items-center justify-between w-full p-2 rounded hover:bg-gray-700 {{ request()->routeIs('reports.*') ? 'bg-gray-700' : '' }}">
                            <div class="flex items-center">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                </svg>
                                Reports
                            </div>
                            <svg id="reports-arrow" xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 transform transition-transform {{ request()->routeIs('reports.*') ? 'rotate-90' : '' }}" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                            </svg>
                        </button>
                        <ul id="reports-submenu" class="ml-6 mt-2 space-y-1 {{ request()->routeIs('reports.*') ? '' : 'hidden' }}">
                            <li>
                                <a href="{{route('reports.payments.board')}}" class="flex items-center p-2 rounded hover:bg-gray-600 text-sm {{request()->routeIs('reports.payments.board*') ? 'bg-gray-600 text-blue-400' : ''}}">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" />
                                    </svg>
                                    Payments Board
                                </a>
                            </li>
                        </ul>
                    </div>
                </li>
                <li class="mb-2">
                    <a href="#" class="flex items-center p-2 rounded hover:bg-gray-700">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z" />
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                        </svg>
                        Settings
                    </a>
                </li>
            </ul>
        </nav>
    </div>

    <!-- User Profile Link -->
    <div class="border-t border-gray-700 p-4 mt-6">
        <a href="{{ route('profile.edit') }}" class="flex items-center text-sm">
            <div class="rounded-full bg-gray-600 p-2 mr-2">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                </svg>
            </div>
            <span>Profile Settings</span>
        </a>
    </div>
</div>

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
