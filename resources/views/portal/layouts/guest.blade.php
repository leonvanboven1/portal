<!DOCTYPE html>
<html lang="nl" class="h-full">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title') — {{ config('app.name', 'Minetopia Panel') }}</title>
    @unless (\App\Models\BrandingSetting::current()->logo_path)
        <link rel="icon" type="image/x-icon" href="{{ asset('favicon.ico') }}">
    @endunless
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Schibsted+Grotesk:wght@400..800&family=JetBrains+Mono:wght@500&display=swap" rel="stylesheet">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <script>
        if (localStorage.theme === 'dark' || (!('theme' in localStorage) && window.matchMedia('(prefers-color-scheme: dark)').matches)) {
            document.documentElement.classList.add('dark')
        } else {
            document.documentElement.classList.remove('dark')
        }
    </script>
</head>
<body class="site-auth-page min-h-full" x-data="{
    darkMode: document.documentElement.classList.contains('dark'),
    toggleDarkMode() {
        this.darkMode = !this.darkMode;
        localStorage.theme = this.darkMode ? 'dark' : 'light';
        document.documentElement.classList.toggle('dark', this.darkMode);
    }
}">
    <header class="site-header site-auth-header">
        <a href="{{ route('site.home') }}" class="site-brand" aria-label="Vunex — home">
            <span class="site-brand-mark">
                <img src="{{ \App\Models\BrandingSetting::current()->logoUrl() }}" alt="" @class(['omt-pixel' => \App\Models\BrandingSetting::current()->isLogoPixelated()])>
            </span>
            <span>Vunex<small>MINECRAFT ROLEPLAY</small></span>
        </a>
        @include('site.partials.navigation')
        @include('site.partials.account-menu')
    </header>
    <div class="site-auth-layout">
        <aside class="site-auth-aside">
            <a href="{{ route('site.home') }}" class="site-brand site-auth-brand">
                <span class="site-brand-mark"><img src="{{ asset('images/brand/logo.svg') }}" alt=""></span>
                <span>{{ config('app.name') }}<small>MINECRAFT ROLEPLAY</small></span>
            </a>
            <div class="site-auth-aside-copy">
                <span class="site-eyebrow"><span></span> DE STAD IS VAN JOU</span>
                <h2>Fijn dat je<br>weer <em>terug bent.</em></h2>
                <p>Log in en pak jouw verhaal weer op. Je volgende avontuur is dichterbij dan je denkt.</p>
            </div>
            <div class="site-auth-art" aria-hidden="true">
                <span></span><span></span><span></span><span></span><span></span>
            </div>
            <p class="site-auth-aside-footer">JOUW PERSONAGE. JOUW KEUZES. JOUW VERHAAL.</p>
        </aside>

        <main class="site-auth-main">
            <div class="site-auth-card">
                <div class="site-auth-card-heading">
                    <span class="site-auth-card-mark"><x-heroicon-o-lock-closed class="h-5 w-5"/></span>
                    <p class="site-section-kicker">WELKOM TERUG</p>
                    <h1>@yield('header')</h1>
                    @hasSection('subheader')
                        <p class="site-auth-subheader">@yield('subheader')</p>
                    @else
                        <p class="site-auth-subheader">Log in om verder te gaan naar jouw account.</p>
                    @endif
                </div>

                @if (session()->has(\App\Services\Tenancy\AdminClaim::SESSION_KEY))
                    <div class="site-auth-claim-notice">
                        Log in of maak een account aan. Daarna ben je beheerder van dit portaal.
                    </div>
                @endif

                <div class="site-auth-form">
                    @yield('content')
                </div>
                <a class="site-auth-home-link" href="{{ route('site.home') }}">← Terug naar de website</a>
            </div>
            <p class="site-auth-legal">Door verder te gaan, maak je deel uit van {{ config('app.name') }}.</p>
        </main>
    </div>
    @include('site.partials.footer')
</body>
</html>
