@extends('admin.layouts.app')
@section('title', $platform->name . ' Cookies')

@section('content')
    <a href="{{ route('admin.cookies.index') }}" class="text-sm text-violet-600 dark:text-violet-400 hover:underline">← All
        platforms</a>

    <div class="mt-4 grid lg:grid-cols-3 gap-6">
        {{-- Upload --}}
        <div class="rounded-2xl border border-gray-200/70 dark:border-white/10 bg-white dark:bg-white/[0.03] p-5">
            <h2 class="font-semibold mb-3">Upload cookies</h2>
            <form method="POST" action="{{ route('admin.cookies.store', $platform->slug) }}" enctype="multipart/form-data"
                class="space-y-3">
                @csrf
                <input type="file" name="cookies[]" accept=".txt" multiple required
                    class="block w-full text-sm text-gray-600 dark:text-gray-300 file:mr-3 file:rounded-lg file:border-0 file:bg-violet-600 file:text-white file:px-3 file:py-2 file:text-sm">
                @error('cookies')
                    <p class="text-sm text-red-500">{{ $message }}</p>
                @enderror
                @error('cookies.0')
                    <p class="text-sm text-red-500">{{ $message }}</p>
                @enderror
                <button
                    class="w-full rounded-xl bg-gradient-to-r from-violet-600 to-fuchsia-600 hover:from-violet-500 hover:to-fuchsia-500 text-white font-semibold py-2.5 transition">Upload</button>
                <p class="text-xs text-gray-500">Netscape format, .txt, up to 200 KB each. Stored privately and never shown
                    again.</p>
            </form>
        </div>

        {{-- Existing files --}}
        <div
            class="lg:col-span-2 rounded-2xl border border-gray-200/70 dark:border-white/10 bg-white dark:bg-white/[0.03] p-5">
            <h2 class="font-semibold mb-3">Cookie pool ({{ count($files) }})</h2>
            @if (empty($files))
                <p class="text-sm text-gray-500">No cookies yet. Public downloads still work — cookies are only needed for
                    private or age-restricted content.</p>
            @else
                <div class="divide-y divide-gray-100 dark:divide-white/5">
                    @foreach ($files as $f)
                        <div class="flex items-center justify-between py-3">
                            <div class="min-w-0">
                                <div class="font-mono text-sm truncate">{{ $f['name'] }}</div>
                                <div class="text-xs text-gray-500">
                                    {{ number_format($f['size'] / 1024, 1) }} KB · {{ $f['modified'] }}
                                    @unless ($f['valid'])
                                        <span class="text-red-500">· invalid</span>
                                    @endunless
                                </div>
                            </div>
                            <form method="POST"
                                action="{{ route('admin.cookies.destroy', [$platform->slug, $f['name']]) }}"
                                onsubmit="return confirm('Delete {{ $f['name'] }}?')">
                                @csrf @method('DELETE')
                                <button
                                    class="text-sm text-red-600 hover:text-red-700 px-3 py-1.5 rounded-lg hover:bg-red-50 dark:hover:bg-red-950/30">Delete</button>
                            </form>
                        </div>
                    @endforeach
                </div>
            @endif
        </div>
    </div>
@endsection
