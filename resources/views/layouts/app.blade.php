<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>QR code generator</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700;800&display=swap" rel="stylesheet">
    <style>body { font-family: 'Inter', sans-serif; }</style>
    <meta name="csrf-token" content="{{ csrf_token() }}">
</head>
<body class="bg-[#0B0F1A] min-h-screen text-gray-100 selection:bg-blue-500/30">
    <nav class="bg-[#0B0F1A]/80 backdrop-blur-xl border-b border-white/5 sticky top-0 z-50">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between h-16 items-center">
                <div class="flex items-center gap-3">
                    <div class="bg-gradient-to-br from-blue-500 to-indigo-600 p-2 rounded-xl shadow-lg shadow-blue-500/20">
                        <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v1m0 11v1m-5-5h1m11 0h1M4 12C4 7.58172 7.58172 4 12 4C16.4183 4 20 7.58172 20 12C20 16.4183 16.4183 20 12 20C7.58172 20 4 16.4183 4 12ZM15 12C15 13.6569 13.6568 15 12 15C10.3431 15 9 13.6569 9 12C9 10.3431 10.3431 9 12 9C13.6568 9 15 10.3431 15 12Z"/>
                        </svg>
                    </div>
                    <a href="/dashboard" class="font-black text-xl tracking-tighter text-white uppercase group">
                        QR code generator
                    </a>
                </div>
                <div class="flex items-center gap-6">
                    @auth
                        <a href="{{ route('profile') }}" class="hidden sm:flex flex-col items-end leading-none hover:opacity-80 transition-opacity">
                            <span class="text-[10px] font-bold text-gray-500 uppercase tracking-widest mb-0.5">User</span>
                            <span class="text-sm font-bold text-gray-300">{{ auth()->user()->name }}</span>
                        </a>
                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <button type="submit" class="bg-white/5 hover:bg-red-500/10 text-gray-400 hover:text-red-400 px-4 py-2 rounded-xl text-sm font-bold transition-all border border-white/5 hover:border-red-500/20 flex items-center gap-2 group">
                                <span>Sign Out</span>
                                <svg class="w-4 h-4 group-hover:translate-x-0.5 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/></svg>
                            </button>
                        </form>
                    @endauth
                </div>
            </div>
        </div>
    </nav>
    <main class="py-4">
        @yield('content')
    </main>
    @stack('scripts')
</body>
</html>
