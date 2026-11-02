<!DOCTYPE html>
<html lang="nl">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="theme-color" content="#f5f4ef">
    <meta name="description" content="Bekijk de spelregels van Vunex en speel mee aan een fijne roleplaystad.">
    <title>Spelregels — Vunex</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="site-page site-rules-page">
    <header class="site-header">
        <a href="{{ route('site.home') }}" class="site-brand" aria-label="Vunex — home">
            <span class="site-brand-mark"><img src="{{ $branding->logoUrl() }}" alt="" @class(['omt-pixel' => $branding->isLogoPixelated()])></span>
            <span>Vunex<small>MINECRAFT ROLEPLAY</small></span>
        </a>
        <nav class="site-nav" aria-label="Hoofdnavigatie">
            <a href="{{ route('site.home') }}#over-ons">Over ons</a>
            <a href="{{ route('site.home') }}#starten">Beginnen</a>
        </nav>
        <div class="site-account">
            @auth
                <div class="site-account-menu" x-data="{ open: false }" @keydown.escape.window="open = false">
                    <button type="button" class="site-account-trigger" @click="open = !open" :aria-expanded="open" aria-haspopup="true">
                        <img src="https://mc-heads.net/avatar/{{ rawurlencode(auth()->user()->minecraft_username ?: auth()->user()->name) }}/40" alt="" class="site-account-avatar">
                        <span class="site-account-name">{{ auth()->user()->minecraft_username ?: auth()->user()->name }}</span>
                        <x-heroicon-o-chevron-down class="h-4 w-4"/>
                    </button>
                    <div class="site-dropdown" x-show="open" x-cloak x-transition.origin.top.right @click.outside="open = false">
                        <div class="site-dropdown-identity"><strong>{{ auth()->user()->name }}</strong><span>{{ auth()->user()->minecraft_username }}</span></div>
                        <a href="{{ route('dashboard') }}"><x-heroicon-o-squares-2x2 class="h-4 w-4"/> Naar het panel</a>
                        <form method="POST" action="{{ route('logout') }}">@csrf<button type="submit"><x-heroicon-o-arrow-right-on-rectangle class="h-4 w-4"/> Uitloggen</button></form>
                    </div>
                </div>
            @else
                <a class="site-login-link" href="{{ route('login') }}">Inloggen</a>
                <a class="site-button site-button-small" href="{{ route('register') }}">Account maken</a>
            @endauth
        </div>
    </header>

    <main>
        <section class="site-rules-hero">
            <div class="site-section-marker"><span>VUNEX</span><i></i> SAMEN SPELEN</div>
            <p class="site-section-kicker">EEN FIJNE STAD MAKEN WE SAMEN</p>
            <h1>De spelregels</h1>
            <p>Zo houden we Vunex eerlijk, veilig en leuk voor iedereen. Lees de categorieën goed door voordat je begint.</p>
            <a class="site-text-link" href="{{ route('site.home') }}"><x-heroicon-o-arrow-left class="h-4 w-4"/> Terug naar de homepage</a>
        </section>

        <section class="site-rule-categories" aria-label="Spelregels per categorie">
            @forelse($categories as $category)
                <article class="site-rule-category">
                    <div class="site-rule-category-heading">
                        <span>{{ str_pad((string) $loop->iteration, 2, '0', STR_PAD_LEFT) }}</span>
                        <h2>{{ $category->name }}</h2>
                    </div>
                    <div class="site-rule-list">
                        @forelse($category->rules as $rule)
                            <section class="site-rule-item">
                                <span>{{ str_pad((string) $loop->iteration, 2, '0', STR_PAD_LEFT) }}</span>
                                <div><h3>{{ $rule->title }}</h3><p>{{ $rule->body }}</p></div>
                            </section>
                        @empty
                            <p class="site-rule-empty">De regels voor deze categorie worden binnenkort toegevoegd.</p>
                        @endforelse
                    </div>
                </article>
            @empty
                <div class="site-rules-empty">
                    <x-heroicon-o-document-text class="h-8 w-8"/>
                    <h2>De regels worden voorbereid</h2>
                    <p>Er zijn nog geen regels gepubliceerd. Kom binnenkort terug.</p>
                </div>
            @endforelse
        </section>
    </main>

    <footer class="site-footer">
        <a href="{{ route('site.home') }}" class="site-brand site-brand-footer">
            <span class="site-brand-mark"><img src="{{ $branding->logoUrl() }}" alt="" @class(['omt-pixel' => $branding->isLogoPixelated()])></span>
            <span>Vunex<small>MINECRAFT ROLEPLAY</small></span>
        </a>
        <span>Een wereld gebouwd op verhalen, gemaakt door spelers.</span>
        <a href="{{ route('site.home') }}">Terug naar Vunex <x-heroicon-o-arrow-up-right class="h-3.5 w-3.5"/></a>
    </footer>
</body>
</html>
