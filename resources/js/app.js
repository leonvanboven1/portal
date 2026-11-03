import './bootstrap';
import Alpine from 'alpinejs'
import './minecraft-preview'

window.Alpine = Alpine

Alpine.data('rulesSearch', () => ({
    query: '',
    get normalizedQuery() {
        return this.query.trim().toLocaleLowerCase('nl')
    },
    get resultLabel() {
        const total = this.$root.querySelectorAll('[data-rule-item]').length
        const matches = [...this.$root.querySelectorAll('[data-rule-item]')].filter((rule) => this.ruleMatches(rule)).length
        return this.normalizedQuery ? `${matches} van ${total} regels` : `${total} regels`
    },
    ruleMatches(rule) {
        return !this.normalizedQuery
            || rule.dataset.search.includes(this.normalizedQuery)
            || rule.closest('[data-rule-category]').dataset.category.includes(this.normalizedQuery)
    },
    categoryMatches(category) {
        return !this.normalizedQuery
            || category.dataset.category.includes(this.normalizedQuery)
            || [...category.querySelectorAll('[data-rule-item]')].some((rule) => rule.dataset.search.includes(this.normalizedQuery))
    },
    hasResults() {
        return [...this.$root.querySelectorAll('[data-rule-item]')].some((rule) => this.ruleMatches(rule))
    },
}))

Alpine.data('siteChat', (config) => ({
    ...config,
    open: false,
    available: false,
    conversation: null,
    messages: [],
    visitorName: '',
    draft: '',
    error: '',
    sending: false,
    timer: null,
    init() {
        this.refresh()
        this.timer = window.setInterval(() => this.refresh(), 4000)
    },
    destroy() {
        window.clearInterval(this.timer)
    },
    async refresh() {
        try {
            const response = await fetch(this.statusUrl, { headers: { Accept: 'application/json' } })
            if (!response.ok) throw new Error('De chatstatus kon niet worden opgehaald.')
            const data = await response.json()
            this.available = data.available
            this.conversation = data.conversation
            this.messages = data.messages
            if (this.conversation && !this.visitorName) this.visitorName = this.conversation.visitor_name
            this.error = ''
            this.scrollToLatest()
        } catch (error) {
            this.error = error.message || 'De chat is tijdelijk niet bereikbaar.'
        }
    },
    async start() {
        await this.sendRequest(this.startUrl, {
            visitor_name: this.visitorName,
            body: this.draft,
        }, (data) => {
            this.conversation = data.conversation
            this.messages = data.messages
            this.draft = ''
        })
    },
    async send() {
        await this.sendRequest(this.sendUrl, { body: this.draft }, () => {
            this.draft = ''
            this.refresh()
        })
    },
    async sendRequest(url, payload, onSuccess) {
        if (this.sending) return
        this.sending = true
        this.error = ''
        try {
            const response = await fetch(url, {
                method: 'POST',
                headers: {
                    Accept: 'application/json',
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': this.csrfToken,
                },
                body: JSON.stringify(payload),
            })
            const data = await response.json()
            if (!response.ok) throw new Error(data.message || 'Je bericht kon niet worden verstuurd.')
            onSuccess(data)
            this.$nextTick(() => this.scrollToLatest())
        } catch (error) {
            this.error = error.message || 'Je bericht kon niet worden verstuurd.'
        } finally {
            this.sending = false
        }
    },
    scrollToLatest() {
        this.$nextTick(() => {
            if (this.$refs.messages) this.$refs.messages.scrollTop = this.$refs.messages.scrollHeight
        })
    },
}))

Alpine.data('staffChat', (config) => ({
    ...config,
    conversations: [],
    selected: null,
    messages: [],
    draft: '',
    error: '',
    sending: false,
    timer: null,
    init() {
        this.refresh()
        this.timer = window.setInterval(() => this.refresh(), 3000)
    },
    destroy() {
        window.clearInterval(this.timer)
    },
    async refresh() {
        try {
            const response = await fetch(this.conversationsUrl, { headers: { Accept: 'application/json' } })
            if (!response.ok) throw new Error('De gesprekken konden niet worden opgehaald.')
            const data = await response.json()
            this.conversations = data.conversations
            if (this.selected) {
                const updated = this.conversations.find((conversation) => conversation.id === this.selected.id)
                if (!updated) {
                    this.selected = null
                    this.messages = []
                } else {
                    this.selected = updated
                    await this.loadMessages()
                }
            }
            this.error = ''
        } catch (error) {
            this.error = error.message || 'De livechat is tijdelijk niet bereikbaar.'
        }
    },
    async select(conversation) {
        this.selected = conversation
        this.error = ''
        try {
            await this.loadMessages()
        } catch (error) {
            this.error = error.message || 'De berichten konden niet worden opgehaald.'
        }
    },
    async loadMessages() {
        if (!this.selected) return
        const url = this.messagesUrl.replace('__ID__', encodeURIComponent(this.selected.id))
        const response = await fetch(url, { headers: { Accept: 'application/json' } })
        if (!response.ok) throw new Error('De berichten konden niet worden opgehaald.')
        const data = await response.json()
        this.messages = data.messages
        this.selected = { ...this.selected, ...data.conversation }
        this.$nextTick(() => {
            if (this.$refs.messages) this.$refs.messages.scrollTop = this.$refs.messages.scrollHeight
        })
    },
    async reply() {
        if (!this.selected || this.sending) return
        this.sending = true
        this.error = ''
        const url = this.replyUrl.replace('__ID__', encodeURIComponent(this.selected.id))
        try {
            const response = await fetch(url, {
                method: 'POST',
                headers: {
                    Accept: 'application/json',
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                },
                body: JSON.stringify({ body: this.draft }),
            })
            const data = await response.json()
            if (!response.ok) throw new Error(data.message || 'Je antwoord kon niet worden verstuurd.')
            this.draft = ''
            await this.loadMessages()
            await this.refresh()
        } catch (error) {
            this.error = error.message || 'Je antwoord kon niet worden verstuurd.'
        } finally {
            this.sending = false
        }
    },
    async closeConversation() {
        if (!this.selected) return
        const url = this.closeUrl.replace('__ID__', encodeURIComponent(this.selected.id))
        try {
            const response = await fetch(url, {
                method: 'POST',
                headers: {
                    Accept: 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                },
            })
            if (!response.ok) throw new Error('Het gesprek kon niet worden gesloten.')
            this.selected = null
            this.messages = []
            await this.refresh()
        } catch (error) {
            this.error = error.message || 'Het gesprek kon niet worden gesloten.'
        }
    },
}))

Alpine.start()
