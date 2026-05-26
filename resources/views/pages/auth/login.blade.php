<x-layouts.app-layout title="Login">
    <div class="flex min-h-screen items-center justify-center bg-[radial-gradient(circle_at_top,rgba(88,28,135,0.45),transparent_38%),linear-gradient(180deg,#05010a_0%,#08030f_100%)] px-4">
        <div class="w-full max-w-md overflow-hidden rounded-2xl border border-violet-500/25 bg-[#08030f]/95 p-8 shadow-[0_28px_90px_-44px_rgba(88,28,135,0.8)] backdrop-blur-xl">
            <h2 class="mb-6 text-center text-2xl font-bold text-white">Login</h2>
            <form method="POST" action="{{ route('login') }}">
                @csrf
                <x-forms.input id="email" name="email" type="email" label="Email" required autofocus />
                <x-forms.input id="password" name="password" type="password" label="Password" required />
                <x-forms.checkbox-toggle id="remember_me" name="remember" label="Remember Me" description="Keep me logged in on this device." icon="fa-solid fa-check" />
                <button type="submit" class="btn-primary mt-4 w-full">Login</button>
            </form>
            <p class="mt-4 text-center text-sm text-violet-300/70">
                <a href="{{ route('password.request') }}" class="text-violet-200 hover:text-white hover:underline">Forgot your password?</a>
            </p>
        </div>
    </div>
</x-layouts.app-layout>
