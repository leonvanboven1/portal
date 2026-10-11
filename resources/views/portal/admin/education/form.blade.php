@extends('portal.layouts.app')

@php($editing = $course->exists)
@section('title', $editing ? 'Opleiding bewerken' : 'Opleiding aanmaken')
@section('header', $editing ? 'Opleiding bewerken' : 'Opleiding aanmaken')

@section('content')
    <div class="mx-auto max-w-4xl space-y-6">
        <a href="{{ route('portal.admin.education.index') }}" class="inline-flex items-center gap-2 text-sm font-medium text-gray-500 hover:text-gray-900 dark:text-gray-400 dark:hover:text-white">
            <x-heroicon-s-arrow-left class="h-4 w-4" />
            Terug naar opleidingen
        </a>

        <form action="{{ $editing ? route('portal.admin.education.update', $course) : route('portal.admin.education.store') }}" method="POST" class="space-y-5">
            @csrf
            @if ($editing) @method('PUT') @endif

            <section class="omt-education-form-section">
                <div class="omt-education-form-heading">
                    <span>01</span>
                    <div><h2>De opleiding</h2><p>Geef spelers duidelijkheid over inhoud, kosten en studieduur.</p></div>
                </div>
                <div class="grid grid-cols-1 gap-5 sm:grid-cols-2">
                    <div class="sm:col-span-2">
                        <label for="title">Naam</label>
                        <input id="title" name="title" value="{{ old('title', $course->title) }}" required maxlength="120" placeholder="Bijvoorbeeld: Starten als ondernemer">
                        @error('title')<p class="omt-form-error">{{ $message }}</p>@enderror
                    </div>
                    <div class="sm:col-span-2">
                        <label for="description">Beschrijving</label>
                        <textarea id="description" name="description" rows="4" maxlength="2000" placeholder="Wat leert de speler in deze opleiding?">{{ old('description', $course->description) }}</textarea>
                        @error('description')<p class="omt-form-error">{{ $message }}</p>@enderror
                    </div>
                    <div>
                        <label for="price">Kosten in euro</label>
                        <input id="price" name="price" type="number" min="0" max="99999999" step="0.01" value="{{ old('price', $course->price ?? '0.00') }}" required>
                        @error('price')<p class="omt-form-error">{{ $message }}</p>@enderror
                    </div>
                    <div>
                        <label for="duration_hours">Studieduur in uren</label>
                        <input id="duration_hours" name="duration_hours" type="number" min="1" max="8760" step="1" value="{{ old('duration_hours', $course->duration_hours ?? 24) }}" required>
                        @error('duration_hours')<p class="omt-form-error">{{ $message }}</p>@enderror
                    </div>
                </div>
            </section>

            <section class="omt-education-form-section">
                <div class="omt-education-form-heading">
                    <span>02</span>
                    <div><h2>De route</h2><p>Een opleiding wordt pas beschikbaar als alle gekozen voorgangers zijn afgerond.</p></div>
                </div>
                @if ($courses->isEmpty())
                    <p class="omt-form-help">Dit is de eerste opleiding; er zijn nog geen vereisten om te kiezen.</p>
                @else
                    <div class="omt-prerequisite-grid">
                        @foreach ($courses as $prerequisite)
                            <label class="omt-prerequisite-option">
                                <input type="checkbox" name="prerequisites[]" value="{{ $prerequisite->id }}"
                                       @checked(collect(old('prerequisites', $course->prerequisites->modelKeys()))->contains($prerequisite->id))>
                                <span><strong>{{ $prerequisite->title }}</strong><small>{{ $prerequisite->duration_hours }} uur · {{ $prerequisite->is_active ? 'Actief' : 'Inactief' }}</small></span>
                            </label>
                        @endforeach
                    </div>
                @endif
                @error('prerequisites')<p class="omt-form-error">{{ $message }}</p>@enderror
                @error('prerequisites.*')<p class="omt-form-error">{{ $message }}</p>@enderror
            </section>

            <section class="omt-education-form-section">
                <div class="omt-education-form-heading">
                    <span>03</span>
                    <div><h2>Toegang en publicatie</h2><p>Leg vast of deze opleiding een extra bevoegdheid oplevert.</p></div>
                </div>
                <div class="space-y-3">
                    <label class="omt-education-toggle">
                        <input type="checkbox" name="qualifies_for_company" value="1" @checked(old('qualifies_for_company', $course->qualifies_for_company))>
                        <span><strong>Geeft toegang tot bedrijfsaanvragen</strong><small>Als minstens één actieve opleiding deze optie heeft, moet de speler er één afronden voor een bedrijf kan worden aangevraagd.</small></span>
                    </label>
                    <label class="omt-education-toggle">
                        <input type="checkbox" name="is_active" value="1" @checked(old('is_active', $course->is_active ?? true))>
                        <span><strong>Opleiding publiceren</strong><small>Inactieve opleidingen worden niet aan spelers getoond.</small></span>
                    </label>
                </div>
            </section>

            <div class="flex flex-wrap justify-end gap-3">
                <a href="{{ route('portal.admin.education.index') }}" class="omt-education-secondary-action">Annuleren</a>
                <button type="submit" class="omt-education-primary-action"><x-heroicon-s-check class="h-4 w-4" />{{ $editing ? 'Wijzigingen opslaan' : 'Opleiding aanmaken' }}</button>
            </div>
        </form>
    </div>
@endsection