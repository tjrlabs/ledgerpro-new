<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>{{ $title ?? 'LedgerPro' }}</title>
        <meta name="csrf-token" content="{{ csrf_token() }}">
        @vite(['resources/css/app.css', 'resources/js/app.js']) {{-- if you're using Vite --}}
    </head>
    <body class="min-h-screen text-violet-50 antialiased">
    <div x-data="appShell()" x-init="init()">
        <x-partials.header />
        <main :class="sidebarCollapsed ? 'lg:gap-0' : 'lg:gap-5'" class="mx-auto flex min-h-[calc(100vh-8.5rem)] w-full max-w-[1800px] items-stretch gap-5 px-4 py-5 sm:px-6 lg:px-8 transition-[gap] duration-300">
            <x-partials.sidebar />
            <div class="min-w-0 flex-1 overflow-hidden rounded-lg border border-violet-500/20 bg-black/45 shadow-[0_28px_90px_-48px_rgba(88,28,135,0.72)] backdrop-blur-xl">
                {{ $slot }}
            </div>
        </main>
        <x-partials.footer />
    </div>
    <script type="text/javascript">
        const baseUrl = '{{ url('/') }}';

        function appShell() {
            return {
                sidebarCollapsed: false,
                init() {
                    this.sidebarCollapsed = window.localStorage.getItem('ledgerpro:sidebar-collapsed') === 'true';
                },
                toggleSidebar() {
                    this.sidebarCollapsed = !this.sidebarCollapsed;
                    window.localStorage.setItem('ledgerpro:sidebar-collapsed', this.sidebarCollapsed ? 'true' : 'false');
                },
            };
        }
    </script>
    @stack('scripts')
    </body>
</html>
