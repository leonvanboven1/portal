<div
    class="site-chat"
    x-data="siteChat({
        statusUrl: @js(route('site-chat.status', [], false)),
        startUrl: @js(route('site-chat.start', [], false)),
        sendUrl: @js(route('site-chat.send', [], false)),
        csrfToken: @js(csrf_token()),
    })"
>
    <section class="site-chat-panel" x-show="open" x-cloak x-transition.origin.bottom.right aria-label="Vunex livechat">
        <header class="site-chat-header">
            <div>
                <strong>Chat met Vunex</strong>
                <span class="site-chat-presence">
                    <i :class="available ? 'is-online' : 'is-offline'"></i>
                    <span x-text="available ? 'Medewerker aanwezig' : 'Er is momenteel niemand aanwezig'"></span>
                </span>
            </div>
            <button type="button" class="site-chat-close" @click="open = false" aria-label="Chat sluiten">&times;</button>
        </header>

        <div class="site-chat-staff" x-show="conversation">
            <template x-if="conversation?.staff_name">
                <p>Je chat met <strong x-text="conversation.staff_name"></strong>.</p>
            </template>
            <template x-if="conversation && !conversation.staff_name">
                <p>We helpen je zo snel mogelijk. Je chat is nog niet door een medewerker opgepakt.</p>
            </template>
        </div>
        <p x-show="!available && !conversation" class="site-chat-offline-note">Laat gerust een bericht achter. Zodra een medewerker beschikbaar is, kan het team je chat beantwoorden.</p>
        <p x-show="error" x-text="error" role="alert" class="site-chat-error"></p>

        <div class="site-chat-messages" x-ref="messages" x-show="conversation">
            <template x-for="message in messages" :key="message.id">
                <article class="site-chat-message" :class="message.sender_type === 'staff' ? 'is-staff' : 'is-visitor'">
                    <div><strong x-text="message.sender_name"></strong><time x-text="message.created_at"></time></div>
                    <p x-text="message.body"></p>
                </article>
            </template>
        </div>

        <form x-show="!conversation" @submit.prevent="start()" class="site-chat-form">
            <label for="site-chat-name">Hoe mogen we je noemen?</label>
            <input id="site-chat-name" x-model.trim="visitorName" maxlength="80" required placeholder="Je naam">
            <label for="site-chat-first-message">Je bericht</label>
            <textarea id="site-chat-first-message" x-model.trim="draft" maxlength="2000" rows="3" required placeholder="Waar kunnen we je mee helpen?"></textarea>
            <button type="submit" :disabled="sending" class="site-chat-submit">Start gesprek</button>
        </form>

        <form x-show="conversation" @submit.prevent="send()" class="site-chat-form site-chat-reply-form">
            <label class="sr-only" for="site-chat-reply">Typ een bericht</label>
            <textarea id="site-chat-reply" x-model.trim="draft" maxlength="2000" rows="2" required placeholder="Typ een bericht..."></textarea>
            <button type="submit" :disabled="sending" class="site-chat-submit">Verstuur</button>
        </form>
    </section>

    <button type="button" class="site-chat-launcher" @click="open = !open" :aria-expanded="open" aria-label="Open Vunex livechat">
        <x-heroicon-o-chat-bubble-left-right class="h-6 w-6"/>
        <span>Livechat</span>
        <i :class="available ? 'is-online' : 'is-offline'"></i>
    </button>
</div>
