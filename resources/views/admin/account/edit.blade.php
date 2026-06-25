@extends('admin.layouts.app')
@section('title', 'My Account')

@section('content')
    @php $field = 'w-full rounded-xl border border-gray-300 dark:border-white/10 bg-white dark:bg-gray-900 px-3 py-2.5 outline-none focus:ring-2 focus:ring-violet-500'; @endphp

    @if ($errors->any())
        <div
            class="mb-5 rounded-xl border border-red-300 bg-red-50 dark:bg-red-950/30 dark:border-red-900 text-red-700 dark:text-red-300 px-4 py-3 text-sm">
            <ul class="list-disc list-inside">
                @foreach ($errors->all() as $e)
                    <li>{{ $e }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div class="grid lg:grid-cols-2 gap-6 max-w-4xl">
        <form method="POST" action="{{ route('admin.account.update') }}"
            class="rounded-2xl border border-gray-200/70 dark:border-white/10 bg-white dark:bg-white/[0.03] p-6 space-y-4">
            @csrf @method('PUT')
            <h2 class="font-semibold">Profile</h2>
            <div><label class="block text-sm font-medium mb-1">Name</label><input type="text" name="name"
                    value="{{ old('name', $user->name) }}" class="{{ $field }}"></div>
            <div><label class="block text-sm font-medium mb-1">Email</label><input type="email" name="email"
                    value="{{ old('email', $user->email) }}" class="{{ $field }}"></div>
            <button
                class="inline-flex items-center justify-center rounded-xl bg-violet-600 hover:bg-violet-700 text-white text-sm font-semibold px-6 py-2.5 transition">Save
                profile</button>
        </form>

        <form method="POST" action="{{ route('admin.account.password') }}"
            class="rounded-2xl border border-gray-200/70 dark:border-white/10 bg-white dark:bg-white/[0.03] p-6 space-y-4">
            @csrf @method('PUT')
            <h2 class="font-semibold">Change password</h2>
            <div><label class="block text-sm font-medium mb-1">Current password</label><input type="password"
                    name="current_password" class="{{ $field }}"></div>
            <div><label class="block text-sm font-medium mb-1">New password</label><input type="password" name="password"
                    class="{{ $field }}"></div>
            <div><label class="block text-sm font-medium mb-1">Confirm new password</label><input type="password"
                    name="password_confirmation" class="{{ $field }}"></div>
            <button
                class="inline-flex items-center justify-center rounded-xl bg-gradient-to-r from-violet-600 to-fuchsia-600 hover:from-violet-500 hover:to-fuchsia-500 text-white text-sm font-semibold px-6 py-2.5 transition">Change
                password</button>
        </form>
    </div>
@endsection
