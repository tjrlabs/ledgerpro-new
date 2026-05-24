<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'LedgerPro') }}</title>

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

        <!-- Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="font-sans text-violet-50 antialiased">
        <div class="grid min-h-screen lg:grid-cols-[1.05fr_0.95fr]">
            <div class="hidden border-r border-violet-500/20 bg-black/75 p-8 text-white lg:flex lg:flex-col lg:justify-between">
                <a href="/" class="flex items-center gap-3">
                    <span class="flex h-11 w-11 items-center justify-center rounded-lg bg-violet-600 text-sm font-black tracking-[0.2em] text-white shadow-lg shadow-violet-950/70">LP</span>
                    <span>
                        <span class="block text-[11px] font-semibold uppercase tracking-[0.2em] text-violet-200/60">LedgerPro</span>
                        <span class="block text-lg font-black tracking-tight">Business Ledger</span>
                    </span>
                </a>

                <div class="max-w-xl">
                    <p class="text-[11px] font-semibold uppercase tracking-[0.22em] text-violet-300">Operations workspace</p>
                    <h1 class="mt-4 text-5xl font-black tracking-tight">A sharper way to run records, money, payroll, and reports.</h1>
                    <p class="mt-5 text-base leading-7 text-violet-100/70">LedgerPro keeps daily business flows close at hand with a focused interface built for repeated work.</p>
                </div>

                <div class="grid gap-3 rounded-lg border border-violet-500/20 bg-violet-950/20 p-4">
                    <div class="flex items-center justify-between border-b border-violet-500/20 pb-3">
                        <span class="text-sm font-semibold text-violet-100">Today</span>
                        <span class="badge badge-success">Balanced</span>
                    </div>
                    <div class="grid grid-cols-3 gap-3">
                        <div class="rounded-lg bg-black/35 p-3">
                            <p class="text-xs text-violet-200/55">Sales</p>
                            <p class="mt-1 text-xl font-bold">Ready</p>
                        </div>
                        <div class="rounded-lg bg-black/35 p-3">
                            <p class="text-xs text-violet-200/55">Payments</p>
                            <p class="mt-1 text-xl font-bold">Tracked</p>
                        </div>
                        <div class="rounded-lg bg-black/35 p-3">
                            <p class="text-xs text-violet-200/55">Payroll</p>
                            <p class="mt-1 text-xl font-bold">Current</p>
                        </div>
                    </div>
                </div>
            </div>

            <div class="flex min-h-screen flex-col items-center justify-center px-4 py-10 sm:px-6 lg:px-10">
                <div class="mb-8 lg:hidden">
                    <a href="/" class="flex items-center gap-3">
                        <span class="flex h-11 w-11 items-center justify-center rounded-lg bg-violet-600 text-sm font-black tracking-[0.2em] text-white">LP</span>
                        <span class="text-xl font-black tracking-tight text-white">LedgerPro</span>
                    </a>
                </div>

                <div class="w-full max-w-md overflow-hidden rounded-lg border border-violet-500/25 bg-black/62 p-6 shadow-[0_28px_90px_-44px_rgba(88,28,135,0.75)] backdrop-blur-xl sm:p-8">
                    {{ $slot }}
                </div>
            </div>
        </div>
    </body>
</html>
