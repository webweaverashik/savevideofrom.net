@extends('admin.layouts.app')
@section('title', 'Supported Sites')

@section('content')
    <div class="flex flex-wrap items-center justify-between gap-3 mb-5">
        <form method="GET" class="flex gap-2">
            <input type="text" name="q" value="{{ request('q') }}" placeholder="Search sites…"
                class="rounded-lg border border-gray-300 dark:border-white/10 bg-white dark:bg-gray-900 px-3 py-2 text-sm outline-none focus:ring-2 focus:ring-violet-500">
            <button class="rounded-lg bg-violet-600 hover:bg-violet-700 text-white text-sm px-4 py-2">Search</button>
        </form>
        <a href="{{ route('admin.sites.create') }}"
            class="rounded-lg bg-gradient-to-r from-violet-600 to-fuchsia-600 hover:from-violet-500 hover:to-fuchsia-500 text-white text-sm font-semibold px-4 py-2">+
            Add site</a>
    </div>

    <div class="rounded-2xl border border-gray-200/70 dark:border-white/10 bg-white dark:bg-white/[0.03] overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead class="text-left text-xs text-gray-500 border-b border-gray-200/70 dark:border-white/10">
                    <tr>
                        <th class="px-4 py-3">#</th>
                        <th class="px-4 py-3">Name</th>
                        <th class="px-4 py-3 hidden md:table-cell">Description</th>
                        <th class="px-4 py-3">Status</th>
                        <th class="px-4 py-3 text-right">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($sites as $site)
                        <tr class="border-b border-gray-100 dark:border-white/5">
                            <td class="px-4 py-3 text-gray-400">{{ $site->sort_order }}</td>
                            <td class="px-4 py-3 font-medium whitespace-nowrap">{{ $site->name }}</td>
                            <td class="px-4 py-3 hidden md:table-cell max-w-md truncate text-gray-500">
                                {{ $site->description }}</td>
                            <td class="px-4 py-3">
                                <span
                                    class="inline-block rounded-full px-2 py-0.5 text-xs font-medium {{ $site->is_active ? 'bg-emerald-100 text-emerald-700 dark:bg-emerald-500/15 dark:text-emerald-400' : 'bg-gray-100 text-gray-500 dark:bg-white/10 dark:text-gray-400' }}">
                                    {{ $site->is_active ? 'Active' : 'Hidden' }}
                                </span>
                            </td>
                            <td class="px-4 py-3 text-right whitespace-nowrap">
                                <a href="{{ route('admin.sites.edit', $site) }}"
                                    class="text-violet-600 dark:text-violet-400 hover:underline">Edit</a>
                                <form method="POST" action="{{ route('admin.sites.destroy', $site) }}" class="inline ml-2"
                                    onsubmit="return confirm('Delete “{{ $site->name }}”?')">
                                    @csrf @method('DELETE')
                                    <button class="text-red-600 hover:underline">Delete</button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-4 py-8 text-center text-gray-500">No sites found.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <div class="mt-4">{{ $sites->links() }}</div>
@endsection
