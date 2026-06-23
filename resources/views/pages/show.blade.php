@extends('layouts.app')
@section('title', ($page->meta_title ?: $page->title) . ' | SaveVideoFrom.net')
@section('description', $page->meta_description ?: '')

@section('content')
    <section class="max-w-4xl mx-auto px-4 py-12">
        <x-breadcrumbs :items="[['Home', route('home')], [$page->title, null]]" />
        <h1 class="font-display text-3xl font-bold mt-6 mb-6">{{ $page->title }}</h1>
        <div class="page-content text-gray-700 dark:text-gray-300">{!! $page->body !!}</div>
    </section>
@endsection
