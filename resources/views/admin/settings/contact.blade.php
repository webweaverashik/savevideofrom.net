@extends('admin.layouts.app')
@section('title', 'Contact Info')

@section('content')
    <form method="POST" action="{{ route('admin.settings.contact.update') }}" class="max-w-2xl space-y-5">
        @csrf
        @php $field = 'w-full rounded-xl border border-gray-300 dark:border-white/10 bg-white dark:bg-gray-900 px-3 py-2.5 outline-none focus:ring-2 focus:ring-violet-500'; @endphp
        <div class="rounded-2xl border border-gray-200/70 dark:border-white/10 bg-white dark:bg-white/[0.03] p-6 space-y-5">
            <p class="text-sm text-gray-500">Shown on the public contact page. Leave any field blank to hide it.</p>
            <div><label class="block text-sm font-medium mb-1">Email</label><input type="email" name="contact_email"
                    value="{{ $s->get('contact_email') }}" class="{{ $field }}"></div>
            <div><label class="block text-sm font-medium mb-1">Phone</label><input type="text" name="contact_phone"
                    value="{{ $s->get('contact_phone') }}" class="{{ $field }}"></div>
            <div><label class="block text-sm font-medium mb-1">Address</label>
                <textarea name="contact_address" rows="2" class="{{ $field }}">{{ $s->get('contact_address') }}</textarea>
            </div>
        </div>
        <button
            class="rounded-xl bg-gradient-to-r from-violet-600 to-fuchsia-600 hover:from-violet-500 hover:to-fuchsia-500 text-white font-semibold px-6 py-2.5 transition">Save</button>
    </form>
@endsection
