@extends('portal.layouts.guest')

@section('title', 'Inloggen')
@section('header', 'Log in op je account')
@section('subheader', 'Jouw volgende hoofdstuk begint waar je gebleven was.')

@section('content')
    @if (\App\Support\Portal::demo())
        <div class="site-demo-notice mb-6 rounded-md px-4 py-3 text-sm">
            Demo: log in met <span class="font-mono">{{ \Database\Seeders\DemoSeeder::EMAIL }}</span> en wachtwoord <span class="font-mono">{{ \Database\Seeders\DemoSeeder::PASSWORD }}</span>.
        </div>
    @endif
    <x-portal.status />

    <form method="POST" action="{{ route('login') }}" class="site-login-form space-y-5">
        @csrf
        <x-portal.input name="email" type="email" label="E-mailadres" required autofocus autocomplete="email" />
        <x-portal.input name="password" type="password" label="Wachtwoord" required autocomplete="current-password" />

        <div class="flex items-center justify-between">
            <label class="flex items-center gap-2 text-sm text-gray-700 dark:text-gray-300">
                <input type="checkbox" name="remember" id="remember" class="h-4 w-4 rounded border-gray-300 text-indigo-600 focus:ring-indigo-600">
                Ingelogd blijven
            </label>
            <a href="{{ route('password.request') }}" class="text-sm font-semibold hover:underline">Wachtwoord vergeten?</a>
        </div>

        <button type="submit" class="site-button w-full justify-center px-4 py-3">
            Inloggen <x-heroicon-o-arrow-right class="h-4 w-4"/>
        </button>
    </form>

    <p class="site-auth-register mt-8 border-t pt-6 text-sm">
        Nog geen account? <a href="{{ route('register') }}" class="font-semibold hover:underline">Account aanmaken</a>
    </p>
@endsection
