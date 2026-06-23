@extends('admin.layouts.app')
@section('title', 'Message')

@section('content')
    <a href="{{ route('admin.messages.index') }}" class="text-sm text-violet-600 dark:text-violet-400 hover:underline">← All
        messages</a>
    <div class="mt-4 max-w-2xl rounded-2xl border border-gray-200/70 dark:border-white/10 bg-white dark:bg-white/[0.03] p-6">
        <div class="flex items-start justify-between gap-3">
            <div>
                <p class="font-semibold">{{ $message->name }}</p>
                <a href="mailto:{{ $message->email }}"
                    class="text-sm text-violet-600 dark:text-violet-400">{{ $message->email }}</a>
            </div>
            <span class="text-xs text-gray-400">{{ $message->created_at->format('M j, Y g:i A') }}</span>
        </div>
        @if ($message->subject)
            <p class="mt-4 font-medium">{{ $message->subject }}</p>
        @endif
        <p class="mt-3 text-gray-700 dark:text-gray-300 whitespace-pre-line leading-relaxed">{{ $message->message }}</p>
        <div class="mt-6 flex gap-3">
            <a href="mailto:{{ $message->email }}?subject={{ urlencode('Re: ' . ($message->subject ?: 'Your message')) }}"
                class="rounded-xl bg-violet-600 hover:bg-violet-700 text-white text-sm font-semibold px-4 py-2">Reply by
                email</a>
            <form method="POST" action="{{ route('admin.messages.destroy', $message) }}"
                onsubmit="return confirm('Delete this message?')">@csrf @method('DELETE')<button
                    class="rounded-xl border border-red-300 dark:border-red-900 text-red-600 text-sm font-semibold px-4 py-2">Delete</button>
            </form>
        </div>
    </div>
@endsection
