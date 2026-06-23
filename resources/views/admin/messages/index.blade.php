@extends('admin.layouts.app')
@section('title', 'Contact Messages')

@section('content')
    <div class="rounded-2xl border border-gray-200/70 dark:border-white/10 bg-white dark:bg-white/[0.03] overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead class="text-left text-xs text-gray-500 border-b border-gray-200/70 dark:border-white/10">
                    <tr>
                        <th class="px-4 py-3">From</th>
                        <th class="px-4 py-3 hidden sm:table-cell">Subject</th>
                        <th class="px-4 py-3">When</th>
                        <th class="px-4 py-3 text-right">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($messages as $m)
                        <tr class="border-b border-gray-100 dark:border-white/5 {{ $m->is_read ? '' : 'font-semibold' }}">
                            <td class="px-4 py-3">
                                @unless ($m->is_read)
                                    <span class="inline-block w-2 h-2 rounded-full bg-violet-500 mr-1 align-middle"></span>
                                @endunless
                                {{ $m->name }} <span
                                    class="block text-xs font-normal text-gray-400">{{ $m->email }}</span>
                            </td>
                            <td class="px-4 py-3 hidden sm:table-cell text-gray-500 max-w-xs truncate">
                                {{ $m->subject ?: '—' }}</td>
                            <td class="px-4 py-3 text-gray-500 whitespace-nowrap">{{ $m->created_at->diffForHumans() }}</td>
                            <td class="px-4 py-3 text-right whitespace-nowrap">
                                <a href="{{ route('admin.messages.show', $m) }}"
                                    class="text-violet-600 dark:text-violet-400 hover:underline">View</a>
                                <form method="POST" action="{{ route('admin.messages.destroy', $m) }}" class="inline ml-2"
                                    onsubmit="return confirm('Delete this message?')">@csrf @method('DELETE')<button
                                        class="text-red-600 hover:underline">Delete</button></form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="px-4 py-8 text-center text-gray-500">No messages yet.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
    <div class="mt-4">{{ $messages->links() }}</div>
@endsection
