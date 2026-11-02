<nav class="flex justify-center border-b border-gray-200 dark:border-gray-700" aria-label="Politie">
    <div class="-mb-px flex gap-6">
        <a href="{{ route('portal.police.players.index') }}"
           @if($active === 'players') aria-current="page" @endif
           @class([
               'inline-flex items-center gap-2 border-b-2 px-1 pb-3 text-sm font-medium transition-colors',
               'border-indigo-500 text-indigo-600 dark:border-indigo-400 dark:text-indigo-400' => $active === 'players',
               'border-transparent text-gray-500 hover:border-gray-300 hover:text-gray-700 dark:text-gray-400 dark:hover:text-gray-300' => $active !== 'players',
           ])>
            <x-heroicon-o-users class="h-4 w-4"/>
            Spelersdatabase
        </a>
        <a href="{{ route('portal.police.vogs.index') }}"
           @if($active === 'vogs') aria-current="page" @endif
           @class([
               'inline-flex items-center gap-2 border-b-2 px-1 pb-3 text-sm font-medium transition-colors',
               'border-indigo-500 text-indigo-600 dark:border-indigo-400 dark:text-indigo-400' => $active === 'vogs',
               'border-transparent text-gray-500 hover:border-gray-300 hover:text-gray-700 dark:text-gray-400 dark:hover:text-gray-300' => $active !== 'vogs',
           ])>
            <x-heroicon-o-document-check class="h-4 w-4"/>
            VOG aanvragen
        </a>
    </div>
</nav>
