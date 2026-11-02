<!DOCTYPE html>
<html lang="nl">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="theme-color" content="#f5f4ef">
    <meta name="description" content="{{ $content->hero_text }}">
    <title>{{ config('app.name') }} — {{ $content->hero_title }}</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="site-page">
    <header class="site-header">
        <a href="{{ route('site.home') }}" class="site-brand" aria-label="{{ config('app.name') }} — home">
            <span class="site-brand-mark"><img src="{{ asset('images/brand/logo.svg') }}" alt=""></span>
            <span>{{ config('app.name') }}<small>MINECRAFT ROLEPLAY</small></span>
        </a>

        <nav class="site-nav" aria-label="Hoofdnavigatie">
            <a href="#over-ons">Over ons</a>
            <a href="#regels">Onze regels</a>
        </nav>

        <div class="site-account">
            @auth
                <div class="site-account-menu" x-data="{ open: false }" @keydown.escape.window="open = false">
                    <button type="button" class="site-account-trigger" @click="open = !open" :aria-expanded="open" aria-haspopup="true">
                        <img src="https://mc-heads.net/avatar/{{ rawurlencode(auth()->user()->minecraft_username ?: auth()->user()->name) }}/40" alt="" class="site-account-avatar">
                        <span class="site-account-name">{{ auth()->user()->minecraft_username ?: auth()->user()->name }}</span>
                        <svg viewBox="0 0 20 20" fill="currentColor" aria-hidden="true"><path fill-rule="evenodd" d="M5.22 7.47a.75.75 0 0 1 1.06 0L10 11.19l3.72-3.72a.75.75 0 1 1 1.06 1.06l-4.25 4.25a.75.75 0 0 1-1.06 0L5.22 8.53a.75.75 0 0 1 0-1.06Z" clip-rule="evenodd"/></svg>
                    </button>
                    <div class="site-dropdown" x-show="open" x-cloak x-transition.origin.top.right @click.outside="open = false">
                        <div class="site-dropdown-identity">
                            <strong>{{ auth()->user()->name }}</strong>
                            <span>{{ auth()->user()->minecraft_username }}</span>
                        </div>
                        <a href="{{ route('dashboard') }}">
                            <x-heroicon-o-squares-2x2 class="h-4 w-4"/>
                            Naar het panel
                        </a>
                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <button type="submit">
                                <x-heroicon-o-arrow-right-on-rectangle class="h-4 w-4"/>
                                Uitloggen
                            </button>
                        </form>
                    </div>
                </div>
            @else
                <a class="site-login-link" href="{{ route('login') }}">Inloggen</a>
                <a class="site-button site-button-small" href="{{ route('register') }}">Account maken</a>
            @endauth
        </div>
    </header>

    <main>
        <section class="site-hero">
            <div class="site-hero-glow site-hero-glow-one"></div>
            <div class="site-hero-glow site-hero-glow-two"></div>
            <div class="site-hero-content">
                <span class="site-eyebrow"><span></span> EEN STAD VOL VERHALEN</span>
                <h1>{{ $content->hero_title }}</h1>
                <p>{{ $content->hero_text }}</p>
                <div class="site-hero-actions">
                    @auth
                        <a class="site-button" href="{{ route('dashboard') }}">Naar het panel <x-heroicon-o-arrow-right class="h-4 w-4"/></a>
                    @else
                        <a class="site-button" href="{{ route('register') }}">Word onderdeel van de stad <x-heroicon-o-arrow-right class="h-4 w-4"/></a>
                        <a class="site-button-secondary" href="{{ route('login') }}">Ik heb al een account</a>
                    @endauth
                </div>
                <div class="site-hero-note"><span class="site-status-dot"></span> Jouw personage. Jouw keuzes. Jouw verhaal.</div>
            </div>
            <div class="site-hero-art" aria-hidden="true">
                <div class="site-art-sun"></div>
                <div class="site-art-orbit site-art-orbit-one"></div>
                <div class="site-art-orbit site-art-orbit-two"></div>
                <div class="site-art-card site-art-card-main">
                    <span class="site-art-card-kicker">WESTERDAM · 18:42</span>
                    <div class="site-art-skyline">
                        <span></span><span></span><span></span><span></span><span></span><span></span><span></span>
                    </div>
                    <div class="site-art-street"></div>
                    <div class="site-art-card-footer"><span>DE STAD WACHT OP JOU</span><span class="site-art-coordinate">52° 09' N</span></div>
                </div>
                <div class="site-art-float site-art-float-top"><span class="site-art-float-icon">✳</span><span><b>Een wereld om te ontdekken</b><small>Er is altijd iets te beleven</small></span></div>
                <div class="site-art-float site-art-float-bottom"><span class="site-art-online-dot"></span><span><b>Jouw volgende hoofdstuk</b><small>Begint met één stap</small></span></div>
                <span class="site-art-spark site-art-spark-one">✳</span>
                <span class="site-art-spark site-art-spark-two">✦</span>
            </div>
            <a class="site-scroll-cue" href="#over-ons"><span></span> ONTDEK DE STAD</a>
        </section>

        <section id="over-ons" class="site-section site-about">
            <div class="site-section-marker"><span>01</span><i></i> DE STAD</div>
            <div class="site-about-grid">
                <div>
                    <p class="site-section-kicker">FIJN DAT JE ER BENT</p>
                    <h2>{{ $content->about_title }}</h2>
                </div>
                <div>
                    <p class="site-about-copy">{{ $content->about_text }}</p>
                    <a class="site-text-link" href="{{ auth()->check() ? route('dashboard') : route('register') }}">
                        {{ auth()->check() ? 'Naar jouw panel' : 'Ontdek jouw plek' }}
                        <x-heroicon-o-arrow-up-right class="h-4 w-4"/>
                    </a>
                </div>
            </div>
            <div class="site-feature-grid">
                <article class="site-feature-card">
                    <span class="site-feature-number">01 / SAMEN</span>
                    <span class="site-feature-icon"><x-heroicon-o-user-group class="h-6 w-6"/></span>
                    <h3>Een stad om in te leven</h3>
                    <p>Ontmoet andere spelers en geef samen kleur aan het dagelijks leven in de stad.</p>
                </article>
                <article class="site-feature-card">
                    <span class="site-feature-number">02 / GROEI</span>
                    <span class="site-feature-icon"><x-heroicon-o-arrow-trending-up class="h-6 w-6"/></span>
                    <h3>Jouw eigen pad</h3>
                    <p>Ontdek beroepen, volg opleidingen en bouw stap voor stap aan jouw toekomst.</p>
                </article>
                <article class="site-feature-card">
                    <span class="site-feature-number">03 / BELEEF</span>
                    <span class="site-feature-icon"><x-heroicon-o-sparkles class="h-6 w-6"/></span>
                    <h3>Verhalen die blijven</h3>
                    <p>Van kleine ontmoetingen tot grote plannen: jouw keuzes maken de roleplay.</p>
                </article>
            </div>
        </section>

        <section id="regels" class="site-rules-section">
            <div class="site-section site-rules-grid">
                <div class="site-rules-intro">
                    <div class="site-section-marker site-section-marker-light"><span>02</span><i></i> SAMEN SPELEN</div>
                    <p class="site-section-kicker">EEN FIJNE STAD MAKEN WE SAMEN</p>
                    <h2>{{ $content->rules_title }}</h2>
                    <p class="site-rules-description">Neem even de tijd om onze regels door te lezen. Zo weet iedereen waar die aan toe is.</p>
                </div>
                <div class="site-rules-card">
                    <div class="site-rules-card-header"><span>STADSAFSPRAKEN</span><span>{{ config('app.name') }}</span></div>
                    <div class="site-rules-copy">{{ $content->rules_text }}</div>
                    <div class="site-rules-card-footer"><x-heroicon-o-heart class="h-4 w-4"/> Respect voor elkaar staat altijd op één.</div>
                </div>
            </div>
        </section>

        <section class="site-final-cta">
            <span class="site-section-kicker">KLAAR VOOR HET VOLGENDE HOOFDSTUK?</span>
            <h2>De stad is er.<br><span>Jouw verhaal nog niet.</span></h2>
            @auth
                <a class="site-button" href="{{ route('dashboard') }}">Open het panel <x-heroicon-o-arrow-right class="h-4 w-4"/></a>
            @else
                <a class="site-button" href="{{ route('register') }}">Maak jouw account <x-heroicon-o-arrow-right class="h-4 w-4"/></a>
            @endauth
        </section>
    </main>

    <footer class="site-footer">
        <a href="{{ route('site.home') }}" class="site-brand site-brand-footer">
            <span class="site-brand-mark"><img src="{{ asset('images/brand/logo.svg') }}" alt=""></span>
            <span>{{ config('app.name') }}<small>MINECRAFT ROLEPLAY</small></span>
        </a>
        <span>Een wereld gebouwd op verhalen, gemaakt door spelers.</span>
        <a href="#regels">Lees de regels <x-heroicon-o-arrow-up-right class="h-3.5 w-3.5"/></a>
    </footer>
</body>
</html>
