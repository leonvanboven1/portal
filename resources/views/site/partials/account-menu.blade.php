<div class="site-account">
    @auth
        <div class="site-account-menu" x-data="{ open: false }" @keydown.escape.window="open = false">
            <button type="button" class="site-account-trigger" @click="open = !open" :aria-expanded="open" aria-haspopup="true">
                <img src="https://mc-heads.net/avatar/{{ rawurlencode(auth()->user()->minecraft_username ?: auth()->user()->name) }}/40" alt="" class="site-account-avatar">
                <span class="site-account-name">{{ auth()->user()->minecraft_username ?: auth()->user()->name }}</span>
                <x-heroicon-o-chevron-down class="h-4 w-4"/>
            </button>
            <div class="site-dropdown" x-show="open" x-cloak x-transition.origin.top.right @click.outside="open = false">
                <div class="site-dropdown-identity">
                    <strong>{{ auth()->user()->name }}</strong>
                    <span>{{ auth()->user()->minecraft_username }}</span>
                </div>
                <a href="{{ route('account.edit') }}">
                    <x-heroicon-o-user class="h-4 w-4"/>
                    Mijn gegevens
                </a>
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
