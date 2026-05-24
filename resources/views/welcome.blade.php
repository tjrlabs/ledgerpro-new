<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>LedgerPro</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="text-violet-50 antialiased">
    <main class="min-h-screen">
        <section class="mx-auto flex min-h-screen w-full max-w-[1800px] flex-col px-4 py-5 sm:px-6 lg:px-8">
            <nav class="flex items-center justify-between rounded-lg border border-violet-500/20 bg-black/62 px-4 py-3 shadow-sm shadow-black/40 backdrop-blur-xl">
                <a href="/" class="flex items-center gap-3">
                    <span class="flex h-10 w-10 items-center justify-center rounded-lg bg-violet-600 text-sm font-black tracking-[0.2em] text-white shadow-lg shadow-violet-950/60">LP</span>
                    <span class="text-lg font-black tracking-tight text-white">LedgerPro</span>
                </a>

                <div class="flex items-center gap-2">
                    @auth
                        <a href="{{ route('dashboard') }}" class="btn-primary">Dashboard</a>
                    @else
                        <a href="{{ route('login') }}" class="btn-secondary">Log in</a>
                        @if (Route::has('register'))
                            <a href="{{ route('register') }}" class="btn-primary">Register</a>
                        @endif
                    @endauth
                </div>
            </nav>

            <div class="grid flex-1 items-center gap-10 py-10 lg:grid-cols-[0.9fr_1.1fr] lg:py-14">
                <div class="max-w-2xl">
                    <span class="badge badge-info">Business ledger workspace</span>
                    <h1 class="mt-5 text-4xl font-black tracking-tight text-white sm:text-6xl">
                        LedgerPro
                    </h1>
                    <p class="mt-5 text-lg leading-8 text-violet-100/70">
                        A modern operating desk for clients, inventory, sales, payments, payroll, attendance, and monthly reporting.
                    </p>
                    <div class="mt-8 flex flex-col gap-3 sm:flex-row">
                        @auth
                            <a href="{{ route('dashboard') }}" class="btn-primary">Open Dashboard</a>
                        @else
                            <a href="{{ route('login') }}" class="btn-primary">Start Working</a>
                            @if (Route::has('register'))
                                <a href="{{ route('register') }}" class="btn-secondary">Create Account</a>
                            @endif
                        @endauth
                    </div>
                </div>

                <div class="rounded-lg border border-violet-500/20 bg-black/45 p-4 shadow-[0_32px_100px_-52px_rgba(88,28,135,0.78)] backdrop-blur-xl">
                    <div class="rounded-lg border border-violet-500/20 bg-[#08030f] p-4 text-white">
                        <div class="flex items-center justify-between border-b border-violet-500/20 pb-4">
                            <div>
                                <p class="text-[11px] font-semibold uppercase tracking-[0.2em] text-violet-300">Live Workspace</p>
                                <p class="mt-1 text-xl font-black">Daily Operations</p>
                            </div>
                            <span class="badge badge-success">Ready</span>
                        </div>

                        <div class="mt-4 grid gap-3 sm:grid-cols-3">
                            <div class="rounded-lg bg-violet-950/25 p-4">
                                <p class="text-xs text-violet-200/55">Receivables</p>
                                <p class="mt-2 text-2xl font-black">Sales</p>
                                <div class="mt-4 h-2 rounded-full bg-black/70">
                                    <div class="h-2 w-3/4 rounded-full bg-violet-400"></div>
                                </div>
                            </div>
                            <div class="rounded-lg bg-violet-950/25 p-4">
                                <p class="text-xs text-violet-200/55">Collections</p>
                                <p class="mt-2 text-2xl font-black">Payments</p>
                                <div class="mt-4 h-2 rounded-full bg-black/70">
                                    <div class="h-2 w-2/3 rounded-full bg-fuchsia-400"></div>
                                </div>
                            </div>
                            <div class="rounded-lg bg-violet-950/25 p-4">
                                <p class="text-xs text-violet-200/55">Payroll</p>
                                <p class="mt-2 text-2xl font-black">Attendance</p>
                                <div class="mt-4 h-2 rounded-full bg-black/70">
                                    <div class="h-2 w-4/5 rounded-full bg-purple-300"></div>
                                </div>
                            </div>
                        </div>

                        <div class="mt-4 overflow-hidden rounded-lg border border-violet-500/20">
                            <div class="grid grid-cols-4 bg-violet-950/35 px-4 py-3 text-[11px] font-semibold uppercase tracking-[0.14em] text-violet-100/70">
                                <span>Module</span>
                                <span>Status</span>
                                <span>Owner</span>
                                <span class="text-right">Action</span>
                            </div>
                            <div class="divide-y divide-violet-500/15 text-sm">
                                <div class="grid grid-cols-4 px-4 py-3 text-violet-100/85">
                                    <span>Clients</span>
                                    <span class="text-purple-300">Active</span>
                                    <span>Admin</span>
                                    <span class="text-right text-violet-300">Open</span>
                                </div>
                                <div class="grid grid-cols-4 px-4 py-3 text-violet-100/85">
                                    <span>Ledger</span>
                                    <span class="text-fuchsia-300">Review</span>
                                    <span>Finance</span>
                                    <span class="text-right text-violet-300">Open</span>
                                </div>
                                <div class="grid grid-cols-4 px-4 py-3 text-violet-100/85">
                                    <span>Payments Board</span>
                                    <span class="text-purple-300">Synced</span>
                                    <span>Ops</span>
                                    <span class="text-right text-violet-300">Open</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>
    </main>
</body>
</html>
