@extends('portal.layouts.app')

@section('title', 'Opleidingen')
@section('header', 'Opleidingen')

@section('content')
    @php
        $completedCount = $roadmap->where('state', 'completed')->count();
        $activeCount = $roadmap->count();
        $percent = $activeCount ? (int) round($completedCount / $activeCount * 100) : 0;
    @endphp
    <div class="space-y-6">
        <section class="omt-learning-hero">
            <div class="omt-learning-hero-copy">
                <p class="omt-eyebrow">JOUW VOLGENDE STAP</p>
                <h2>Van ambitie naar beroep.</h2>
                <p>Volg de route in jouw tempo. Elke afgeronde opleiding brengt nieuwe mogelijkheden.</p>
            </div>
            <div class="omt-learning-progress" aria-label="{{ $completedCount }} van {{ $activeCount }} opleidingen afgerond">
                <div class="omt-progress-ring" style="--progress: {{ $percent }}%"><span>{{ $percent }}<small>%</small></span></div>
                <p>{{ $completedCount }} / {{ $activeCount }} afgerond</p>
            </div>
        </section>

        @if ($companyAccessIssue)
            <div class="omt-learning-notice">
                <x-heroicon-o-building-office class="h-5 w-5" />
                <p>
                    @switch($companyAccessIssue)
                        @case('companies_disabled')
                            De functie Bedrijven staat uit. Vraag een beheerder om deze functie in te schakelen.
                            @break
                        @case('company_permission_missing')
                            Je rol heeft geen toegang tot Bedrijven. Vraag een beheerder om de permissie Bedrijven aan je rol toe te voegen.
                            @break
                        @case('no_qualifying_course')
                            Er is nog geen opleiding ingesteld die toegang geeft tot bedrijfsaanvragen. Een beheerder kan dit aanvinken bij Opleidingen beheren.
                            @break
                        @case('course_unavailable')
                            De opleiding die toegang geeft tot bedrijven is niet beschikbaar. Vraag een beheerder om die opleiding weer te activeren.
                            @break
                        @default
                            Rond eerst een opleiding af die toegang geeft tot bedrijfsaanvragen.
                    @endswitch
                    <a class="font-semibold underline" href="{{ route('portal.education.index') }}">Bekijk je leerroute</a>.
                </p>
            </div>
        @endif

        @if ($roadmap->isEmpty())
            <section class="omt-learning-empty">
                <x-heroicon-o-map class="h-8 w-8" />
                <h2>Je route wordt voorbereid</h2>
                <p>Er zijn nog geen opleidingen gepubliceerd. Kom later terug om je route te bekijken.</p>
            </section>
        @else
            <section class="omt-learning-map" aria-label="Opleidingsroute">
                <div class="omt-learning-map-heading">
                    <div><p class="omt-eyebrow">OPLEIDINGSROUTE</p><h2>Jouw leerpad</h2></div>
                    <span>{{ $roadmap->count() }} stappen</span>
                </div>

                <div class="omt-learning-steps">
                    @foreach ($roadmap as $item)
                        @php
                            $course = $item['course'];
                            $enrollment = $item['enrollment'];
                            $durationSeconds = $enrollment ? max(1, $enrollment->started_at->diffInSeconds($enrollment->completes_at)) : 1;
                            $elapsedSeconds = $enrollment ? max(0, $enrollment->started_at->diffInSeconds(now())) : 0;
                            $progressPercent = $item['state'] === 'completed' ? 100 : min(99, (int) floor($elapsedSeconds / $durationSeconds * 100));
                        @endphp
                        <article class="omt-learning-step state-{{ $item['state'] }}">
                            <div class="omt-learning-step-marker">
                                @if ($item['state'] === 'completed')
                                    <x-heroicon-s-check class="h-5 w-5" />
                                @elseif ($item['state'] === 'locked')
                                    <x-heroicon-s-lock-closed class="h-5 w-5" />
                                @else
                                    <span>{{ str_pad((string) $loop->iteration, 2, '0', STR_PAD_LEFT) }}</span>
                                @endif
                            </div>
                            <div class="omt-learning-step-card">
                                <div class="omt-learning-step-topline">
                                    <span>STAP {{ str_pad((string) $loop->iteration, 2, '0', STR_PAD_LEFT) }}</span>
                                    <span class="omt-learning-state">{{ match ($item['state']) { 'completed' => 'Afgerond', 'in_progress' => 'Bezig', 'locked' => 'Vergrendeld', default => 'Beschikbaar' } }}</span>
                                </div>
                                <div class="omt-learning-step-content">
                                    <div class="min-w-0 flex-1">
                                        <h3>{{ $course->title }}</h3>
                                        @if ($course->description)<p class="omt-learning-description">{{ $course->description }}</p>@endif
                                        <div class="omt-learning-meta">
                                            <span><x-heroicon-o-clock class="h-4 w-4" />{{ $course->duration_hours }} uur</span>
                                            <span><x-heroicon-o-banknotes class="h-4 w-4" />{{ $course->price > 0 ? '€ '.number_format($course->price, 2, ',', '.') : 'Gratis' }}</span>
                                            @if ($course->qualifies_for_company)<span class="omt-learning-qualification">Bedrijf aanvragen</span>@endif
                                        </div>
                                        @if ($item['missing']->isNotEmpty())
                                            <p class="omt-learning-locked-note">Eerst afronden: {{ $item['missing']->pluck('title')->join(', ') }}</p>
                                        @endif
                                        @if ($item['state'] === 'in_progress')
                                            <div class="omt-learning-timer">
                                                <div><span>Verwachte afronding</span><strong>{{ $enrollment->completes_at->format('d-m-Y H:i') }}</strong></div>
                                                <div class="omt-learning-progress-track"><span style="width: {{ $progressPercent }}%"></span></div>
                                            </div>
                                        @elseif ($item['state'] === 'completed')
                                            <p class="omt-learning-completed-date">Afgerond op {{ $enrollment->completed_at->format('d-m-Y H:i') }}</p>
                                        @elseif ($item['state'] === 'available')
                                            <form action="{{ route('portal.education.enroll', $course) }}" method="POST" class="omt-learning-enroll-form">
                                                @csrf
                                                @if ($course->price > 0)
                                                    <label for="payer_{{ $course->id }}" class="sr-only">Betaalrekening voor {{ $course->title }}</label>
                                                    <select id="payer_{{ $course->id }}" name="payer_bank_account_uuid" required>
                                                        <option value="">Kies een betaalrekening</option>
                                                        @foreach ($bankAccounts as $account)
                                                            <option value="{{ $account['uuid'] }}" @selected(old('payer_bank_account_uuid') === $account['uuid'])>{{ $account['name'] }} · € {{ number_format($account['balance'], 2, ',', '.') }}</option>
                                                        @endforeach
                                                    </select>
                                                @endif
                                                <button type="submit" class="omt-education-primary-action"><x-heroicon-s-play class="h-4 w-4" />Opleiding starten</button>
                                            </form>
                                            @error('payer_bank_account_uuid')<p class="omt-form-error">{{ $message }}</p>@enderror
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </article>
                    @endforeach
                </div>
            </section>
        @endif
    </div>
@endsection