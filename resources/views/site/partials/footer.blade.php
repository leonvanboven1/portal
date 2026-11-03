<footer class="site-footer">
    <a href="{{ route('site.home') }}" class="site-brand site-brand-footer">
        <span class="site-brand-mark">
            <img src="{{ \App\Models\BrandingSetting::current()->logoUrl() }}" alt="" @class(['omt-pixel' => \App\Models\BrandingSetting::current()->isLogoPixelated()])>
        </span>
        <span>Vunex<small>MINECRAFT ROLEPLAY</small></span>
    </a>
    <div class="site-footer-information">
        <strong>Informatie</strong>
        <span>Vunex is niet verbonden met Mojang AB of Microsoft. Alle Minecraft handelsmerken zijn eigendom van Mojang Studios.</span>
    </div>
    <a class="site-footer-discord" href="https://discord.vunex.nl" target="_blank" rel="noopener noreferrer">
        <x-heroicon-o-chat-bubble-left-right class="h-4 w-4"/> Join de Discord
        <x-heroicon-o-arrow-up-right class="h-3.5 w-3.5"/>
    </a>
</footer>
@include('site.partials.chat-widget')
