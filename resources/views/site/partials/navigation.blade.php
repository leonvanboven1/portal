<nav class="site-nav" aria-label="Hoofdnavigatie">
    <a href="{{ route('site.home') }}" @class(['is-current' => request()->routeIs('site.home')]) @if(request()->routeIs('site.home')) aria-current="page" @endif>
        {{ request()->routeIs('site.home') ? 'Hoofdpagina · hier' : 'Hoofdpagina' }}
    </a>
    <a href="{{ route('site.rules') }}" @class(['is-current' => request()->routeIs('site.rules')]) @if(request()->routeIs('site.rules')) aria-current="page" @endif>
        {{ request()->routeIs('site.rules') ? 'Regels (je bent hier)' : 'Onze regels' }}
    </a>
    <a href="{{ route('site.home') }}#starten">Beginnen</a>
</nav>
