@extends('layouts.app')

@section('content')
<div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
    <div class="mb-10">
        <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-indigo-500/10 border border-indigo-500/20 text-indigo-400 text-[10px] font-bold uppercase tracking-widest mb-4">
            Security & Identity
        </div>
        <h1 class="text-4xl font-black text-white tracking-tight">Account Settings</h1>
        <p class="text-gray-400 mt-2 font-medium">Manage your personal information and partner status.</p>
    </div>

    @if(session('success'))
    <div class="mb-8 p-4 bg-green-500/10 border border-green-500/20 rounded-2xl text-green-400 text-sm font-bold flex items-center gap-3">
        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
        {{ session('success') }}
    </div>
    @endif

    <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
        <!-- Sidebar Navigation -->
        <div class="space-y-2">
            <button class="w-full text-left px-5 py-4 bg-white/5 border border-white/10 rounded-2xl text-white font-black uppercase tracking-tighter text-sm flex items-center justify-between group">
                Profile Overview
                <svg class="w-4 h-4 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
            </button>
            <div class="p-6 bg-blue-600/5 rounded-3xl border border-blue-500/10 mt-6">
                <h4 class="text-[10px] font-black text-blue-400 uppercase tracking-widest mb-3">Statistics</h4>
                <div class="space-y-4">
                    <div>
                        <span class="block text-[9px] font-bold text-gray-500 uppercase tracking-widest">Member Since</span>
                        <span class="text-sm font-black text-white">{{ auth()->user()->created_at->format('M Y') }}</span>
                    </div>
                    <div>
                        <span class="block text-[9px] font-bold text-gray-500 uppercase tracking-widest">Account Type</span>
                        <span class="text-sm font-black {{ auth()->user()->is_partner ? 'text-indigo-400' : 'text-gray-400' }} uppercase">
                            {{ auth()->user()->is_partner ? 'Partner' : 'Standard' }}
                        </span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Main Form Content -->
        <div class="md:col-span-2">
            <div class="bg-[#111827] rounded-3xl shadow-2xl border border-white/5 overflow-hidden">
                <div class="p-8 border-b border-white/5 bg-gradient-to-r from-transparent to-white/[0.02]">
                    <h3 class="text-lg font-black text-white uppercase tracking-tighter">Personal Information</h3>
                </div>
                
                <form action="{{ route('profile.update') }}" method="POST" class="p-8 space-y-6">
                    @csrf
                    @method('PUT')

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label class="block text-[10px] font-black text-gray-500 uppercase tracking-[0.2em] mb-2.5 ml-1">Full Name</label>
                            <input type="text" name="name" value="{{ old('name', auth()->user()->name) }}"
                                class="w-full px-5 py-3.5 bg-white/5 border border-white/5 rounded-2xl text-white placeholder-gray-600 focus:bg-white/10 focus:ring-2 focus:ring-blue-500/50 outline-none transition-all shadow-inner" 
                                required>
                            @error('name') <span class="text-red-500 text-[10px] mt-1 ml-1 font-bold uppercase">{{ $message }}</span> @enderror
                        </div>

                        <div>
                            <label class="block text-[10px] font-black text-gray-500 uppercase tracking-[0.2em] mb-2.5 ml-1">Email Address</label>
                            <input type="email" name="email" value="{{ old('email', auth()->user()->email) }}"
                                class="w-full px-5 py-3.5 bg-white/5 border border-white/5 rounded-2xl text-white placeholder-gray-600 focus:bg-white/10 focus:ring-2 focus:ring-blue-500/50 outline-none transition-all shadow-inner" 
                                required>
                            @error('email') <span class="text-red-500 text-[10px] mt-1 ml-1 font-bold uppercase">{{ $message }}</span> @enderror
                        </div>
                    </div>

                    <div class="pt-4 border-t border-white/5">
                        <div class="flex items-center gap-4 mb-6 p-4 bg-white/5 rounded-2xl border border-white/5">
                            <div class="flex-shrink-0 w-10 h-10 bg-indigo-500/10 rounded-xl flex items-center justify-center text-indigo-400">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/></svg>
                            </div>
                            <div>
                                <h4 class="text-xs font-black text-white uppercase tracking-tighter">Partner Program</h4>
                                <p class="text-[10px] text-gray-500 font-bold uppercase tracking-widest mt-0.5">Unlock API access & link tracking</p>
                            </div>
                            <div class="ml-auto">
                                <label class="relative inline-flex items-center cursor-pointer">
                                    <input type="checkbox" name="is_partner" value="1" class="sr-only peer" {{ auth()->user()->is_partner ? 'checked' : '' }}>
                                    <div class="w-11 h-6 bg-white/10 peer-focus:outline-none rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-indigo-600"></div>
                                </label>
                            </div>
                        </div>
                    </div>

                    <div class="pt-6">
                        <button type="submit" class="w-full bg-blue-600 hover:bg-blue-500 text-white font-black py-4 rounded-2xl shadow-xl shadow-blue-600/20 transition-all hover:-translate-y-0.5 active:translate-y-0 uppercase tracking-widest text-xs">
                            Update Profile Configuration
                        </button>
                    </div>
                </form>
            </div>

            <!-- Password Change Section (Optional but good for completeness) -->
            <div class="mt-8 bg-[#111827] rounded-3xl shadow-2xl border border-white/5 overflow-hidden opacity-60 hover:opacity-100 transition-opacity duration-300">
                <div class="p-8">
                    <h3 class="text-lg font-black text-white uppercase tracking-tighter mb-1">Security Credentials</h3>
                    <p class="text-xs text-gray-500 font-bold uppercase tracking-widest">Update your login password</p>
                    
                    <form action="{{ route('profile.password') }}" method="POST" class="mt-6 grid grid-cols-1 md:grid-cols-2 gap-6">
                        @csrf
                        @method('PUT')
                        <div>
                            <label class="block text-[10px] font-black text-gray-500 uppercase tracking-[0.2em] mb-2.5 ml-1">New Password</label>
                            <input type="password" name="password" 
                                class="w-full px-5 py-3.5 bg-white/5 border border-white/5 rounded-2xl text-white placeholder-gray-600 focus:bg-white/10 focus:ring-2 focus:ring-red-500/50 outline-none transition-all shadow-inner" 
                                placeholder="••••••••">
                        </div>
                        <div>
                            <label class="block text-[10px] font-black text-gray-500 uppercase tracking-[0.2em] mb-2.5 ml-1">Confirm Update</label>
                            <input type="password" name="password_confirmation" 
                                class="w-full px-5 py-3.5 bg-white/5 border border-white/5 rounded-2xl text-white placeholder-gray-600 focus:bg-white/10 focus:ring-2 focus:ring-red-500/50 outline-none transition-all shadow-inner" 
                                placeholder="••••••••">
                        </div>
                        <div class="md:col-span-2">
                            <button type="submit" class="w-full bg-white/5 hover:bg-white/10 text-white font-black py-4 rounded-2xl transition-all uppercase tracking-widest text-xs border border-white/5">
                                Upgrade Security Token
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
