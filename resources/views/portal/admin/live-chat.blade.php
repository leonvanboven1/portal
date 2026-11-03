@extends('portal.layouts.app')

@section('title', 'Livechat')
@section('header', 'Livechat')

@section('content')
    <div
        x-data="staffChat({
            conversationsUrl: @js(route('portal.admin.live-chat.conversations')),
            messagesUrl: @js(route('portal.admin.live-chat.messages', ['conversationId' => '__ID__'])),
            replyUrl: @js(route('portal.admin.live-chat.reply', ['conversationId' => '__ID__'])),
            closeUrl: @js(route('portal.admin.live-chat.close', ['conversationId' => '__ID__'])),
        })"
        class="grid min-h-[65vh] gap-5 lg:grid-cols-[320px_minmax(0,1fr)]"
    >
        <section class="overflow-hidden rounded-xl border border-gray-200 bg-white shadow-sm dark:border-gray-700 dark:bg-gray-800">
            <div class="flex items-center justify-between border-b border-gray-200 px-4 py-4 dark:border-gray-700">
                <div>
                    <h2 class="font-semibold text-gray-900 dark:text-white">Open gesprekken</h2>
                    <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">Nieuwe chats verschijnen automatisch.</p>
                </div>
                <span class="rounded-full bg-emerald-50 px-2.5 py-1 text-xs font-semibold text-emerald-700 dark:bg-emerald-500/10 dark:text-emerald-300" x-text="conversations.length"></span>
            </div>
            <p x-show="error" x-text="error" role="alert" class="m-3 rounded-lg bg-red-50 p-3 text-sm text-red-700 dark:bg-red-500/10 dark:text-red-300"></p>
            <div class="max-h-[65vh] divide-y divide-gray-100 overflow-y-auto dark:divide-gray-700">
                <template x-for="conversation in conversations" :key="conversation.id">
                    <button type="button" @click="select(conversation)" class="block w-full px-4 py-3 text-left transition hover:bg-gray-50 dark:hover:bg-gray-700/60" :class="selected?.id === conversation.id ? 'bg-indigo-50 dark:bg-indigo-500/10' : ''">
                        <span class="flex items-center justify-between gap-2">
                            <strong class="truncate text-sm text-gray-900 dark:text-white" x-text="conversation.visitor_name"></strong>
                            <small class="shrink-0 text-[10px] text-gray-500" x-text="conversation.last_message_at"></small>
                        </span>
                        <span class="mt-1 block truncate text-xs text-gray-500 dark:text-gray-400" x-text="conversation.last_message"></span>
                    </button>
                </template>
                <p x-show="conversations.length === 0" class="px-4 py-10 text-center text-sm text-gray-500 dark:text-gray-400">Er zijn nog geen open gesprekken.</p>
            </div>
        </section>

        <section class="flex min-h-[65vh] flex-col overflow-hidden rounded-xl border border-gray-200 bg-white shadow-sm dark:border-gray-700 dark:bg-gray-800">
            <template x-if="!selected">
                <div class="grid flex-1 place-items-center p-8 text-center">
                    <div>
                        <x-heroicon-o-chat-bubble-left-right class="mx-auto h-10 w-10 text-gray-400"/>
                        <p class="mt-3 text-sm text-gray-500 dark:text-gray-400">Selecteer links een gesprek om de chat te bekijken.</p>
                    </div>
                </div>
            </template>
            <template x-if="selected">
                <div class="flex min-h-[65vh] flex-1 flex-col">
                    <header class="flex items-center justify-between gap-3 border-b border-gray-200 px-4 py-4 dark:border-gray-700">
                        <div>
                            <h2 class="font-semibold text-gray-900 dark:text-white" x-text="selected.visitor_name"></h2>
                            <p class="text-xs text-gray-500 dark:text-gray-400">Bezoeker op vunex.nl</p>
                        </div>
                        <button type="button" @click="closeConversation()" class="rounded-lg border border-gray-200 px-3 py-2 text-xs font-semibold text-gray-600 hover:bg-gray-50 dark:border-gray-600 dark:text-gray-300 dark:hover:bg-gray-700">Gesprek sluiten</button>
                    </header>
                    <div class="flex-1 space-y-3 overflow-y-auto bg-gray-50/70 p-4 dark:bg-gray-900/30" x-ref="messages">
                        <template x-for="message in messages" :key="message.id">
                            <article class="max-w-[85%] rounded-xl border border-gray-200 bg-white px-3 py-2 shadow-sm dark:border-gray-700 dark:bg-gray-800" :class="message.sender_type === 'staff' ? 'ml-auto' : ''">
                                <div class="flex items-center justify-between gap-4 text-[10px] text-gray-500">
                                    <strong class="text-gray-700 dark:text-gray-300" x-text="message.sender_name"></strong>
                                    <time x-text="message.created_at"></time>
                                </div>
                                <p class="mt-1 whitespace-pre-wrap break-words text-sm text-gray-800 dark:text-gray-100" x-text="message.body"></p>
                            </article>
                        </template>
                    </div>
                    <form @submit.prevent="reply()" class="flex gap-2 border-t border-gray-200 p-3 dark:border-gray-700">
                        <label class="sr-only" for="staff-chat-message">Bericht aan bezoeker</label>
                        <input id="staff-chat-message" x-model="draft" maxlength="2000" required placeholder="Typ je antwoord..." class="min-w-0 flex-1 rounded-lg border-gray-300 text-sm focus:border-indigo-500 focus:ring-indigo-500 dark:border-gray-600 dark:bg-gray-700 dark:text-white">
                        <button type="submit" :disabled="sending" class="rounded-lg bg-indigo-600 px-4 py-2 text-sm font-semibold text-white hover:bg-indigo-500 disabled:opacity-50">Verstuur</button>
                    </form>
                </div>
            </template>
        </section>
    </div>
@endsection
