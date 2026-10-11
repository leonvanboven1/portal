@extends('portal.layouts.app')

@section('title', 'Opleidingen beheren')
@section('header', 'Opleidingen beheren')

@section('content')
    <div class="space-y-6">
        <section class="omt-education-admin-hero">
            <div>
                <p class="omt-eyebrow">BEHEER / ONTWIKKELING</p>
                <h2>Opleidingsroute samenstellen</h2>
                <p>Stel stappen, studieduur, kosten en toegang tot bedrijfsaanvragen in.</p>
            </div>
            <a href="{{ route('portal.admin.education.create') }}" class="omt-education-primary-action">
                <x-heroicon-s-plus class="h-4 w-4" />
                Opleiding toevoegen
            </a>
        </section>

        <section class="omt-education-admin-list" aria-labelledby="education-admin-list-title">
            <div class="omt-education-admin-list-heading">
                <div>
                    <p class="omt-eyebrow">OVERZICHT</p>
                    <h2 id="education-admin-list-title">Aangemaakte opleidingen</h2>
                </div>
                <span>{{ $courses->count() }} {{ $courses->count() === 1 ? 'opleiding' : 'opleidingen' }}</span>
            </div>

            @forelse ($courses as $course)
                <article class="omt-education-admin-row">
                    <span class="omt-education-row-index">{{ str_pad((string) $loop->iteration, 2, '0', STR_PAD_LEFT) }}</span>
                    <div class="min-w-0 flex-1">
                        <div class="flex flex-wrap items-center gap-2">
                            <h3>{{ $course->title }}</h3>
                            <span class="omt-education-status {{ $course->is_active ? 'is-active' : 'is-paused' }}">{{ $course->is_active ? 'Actief' : 'Inactief' }}</span>
                            @if ($course->qualifies_for_company)
                                <span class="omt-education-status is-qualification">Toegang tot bedrijf</span>
                            @endif
                        </div>
                        <p>{{ $course->description ?: 'Geen beschrijving toegevoegd.' }}</p>
                        <div class="omt-education-admin-meta">
                            <span>{{ $course->duration_hours }} uur</span>
                            <span>{{ $course->price > 0 ? '€ '.number_format($course->price, 2, ',', '.') : 'Gratis' }}</span>
                            <span>{{ $course->enrollments_count }} inschrijvingen</span>
                            <span>Vereist: {{ $course->prerequisites->isNotEmpty() ? $course->prerequisites->pluck('title')->join(', ') : 'Geen' }}</span>
                        </div>
                    </div>
                    <div class="omt-education-admin-actions">
                        <a href="{{ route('portal.admin.education.edit', $course) }}" aria-label="{{ $course->title }} bewerken" title="Bewerken">
                            <x-heroicon-o-pencil-square class="h-5 w-5" />
                        </a>
                        @if ($course->enrollments_count === 0 && $course->requiredBy->isEmpty())
                            <form action="{{ route('portal.admin.education.destroy', $course) }}" method="POST" onsubmit="return confirm('Weet je zeker dat je deze opleiding wilt verwijderen?')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" aria-label="{{ $course->title }} verwijderen" title="Verwijderen">
                                    <x-heroicon-o-trash class="h-5 w-5" />
                                </button>
                            </form>
                        @endif
                    </div>
                </article>
            @empty
                <div class="omt-education-empty">
                    <span class="omt-education-empty-icon"><x-heroicon-o-academic-cap class="h-7 w-7" /></span>
                    <h3>De route is nog leeg</h3>
                    <p>Maak de eerste opleiding aan. Daarna verschijnt die direct op de spelersroutekaart.</p>
                    <a href="{{ route('portal.admin.education.create') }}">Eerste opleiding aanmaken</a>
                </div>
            @endforelse
        </section>
    </div>
@endsection