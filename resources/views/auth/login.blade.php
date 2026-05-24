<x-guest-layout>
    <div class="mb-6">
        <p class="text-[11px] font-semibold uppercase tracking-[0.2em] text-violet-300">Welcome back</p>
        <h1 class="mt-2 text-2xl font-black tracking-tight text-white">Sign in to LedgerPro</h1>
        <p class="mt-2 text-sm leading-6 text-violet-100/65">Open your business workspace and continue from your active company profile.</p>
    </div>

    <!-- Session Status -->
    <x-auth-session-status class="mb-4" :status="session('status')" />

    <form method="POST" action="{{ route('login') }}" class="space-y-5">
        @csrf

        <!-- Email Address -->
        <div>
            <x-input-label for="email" :value="__('Email')" />
            <x-text-input id="email" class="block mt-1 w-full" type="email" name="email" :value="old('email')" required autofocus autocomplete="username" />
            <x-input-error :messages="$errors->get('email')" class="mt-2" />
        </div>

        <!-- Password -->
        <div>
            <x-input-label for="password" :value="__('Password')" />

            <x-text-input id="password" class="block mt-1 w-full"
                            type="password"
                            name="password"
                            required autocomplete="current-password" />

            <x-input-error :messages="$errors->get('password')" class="mt-2" />
        </div>

        <!-- Remember Me -->
        <div class="block">
            <label for="remember_me" class="inline-flex items-center">
                <input id="remember_me" type="checkbox" class="rounded border-violet-500/30 bg-black/45 text-violet-500 shadow-sm focus:ring-violet-500" name="remember">
                <span class="ms-2 text-sm text-violet-100/70">{{ __('Remember me') }}</span>
            </label>
        </div>

        <div class="flex items-center justify-between gap-4">
            @if (Route::has('password.request'))
                <a class="text-sm font-semibold text-violet-200/65 transition hover:text-violet-100 focus:outline-hidden focus:ring-4 focus:ring-violet-500/20" href="{{ route('password.request') }}">
                    {{ __('Forgot your password?') }}
                </a>
            @endif

            <x-primary-button>
                {{ __('Log in') }}
            </x-primary-button>
        </div>
    </form>
</x-guest-layout>
