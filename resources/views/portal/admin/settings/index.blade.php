@extends('portal.layouts.app')

@section('title', 'Portal Instellingen')
@section('header', 'Portal Instellingen')

@section('content')
    <div class="space-y-6">
        <!-- Back Button -->
        <div>
            <a href="{{ route('dashboard') }}" class="inline-flex items-center text-sm font-medium text-gray-500 hover:text-gray-700 dark:text-gray-400 dark:hover:text-gray-300">
                <x-heroicon-s-arrow-left class="w-5 h-5 mr-1" />
                Terug naar overzicht
            </a>
        </div>

        <section class="overflow-hidden rounded-lg bg-white shadow-sm dark:bg-gray-800">
            <div class="border-b border-gray-200 px-4 py-5 dark:border-gray-700 sm:px-6">
                <div class="flex items-start gap-3">
                    <span class="grid h-10 w-10 shrink-0 place-items-center rounded-lg bg-indigo-50 text-indigo-600 dark:bg-indigo-500/10 dark:text-indigo-400">
                        <x-heroicon-o-globe-americas class="h-5 w-5"/>
                    </span>
                    <div>
                        <h2 class="text-base font-semibold text-gray-900 dark:text-white">Openbare website</h2>
                        <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">Beheer de homepage, afbeeldingen en spelregels van Vunex.</p>
                    </div>
                </div>
            </div>
            <form action="{{ route('portal.admin.settings.update-site-content') }}" method="POST" class="space-y-5 p-4 sm:p-6">
                @csrf
                @method('PUT')
                @foreach([
                    'hero_title' => ['Hoofdtitel', 'Bijvoorbeeld: Jouw verhaal begint hier.'],
                    'hero_text' => ['Introductie', 'Korte tekst die bezoekers als eerste lezen.'],
                    'about_title' => ['Titel over ons', 'De titel boven de informatie over de stad.'],
                    'about_text' => ['Informatie over de stad', 'Vertel bezoekers wat jouw roleplayserver bijzonder maakt.'],
                ] as $field => [$label, $hint])
                    <div>
                        <label for="{{ $field }}" class="block text-sm font-medium text-gray-900 dark:text-white">{{ $label }}</label>
                        @if(in_array($field, ['hero_title', 'about_title'], true))
                            <input id="{{ $field }}" name="{{ $field }}" type="text" maxlength="120" required
                                   value="{{ old($field, $siteContent->{$field}) }}"
                                   class="mt-1.5 block w-full rounded-md border-0 bg-white px-3 py-2.5 text-sm text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 focus:ring-2 focus:ring-indigo-600 dark:bg-gray-700 dark:text-white dark:ring-gray-600 dark:focus:ring-indigo-500">
                        @else
                            <textarea id="{{ $field }}" name="{{ $field }}" rows="4" required
                                      class="mt-1.5 block w-full rounded-md border-0 bg-white px-3 py-2.5 text-sm text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 focus:ring-2 focus:ring-indigo-600 dark:bg-gray-700 dark:text-white dark:ring-gray-600 dark:focus:ring-indigo-500">{{ old($field, $siteContent->{$field}) }}</textarea>
                        @endif
                        <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">{{ $hint }}</p>
                        @error($field)<p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>@enderror
                    </div>
                @endforeach
                <div class="flex justify-end border-t border-gray-200 pt-4 dark:border-gray-700">
                    <button type="submit" class="inline-flex items-center gap-2 rounded-md bg-indigo-600 px-4 py-2 text-sm font-semibold text-white shadow-sm transition hover:bg-indigo-500">
                        <x-heroicon-s-check class="h-4 w-4"/>
                        Website opslaan
                    </button>
                </div>
            </form>
        </section>

        <section class="overflow-hidden rounded-lg bg-white shadow-sm dark:bg-gray-800">
            <div class="border-b border-gray-200 px-4 py-5 dark:border-gray-700 sm:px-6">
                <h2 class="text-base font-semibold text-gray-900 dark:text-white">Afbeeldingen openbare website</h2>
                <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">Pas alle afbeeldingen van de openbare website aan. JPG, PNG of WebP tot 10 MB.</p>
            </div>
            <form action="{{ route('portal.admin.settings.update-site-images') }}" method="POST" enctype="multipart/form-data" class="grid gap-6 p-4 sm:grid-cols-2 sm:p-6 lg:grid-cols-4">
                @csrf
                @foreach([
                    'hero_image' => ['Hoofdfoto', 'hero_image_path'],
                    'unique_image' => ['Vunex uniek-foto', 'unique_image_path'],
                    'steps_image' => ['Avontuur-foto', 'steps_image_path'],
                ] as $field => [$label, $column])
                    <div class="space-y-3">
                        <label for="{{ $field }}" class="block text-sm font-medium text-gray-900 dark:text-white">{{ $label }}</label>
                        @if($siteContent->{$column})
                            <img src="{{ \Illuminate\Support\Facades\Storage::disk('public')->url($siteContent->{$column}) }}" alt="Huidige {{ strtolower($label) }}" class="h-36 w-full rounded-lg object-cover">
                        @else
                            <div class="grid h-36 place-items-center rounded-lg border border-dashed border-gray-300 text-sm text-gray-500 dark:border-gray-600 dark:text-gray-400">Nog geen foto ingesteld</div>
                        @endif
                        <input id="{{ $field }}" name="{{ $field }}" type="file" accept="image/jpeg,image/png,image/webp" class="block w-full text-sm text-gray-600 file:mr-3 file:rounded-md file:border-0 file:bg-indigo-50 file:px-3 file:py-2 file:text-sm file:font-semibold file:text-indigo-700 dark:text-gray-300 dark:file:bg-indigo-500/10 dark:file:text-indigo-300">
                        @if($siteContent->{$column})
                            <label class="flex items-center gap-2 text-sm text-gray-600 dark:text-gray-300">
                                <input type="checkbox" name="remove_{{ $field }}" value="1" class="rounded border-gray-300 text-indigo-600 focus:ring-indigo-600">
                                Verwijder deze foto
                            </label>
                        @endif
                        @error($field)<p class="text-sm text-red-600">{{ $message }}</p>@enderror
                    </div>
                @endforeach
                <div class="flex justify-end border-t border-gray-200 pt-4 dark:border-gray-700 sm:col-span-2 lg:col-span-4">
                    <button type="submit" class="inline-flex items-center gap-2 rounded-md bg-indigo-600 px-4 py-2 text-sm font-semibold text-white shadow-sm hover:bg-indigo-500">
                        <x-heroicon-s-check class="h-4 w-4"/> Afbeeldingen opslaan
                    </button>
                </div>
            </form>
        </section>

        <section class="overflow-hidden rounded-lg bg-white shadow-sm dark:bg-gray-800">
            <div class="border-b border-gray-200 px-4 py-5 dark:border-gray-700 sm:px-6">
                <h2 class="text-base font-semibold text-gray-900 dark:text-white">Spelregels beheren</h2>
                <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">Maak categorieën aan en voeg per categorie regels toe. Bezoekers vinden ze op de aparte pagina Regels.</p>
            </div>
            <div class="space-y-6 p-4 sm:p-6">
                @if($errors->has('title') || $errors->has('body'))
                    <div role="alert" class="rounded-md bg-red-50 p-3 text-sm text-red-700 dark:bg-red-500/10 dark:text-red-300">
                        Controleer de titel en uitleg van de regel. De titel mag maximaal 160 tekens zijn en de uitleg maximaal 5000.
                    </div>
                @endif
                <form action="{{ route('portal.admin.settings.site-rules.categories.store') }}" method="POST" class="flex flex-col gap-3 sm:flex-row">
                    @csrf
                    <label for="site-rule-category-name" class="sr-only">Nieuwe categorie</label>
                    <input id="site-rule-category-name" name="name" required maxlength="100" placeholder="Nieuwe categorie, bijvoorbeeld Roleplay" class="min-w-0 flex-1 rounded-md border-0 bg-white px-3 py-2.5 text-sm text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 dark:bg-gray-700 dark:text-white dark:ring-gray-600">
                    <button type="submit" class="rounded-md bg-indigo-600 px-4 py-2 text-sm font-semibold text-white hover:bg-indigo-500">Categorie toevoegen</button>
                    @error('name')<p class="text-sm text-red-600">{{ $message }}</p>@enderror
                </form>

                @forelse($siteRuleCategories as $category)
                    <article class="rounded-lg border border-gray-200 dark:border-gray-700">
                        <div class="flex flex-wrap items-center justify-between gap-3 border-b border-gray-200 px-4 py-3 dark:border-gray-700">
                            <h3 class="font-semibold text-gray-900 dark:text-white">{{ $category->name }}</h3>
                            <form action="{{ route('portal.admin.settings.site-rules.categories.destroy', $category) }}" method="POST" onsubmit="return confirm('Verwijder deze categorie en alle regels erin?')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="text-sm font-medium text-red-600 hover:text-red-500">Categorie verwijderen</button>
                            </form>
                        </div>
                        <div class="space-y-3 p-4">
                            @forelse($category->rules as $siteRule)
                                <div class="rounded-md bg-gray-50 p-3 dark:bg-gray-700/50">
                                    <form action="{{ route('portal.admin.settings.site-rules.update', $siteRule) }}" method="POST" class="grid gap-3">
                                        @csrf
                                        @method('PUT')
                                        <input name="title" required maxlength="160" value="{{ $siteRule->title }}" aria-label="Titel van de regel" class="rounded-md border-0 bg-white px-3 py-2 text-sm text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 dark:bg-gray-700 dark:text-white dark:ring-gray-600">
                                        <textarea name="body" required maxlength="5000" rows="2" aria-label="Uitleg van de regel" class="rounded-md border-0 bg-white px-3 py-2 text-sm text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 dark:bg-gray-700 dark:text-white dark:ring-gray-600">{{ $siteRule->body }}</textarea>
                                        <div class="flex flex-wrap justify-between gap-2">
                                            <button type="submit" class="text-sm font-semibold text-indigo-600 hover:text-indigo-500">Wijzigingen opslaan</button>
                                            <button form="delete-site-rule-{{ $siteRule->id }}" type="submit" class="text-sm font-medium text-red-600 hover:text-red-500">Verwijderen</button>
                                        </div>
                                    </form>
                                    <form id="delete-site-rule-{{ $siteRule->id }}" action="{{ route('portal.admin.settings.site-rules.destroy', $siteRule) }}" method="POST">
                                        @csrf
                                        @method('DELETE')
                                    </form>
                                </div>
                            @empty
                                <p class="text-sm text-gray-500 dark:text-gray-400">Nog geen regels in deze categorie.</p>
                            @endforelse
                            <form action="{{ route('portal.admin.settings.site-rules.store', $category) }}" method="POST" class="grid gap-3 border-t border-gray-200 pt-4 dark:border-gray-700">
                                @csrf
                                <input name="title" required maxlength="160" placeholder="Titel van de regel" class="rounded-md border-0 bg-white px-3 py-2.5 text-sm text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 dark:bg-gray-700 dark:text-white dark:ring-gray-600">
                                <textarea name="body" required maxlength="5000" rows="2" placeholder="Leg de regel duidelijk uit" class="rounded-md border-0 bg-white px-3 py-2.5 text-sm text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 dark:bg-gray-700 dark:text-white dark:ring-gray-600"></textarea>
                                <button type="submit" class="justify-self-start rounded-md bg-gray-900 px-4 py-2 text-sm font-semibold text-white hover:bg-gray-700 dark:bg-gray-600 dark:hover:bg-gray-500">Regel toevoegen</button>
                            </form>
                        </div>
                    </article>
                @empty
                    <p class="rounded-lg bg-gray-50 p-4 text-sm text-gray-600 dark:bg-gray-700/50 dark:text-gray-300">Er zijn nog geen categorieën. Voeg hierboven je eerste categorie toe.</p>
                @endforelse
            </div>
        </section>

        <!-- Main Content -->
        <div class="grid grid-cols-1 gap-6 lg:grid-cols-3">
            <!-- Left Column - Main Settings -->
            <div class="lg:col-span-2 space-y-6">
                <!-- Portal Features Card -->
                <div class="bg-white dark:bg-gray-800 shadow-sm rounded-lg">
                    <div class="px-4 py-5 sm:px-6 border-b border-gray-200 dark:border-gray-700">
                        <div class="flex items-center justify-between">
                            <div>
                                <h3 class="text-base font-semibold text-gray-900 dark:text-white">Portal Functies</h3>
                                <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">Wat uitstaat is voor niemand zichtbaar, ook niet voor beheerders. Wie wat mag binnen een feature stel je in bij Rollen.</p>
                            </div>
                        </div>
                    </div>
                    <form action="{{ route('portal.admin.settings.update-features') }}" method="POST">
                        @csrf
                        @method('PUT')
                        
                        <div class="px-4 py-5 sm:p-6 space-y-4">
                            @foreach($features as $feature)
                                <div class="relative flex items-start py-2">
                                    <div class="flex h-6 items-center">
                                        <input type="checkbox" 
                                               name="features[{{ $feature->key }}]" 
                                               id="feature_{{ $feature->key }}"
                                               @checked($feature->is_enabled)
                                               class="h-4 w-4 rounded border-gray-300 text-indigo-600 focus:ring-indigo-600 dark:border-gray-600 dark:bg-gray-700 dark:ring-offset-gray-800">
                                    </div>
                                    <div class="ml-3">
                                        <label for="feature_{{ $feature->key }}" class="text-sm font-medium text-gray-700 dark:text-gray-300">
                                            {{ $feature->name }}
                                        </label>
                                        @if($feature->description)
                                            <p class="text-sm text-gray-500 dark:text-gray-400">{{ $feature->description }}</p>
                                        @endif
                                        @if($requires = \App\Support\Features::requires($feature->key))
                                            <p class="text-xs text-gray-500 dark:text-gray-400">Werkt alleen als {{ \App\Support\Features::name($requires) }} aanstaat.</p>
                                        @endif
                                    </div>
                                </div>
                            @endforeach
                        </div>
                        
                        <div class="px-4 py-3 bg-gray-50 dark:bg-gray-800/50 text-right sm:px-6 border-t border-gray-200 dark:border-gray-700">
                            <button type="submit" class="inline-flex justify-center rounded-md bg-indigo-600 px-3 py-2 text-sm font-semibold text-white shadow-sm hover:bg-indigo-500 dark:bg-indigo-500 dark:hover:bg-indigo-400">
                                <x-heroicon-s-check class="h-4 w-4 mr-2" />
                                Instellingen Opslaan
                            </button>
                        </div>
                    </form>
                </div>

                <!-- Plot Image Settings -->
                <div class="bg-white dark:bg-gray-800 shadow-sm rounded-lg">
                    <div class="px-4 py-5 sm:px-6 border-b border-gray-200 dark:border-gray-700">
                        <div class="flex items-center justify-between">
                            <div>
                                <h3 class="text-base font-semibold text-gray-900 dark:text-white">Plot Afbeeldingen</h3>
                                <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">Stel in hoe groot een foto mag zijn bij het plaatsen van een plot te koop of te huur.</p>
                            </div>
                        </div>
                    </div>
                    <form action="{{ route('portal.admin.settings.update-plot-listing-settings') }}" method="POST" class="px-4 py-5 sm:p-6">
                        @csrf
                        @method('PUT')

                        <div class="space-y-4">
                            <div>
                                <label for="max_image_size_mb" class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                                    Maximale afbeeldingsgrootte (MB)
                                </label>
                                <div class="mt-1">
                                    <input type="number"
                                           name="max_image_size_mb"
                                           id="max_image_size_mb"
                                           min="1"
                                           max="200"
                                           step="1"
                                           value="{{ old('max_image_size_mb', $plotListingSettings->max_image_size_mb ?? \App\Models\PlotListingSetting::DEFAULT_MAX_IMAGE_SIZE_MB) }}"
                                           class="block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 dark:border-gray-600 dark:bg-gray-700 dark:text-white sm:text-sm">
                                </div>
                                <p class="mt-2 text-sm text-gray-500 dark:text-gray-400">
                                    Deze instelling geldt voor foto’s bij plotverkoop en plotverhuur. Voor grote afbeeldingen kun je hier meer MB instellen.
                                </p>
                            </div>
                        </div>

                        <div class="mt-6 flex justify-end">
                            <button type="submit"
                                    class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md shadow-sm text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 dark:focus:ring-offset-gray-800">
                                <x-heroicon-s-check class="h-4 w-4 mr-2"/>
                                Instellingen Opslaan
                            </button>
                        </div>
                    </form>
                </div>

                <!-- Plugin Integration Settings -->
                <div class="bg-white dark:bg-gray-800 shadow-sm rounded-lg">
                    <div class="px-4 py-5 sm:px-6 border-b border-gray-200 dark:border-gray-700">
                        <h3 class="text-base font-semibold text-gray-900 dark:text-white">Plugin Integratie</h3>
                        <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">Configureer de verbinding met de Minecraft server</p>
                    </div>
                    <div class="px-4 py-5 sm:p-6">
                        <dl class="grid grid-cols-1 gap-6 sm:grid-cols-2">
                            <div>
                                <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">API Status</dt>
                                <dd class="mt-1">
                                    <span class="inline-flex items-center rounded-md bg-green-50 px-2 py-1 text-xs font-medium text-green-700 ring-1 ring-inset ring-green-600/20 dark:bg-green-500/10 dark:text-green-400 dark:ring-green-500/20">
                                        Verbonden
                                    </span>
                                </dd>
                            </div>
                            <div>
                                <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Laatste Synchronisatie</dt>
                                <dd class="mt-1 text-sm text-gray-900 dark:text-white">{{ now()->subMinutes(5)->diffForHumans() }}</dd>
                            </div>
                        </dl>
                    </div>
                </div>

                <!-- Permit Settings -->
                @feature('permits')
                    <div class="bg-white dark:bg-gray-800 shadow-sm rounded-lg">
                        <div class="px-4 py-5 sm:px-6 border-b border-gray-200 dark:border-gray-700">
                            <div class="flex items-center justify-between">
                                <div>
                                    <h3 class="text-base font-semibold text-gray-900 dark:text-white">Vergunningen Instellingen</h3>
                                    <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">Configureer de financiële instellingen voor vergunningen</p>
                                </div>
                            </div>
                        </div>
                        <form action="{{ route('portal.admin.settings.update-permit-settings') }}" method="POST" class="px-4 py-5 sm:p-6">
                            @csrf
                            @method('PUT')
                            
                            <div class="space-y-4">
                                <!-- Bank Account Selection -->
                                <div>
                                    <label for="payout_bank_account_uuid" class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                                        Uitbetalingsrekening
                                    </label>
                                    <div class="mt-1">
                                        <select name="payout_bank_account_uuid" 
                                                id="payout_bank_account_uuid"
                                                class="block w-full pl-3 pr-10 py-2 text-base border-gray-300 dark:border-gray-600 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm rounded-md dark:bg-gray-700 dark:text-white">
                                            <option value="">Selecteer een bankrekening</option>
                                            @foreach(auth()->user()->bank_accounts as $account)
                                                @if($account['type'] !== 'PRIVATE')
                                                    <option value="{{ $account['uuid'] }}" 
                                                            @if($permitSettings->payout_bank_account_uuid === $account['uuid']) selected @endif>
                                                        {{ $account['name'] }} (€ {{ number_format($account['balance'], 2, ',', '.') }})
                                                    </option>
                                                @endif
                                            @endforeach
                                        </select>
                                    </div>
                                    <p class="mt-2 text-sm text-gray-500 dark:text-gray-400">
                                        Selecteer de overheidsrekening waar alle vergunningsgelden op worden gestort. 
                                        Let op: alleen overheidsrekeningen worden getoond.
                                    </p>
                                </div>

                                <div class="bg-blue-50 dark:bg-blue-500/10 rounded-lg p-4">
                                    <div class="flex">
                                        <div class="flex-shrink-0">
                                            <x-heroicon-s-information-circle class="h-5 w-5 text-blue-400"/>
                                        </div>
                                        <div class="ml-3">
                                            <h3 class="text-sm font-medium text-blue-800 dark:text-blue-300">
                                                Alleen overheidsrekeningen
                                            </h3>
                                            <div class="mt-2 text-sm text-blue-700 dark:text-blue-200">
                                                <p>Als administrator met OP-rechten op de server zie je alle overheidsrekeningen. 
                                                Deze rekening wordt gebruikt voor alle financiële transacties rondom vergunningen, 
                                                zoals betalingen en terugstortingen.</p>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="mt-6 flex justify-end">
                                <button type="submit" 
                                        class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md shadow-sm text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 dark:focus:ring-offset-gray-800">
                                    <x-heroicon-s-check class="h-4 w-4 mr-2"/>
                                    Instellingen Opslaan
                                </button>
                            </div>
                        </form>
                    </div>
                @endif

                <!-- After Permit Settings -->
                @feature('companies')
                    <div class="bg-white dark:bg-gray-800 shadow-sm rounded-lg">
                        <div class="px-4 py-5 sm:px-6 border-b border-gray-200 dark:border-gray-700">
                            <div class="flex items-center justify-between">
                                <div>
                                    <h3 class="text-base font-semibold text-gray-900 dark:text-white">Bedrijven Instellingen</h3>
                                    <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">Configureer de financiële instellingen voor bedrijfsaanvragen</p>
                                </div>
                            </div>
                        </div>
                        <form action="{{ route('portal.admin.settings.update-company-settings') }}" method="POST" class="px-4 py-5 sm:p-6">
                            @csrf
                            @method('PUT')
                            
                            <div class="space-y-4">
                                <!-- Bank Account Selection -->
                                <div>
                                    <label for="company_payout_bank_account_uuid" class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                                        Uitbetalingsrekening
                                    </label>
                                    <div class="mt-1">
                                        <select name="payout_bank_account_uuid" 
                                                id="company_payout_bank_account_uuid"
                                                class="block w-full pl-3 pr-10 py-2 text-base border-gray-300 dark:border-gray-600 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm rounded-md dark:bg-gray-700 dark:text-white">
                                            <option value="">Selecteer een bankrekening</option>
                                            @foreach(auth()->user()->bank_accounts as $account)
                                                @if($account['type'] !== 'PRIVATE')
                                                    <option value="{{ $account['uuid'] }}" 
                                                            @if($companySettings->payout_bank_account_uuid === $account['uuid']) selected @endif>
                                                        {{ $account['name'] }} (€ {{ number_format($account['balance'], 2, ',', '.') }})
                                                    </option>
                                                @endif
                                            @endforeach
                                        </select>
                                    </div>
                                    <p class="mt-2 text-sm text-gray-500 dark:text-gray-400">
                                        Selecteer de overheidsrekening waar alle bedrijfsaanvraag gelden op worden gestort. 
                                        Let op: alleen overheidsrekeningen worden getoond.
                                    </p>
                                </div>

                                <div class="bg-blue-50 dark:bg-blue-500/10 rounded-lg p-4">
                                    <div class="flex">
                                        <div class="flex-shrink-0">
                                            <x-heroicon-s-information-circle class="h-5 w-5 text-blue-400"/>
                                        </div>
                                        <div class="ml-3">
                                            <h3 class="text-sm font-medium text-blue-800 dark:text-blue-300">
                                                Alleen overheidsrekeningen
                                            </h3>
                                            <div class="mt-2 text-sm text-blue-700 dark:text-blue-200">
                                                <p>Als administrator met OP-rechten op de server zie je alle overheidsrekeningen. 
                                                Deze rekening wordt gebruikt voor alle financiële transacties rondom bedrijfsaanvragen, 
                                                zoals registratiekosten en terugstortingen.</p>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="mt-6 flex justify-end">
                                <button type="submit" 
                                        class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md shadow-sm text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 dark:focus:ring-offset-gray-800">
                                    <x-heroicon-s-check class="h-4 w-4 mr-2"/>
                                    Instellingen Opslaan
                                </button>
                            </div>
                        </form>
                    </div>
                @endif
            </div>

            <!-- Right Column -->
            <div class="space-y-6">
                <!-- Quick Actions -->
                <div class="bg-white dark:bg-gray-800 shadow-sm rounded-lg">
                    <div class="px-4 py-5 sm:px-6 border-b border-gray-200 dark:border-gray-700">
                        <h3 class="text-base font-semibold text-gray-900 dark:text-white">Snelle Acties</h3>
                    </div>
                    <div class="px-4 py-5 sm:p-6 space-y-4">
                        <button type="button" 
                                class="w-full inline-flex items-center justify-center rounded-md bg-indigo-600 px-3 py-2 text-sm font-semibold text-white shadow-sm hover:bg-indigo-500 dark:bg-indigo-500 dark:hover:bg-indigo-400">
                            <x-heroicon-s-arrow-path class="h-4 w-4 mr-2" />
                            Synchroniseer Data
                        </button>
                        <button type="button"
                                class="w-full inline-flex items-center justify-center rounded-md bg-white dark:bg-gray-700 px-3 py-2 text-sm font-semibold text-gray-900 dark:text-gray-300 shadow-sm ring-1 ring-inset ring-gray-300 dark:ring-gray-600 hover:bg-gray-50 dark:hover:bg-gray-600">
                            <x-heroicon-s-trash class="h-4 w-4 mr-2" />
                            Cache Legen
                        </button>
                    </div>
                </div>

                <!-- System Info -->
                <div class="bg-white dark:bg-gray-800 shadow-sm rounded-lg">
                    <div class="px-4 py-5 sm:px-6 border-b border-gray-200 dark:border-gray-700">
                        <h3 class="text-base font-semibold text-gray-900 dark:text-white">Systeem Informatie</h3>
                    </div>
                    <div class="px-4 py-5 sm:p-6">
                        <dl class="space-y-4">
                            <div>
                                <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">PHP Versie</dt>
                                <dd class="mt-1 text-sm text-gray-900 dark:text-white">{{ phpversion() }}</dd>
                            </div>
                            <div>
                                <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Laravel Versie</dt>
                                <dd class="mt-1 text-sm text-gray-900 dark:text-white">{{ app()->version() }}</dd>
                            </div>
                            <div>
                                <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Omgeving</dt>
                                <dd class="mt-1 text-sm text-gray-900 dark:text-white">{{ ucfirst(config('app.env')) }}</dd>
                            </div>
                            <div>
                                <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Debug Mode</dt>
                                <dd class="mt-1">
                                    <span class="inline-flex items-center rounded-md px-2 py-1 text-xs font-medium {{ config('app.debug')
                                        ? 'bg-yellow-50 text-yellow-700 ring-1 ring-inset ring-yellow-600/20 dark:bg-yellow-500/10 dark:text-yellow-400 dark:ring-yellow-500/20'
                                        : 'bg-green-50 text-green-700 ring-1 ring-inset ring-green-600/20 dark:bg-green-500/10 dark:text-green-400 dark:ring-green-500/20'
                                    }}">
                                        {{ config('app.debug') ? 'Ingeschakeld' : 'Uitgeschakeld' }}
                                    </span>
                                </dd>
                            </div>
                        </dl>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection 