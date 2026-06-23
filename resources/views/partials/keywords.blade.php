<section class="reveal-on-scroll max-w-5xl mx-auto px-4 pb-16">
    <h2 class="font-display text-lg font-semibold text-center mb-5 text-gray-500">Popular searches</h2>
    <div class="flex flex-wrap justify-center gap-2">
        @foreach (['video downloader', 'online video downloader', 'download video from link', 'save video from any website', 'mp4 downloader', '4k video downloader', 'mp3 audio downloader', 'hd video download', 'download private video', 'free video downloader', 'no watermark downloader', 'playlist downloader'] as $kw)
            <span
                class="rounded-full border border-gray-200/70 dark:border-white/10 bg-white dark:bg-white/[0.03] px-3 py-1.5 text-xs text-gray-500">#{{ $kw }}</span>
        @endforeach
    </div>
</section>
