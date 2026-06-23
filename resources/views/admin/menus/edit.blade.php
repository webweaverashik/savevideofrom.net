@extends('admin.layouts.app')
@section('title', 'Edit: ' . $menu->label)

@section('content')
    <a href="{{ route('admin.menus.index') }}" class="text-sm text-violet-600 dark:text-violet-400 hover:underline">← All
        menus</a>
    <form method="POST" action="{{ route('admin.menus.update', $menu) }}" class="mt-4 max-w-2xl">
        @csrf @method('PUT')
        @include('admin.menus._form', ['menu' => $menu, 'parents' => $parents])
        <button
            class="mt-5 rounded-xl bg-gradient-to-r from-violet-600 to-fuchsia-600 hover:from-violet-500 hover:to-fuchsia-500 text-white font-semibold px-6 py-2.5 transition">Save
            changes</button>
    </form>
@endsection
