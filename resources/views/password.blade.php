@extends('layouts.app')

@section('content')
<div class="flex items-center justify-center min-h-[80vh] px-4">
    <div class="bg-[#111827] rounded-3xl shadow-2xl border border-white/5 p-8 relative overflow-hidden w-full max-w-md">
        <div class="absolute top-0 right-0 w-64 h-64 bg-blue-600/5 blur-[100px] pointer-events-none"></div>
        
        <div class="flex items-center justify-center gap-3 mb-8 relative z-10">
            <div class="bg-blue-500/10 p-2.5 rounded-xl text-blue-500">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/></svg>
            </div>
            <div>
                <h2 class="text-xl font-black text-white uppercase tracking-tighter">Password Required</h2>
                <p class="text-[10px] text-gray-500 font-bold uppercase tracking-widest mt-0.5 mt-1">Access Restricted</p>
            </div>
        </div>

        <div class="mb-8 text-center bg-white/5 border border-white/5 rounded-2xl p-4 relative z-10 text-sm">
            <div class="flex flex-col gap-2">
                <div class="flex justify-between items-center text-gray-400">
                    <span class="text-[10px] font-black uppercase tracking-widest">Short Link</span>
                    <span class="font-bold text-blue-400">{{ $short_code ?? '' }}</span>
                </div>
                <div class="h-px bg-white/5 w-full"></div>
                <div class="flex flex-col items-start gap-1">
                    <span class="text-[10px] font-black uppercase tracking-widest text-gray-400">Destination</span>
                    <span class="font-medium text-gray-300 truncate w-full flex-1 text-left" title="{{ $original_url }}">{{ $original_url ? Str::limit($original_url, 40) : '' }}</span>
                </div>
            </div>
        </div>

        @if(isset($error) && $error)
            <div class="mb-6 bg-red-500/10 border border-red-500/20 text-red-500/90 text-sm font-bold px-4 py-3 rounded-xl relative z-10 flex items-center gap-2">
                <svg class="w-4 h-4 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                <span>{{ $error }}</span>
            </div>
        @endif

        <form method="POST" action="" id="password-form" class="relative z-10 group space-y-6">
            @csrf
            <div>
                <label for="password" class="block text-[10px] font-black text-gray-500 uppercase tracking-widest mb-2.5 ml-1">Access Password</label>
                <div class="relative group">
                    <input type="password" name="password" id="password" 
                        class="w-full pl-5 pr-12 py-4 bg-white/5 border border-white/5 rounded-2xl text-white placeholder-gray-600 focus:bg-white/10 focus:ring-2 focus:ring-blue-500/50 outline-none transition-all shadow-inner text-sm" 
                        placeholder="Enter password" required autofocus autocomplete="current-password">
                    <button type="button" id="toggle-password" class="absolute right-4 top-1/2 -translate-y-1/2 text-gray-500 hover:text-blue-400 transition-colors">
                        <svg class="w-5 h-5" id="pwd-eye" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                    </button>
                </div>
            </div>
            
            <button id="submit-btn" type="submit" class="w-full bg-blue-600 hover:bg-blue-500 text-white font-black py-4 rounded-2xl shadow-xl shadow-blue-600/20 transition-all hover:-translate-y-0.5 active:translate-y-0 uppercase tracking-widest text-sm flex items-center justify-center gap-2">
                <span>Unlock Link</span>
                <svg class="w-4 h-4 ml-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
            </button>
        </form>
        
        <div class="mt-8 text-center relative z-10">
            <a href="/" class="inline-flex items-center gap-1.5 text-xs font-bold text-gray-500 hover:text-blue-400 uppercase tracking-widest transition-colors">
                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
                Back to Home
            </a>
        </div>
    </div>
</div>

@push('scripts')
<script>
    document.getElementById('password').focus();
    const eyeIcon = `<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>`;
    const eyeOffIcon = `<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l18 18" />`;
    
    document.getElementById('toggle-password').onclick = function() {
        const input = document.getElementById('password');
        const iconSvg = document.getElementById('pwd-eye');
        if (input.type === 'password') {
            input.type = 'text';
            iconSvg.innerHTML = eyeOffIcon;
        } else {
            input.type = 'password';
            iconSvg.innerHTML = eyeIcon;
        }
    };
    
    document.getElementById('password-form').onsubmit = function() {
        const btn = document.getElementById('submit-btn');
        btn.disabled = true;
        btn.innerHTML = `<svg class="animate-spin -ml-1 mr-3 h-4 w-4 text-white" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg> <span>Authorizing...</span>`;
        btn.classList.add('opacity-75', 'cursor-not-allowed');
    };
</script>
@endpush
@endsection
