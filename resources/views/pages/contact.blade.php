@extends('layouts.app')
@section('title', 'Contact Us | SaveVideoFrom.net')

@section('content')
    <section class="max-w-4xl mx-auto px-4 py-12">
        <x-breadcrumbs :items="[['Home', route('home')], ['Contact', null]]" />
        <h1 class="font-display text-3xl font-bold mt-6 mb-4">{{ $page?->title ?? 'Contact Us' }}</h1>
        @if ($page?->body)
            <div class="page-content text-gray-700 dark:text-gray-300 mb-8">{!! $page->body !!}</div>
        @endif

        @if (session('contact_success'))
            <div
                class="mb-6 rounded-xl border border-emerald-300 bg-emerald-50 dark:bg-emerald-950/30 dark:border-emerald-900 text-emerald-700 dark:text-emerald-300 px-4 py-3 text-sm">
                {{ session('contact_success') }}</div>
        @endif

        <div class="grid md:grid-cols-3 gap-8">
            <div class="md:col-span-2">
                @if ($errors->any())
                    <div
                        class="mb-4 rounded-xl border border-red-300 bg-red-50 dark:bg-red-950/30 dark:border-red-900 text-red-700 dark:text-red-300 px-4 py-3 text-sm">
                        <ul class="list-disc list-inside">
                            @foreach ($errors->all() as $e)
                                <li>{{ $e }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif
                <form method="POST" action="{{ route('contact.submit') }}" class="space-y-4">
                    @csrf
                    @php $f = 'w-full rounded-xl border border-gray-300 dark:border-white/10 bg-white dark:bg-gray-900 px-4 py-3 outline-none focus:ring-2 focus:ring-violet-500'; @endphp
                    <div class="grid sm:grid-cols-2 gap-4">
                        <input type="text" name="name" value="{{ old('name') }}" placeholder="Your name"
                            class="{{ $f }}">
                        <input type="email" name="email" value="{{ old('email') }}" placeholder="Your email"
                            class="{{ $f }}">
                    </div>
                    <input type="text" name="subject" value="{{ old('subject') }}" placeholder="Subject (optional)"
                        class="{{ $f }}">
                    <textarea name="message" rows="6" placeholder="Your message" class="{{ $f }}">{{ old('message') }}</textarea>
                    <button
                        class="rounded-xl bg-gradient-to-r from-violet-600 to-fuchsia-600 hover:from-violet-500 hover:to-fuchsia-500 text-white font-semibold px-6 py-3 transition">Send
                        message</button>
                </form>
            </div>
            <div class="space-y-4 text-sm">
                @if ($email)
                    <div>
                        <p class="font-semibold">Email</p><a href="mailto:{{ $email }}"
                            class="text-violet-600 dark:text-violet-400 break-all">{{ $email }}</a>
                    </div>
                @endif
                @if ($phone)
                    <div>
                        <p class="font-semibold">Phone</p>
                        <p class="text-gray-500">{{ $phone }}</p>
                    </div>
                @endif
                @if ($address)
                    <div>
                        <p class="font-semibold">Address</p>
                        <p class="text-gray-500 whitespace-pre-line">{{ $address }}</p>
                    </div>
                @endif
            </div>
        </div>
    </section>
@endsection
