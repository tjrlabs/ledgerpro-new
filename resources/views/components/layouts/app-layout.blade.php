<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="UTF-8">
        <title>{{ $title ?? 'Admin Panel' }}</title>
        <meta name="csrf-token" content="{{ csrf_token() }}">
        @vite(['resources/css/app.css', 'resources/js/app.js']) {{-- if you're using Vite --}}
    </head>
    <body class="flex flex-col min-h-screen">
    <x-partials.header />
    <main class="flex-grow flex items-stretch justify-start">
        <x-partials.sidebar />
        <div class="container mx-auto mt-6 flex-1">
            {{ $slot }}
        </div>
    </main>
    <x-partials.footer />
    <script type="text/javascript">
        const baseUrl = '{{ url('/') }}';
    </script>
    @stack('scripts')
    </body>
</html>
