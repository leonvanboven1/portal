<!DOCTYPE html>
<html lang="nl">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="theme-color" content="#f5f4ef">
    <meta name="description" content="{{ $content->hero_text }}">
    <title>Vunex — {{ $content->hero_title }}</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="site-page">
    <header class="site-header">
        <a href="{{ route('site.home') }}" class="site-brand" aria-label="Vunex — home">
            <span class="site-brand-mark"><img src="{{ $branding->logoUrl() }}" alt="" @class(['omt-pixel' => $branding->isLogoPixelated()])></span>
            <span>Vunex<small>MINECRAFT ROLEPLAY</small></span>
        </a>

        @include('site.partials.navigation')

        @include('site.partials.account-menu')
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
                <div class="site-server-address"><span>SERVER IP</span><code>play.vunex.nl</code></div>
            </div>
            <div class="site-hero-art">
                <div class="site-art-sun"></div>
                <div class="site-art-orbit site-art-orbit-one"></div>
                <div class="site-art-orbit site-art-orbit-two"></div>
                <div class="site-art-card site-art-card-main">
                    @if($content->hero_image_path)
                        <img src="{{ \Illuminate\Support\Facades\Storage::disk('public')->url($content->hero_image_path) }}" alt="" class="site-art-photo">
                        <div class="site-art-photo-shade"></div>
                    @else
                        <span class="site-art-card-kicker">VUNEX · JOUW NIEUWE WERELD</span>
                        <div class="site-art-skyline">
                            <span></span><span></span><span></span><span></span><span></span><span></span><span></span>
                        </div>
                        <div class="site-art-street"></div>
                    @endif
                    <div class="site-art-card-footer"><span>DE STAD WACHT OP JOU</span><span class="site-art-coordinate">VUNEX</span></div>
                </div>
                <div class="site-art-float site-art-float-top"><span class="site-art-float-icon">✳</span><span><b>Een wereld om te ontdekken</b><small>Er is altijd iets te beleven</small></span></div>
                <div class="site-art-float site-art-float-bottom">
                    <span class="{{ $serverStatus['online'] ? 'site-art-online-dot' : 'site-art-offline-dot' }}"></span>
                    <span>
                        <b>
                            @if($serverStatus['online'])
                                {{ $serverStatus['players'] }} speler{{ $serverStatus['players'] === 1 ? '' : 's' }} online
                            @elseif($serverStatus['available'])
                                Server is offline
                            @else
                                Status tijdelijk niet beschikbaar
                            @endif
                        </b>
                        <small>{{ $serverStatus['online'] && $serverStatus['max_players'] ? 'van maximaal '.$serverStatus['max_players'].' spelers' : 'play.vunex.nl' }}</small>
                    </span>
                </div>
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
            <div class="site-unique-heading">
                <div><p class="site-section-kicker">MEER DAN ALLEEN MINECRAFT</p><h2>Wat maakt Vunex uniek?</h2></div>
                <p>Een levende roleplaywereld met systemen die jouw keuzes echt laten meetellen.</p>
            </div>
            @if($content->unique_image_path)
                <img src="{{ \Illuminate\Support\Facades\Storage::disk('public')->url($content->unique_image_path) }}" alt="De wereld van Vunex" class="site-unique-photo">
            @endif
            <div class="site-feature-grid">
                <article class="site-feature-card">
                    <span class="site-feature-number">01 / ECONOMIE</span>
                    <span class="site-feature-icon"><x-heroicon-o-user-group class="h-6 w-6"/></span>
                    <h3>Een levende economie</h3>
                    <p>Verdien met je werk, beheer je geld en bouw stap voor stap aan jouw toekomst.</p>
                </article>
                <article class="site-feature-card">
                    <span class="site-feature-number">02 / ONTWIKKELING</span>
                    <span class="site-feature-icon"><x-heroicon-o-arrow-trending-up class="h-6 w-6"/></span>
                    <h3>Opleidingen en beroepen</h3>
                    <p>Leer nieuwe vaardigheden, volg jouw route en ontgrendel nieuwe mogelijkheden.</p>
                </article>
                <article class="site-feature-card">
                    <span class="site-feature-number">03 / JOUW STAD</span>
                    <span class="site-feature-icon"><x-heroicon-o-sparkles class="h-6 w-6"/></span>
                    <h3>Jouw plek in de stad</h3>
                    <p>Koop of huur een plot en maak van jouw eigen plek een thuis of onderneming.</p>
                </article>
                <article class="site-feature-card">
                    <span class="site-feature-number">04 / RECHTVAARDIG</span>
                    <span class="site-feature-icon"><x-heroicon-o-shield-check class="h-6 w-6"/></span>
                    <h3>Een stad met duidelijke afspraken</h3>
                    <p>Politie, strafbladen en VOG-aanvragen helpen de stad eerlijk en veilig te houden.</p>
                </article>
            </div>
        </section>

        <section id="starten" class="site-steps-section">
            <div class="site-section site-steps-layout">
                <div class="site-steps-copy">
                    <div class="site-section-marker"><span>02</span><i></i> JOUW AVONTUUR</div>
                    <p class="site-section-kicker">KLAAR OM TE BEGINNEN?</p>
                    <h2>Hoe begin je jouw avontuur?</h2>
                    <p class="site-steps-description">Je staat er niet alleen voor. Met deze vier stappen ben je zo onderweg in Vunex.</p>
                    <ol class="site-steps-list">
                        <li><span>01</span><div><h3>Start Minecraft</h3><p>Open Minecraft en kies de Java-editie.</p></div></li>
                        <li><span>02</span><div><h3>Voeg de server toe</h3><p>Gebruik het adres <strong>play.vunex.nl</strong>.</p></div></li>
                        <li><span>03</span><div><h3>Kies je beroep</h3><p>Ontdek de mogelijkheden en vind wat bij jouw personage past.</p></div></li>
                        <li><span>04</span><div><h3>Bouw je welvaart</h3><p>Werk, ontmoet spelers en bouw aan jouw eigen toekomst.</p></div></li>
                    </ol>
                    <p class="site-steps-footnote">Maak daarna een account op deze website om je gegevens en voortgang in het Vunex-panel te bekijken.</p>
                </div>
                <div class="site-steps-art">
                    @if($content->steps_image_path)
                        <img src="{{ \Illuminate\Support\Facades\Storage::disk('public')->url($content->steps_image_path) }}" alt="Bouw jouw avontuur op Vunex" class="site-steps-photo">
                    @else
                        <div class="site-steps-placeholder"><x-heroicon-o-map class="h-12 w-12"/><span>JOUW VERHAAL BEGINT HIER</span></div>
                    @endif
                    <div class="site-steps-ip"><span>SERVERADRES</span><strong>play.vunex.nl</strong></div>
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

    @include('site.partials.footer')
</body>
</html>
