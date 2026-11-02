@extends('portal.layouts.app')

@section('title', "VOG's aanvragen")
@section('header', "VOG's aanvragen")

@section('content')
    <div class="space-y-6">
        @include('portal.police.partials.tabs', ['active' => 'vogs'])

        <section class="rounded-xl border border-gray-200 bg-white p-5 shadow-sm dark:border-gray-700 dark:bg-gray-800 sm:p-6">
            <div class="flex items-start gap-3">
                <span class="grid h-10 w-10 shrink-0 place-items-center rounded-lg bg-indigo-50 text-indigo-600 dark:bg-indigo-500/10 dark:text-indigo-400">
                    <x-heroicon-o-shield-check class="h-5 w-5"/>
                </span>
                <div>
                    <h2 class="text-base font-semibold text-gray-900 dark:text-white">Vraag een verklaring aan</h2>
                    <p class="mt-1 text-sm leading-6 text-gray-500 dark:text-gray-400">
                        Zoek een Minecraft-speler. Overtredingen uit de afgelopen maand tellen mee: zonder recente overtredingen is de VOG positief.
                    </p>
                </div>
            </div>

            <form method="GET" action="{{ route('portal.police.vogs.index') }}" class="mt-5 flex flex-col gap-3 sm:flex-row">
                <label for="vog-player-search" class="sr-only">Zoek een Minecraft-speler</label>
                <div class="relative min-w-0 flex-1">
                    <x-heroicon-m-magnifying-glass class="pointer-events-none absolute left-3 top-1/2 h-5 w-5 -translate-y-1/2 text-gray-400"/>
                    <input id="vog-player-search" name="search" type="search" value="{{ $search }}"
                           placeholder="Minecraft-naam of accountnaam"
                           class="block w-full rounded-md border-0 py-2 pl-10 pr-3 text-sm text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-indigo-600 dark:bg-gray-700 dark:text-white dark:ring-gray-600 dark:focus:ring-indigo-500">
                </div>
                <button type="submit" class="inline-flex items-center justify-center gap-2 rounded-md bg-indigo-600 px-4 py-2 text-sm font-semibold text-white shadow-sm transition hover:bg-indigo-500 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 dark:focus:ring-offset-gray-800">
                    Zoeken
                </button>
            </form>
        </section>

        @if($selectedPlayer)
            <section class="rounded-xl border border-gray-200 bg-white shadow-sm dark:border-gray-700 dark:bg-gray-800">
                <div class="flex flex-col gap-4 border-b border-gray-200 p-5 dark:border-gray-700 sm:flex-row sm:items-center sm:justify-between">
                    <div class="flex min-w-0 items-center gap-3">
                        <img src="https://crafatar.com/avatars/{{ $selectedPlayer->minecraft_uuid }}?overlay=true&size=64"
                             alt="" class="h-12 w-12 rounded-lg bg-gray-100 dark:bg-gray-700">
                        <div class="min-w-0">
                            <h2 class="truncate text-base font-semibold text-gray-900 dark:text-white">{{ $selectedPlayer->minecraft_username }}</h2>
                            <p class="text-sm text-gray-500 dark:text-gray-400">{{ count($recentRecords) }} recente {{ count($recentRecords) === 1 ? 'overtreding' : 'overtredingen' }}</p>
                        </div>
                    </div>
                    <div class="flex flex-wrap items-center gap-3">
                        <span @class([
                            'rounded-full px-3 py-1 text-xs font-semibold',
                            'bg-green-50 text-green-700 dark:bg-green-500/10 dark:text-green-400' => count($recentRecords) === 0,
                            'bg-red-50 text-red-700 dark:bg-red-500/10 dark:text-red-400' => count($recentRecords) > 0,
                        ])>Verwachte uitslag: {{ count($recentRecords) === 0 ? 'positief' : 'negatief' }}</span>
                        @if($playerVogs === null)
                            <span class="text-xs text-gray-500 dark:text-gray-400">De plugin kan op dit moment geen VOG afgeven.</span>
                        @elseif($playerVogs['automatic'])
                            <form method="POST" action="{{ route('portal.police.players.vog', $selectedPlayer) }}">
                                @csrf
                                <button type="submit" class="inline-flex items-center gap-2 rounded-md bg-indigo-600 px-4 py-2 text-sm font-semibold text-white shadow-sm transition hover:bg-indigo-500 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 dark:focus:ring-offset-gray-800">
                                    <x-heroicon-s-document-check class="h-4 w-4"/>
                                    VOG aanvragen
                                </button>
                            </form>
                        @else
                            <span class="text-xs text-gray-500 dark:text-gray-400">VOG's worden op deze server handmatig afgegeven.</span>
                        @endif
                    </div>
                </div>

                @if(count($recentRecords))
                    <div class="divide-y divide-gray-100 dark:divide-gray-700">
                        @foreach($recentRecords as $record)
                            <div class="flex flex-col gap-1 px-5 py-3 sm:flex-row sm:items-center sm:justify-between">
                                <span class="text-sm text-gray-700 dark:text-gray-200">{{ $record['reason'] }}</span>
                                <time class="shrink-0 text-xs text-gray-500 dark:text-gray-400" datetime="{{ $vogs->recordDate($record)->toIso8601String() }}">
                                    {{ $vogs->recordDate($record)->format('d-m-Y H:i') }}
                                </time>
                            </div>
                        @endforeach
                    </div>
                @else
                    <p class="px-5 py-4 text-sm text-gray-500 dark:text-gray-400">Geen overtredingen in de afgelopen maand; de VOG wordt positief.</p>
                @endif

                @if($playerVogs && count($playerVogs['vogs']))
                    <div class="space-y-4 border-t border-gray-200 px-5 py-4 dark:border-gray-700">
                        <h3 class="text-sm font-semibold text-gray-900 dark:text-white">Eerder afgegeven VOG's</h3>
                        @foreach(array_slice($playerVogs['vogs'], 0, 5) as $vog)
                            @include('portal.criminal-records.partials.vog', ['vog' => $vog])
                        @endforeach
                    </div>
                @endif
            </section>
        @endif

        <section class="overflow-hidden rounded-xl border border-gray-200 bg-white shadow-sm dark:border-gray-700 dark:bg-gray-800">
            <div class="border-b border-gray-200 px-5 py-4 dark:border-gray-700">
                <h2 class="text-sm font-semibold text-gray-900 dark:text-white">Minecraft-spelers</h2>
                <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">Selecteer een speler om de recente overtredingen en VOG's te bekijken.</p>
            </div>
            <div class="divide-y divide-gray-100 dark:divide-gray-700">
                @forelse($players as $player)
                    <a href="{{ route('portal.police.vogs.index', array_filter(['search' => $search, 'player' => $player->id])) }}"
                       @class([
                           'flex items-center justify-between gap-4 px-5 py-3 transition hover:bg-gray-50 dark:hover:bg-gray-700/40',
                           'bg-indigo-50/70 dark:bg-indigo-500/5' => $selectedPlayer?->is($player),
                       ])>
                        <span class="min-w-0">
                            <span class="block truncate text-sm font-medium text-gray-900 dark:text-white">{{ $player->minecraft_username }}</span>
                            <span class="block truncate text-xs text-gray-500 dark:text-gray-400">{{ $player->name }}</span>
                        </span>
                        <span class="inline-flex shrink-0 items-center gap-1 text-sm font-medium text-indigo-600 dark:text-indigo-400">
                            Selecteren <x-heroicon-o-arrow-right class="h-4 w-4"/>
                        </span>
                    </a>
                @empty
                    <p class="px-5 py-8 text-center text-sm text-gray-500 dark:text-gray-400">
                        {{ $search !== '' ? 'Geen gekoppelde Minecraft-speler gevonden.' : 'Er zijn nog geen Minecraft-spelers gekoppeld.' }}
                    </p>
                @endforelse
            </div>
            @if($players->hasPages())
                <div class="border-t border-gray-200 px-5 py-3 dark:border-gray-700">{{ $players->links() }}</div>
            @endif
        </section>
    </div>
@endsection
