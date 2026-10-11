<div class="omt-user-dock sticky bottom-0 -mx-4 mt-6 px-4 py-4">
    <div class="flex items-center gap-3">
        <img class="omt-pixel omt-user-avatar h-9 w-9 rounded-md"
             src="https://mc-heads.net/avatar/{{ auth()->user()->minecraft_username }}"
             alt="">
        <div class="min-w-0 flex-1">
            <p class="omt-user-name truncate text-sm font-semibold">{{ auth()->user()->name }}</p>
            <p class="omt-user-handle truncate font-mono text-xs">{{ auth()->user()->minecraft_username }}</p>
        </div>
        <button @click="toggleDarkMode" type="button" class="omt-dock-button rounded-md p-2"
                :aria-label="darkMode ? 'Licht thema' : 'Donker thema'">
            <x-heroicon-o-moon x-show="!darkMode" class="h-5 w-5" />
            <x-heroicon-o-sun x-show="darkMode" x-cloak class="h-5 w-5" />
        </button>
        <form method="POST" action="{{ route('logout') }}">
            @csrf
            <button type="submit" class="omt-dock-button rounded-md p-2" aria-label="Uitloggen" title="Uitloggen">
                <x-heroicon-o-arrow-right-on-rectangle class="h-5 w-5" />
            </button>
        </form>
    </div>
</div>
