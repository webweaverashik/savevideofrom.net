@extends('admin.layouts.app')
@section('title', 'Add Menu Item')

@section('content')
    <a href="{{ route('admin.menus.index') }}" class="text-sm text-violet-600 dark:text-violet-400 hover:underline">← All
        menus</a>
    <form method="POST" action="{{ route('admin.menus.store') }}" class="mt-4 max-w-2xl">
        @csrf
        @include('admin.menus._form', ['menu' => null, 'parents' => $parents])
        <button
            class="mt-5 rounded-xl bg-gradient-to-r from-violet-600 to-fuchsia-600 hover:from-violet-500 hover:to-fuchsia-500 text-white font-semibold px-6 py-2.5 transition">Add
            item</button>
    </form>
@endsection
