@extends('admin.layouts.app')
@section('title', 'Ads & AdSense')

@section('content')
    <form method="POST" action="{{ route('admin.settings.ads.update') }}" class="max-w-2xl space-y-5">
        @csrf
        <div class="rounded-2xl border border-gray-200/70 dark:border-white/10 bg-white dark:bg-white/[0.03] p-6 space-y-5">
            <label class="flex items-center gap-3">
                <input type="checkbox" name="ads_enabled" value="1" @checked(old('ads_enabled', $s->get('ads_enabled', false)))>
                <span class="text-sm font-medium">Enable ads site-wide</span>
            </label>
            <div>
                <label class="block text-sm font-medium mb-1">AdSense publisher ID</label>
                <input type="text" name="adsense_client" value="{{ old('adsense_client', $s->get('adsense_client')) }}"
                    placeholder="ca-pub-XXXXXXXXXXXXXXXX"
                    class="w-full rounded-xl border border-gray-300 dark:border-white/10 bg-white dark:bg-gray-900 px-3 py-2.5 outline-none focus:ring-2 focus:ring-violet-500">
                <p class="text-xs text-gray-500 mt-1">Loads the AdSense Auto Ads script when set and ads are enabled.</p>
            </div>
            @foreach ([['ad_header', 'Header ad code'], ['ad_in_content', 'In-content ad code'], ['ad_sidebar', 'Sidebar ad code'], ['ad_footer', 'Footer ad code']] as [$key, $label])
                <div>
                    <label class="block text-sm font-medium mb-1">{{ $label }}</label>
                    <textarea name="{{ $key }}" rows="3" placeholder="<ins class=&quot;adsbygoogle&quot; ...></ins>"
                        class="w-full font-mono text-xs rounded-xl border border-gray-300 dark:border-white/10 bg-white dark:bg-gray-900 px-3 py-2.5 outline-none focus:ring-2 focus:ring-violet-500">{{ old($key, $s->get($key)) }}</textarea>
                </div>
            @endforeach
            <p class="text-xs text-gray-500">Paste the HTML/script from your ad provider. Code is rendered as-is on the
                site.</p>
        </div>
        <button
            class="rounded-xl bg-gradient-to-r from-violet-600 to-fuchsia-600 hover:from-violet-500 hover:to-fuchsia-500 text-white font-semibold px-6 py-2.5 transition">Save
            settings</button>
    </form>
@endsection
