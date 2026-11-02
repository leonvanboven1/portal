<!DOCTYPE html>
<html lang="nl">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="theme-color" content="#f5f4ef">
    <meta name="description" content="Beheer je Vunex-accountgegevens.">
    <title>Mijn gegevens — Vunex</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="site-page">
    <header class="site-header">
        <a href="{{ route('site.home') }}" class="site-brand" aria-label="Vunex — home">
            <span class="site-brand-mark"><img src="{{ $branding->logoUrl() }}" alt="" @class(['omt-pixel' => $branding->isLogoPixelated()])></span>
            <span>Vunex<small>MINECRAFT ROLEPLAY</small></span>
        </a>
        <nav class="site-nav" aria-label="Hoofdnavigatie">
            <a href="{{ route('site.home') }}#over-ons">Over ons</a>
            <a href="{{ route('site.rules') }}">Onze regels</a>
            <a href="{{ route('site.home') }}#starten">Beginnen</a>
        </nav>
        @include('site.partials.account-menu')
    </header>

    <main class="site-account-page">
        <div class="site-account-content">
            <a class="site-text-link" href="{{ route('site.home') }}"><x-heroicon-o-arrow-left class="h-4 w-4"/> Terug naar Vunex</a>
            <p class="site-section-kicker">ACCOUNTBEHEER</p>
            <h1>Mijn gegevens</h1>
            <p class="site-account-intro">Pas je e-mailadres of wachtwoord aan. Je Minecraft-gebruikersnaam is gekoppeld aan je personage en kan hier niet worden gewijzigd.</p>

            @if(session('success'))
                <div class="site-account-notice" role="status">{{ session('success') }}</div>
            @endif

            <section class="site-account-card" aria-labelledby="identity-heading">
                <div class="site-account-card-heading">
                    <span class="site-account-card-icon"><x-heroicon-o-user class="h-5 w-5"/></span>
                    <div>
                        <h2 id="identity-heading">Je Vunex-profiel</h2>
                        <p>Je Minecraft-naam is alleen-lezen en kan niet worden aangepast.</p>
                    </div>
                </div>
                <dl class="site-account-identity">
                    <div><dt>Naam</dt><dd>{{ auth()->user()->name }}</dd></div>
                    <div><dt>Minecraft-gebruikersnaam</dt><dd>{{ auth()->user()->minecraft_username }}</dd></div>
                </dl>
            </section>

            <section class="site-account-card" aria-labelledby="email-heading">
                <div class="site-account-card-heading">
                    <span class="site-account-card-icon"><x-heroicon-o-envelope class="h-5 w-5"/></span>
                    <div>
                        <h2 id="email-heading">E-mailadres wijzigen</h2>
                        <p>Gebruik je huidige wachtwoord om deze wijziging te bevestigen.</p>
                    </div>
                </div>
                <form method="POST" action="{{ route('account.email.update') }}" class="site-account-form">
                    @csrf
                    @method('PUT')
                    <label for="email">Nieuw e-mailadres</label>
                    <input id="email" name="email" type="email" value="{{ old('email', auth()->user()->email) }}" autocomplete="email" required>
                    @error('email')<p class="site-account-error">{{ $message }}</p>@enderror

                    <label for="email_current_password">Huidig wachtwoord</label>
                    <input id="email_current_password" name="email_current_password" type="password" autocomplete="current-password" required>
                    @error('email_current_password')<p class="site-account-error">{{ $message }}</p>@enderror

                    <button class="site-button" type="submit">E-mailadres opslaan</button>
                </form>
            </section>

            <section class="site-account-card" aria-labelledby="password-heading">
                <div class="site-account-card-heading">
                    <span class="site-account-card-icon"><x-heroicon-o-lock-closed class="h-5 w-5"/></span>
                    <div>
                        <h2 id="password-heading">Wachtwoord wijzigen</h2>
                        <p>Kies een wachtwoord van minimaal 8 tekens.</p>
                    </div>
                </div>
                <form method="POST" action="{{ route('account.password.update') }}" class="site-account-form">
                    @csrf
                    @method('PUT')
                    <label for="password_current_password">Huidig wachtwoord</label>
                    <input id="password_current_password" name="current_password" type="password" autocomplete="current-password" required>
                    @error('current_password')<p class="site-account-error">{{ $message }}</p>@enderror

                    <label for="password">Nieuw wachtwoord</label>
                    <input id="password" name="password" type="password" autocomplete="new-password" required>
                    @error('password')<p class="site-account-error">{{ $message }}</p>@enderror

                    <label for="password_confirmation">Herhaal nieuw wachtwoord</label>
                    <input id="password_confirmation" name="password_confirmation" type="password" autocomplete="new-password" required>

                    <button class="site-button" type="submit">Wachtwoord opslaan</button>
                </form>
            </section>
        </div>
    </main>
</body>
</html>
