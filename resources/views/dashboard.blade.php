@extends('layouts.app')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-10">
    <div class="mb-12 flex flex-col md:flex-row md:items-end justify-between gap-6">
        <div>
            <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-blue-500/10 border border-blue-500/20 text-blue-400 text-[10px] font-bold uppercase tracking-widest mb-4">
                <span class="relative flex h-2 w-2">
                    <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-blue-400 opacity-75"></span>
                    <span class="relative inline-flex rounded-full h-2 w-2 bg-blue-500"></span>
                </span>
                Live System Overview
            </div>
            <h1 class="text-4xl font-black text-white tracking-tight">Dashboard</h1>
            <p class="text-gray-400 mt-2 font-medium">Manage your QR codes and shortened links.</p>
        </div>
        <div class="flex items-center gap-4">
            <div class="bg-white/5 border border-white/5 rounded-2xl px-6 py-3 flex items-center gap-8">
                <div class="text-center">
                    <span class="block text-[10px] font-bold text-gray-500 uppercase tracking-widest mb-0.5">Health</span>
                    <span class="text-sm font-black text-green-400 uppercase">Optimal</span>
                </div>
                <div class="w-px h-8 bg-white/5"></div>
                <div class="text-center">
                    <span class="block text-[10px] font-bold text-gray-500 uppercase tracking-widest mb-0.5">Uptime</span>
                    <span class="text-sm font-black text-white">99.9%</span>
                </div>
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-12 gap-8">
        <!-- Main Content -->
        <div class="lg:col-span-8 space-y-8">
            <div class="bg-[#111827] rounded-3xl shadow-2xl border border-white/5 overflow-hidden">
                <div class="px-8 py-6 border-b border-white/5 flex flex-col sm:flex-row items-center justify-between gap-4 bg-gradient-to-r from-transparent to-white/[0.02]">
                    <div class="flex items-center gap-3">
                        <div class="w-2 h-6 bg-blue-600 rounded-full"></div>
                        <h3 class="text-lg font-black text-white uppercase tracking-tighter">My QR Links</h3>
                    </div>
                    <div class="relative w-full sm:w-72 group">
                        <input type="text" id="search-links" 
                            class="w-full pl-11 pr-4 py-3 bg-white/5 border border-white/5 rounded-2xl text-sm text-gray-300 focus:bg-white/10 focus:ring-2 focus:ring-blue-500/50 focus:border-blue-500 outline-none transition-all" 
                            placeholder="Search your links...">
                        <div class="absolute left-4 top-3.5 text-gray-500 group-focus-within:text-blue-400 transition-colors">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                        </div>
                    </div>
                </div>
                
                <ul id="links-list" class="divide-y divide-white/[0.03] min-h-[300px]">
                    <div class="flex items-center justify-center py-24">
                        <div class="flex flex-col items-center gap-4">
                            <div class="w-12 h-12 border-4 border-blue-500/10 border-t-blue-500 rounded-full animate-spin"></div>
                            <span class="text-xs font-bold text-gray-500 uppercase tracking-widest">Loading links...</span>
                        </div>
                    </div>
                </ul>
            </div>

            <!-- Create Form Card -->
            <div class="bg-[#111827] rounded-3xl shadow-2xl border border-white/5 p-8 relative overflow-hidden">
                <div class="absolute top-0 right-0 w-64 h-64 bg-blue-600/5 blur-[100px] pointer-events-none"></div>
                <div class="flex items-center gap-3 mb-8">
                    <div class="bg-blue-500/10 p-2.5 rounded-xl text-blue-500">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                    </div>
                    <div>
                        <h3 class="text-xl font-black text-white uppercase tracking-tighter">Create New QR Link</h3>
                        <p class="text-xs text-gray-500 font-bold uppercase tracking-widest mt-0.5">Generate a shortened URL and QR code</p>
                    </div>
                </div>
                <form id="create-link-form" class="grid grid-cols-1 md:grid-cols-2 gap-6 relative z-10">
                    <div class="md:col-span-2">
                        <label class="block text-[10px] font-black text-gray-500 uppercase tracking-[0.2em] mb-2.5 ml-1">Destination URL</label>
                        <input type="url" name="original_url" 
                            class="w-full px-5 py-4 bg-white/5 border border-white/5 rounded-2xl text-white placeholder-gray-600 focus:bg-white/10 focus:ring-2 focus:ring-blue-500/50 outline-none transition-all shadow-inner" 
                            placeholder="https://example.com/your-destination" required>
                    </div>
                    <div>
                        <label class="block text-[10px] font-black text-gray-500 uppercase tracking-[0.2em] mb-2.5 ml-1">Custom Alias (Optional)</label>
                        <input type="text" name="custom_alias" 
                            class="w-full px-5 py-4 bg-white/5 border border-white/5 rounded-2xl text-white placeholder-gray-600 focus:bg-white/10 focus:ring-2 focus:ring-blue-500/50 outline-none transition-all shadow-inner" 
                            placeholder="my-link-123">
                    </div>
                    <div>
                        <label class="block text-[10px] font-black text-gray-500 uppercase tracking-[0.2em] mb-2.5 ml-1">Expiration Date (Optional)</label>
                        <input type="datetime-local" name="expires_at" 
                            class="w-full px-5 py-4 bg-white/5 border border-white/5 rounded-2xl text-white focus:bg-white/10 focus:ring-2 focus:ring-blue-500/50 outline-none transition-all shadow-inner [color-scheme:dark]">
                    </div>

                    <!-- Advanced QR Options -->
                    <div class="md:col-span-2 mt-4 pt-6 border-t border-white/5">
                        <h4 class="text-[10px] font-black text-blue-400 uppercase tracking-[0.2em] mb-6">Advanced QR Configuration</h4>
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                            <div>
                                <label class="block text-[10px] font-black text-gray-400 uppercase tracking-widest mb-2.5 ml-1">Access Password</label>
                                <input type="password" name="password" 
                                    class="w-full px-5 py-4 bg-white/5 border border-white/5 rounded-2xl text-white placeholder-gray-700 focus:bg-white/10 focus:ring-2 focus:ring-blue-500/50 outline-none transition-all shadow-inner text-sm" 
                                    placeholder="Optional lock">
                            </div>
                            <div>
                                <label class="block text-[10px] font-black text-gray-400 uppercase tracking-widest mb-2.5 ml-1">Foreground Color</label>
                                <div class="relative group">
                                    <input type="color" name="color" value="#000000"
                                        class="absolute inset-0 opacity-0 w-full h-full cursor-pointer">
                                    <div class="w-full px-5 py-4 bg-white/5 border border-white/5 rounded-2xl text-white flex items-center justify-between group-hover:bg-white/10 transition-all pointer-events-none">
                                        <span class="text-xs font-bold uppercase tracking-tight opacity-60">Select Color</span>
                                        <div id="fg-preview" class="w-5 h-5 rounded-lg border border-white/20 shadow-lg" style="background-color: #000000;"></div>
                                    </div>
                                </div>
                            </div>
                            <div>
                                <label class="block text-[10px] font-black text-gray-400 uppercase tracking-widest mb-2.5 ml-1">Background Color</label>
                                <div class="relative group">
                                    <input type="color" name="background_color" value="#ffffff"
                                        class="absolute inset-0 opacity-0 w-full h-full cursor-pointer">
                                    <div class="w-full px-5 py-4 bg-white/5 border border-white/5 rounded-2xl text-white flex items-center justify-between group-hover:bg-white/10 transition-all pointer-events-none">
                                        <span class="text-xs font-bold uppercase tracking-tight opacity-60">Select Color</span>
                                        <div id="bg-preview" class="w-5 h-5 rounded-lg border border-white/20 shadow-lg" style="background-color: #ffffff;"></div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="mt-6">
                            <label class="block text-[10px] font-black text-gray-400 uppercase tracking-widest mb-2.5 ml-1">QR Resolution (Size)</label>
                            <div class="flex items-center gap-4">
                                <input type="range" name="size" min="100" max="1000" step="50" value="300"
                                    class="flex-1 h-1.5 bg-white/5 rounded-lg appearance-none cursor-pointer accent-blue-500 outline-none"
                                    oninput="this.nextElementSibling.innerText = this.value + 'px'">
                                <span class="text-[10px] font-black text-blue-400 bg-blue-500/10 px-3 py-1.5 rounded-lg min-w-[50px] text-center border border-blue-500/20 uppercase tracking-widest">300px</span>
                            </div>
                        </div>
                    </div>

                    <div class="md:col-span-2 pt-4">
                        <button class="w-full bg-blue-600 hover:bg-blue-500 text-white font-black py-5 rounded-2xl shadow-xl shadow-blue-600/20 transition-all hover:-translate-y-0.5 active:translate-y-0 uppercase tracking-widest text-sm" type="submit">
                            Generate Link & QR Code
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <!-- Sidebar -->
        <div class="lg:col-span-4 space-y-8">
            <!-- Tokens Section -->
            @if(auth()->user()->is_partner)
            <div class="bg-[#111827] rounded-3xl shadow-2xl border border-white/5 p-8">
                <div class="flex items-center gap-3 mb-8">
                    <div class="bg-indigo-500/10 p-2.5 rounded-xl text-indigo-400">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 7a2 2 0 012 2m4 0a6 6 0 01-7.743 5.743L11 17H9v2H7v2H4a1 1 0 01-1-1v-2.586a1 1 0 01.293-.707l5.964-5.964A6 6 0 1121 9z"/></svg>
                    </div>
                    <div>
                        <h3 class="text-xl font-black text-white uppercase tracking-tighter">API Access</h3>
                        <p class="text-xs text-gray-500 font-bold uppercase tracking-widest mt-0.5">Manage Access Keys</p>
                    </div>
                </div>
                
                <ul id="tokens-list" class="space-y-4 mb-8">
                    <!-- Tokens will be loaded here -->
                </ul>
                
                <div class="pt-8 border-t border-white/5">
                    <form id="create-token-form" class="space-y-4">
                        <div>
                            <label class="block text-[9px] font-black text-gray-500 uppercase tracking-[0.2em] mb-2 ml-1">Key Identifier</label>
                            <input type="text" name="name" 
                                class="w-full px-4 py-3 bg-white/5 border border-white/5 rounded-2xl text-xs text-white placeholder-gray-600 focus:bg-white/10 focus:ring-2 focus:ring-indigo-500/50 transition outline-none shadow-inner" 
                                placeholder="External_Module_01" required>
                        </div>
                        <div>
                            <label class="block text-[9px] font-black text-gray-500 uppercase tracking-[0.2em] mb-2 ml-1">Description</label>
                            <input type="text" name="description" 
                                class="w-full px-4 py-3 bg-white/5 border border-white/5 rounded-2xl text-xs text-white placeholder-gray-600 focus:bg-white/10 focus:ring-2 focus:ring-indigo-500/50 transition outline-none shadow-inner" 
                                placeholder="Production API Instance">
                        </div>
                        <button class="w-full bg-white/10 hover:bg-white/20 text-white py-4 rounded-2xl text-xs font-black uppercase tracking-widest transition-all shadow-lg active:scale-95" type="submit">
                            Generate New API Key
                        </button>
                    </form>
                </div>
            </div>
            @else
            <div class="bg-gradient-to-br from-indigo-900/20 to-transparent rounded-3xl shadow-xl border border-indigo-500/10 p-8">
                <div class="flex items-center gap-3 mb-4">
                    <div class="bg-indigo-500/10 p-2.5 rounded-xl text-indigo-400">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/></svg>
                    </div>
                    <h3 class="text-lg font-black text-white uppercase tracking-tighter">Partner Program</h3>
                </div>
                <p class="text-sm text-gray-400 font-medium leading-relaxed mb-6">Partners get exclusive access to our API, allowing for automated QR code generation and advanced link tracking.</p>
                <div class="text-[10px] font-bold text-indigo-400 uppercase tracking-[0.2em]">Restricted Access</div>
            </div>
            @endif

            <!-- Download Tip Card -->
            <div class="bg-gradient-to-br from-blue-900 to-[#111827] rounded-3xl shadow-2xl p-8 border border-white/5 relative overflow-hidden group">
                <div class="relative z-10">
                    <div class="w-12 h-12 bg-white/5 backdrop-blur-md rounded-2xl flex items-center justify-center mb-6 border border-white/10 group-hover:rotate-12 transition-transform duration-500">
                        <svg class="w-6 h-6 text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a2 2 0 002 2h12a2 2 0 002-2v-1m-4-8l-4-4m0 0L8 8m4-4v12"/></svg>
                    </div>
                    <h4 class="font-black text-xl text-white uppercase tracking-tighter mb-2">QR Code Printing</h4>
                    <p class="text-blue-200/60 text-sm leading-relaxed font-medium">Download links support high-resolution SVG exports for professional print scaling.</p>
                </div>
                <div class="absolute -right-8 -bottom-8 opacity-5 group-hover:scale-110 group-hover:opacity-10 transition-all duration-700">
                    <svg class="w-48 h-48" fill="currentColor" viewBox="0 0 24 24"><path d="M3 3h6v6H3V3zm12 0h6v6h-6V3zM3 15h6v6H3v-6zm14 0h2v2h-2v-2zm0 4h2v2h-2v-2zm2-2h2v2h-2v-2zm0-4h2v2h-2v-2zm-2 0h2v2h-2v-2zM15 15h2v2h-2v-2zm0 4h2v2h-2v-2zm4-4h2v2h-2v-2z"/></svg>
                </div>
            </div>
        </div>
    </div>
</div>
        </div>
    </div>
</div>

<!-- Token Success Modal -->
<div id="token-modal" class="fixed inset-0 bg-gray-900/90 backdrop-blur-md z-[60] hidden flex items-center justify-center p-4">
    <div class="bg-white rounded-3xl shadow-2xl max-w-sm w-full p-8 relative overflow-hidden text-center">
        <div class="w-20 h-20 bg-green-100 text-green-600 rounded-full flex items-center justify-center mx-auto mb-6">
            <svg class="w-10 h-10" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
        </div>
        <h2 class="text-2xl font-black text-gray-900 mb-2">Key Generated!</h2>
        <p class="text-gray-500 text-sm mb-8 leading-relaxed">For your security, this key is only shown once. Copy it now and store it safely.</p>
        
        <div class="relative group mb-8">
            <div class="flex items-center gap-2 bg-gray-50 border border-gray-100 rounded-2xl p-4 font-mono text-sm overflow-hidden transition-all group-hover:bg-white group-hover:border-blue-100 group-hover:shadow-sm">
                <input type="password" id="new-token-value" readonly 
                    class="bg-transparent border-none outline-none w-full text-gray-800 font-bold tracking-tight">
                <button id="toggle-token-visibility" class="text-gray-400 hover:text-blue-600 transition-colors shrink-0">
                    <svg class="w-5 h-5" id="token-eye-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                </button>
            </div>
            <button id="copy-token-btn" class="absolute -top-3 -right-3 bg-blue-600 text-white p-2 rounded-xl shadow-lg hover:rotate-12 transition-transform active:scale-95">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 5H6a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2v-1M8 5a2 2 0 002 2h2a2 2 0 002-2M8 5a2 2 0 012-2h2a2 2 0 012 2m0 0h2a2 2 0 012 2v3m2 4H10m0 0l3-3m-3 3l3 3"/></svg>
            </button>
        </div>

        <button id="close-token-modal" class="w-full bg-gray-900 text-white font-bold py-4 rounded-2xl hover:bg-black transition-all">
            I've copied the key
        </button>
    </div>
</div>

<!-- Modal Fix: Simple Overlay -->
<div id="qr-modal" class="fixed inset-0 bg-gray-900/80 backdrop-blur-sm z-50 hidden flex items-center justify-center p-4">
    <div class="bg-white rounded-3xl shadow-2xl max-w-sm w-full p-8 relative overflow-hidden">
        <button id="close-qr-modal" class="absolute top-5 right-5 text-gray-400 hover:text-gray-900 bg-gray-100 hover:bg-gray-200 p-1.5 rounded-full transition">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
        </button>
        <div class="text-center">
            <h2 class="text-xl font-black text-gray-900 mb-6">QR Code Link</h2>
            <div id="qr-modal-content" class="flex justify-center items-center py-6 px-4 bg-gray-50 border border-gray-100 rounded-2xl min-h-[220px]"></div>
            <a id="qr-download-link" href="#" download class="inline-flex items-center justify-center mt-8 w-full bg-blue-600 text-white font-black py-4 rounded-2xl hover:bg-blue-700 shadow-xl shadow-blue-500/20 transition-all">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a2 2 0 002 2h12a2 2 0 002-2v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/></svg>
                Download SVG
            </a>
        </div>
    </div>
</div>

<!-- Toast Fix: Modern Pill -->
<div id="toast" class="fixed bottom-8 left-1/2 -translate-x-1/2 md:left-auto md:right-8 md:translate-x-0 bg-gray-900 text-white px-6 py-3.5 rounded-2xl shadow-2xl z-50 hidden transition-all flex items-center gap-3"></div>

@php
    $dashboardJsPath = public_path('js/dashboard.js');
    $dashboardJsVersion = file_exists($dashboardJsPath) ? filemtime($dashboardJsPath) : time();
@endphp
<script src="{{ asset('js/dashboard.js') }}?v={{ $dashboardJsVersion }}"></script>
@endsection
