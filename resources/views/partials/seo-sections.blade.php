@php
    $img = [
        'about' => 'https://savevideofrom.net/wp-content/uploads/2026/04/save-video-from-768x960.avif',
        'quality' => 'https://savevideofrom.net/wp-content/uploads/2026/04/save-video-from-2-1024x724.avif',
        'fourk' =>
            'https://savevideofrom.net/wp-content/uploads/2026/05/Save-Video-From-Any-Website-From-SD-to-4k-Resolution-1024x1024.avif',
        'browser' => 'https://savevideofrom.net/wp-content/uploads/2026/05/Save-Video-From-Any-Browser-1024x573.avif',
        'device' =>
            'https://savevideofrom.net/wp-content/uploads/2026/05/Save-Any-Videos-on-Android-Phone-Tablet-Desktop-of-iPhone-1024x573.avif',
        'legal' =>
            'https://savevideofrom.net/wp-content/uploads/2026/05/Save-Video-From-Is-It-Legal-Image-1024x1024.avif',
    ];
@endphp

<x-content-section :image="$img['about']" title="Easily Download Videos Online" :reverse="true">
    <p>SaveVideoFrom.net is a simple, browser-based way to save videos and audio from across the web — no software, no
        extensions, no account. It runs on desktop and mobile alike, so you can grab a clip in seconds wherever you are.
    </p>
    <p>Copy the link, paste it into the box, and pick a format. From tutorials to highlights to music, you get clean,
        dependable downloads every time.</p>
</x-content-section>

<x-content-section :image="$img['quality']" title="Download High-Quality MP4, HD &amp; 4K Videos">
    <p>Watching offline means full control — no buffering and no dependence on a connection. SaveVideoFrom keeps the
        original resolution and sharpness of the source, so your saved files look just as good offline as they do
        online.</p>
    <p>Build a personal library knowing every clip keeps its clarity, whether you're travelling, on a slow connection,
        or just want instant access later.</p>
</x-content-section>

<x-content-section :image="$img['fourk']" title="Save Videos in Original Quality, Up to 4K" :reverse="true">
    <p>From standard definition to Full HD and ultra-sharp 4K, you always get the best version the source offers.
        There's no re-compression and no quality loss — just clean, high-resolution files ready for offline viewing.</p>
</x-content-section>

<x-content-section :image="$img['browser']" title="Download Videos From Any Browser">
    <p>It works everywhere you do: Chrome, Firefox, Safari, Edge, Opera, and other Chromium browsers. There's nothing to
        install and no setup — open the site, paste a link, and download with fast, consistent performance.</p>
</x-content-section>

<x-content-section :image="$img['device']" title="Save Videos on Any Device" :reverse="true">
    <p>Android phone, iPhone, tablet, Windows PC, or Mac — SaveVideoFrom works the same on all of them. Paste your link,
        choose a format and quality, and download instantly, with smooth performance across every major browser.</p>
</x-content-section>

<x-content-section :image="$img['legal']" title="Is It Legal to Download Online Videos?">
    <p>It depends on how you use the content. Saving videos for personal, offline viewing, learning, or research is
        widely considered acceptable — but copyright still applies, and SaveVideoFrom does not support downloading
        copyrighted material for redistribution, re-uploads, or commercial use.</p>
    <p>Common, legitimate reasons people save videos include:</p>
    <ul class="list-disc list-inside space-y-1 text-sm">
        <li>Keeping educational videos and online-course material for later</li>
        <li>Watching offline while travelling or on a weak connection</li>
        <li>Reducing repeated mobile-data usage</li>
        <li>Saving public clips for personal research or study</li>
    </ul>
    <p>Please download only what you're authorised to keep, and respect each platform's terms and the rights of
        creators.</p>
</x-content-section>
