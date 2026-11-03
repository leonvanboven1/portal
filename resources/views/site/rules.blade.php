<!DOCTYPE html>
<html lang="nl">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
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
        @include('site.partials.navigation')
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

        <section class="site-rule-categories" aria-label="Spelregels per categorie" x-data="rulesSearch">
            <label class="site-rules-search">
                <x-heroicon-o-magnifying-glass class="h-5 w-5"/>
                <span class="sr-only">Zoek in de regels</span>
                <input type="search" x-model="query" placeholder="Zoek een regel of categorie..." autocomplete="off">
                <span class="site-rules-search-count" x-text="resultLabel"></span>
            </label>
            @forelse($categories as $category)
                <article class="site-rule-category" data-rule-category data-category="{{ \Illuminate\Support\Str::lower($category->name) }}" x-show="categoryMatches($el)">
                    <div class="site-rule-category-heading">
                        <span>{{ str_pad((string) $loop->iteration, 2, '0', STR_PAD_LEFT) }}</span>
                        <h2>{{ $category->name }}</h2>
                    </div>
                    <div class="site-rule-list">
                        @forelse($category->rules as $rule)
                            <section class="site-rule-item" data-rule-item data-search="{{ \Illuminate\Support\Str::lower($rule->title.' '.$rule->body) }}" x-show="ruleMatches($el)">
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
            <div class="site-rules-empty" x-cloak x-show="query.trim() !== '' && !hasResults()">
                <x-heroicon-o-magnifying-glass class="h-8 w-8"/>
                <h2>Geen regels gevonden</h2>
                <p>Probeer een andere zoekterm.</p>
            </div>
        </section>
    </main>

    @include('site.partials.footer')
</body>
</html>
