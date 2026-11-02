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
        @include('site.partials.account-menu')
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
