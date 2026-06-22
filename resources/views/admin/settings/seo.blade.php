@extends('admin.layouts.app')
@section('title', 'SEO Settings')

@section('content')
    <form method="POST" action="{{ route('admin.settings.seo.update') }}" class="max-w-2xl space-y-5">
        @csrf
        <div class="rounded-2xl border border-gray-200/70 dark:border-white/10 bg-white dark:bg-white/[0.03] p-6 space-y-5">
            @foreach ([['default_meta_title', 'Default meta title', 'text'], ['default_meta_description', 'Default meta description', 'textarea'], ['default_meta_keywords', 'Default meta keywords', 'text'], ['og_image', 'Open Graph image URL', 'text'], ['google_analytics_id', 'Google Analytics ID', 'text'], ['google_site_verification', 'Google site verification code', 'text']] as [$key, $label, $type])
                <div>
                    <label class="block text-sm font-medium mb-1">{{ $label }}</label>
                    @if ($type === 'textarea')
                        <textarea name="{{ $key }}" rows="3"
                            class="w-full rounded-xl border border-gray-300 dark:border-white/10 bg-white dark:bg-gray-900 px-3 py-2.5 outline-none focus:ring-2 focus:ring-violet-500">{{ old($key, $s->get($key)) }}</textarea>
                    @else
                        <input type="text" name="{{ $key }}" value="{{ old($key, $s->get($key)) }}"
                            class="w-full rounded-xl border border-gray-300 dark:border-white/10 bg-white dark:bg-gray-900 px-3 py-2.5 outline-none focus:ring-2 focus:ring-violet-500">
                    @endif
                    @error($key)
                        <p class="text-sm text-red-500 mt-1">{{ $message }}</p>
                    @enderror
                </div>
            @endforeach
        </div>
        <button
            class="rounded-xl bg-gradient-to-r from-violet-600 to-fuchsia-600 hover:from-violet-500 hover:to-fuchsia-500 text-white font-semibold px-6 py-2.5 transition">Save
            settings</button>
    </form>
@endsection
