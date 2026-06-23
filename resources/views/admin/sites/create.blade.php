@extends('admin.layouts.app')
@section('title', 'Add Supported Site')

@section('content')
    <a href="{{ route('admin.sites.index') }}" class="text-sm text-violet-600 dark:text-violet-400 hover:underline">← All
        sites</a>

    <form method="POST" action="{{ route('admin.sites.store') }}" class="mt-4 max-w-2xl">
        @csrf
        @include('admin.sites._form', ['site' => null])
        <button
            class="mt-5 rounded-xl bg-gradient-to-r from-violet-600 to-fuchsia-600 hover:from-violet-500 hover:to-fuchsia-500 text-white font-semibold px-6 py-2.5 transition">Add
            site</button>
    </form>
@endsection
