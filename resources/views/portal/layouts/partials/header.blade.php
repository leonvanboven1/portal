@php($pageHeading = trim($__env->yieldContent('header')) ?: trim($__env->yieldContent('title', 'Overzicht')))
<header class="omt-topbar sticky top-0 z-30">
    <div class="omt-topbar-inner mx-auto flex min-h-[4.5rem] max-w-[1440px] items-center gap-x-4 px-4 sm:px-6 xl:px-10">
        <button type="button" @click="sidebarOpen = true" class="omt-menu-button -ml-2 rounded-md p-2 lg:hidden">
            <span class="sr-only">Menu openen</span>
            <x-heroicon-o-bars-3 class="h-6 w-6" />
        </button>

        <div class="min-w-0 flex-1">
            <p class="omt-topbar-kicker hidden sm:block">OpenMinetopia <span>/</span> Portaal</p>
            <h1 class="omt-topbar-title truncate">{{ $pageHeading }}</h1>
        </div>

        @php($unread = auth()->user()->unreadNotifications->count())
        <div class="relative" x-data="{ open: false }">
            <button @click="open = !open" class="omt-icon-button relative rounded-md p-2"
                    :aria-expanded="open">
                <span class="sr-only">Notificaties{{ $unread ? " ($unread ongelezen)" : '' }}</span>
                <x-heroicon-o-bell class="h-6 w-6" />
                @if ($unread > 0)
                    <span class="absolute right-1.5 top-1.5 h-2 w-2 rounded-sm bg-brand ring-2 ring-white dark:ring-gray-900"></span>
                @endif
            </button>

            <div x-show="open" x-cloak
                 @click.away="open = false"
                 x-transition:enter="transition ease-out duration-200"
                 x-transition:enter-start="opacity-0 -translate-y-1"
                 x-transition:enter-end="opacity-100 translate-y-0"
                 x-transition:leave="transition ease-in duration-100"
                 x-transition:leave-start="opacity-100"
                 x-transition:leave-end="opacity-0"
                 class="omt-notification-menu absolute right-0 z-10 mt-2 w-80 origin-top-right rounded-lg border border-gray-200 bg-white shadow-lg dark:border-gray-700 dark:bg-gray-800">
                <div class="flex items-center justify-between border-b border-gray-200 px-4 py-3 dark:border-gray-700">
                    <h2 class="text-sm font-semibold text-gray-900 dark:text-white">Notificaties</h2>
                    @if ($unread > 0)
                        <form action="{{ route('notifications.mark-all-read') }}" method="POST">
                            @csrf
                            <button type="submit" class="text-sm text-indigo-600 hover:underline dark:text-indigo-400">Alles gelezen</button>
                        </form>
                    @endif
                </div>

                <div class="max-h-96 divide-y divide-gray-200 overflow-y-auto dark:divide-gray-700">
                    @forelse (auth()->user()->notifications()->latest()->take(5)->get() as $notification)
                        <div class="flex gap-3 px-4 py-3">
                            <span @class(['mt-1.5 h-2 w-2 shrink-0 rounded-sm', 'bg-brand' => ! $notification->read_at, 'bg-transparent' => $notification->read_at])></span>
                            <div class="min-w-0 flex-1">
                                <p class="text-sm font-medium text-gray-900 dark:text-white">{{ $notification->data['title'] }}</p>
                                <p class="text-sm text-gray-600 dark:text-gray-400">{{ $notification->data['message'] }}</p>
                                <p class="mt-1 text-xs text-gray-500">{{ $notification->created_at->diffForHumans() }}</p>
                            </div>
                        </div>
                    @empty
                        <p class="px-4 py-8 text-center text-sm text-gray-500 dark:text-gray-400">Je hebt nog geen notificaties.</p>
                    @endforelse
                </div>
            </div>
        </div>
    </div>
</header>
