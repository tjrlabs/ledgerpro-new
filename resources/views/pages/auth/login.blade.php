<x-layouts.app-layout title="Login">
    <div class="flex items-center justify-center min-h-screen bg-gray-100">
        <div class="w-full max-w-md p-8 bg-white rounded-lg shadow-md">
            <h2 class="text-2xl font-bold text-center mb-6">Login</h2>
            <form method="POST" action="{{ route('login') }}">
                @csrf
                <x-forms.input id="email" name="email" type="email" label="Email" required autofocus />
                <x-forms.input id="password" name="password" type="password" label="Password" required />
                <x-forms.checkbox-toggle id="remember_me" name="remember" label="Remember Me" description="Keep me logged in on this device." icon="fa-solid fa-check" />
                <button type="submit" class="w-full px-4 py-2 mt-4 text-white bg-primary rounded hover:bg-primary-dark">Login</button>
            </form>
            <p class="mt-4 text-center">
                <a href="{{ route('password.request') }}" class="text-sm text-primary hover:underline">Forgot your password?</a>
            </p>
        </div>
    </div>
</x-layouts.app-layout>
