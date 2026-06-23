@extends('admin.layouts.app')
@section('title', 'Edit: ' . $site->name)

@section('content')
    <a href="{{ route('admin.sites.index') }}" class="text-sm text-violet-600 dark:text-violet-400 hover:underline">← All
        sites</a>

    <form method="POST" action="{{ route('admin.sites.update', $site) }}" class="mt-4 max-w-2xl">
        @csrf @method('PUT')
        @include('admin.sites._form', ['site' => $site])
        <button
            class="mt-5 rounded-xl bg-gradient-to-r from-violet-600 to-fuchsia-600 hover:from-violet-500 hover:to-fuchsia-500 text-white font-semibold px-6 py-2.5 transition">Save
            changes</button>
    </form>
@endsection
