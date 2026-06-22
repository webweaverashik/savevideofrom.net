<!DOCTYPE html>
<html lang="en" class="dark">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Admin Login · SaveVideoFrom.net</title>
    @vite(['resources/css/app.css'])
</head>
<body class="min-h-screen flex items-center justify-center bg-[#0B0B12] text-gray-100 antialiased px-4">
    <div class="w-full max-w-sm">
        <h1 class="text-center font-bold text-xl mb-6">SaveVideoFrom<span class="text-gradient">.net</span> Admin</h1>
        <form method="POST" action="{{ route('admin.login.attempt') }}" class="rounded-2xl border border-white/10 bg-white/[0.03] p-6 space-y-4">
            @csrf
            @error('email')<p class="text-sm text-red-400">{{ $message }}</p>@enderror
            <div>
                <label class="block text-sm mb-1">Email</label>
                <input name="email" type="email" value="{{ old('email') }}" required autofocus
                    class="w-full rounded-xl border border-white/10 bg-gray-900 px-3 py-2.5 outline-none focus:ring-2 focus:ring-violet-500">
            </div>
            <div>
                <label class="block text-sm mb-1">Password</label>
                <input name="password" type="password" required
                    class="w-full rounded-xl border border-white/10 bg-gray-900 px-3 py-2.5 outline-none focus:ring-2 focus:ring-violet-500">
            </div>
            <label class="flex items-center gap-2 text-sm"><input type="checkbox" name="remember"> Remember me</label>
            <button class="w-full rounded-xl bg-gradient-to-r from-violet-600 to-fuchsia-600 hover:from-violet-500 hover:to-fuchsia-500 text-white font-semibold py-2.5 transition">Log in</button>
        </form>
    </div>
</body>
</html>