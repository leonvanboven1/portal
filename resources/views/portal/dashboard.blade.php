@extends('portal.layouts.app')

@section('title', 'Overzicht')
@section('header', 'Overzicht')

@section('content')
    @php
        $user = auth()->user();
        $stats = array_values(array_filter([
            $user->can('use-bank') ? ['label' => 'Saldo', 'value' => $user->formatted_balance_with_currency, 'href' => route('portal.bank-accounts.index'), 'icon' => 'banknotes', 'tone' => 'lime'] : null,
            $user->can('use-plots') ? ['label' => 'Plots', 'value' => count($user->getPlotsAttribute()), 'href' => route('portal.plots.index'), 'icon' => 'map', 'tone' => 'blue'] : null,
            ['label' => 'Baan', 'value' => $user->getPrefixAttribute(), 'href' => null, 'icon' => 'briefcase', 'tone' => 'copper'],
            ['label' => 'Level', 'value' => $user->level, 'href' => null, 'icon' => 'star', 'tone' => 'blue'],
            ['label' => 'Fitheid', 'value' => $user->getFitnessAttribute(), 'href' => null, 'icon' => 'bolt', 'tone' => 'lime'],
        ]));
        $shortcuts = array_filter([
            $user->can('use-bank') ? ['label' => 'Bankrekeningen', 'detail' => 'Saldo en transacties', 'href' => route('portal.bank-accounts.index'), 'icon' => 'credit-card'] : null,
            $user->can('use-plots') ? ['label' => 'Mijn plots', 'detail' => 'Bekijk je kavels', 'href' => route('portal.plots.index'), 'icon' => 'map'] : null,
            $user->can('use-broker') ? ['label' => 'Makelaar', 'detail' => 'Kopen en huren', 'href' => route('portal.plots.listings.index'), 'icon' => 'currency-dollar'] : null,
            $user->can('request-permits') ? ['label' => 'Vergunningen', 'detail' => 'Aanvragen en status', 'href' => route('portal.permits.index'), 'icon' => 'document-check'] : null,
            $user->can('use-companies') ? ['label' => 'Bedrijven', 'detail' => 'Je ondernemingen', 'href' => route('portal.companies.index'), 'icon' => 'building-office'] : null,
            $user->can('use-education') ? ['label' => 'Opleidingen', 'detail' => 'Jouw leerroute', 'href' => route('portal.education.index'), 'icon' => 'academic-cap'] : null,
        ]);
    @endphp

    <div class="omt-dashboard-intro">
        <div class="flex min-w-0 items-center gap-4">
            <div class="omt-dashboard-avatar-wrap">
                <img src="https://mc-heads.net/avatar/{{ $user->minecraft_username }}/64" alt=""
                     class="omt-pixel omt-dashboard-avatar h-16 w-16">
            </div>
            <div class="min-w-0">
                <p class="omt-eyebrow">JOUW STADSPORTAAL</p>
                <h2 class="omt-welcome-title">Hoi {{ $user->name }}</h2>
                <p class="omt-welcome-subtitle">Welkom terug, <span class="font-mono">{{ $user->minecraft_username }}</span>.</p>
            </div>
        </div>
        <div class="omt-date-stamp" aria-label="Vandaag">
            <span class="omt-date-day">{{ now()->format('d') }}</span>
            <span class="omt-date-month">{{ ucfirst(now()->translatedFormat('M Y')) }}</span>
        </div>
    </div>

    <dl class="omt-stat-grid">
        @foreach ($stats as $stat)
            @php($tag = $stat['href'] ? 'a' : 'div')
            <{{ $tag }} @if($stat['href']) href="{{ $stat['href'] }}" @endif
                class="omt-stat-card group {{ $stat['href'] ? 'omt-stat-card-link' : '' }}">
                <dt class="omt-stat-label">
                    <span class="omt-stat-icon omt-tone-{{ $stat['tone'] }}"><x-dynamic-component :component="'heroicon-o-'.$stat['icon']" class="h-5 w-5" /></span>
                    <span>{{ $stat['label'] }}</span>
                    @if ($stat['href'])
                        <x-heroicon-o-arrow-up-right class="omt-stat-arrow h-4 w-4" />
                    @endif
                </dt>
                <dd class="omt-stat-value">{{ $stat['value'] }}</dd>
            </{{ $tag }}>
        @endforeach
    </dl>

    <div class="omt-dashboard-columns">
        <section class="omt-dashboard-section" aria-labelledby="quick-links-title">
            <div class="omt-section-heading">
                <div>
                    <p class="omt-eyebrow">SNEL NAAR</p>
                    <h2 id="quick-links-title">Jouw portaal</h2>
                </div>
                <span class="omt-section-index">01 / {{ str_pad((string) count($shortcuts), 2, '0', STR_PAD_LEFT) }}</span>
            </div>
            @if (count($shortcuts))
                <div class="omt-shortcut-list">
                    @foreach ($shortcuts as $shortcut)
                        <a href="{{ $shortcut['href'] }}" class="omt-shortcut-link">
                            <span class="omt-shortcut-icon"><x-dynamic-component :component="'heroicon-o-'.$shortcut['icon']" class="h-5 w-5" /></span>
                            <span class="min-w-0 flex-1">
                                <span class="omt-shortcut-title">{{ $shortcut['label'] }}</span>
                                <span class="omt-shortcut-detail">{{ $shortcut['detail'] }}</span>
                            </span>
                            <x-heroicon-o-arrow-right class="omt-shortcut-arrow h-4 w-4" />
                        </a>
                    @endforeach
                </div>
            @else
                <div class="omt-empty-profile">
                    <x-heroicon-o-squares-2x2 class="h-6 w-6" />
                    <p>Er zijn geen onderdelen aan je account gekoppeld.</p>
                </div>
            @endif
        </section>

        <aside class="omt-player-card" aria-label="Spelersprofiel">
            <div class="omt-player-card-topline"><span>SPELERSPROFIEL</span><span class="omt-online-dot"></span></div>
            <div class="omt-player-card-identity">
                <img src="https://mc-heads.net/avatar/{{ $user->minecraft_username }}/64" alt="" class="omt-pixel h-12 w-12 rounded-md">
                <div class="min-w-0">
                    <p class="omt-player-name truncate">{{ $user->name }}</p>
                    <p class="omt-player-rank truncate">{{ $user->getPrefixAttribute() }}</p>
                </div>
            </div>
            <div class="omt-player-facts">
                <div><span>LEVEL</span><strong>{{ $user->level }}</strong></div>
                <div><span>FITHEID</span><strong>{{ $user->getFitnessAttribute() }}</strong></div>
            </div>
            <div class="omt-player-card-footer">
                <span>Gekoppeld account</span>
                <span class="omt-account-state"><span></span> Actief</span>
            </div>
        </aside>
    </div>

    @if ($educationProgress)
        <a href="{{ $educationProgress['href'] }}" class="omt-dashboard-education">
            <span class="omt-dashboard-education-icon"><x-heroicon-o-academic-cap class="h-5 w-5" /></span>
            <span class="min-w-0 flex-1">
                <span class="omt-dashboard-education-kicker">JOUW LEERROUTE</span>
                @if ($educationProgress['inProgress'])
                    <span class="omt-dashboard-education-title">{{ $educationProgress['inProgress']['course']->title }}</span>
                    <span class="omt-dashboard-education-detail">Klaar op {{ $educationProgress['inProgress']['enrollment']->completes_at->format('d-m-Y H:i') }}</span>
                @else
                    <span class="omt-dashboard-education-title">{{ $educationProgress['completed'] }} van {{ $educationProgress['total'] }} opleidingen afgerond</span>
                    <span class="omt-dashboard-education-detail">Bekijk je volgende stap op de routekaart.</span>
                @endif
            </span>
            <x-heroicon-o-arrow-right class="omt-shortcut-arrow h-4 w-4" />
        </a>
    @endif
@endsection
