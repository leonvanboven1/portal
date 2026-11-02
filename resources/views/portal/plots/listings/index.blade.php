@extends('portal.layouts.app')

@section('title', 'Makelaar')
@section('header', 'Makelaar')

@php
    $tabs = [
        'alles' => ['label' => 'Alles', 'count' => $listingCount + $rentalOfferCount],
        'koop' => ['label' => 'Te koop', 'count' => $listingCount],
        'huur' => ['label' => 'Te huur', 'count' => $rentalOfferCount],
    ];
@endphp

@section('content')
    <div class="space-y-6">
        <div class="relative flex flex-wrap items-center justify-center gap-3 border-b border-gray-200 dark:border-gray-700">
            <nav class="-mb-px flex w-full justify-center gap-6" aria-label="Kopen of huren">
                @foreach($tabs as $key => $item)
                    <a href="{{ route('portal.plots.listings.index', array_filter(['soort' => $key === 'alles' ? 'alles' : ($key === 'huur' ? 'huur' : 'koop'), 'search' => $search])) }}"
                       @if($tab === $key) aria-current="page" @endif
                       @class([
                           'inline-flex items-center gap-2 border-b-2 px-1 pb-3 text-sm font-medium transition-colors',
                           'border-indigo-500 text-indigo-600 dark:border-indigo-400 dark:text-indigo-400' => $tab === $key,
                           'border-transparent text-gray-500 hover:border-gray-300 hover:text-gray-700 dark:text-gray-400 dark:hover:text-gray-300' => $tab !== $key,
                       ])>
                        {{ $item['label'] }}
                        <span @class([
                            'rounded-full px-2 py-0.5 text-xs font-medium',
                            'bg-indigo-100 text-indigo-600 dark:bg-indigo-500/10 dark:text-indigo-400' => $tab === $key,
                            'bg-gray-100 text-gray-600 dark:bg-gray-800 dark:text-gray-400' => $tab !== $key,
                        ])>{{ $item['count'] }}</span>
                    </a>
                @endforeach
            </nav>
            <a href="{{ route('portal.plots.rentals.index') }}" class="mb-3 inline-flex items-center gap-1.5 text-sm font-medium text-indigo-600 hover:text-indigo-500 dark:text-indigo-400 sm:absolute sm:right-0">
                <x-heroicon-s-key class="w-4 h-4"/>
                Mijn huur
            </a>
        </div>

        <form method="GET" action="{{ route('portal.plots.listings.index') }}" class="flex flex-col gap-3 rounded-lg border border-gray-200 bg-white p-4 shadow-sm dark:border-gray-700 dark:bg-gray-800 sm:flex-row">
            <input type="hidden" name="soort" value="{{ $tab }}">
            <label for="plot-search" class="sr-only">Zoek op plotnaam</label>
            <div class="relative min-w-0 flex-1">
                <x-heroicon-m-magnifying-glass class="pointer-events-none absolute left-3 top-1/2 h-5 w-5 -translate-y-1/2 text-gray-400"/>
                <input id="plot-search" name="search" type="search" value="{{ $search }}"
                       placeholder="Zoek een plot op naam..."
                       class="block w-full rounded-md border-0 py-2 pl-10 pr-3 text-sm text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-indigo-600 dark:bg-gray-700 dark:text-white dark:ring-gray-600 dark:focus:ring-indigo-500">
            </div>
            <button type="submit" class="inline-flex items-center justify-center gap-2 rounded-md bg-indigo-600 px-4 py-2 text-sm font-semibold text-white shadow-sm transition hover:bg-indigo-500 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 dark:focus:ring-offset-gray-800">
                Zoeken
            </button>
            @if($search !== '')
                <a href="{{ route('portal.plots.listings.index', ['soort' => $tab]) }}" class="inline-flex items-center justify-center rounded-md px-3 py-2 text-sm font-medium text-gray-600 hover:bg-gray-100 dark:text-gray-300 dark:hover:bg-gray-700">
                    Wissen
                </a>
            @endif
        </form>

        @if($tab === 'alles')
            <div class="grid grid-cols-1 gap-5 sm:grid-cols-2 xl:grid-cols-3">
                @forelse($marketplaceListings as $entry)
                    @php($listing = $entry->listing)
                    <article class="overflow-hidden rounded-xl border border-gray-200 bg-white shadow-sm transition hover:-translate-y-0.5 hover:shadow-md dark:border-gray-700 dark:bg-gray-800">
                        @if($listing->image_path)
                            <img src="{{ Storage::url($listing->image_path) }}" alt="Afbeelding van {{ $listing->plot_name }}" class="h-44 w-full object-cover">
                        @else
                            <div class="flex h-44 items-center justify-center bg-gray-100 dark:bg-gray-700">
                                @if($entry->listing_type === 'koop')
                                    <x-heroicon-o-home class="h-12 w-12 text-gray-400 dark:text-gray-500"/>
                                @else
                                    <x-heroicon-o-key class="h-12 w-12 text-gray-400 dark:text-gray-500"/>
                                @endif
                            </div>
                        @endif
                        <div class="p-5">
                            <div class="flex flex-wrap items-start justify-between gap-3">
                                <div class="min-w-0">
                                    <span @class([
                                        'inline-flex rounded-full px-2.5 py-1 text-xs font-semibold',
                                        'bg-green-50 text-green-700 dark:bg-green-500/10 dark:text-green-400' => $entry->listing_type === 'koop',
                                        'bg-blue-50 text-blue-700 dark:bg-blue-500/10 dark:text-blue-400' => $entry->listing_type === 'huur',
                                    ])>{{ $entry->listing_type === 'koop' ? 'Te koop' : 'Te huur' }}</span>
                                    <h2 class="mt-2 break-words text-lg font-semibold text-gray-900 dark:text-white">{{ $listing->plot_name }}</h2>
                                    <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">
                                        {{ $entry->listing_type === 'koop' ? 'Verkoper: '.$listing->seller_name : 'Verhuurder: '.$listing->landlord_name }}
                                    </p>
                                </div>
                                <span class="shrink-0 rounded-md bg-gray-100 px-2.5 py-1.5 text-sm font-semibold text-gray-800 dark:bg-gray-700 dark:text-gray-100">
                                    {{ $listing->formatted_price }}{{ $entry->listing_type === 'huur' ? ' '.$listing->period_text : '' }}
                                </span>
                            </div>
                            @if(filled($listing->description))
                                <p class="mt-4 text-sm leading-6 text-gray-600 dark:text-gray-300">{{ Str::limit($listing->description, 120) }}</p>
                            @endif
                            <div class="mt-5 flex items-center justify-between gap-3 border-t border-gray-100 pt-4 dark:border-gray-700">
                                <span class="text-xs text-gray-500 dark:text-gray-400">
                                    {{ $listing->dimensions['width'] }}×{{ $listing->dimensions['length'] }} · {{ $listing->area }} m²
                                </span>
                                @if($entry->listing_type === 'koop')
                                    @if($listing->instant_buy)
                                        <a href="{{ route('portal.plots.listings.buy.show', $listing) }}" class="inline-flex items-center gap-1.5 rounded-md bg-indigo-600 px-3 py-2 text-sm font-semibold text-white hover:bg-indigo-500">
                                            <x-heroicon-s-shopping-cart class="h-4 w-4"/> Kopen
                                        </a>
                                    @else
                                        <span class="text-sm font-medium text-gray-500 dark:text-gray-400">Contact verkoper</span>
                                    @endif
                                @elseif($listing->landlord_id === auth()->id() && !$listing->is_server_listing)
                                    <span class="text-sm font-medium text-gray-500 dark:text-gray-400">Jouw plot</span>
                                @else
                                    <a href="{{ route('portal.plots.rentals.rent.show', $listing) }}" class="inline-flex items-center gap-1.5 rounded-md bg-indigo-600 px-3 py-2 text-sm font-semibold text-white hover:bg-indigo-500">
                                        <x-heroicon-s-key class="h-4 w-4"/> Huren
                                    </a>
                                @endif
                            </div>
                        </div>
                    </article>
                @empty
                    <div class="col-span-full rounded-xl border border-dashed border-gray-300 bg-white px-6 py-12 text-center dark:border-gray-700 dark:bg-gray-800">
                        <x-heroicon-o-magnifying-glass class="mx-auto h-10 w-10 text-gray-400"/>
                        <h2 class="mt-3 text-sm font-semibold text-gray-900 dark:text-white">Geen plots gevonden</h2>
                        <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">Pas je zoekopdracht aan of bekijk een andere categorie.</p>
                    </div>
                @endforelse
            </div>
            <div>{{ $marketplaceListings->links() }}</div>
        @elseif($tab === 'koop')
        <!-- Listings Grid -->
        <div class="grid grid-cols-1 gap-6 sm:grid-cols-2 lg:grid-cols-3">
            @forelse($listings as $listing)
                <div class="bg-white dark:bg-gray-800 shadow-sm rounded-lg overflow-hidden">
                    @if($listing->image_path)
                        <img src="{{ Storage::url($listing->image_path) }}"
                             alt="Afbeelding van {{ $listing->plot_name }}"
                             class="w-full h-48 object-cover">
                    @else
                        <div class="w-full h-48 bg-gray-100 dark:bg-gray-700 flex items-center justify-center">
                            <x-heroicon-o-photo class="w-12 h-12 text-gray-400 dark:text-gray-500"/>
                        </div>
                    @endif

                    <div class="p-6">
                        <div class="flex flex-wrap justify-between items-start gap-2">
                            <div class="min-w-0">
                                <h3 class="text-lg font-semibold text-gray-900 dark:text-white [overflow-wrap:anywhere]">
                                    {{ $listing->plot_name }}
                                </h3>
                                <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">
                                    Verkoper: {{ $listing->seller_name }}
                                </p>
                            </div>
                            <span class="inline-flex shrink-0 items-center whitespace-nowrap rounded-md px-2 py-1 text-xs font-medium ring-1 ring-inset bg-green-50 text-green-700 ring-green-600/20 dark:bg-green-500/10 dark:text-green-400 dark:ring-green-500/20">
                                {{ $listing->formatted_price }}
                            </span>
                        </div>

                        @if (filled($listing->description))
                            <p class="mt-4 text-sm text-gray-600 dark:text-gray-300">
                                {{ Str::limit($listing->description, 150) }}
                            </p>
                        @endif

                        <div class="mt-4 flex items-center justify-between text-sm">
                            <div class="text-gray-500 dark:text-gray-400">
                                <div class="flex flex-col gap-1">
                                    <span class="flex items-center gap-1">
                                        <x-heroicon-s-map-pin class="w-4 h-4"/>
                                        Min: {{ $listing->min_x }}, {{ $listing->min_y }}, {{ $listing->min_z }}
                                    </span>
                                    <span class="flex items-center gap-1">
                                        <x-heroicon-s-map-pin class="w-4 h-4"/>
                                        Max: {{ $listing->max_x }}, {{ $listing->max_y }}, {{ $listing->max_z }}
                                    </span>
                                </div>
                            </div>
                            <div class="text-gray-500 dark:text-gray-400">
                                {{ $listing->dimensions['width'] }}x{{ $listing->dimensions['length'] }}x{{ $listing->dimensions['height'] }}
                                ({{ $listing->area }} m²)
                            </div>
                        </div>

                        <div class="mt-6 flex justify-end">
                            @if($listing->instant_buy)
                                <a href="{{ route('portal.plots.listings.buy.show', $listing) }}"
                                   class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md shadow-sm text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 dark:focus:ring-offset-gray-800">
                                    <x-heroicon-s-shopping-cart class="w-4 h-4 mr-2"/>
                                    Plot kopen voor {{ $listing->formatted_price }}
                                </a>
                            @else
                                <span class="inline-flex items-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-md text-gray-700 bg-white dark:bg-gray-700 dark:border-gray-600 dark:text-gray-200">
                                    <x-heroicon-s-chat-bubble-left-right class="w-4 h-4 mr-2"/>
                                    Contact verkoper
                                </span>
                            @endif
                        </div>
                    </div>
                </div>
            @empty
                <div class="col-span-full">
                    <div class="text-center bg-white dark:bg-gray-800 rounded-lg shadow-sm px-6 py-8">
                        <x-heroicon-o-home class="mx-auto h-12 w-12 text-gray-400 dark:text-gray-500"/>
                        <h3 class="mt-2 text-sm font-medium text-gray-900 dark:text-white">Geen plots te koop</h3>
                        <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">
                            Er staan momenteel geen plots te koop.
                        </p>
                    </div>
                </div>
            @endforelse
        </div>
        <div>{{ $listings->links() }}</div>
        @else
        <!-- Rental Offers Grid -->
        <div class="grid grid-cols-1 gap-6 sm:grid-cols-2 lg:grid-cols-3">
            @forelse($rentalOffers as $offer)
                <div class="bg-white dark:bg-gray-800 shadow-sm rounded-lg overflow-hidden">
                    @if($offer->image_path)
                        <img src="{{ Storage::url($offer->image_path) }}"
                             alt="Afbeelding van {{ $offer->plot_name }}"
                             class="w-full h-48 object-cover">
                    @else
                        <div class="w-full h-48 bg-gray-100 dark:bg-gray-700 flex items-center justify-center">
                            <x-heroicon-o-key class="w-12 h-12 text-gray-400 dark:text-gray-500"/>
                        </div>
                    @endif

                    <div class="p-6">
                        <div class="flex flex-wrap justify-between items-start gap-2">
                            <div class="min-w-0">
                                <h3 class="text-lg font-semibold text-gray-900 dark:text-white [overflow-wrap:anywhere]">{{ $offer->plot_name }}</h3>
                                <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">Verhuurder: {{ $offer->landlord_name }}</p>
                            </div>
                            <span class="inline-flex shrink-0 items-center rounded-md px-2 py-1 text-xs font-medium ring-1 ring-inset bg-blue-50 text-blue-700 ring-blue-600/20 dark:bg-blue-500/10 dark:text-blue-400 dark:ring-blue-500/20 whitespace-nowrap">
                                {{ $offer->formatted_price }} {{ $offer->period_text }}
                            </span>
                        </div>

                        @if (filled($offer->description))
                            <p class="mt-4 text-sm text-gray-600 dark:text-gray-300">{{ Str::limit($offer->description, 150) }}</p>
                        @endif

                        <div class="mt-4 flex items-center justify-between text-sm text-gray-500 dark:text-gray-400">
                            <span class="flex items-center gap-1">
                                <x-heroicon-s-map-pin class="w-4 h-4"/>
                                {{ $offer->min_x }}, {{ $offer->min_y }}, {{ $offer->min_z }}
                            </span>
                            <span>{{ $offer->dimensions['width'] }}x{{ $offer->dimensions['length'] }} ({{ $offer->area }} m²)</span>
                        </div>

                        <div class="mt-6 flex justify-end">
                            @if($offer->landlord_id === auth()->id() && !$offer->is_server_listing)
                                <span class="text-sm text-gray-500 dark:text-gray-400">Jouw plot</span>
                            @else
                                <a href="{{ route('portal.plots.rentals.rent.show', $offer) }}"
                                   class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md shadow-sm text-white bg-indigo-600 hover:bg-indigo-700">
                                    <x-heroicon-s-key class="w-4 h-4 mr-2"/>
                                    Huren
                                </a>
                            @endif
                        </div>
                    </div>
                </div>
            @empty
                <div class="col-span-full">
                    <div class="text-center bg-white dark:bg-gray-800 rounded-lg shadow-sm px-6 py-8">
                        <x-heroicon-o-key class="mx-auto h-12 w-12 text-gray-400 dark:text-gray-500"/>
                        <h3 class="mt-2 text-sm font-medium text-gray-900 dark:text-white">Geen plots te huur</h3>
                        <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">Er staan momenteel geen plots te huur.</p>
                    </div>
                </div>
            @endforelse
        </div>
        <div>{{ $rentalOffers->links() }}</div>
        @endif
    </div>
@endsection
