@extends('admin.layouts.app')
@section('title', 'Menus')

@section('content')
    <div class="flex justify-end mb-5">
        <a href="{{ route('admin.menus.create') }}"
            class="rounded-lg bg-gradient-to-r from-violet-600 to-fuchsia-600 hover:from-violet-500 hover:to-fuchsia-500 text-white text-sm font-semibold px-4 py-2">+
            Add menu item</a>
    </div>

    <div class="grid lg:grid-cols-2 gap-6">
        @foreach (['header' => $header, 'footer' => $footer] as $location => $items)
            <div class="rounded-2xl border border-gray-200/70 dark:border-white/10 bg-white dark:bg-white/[0.03] p-5">
                <h2 class="font-semibold mb-4 capitalize">{{ $location }} menu</h2>
                <div class="space-y-1">
                    @forelse ($items as $item)
                        @include('admin.menus._row', ['item' => $item, 'child' => false])
                        @foreach ($item->children as $c)
                            @include('admin.menus._row', ['item' => $c, 'child' => true])
                        @endforeach
                    @empty
                        <p class="text-sm text-gray-500">No items yet.</p>
                    @endforelse
                </div>
            </div>
        @endforeach
    </div>
@endsection
