const csrfToken = document.querySelector('meta[name="csrf-token"]')?.content;

window.toggleTokenRow = function(id) {
    const input = document.getElementById(`token-input-${id}`);
    const eyeIcon = document.getElementById(`token-eye-${id}`);
    if (input.style.webkitTextSecurity === 'disc') {
        input.style.webkitTextSecurity = 'none';
        eyeIcon.innerHTML = `<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l18 18"/>`;
    } else {
        input.style.webkitTextSecurity = 'disc';
        eyeIcon.innerHTML = `<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>`;
    }
};

document.addEventListener('DOMContentLoaded', async function() {
    const csrfMark = document.querySelector('meta[name="csrf-token"]');
    const globalHeaders = {
        'Accept': 'application/json',
        'X-Requested-With': 'XMLHttpRequest',
        ...(csrfMark ? { 'X-CSRF-TOKEN': csrfMark.content } : {})
    };
    window.globalHeaders = globalHeaders;

    async function fetchLinks() {
        const linksRes = await fetch('/api/links', { credentials: 'same-origin',  headers: globalHeaders,  });
        const linksData = await linksRes.json();
        const linksList = document.getElementById('links-list');
        
        if (linksData.data && linksData.data.length) {
            linksList.innerHTML = linksData.data.map(link => `
                <li class="group hover:bg-white/[0.02] transition-all duration-300">
                    <div class="px-8 py-6">
                        <div class="flex items-start justify-between gap-6 text-left">
                            <div class="flex-1 min-w-0">
                                <div class="flex items-center gap-3 mb-2">
                                    <span class="bg-blue-500/10 text-blue-400 text-[10px] font-black px-2 py-0.5 rounded uppercase tracking-widest border border-blue-500/20">
                                        /l/${link.short_code}
                                    </span>
                                    <span class="text-[10px] font-bold text-gray-500 uppercase tracking-widest">
                                        ${new Date(link.created_at).toLocaleDateString()}
                                    </span>
                                </div>
                                <h4 class="text-lg font-black text-white truncate group-hover:text-blue-400 transition-colors">
                                    ${new URL(link.original_url).hostname}
                                </h4>
                                <p class="text-xs text-gray-500 truncate mt-1 font-medium font-mono">${link.original_url}</p>
                                <div class="flex items-center gap-6 mt-4">
                                    <div class="flex items-center gap-2">
                                        <div class="p-1.5 rounded-lg bg-emerald-500/10 text-emerald-500">
                                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                                        </div>
                                        <div>
                                            <span class="block text-[10px] font-black text-gray-500 uppercase tracking-tighter leading-none">Scans</span>
                                            <span class="text-sm font-black text-white">${link.total_scans || 0}</span>
                                        </div>
                                    </div>
                                    <div class="w-px h-6 bg-white/5"></div>
                                    <div class="flex items-center gap-2">
                                        <div class="p-1.5 rounded-lg bg-blue-500/10 text-blue-500">
                                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/></svg>
                                        </div>
                                        <div>
                                            <span class="block text-[10px] font-black text-gray-500 uppercase tracking-tighter leading-none">Short URL</span>
                                            <a href="${window.location.origin}/l/${link.short_code}" target="_blank" class="text-xs font-bold text-blue-400 hover:underline">
                                                ${window.location.origin.replace(/^https?:\/\//, '')}/l/${link.short_code}
                                            </a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="flex flex-col gap-2">
                                <button onclick='window.showQrModal("${link.qr_code_download_url}")' class="p-3 bg-white/5 hover:bg-white/10 text-gray-400 hover:text-white rounded-2xl transition-all border border-white/5">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v1m0 11v1m-5-5h1m11 0h1M4 12C4 7.58172 7.58172 4 12 4C16.4183 4 20 7.58172 20 12C20 16.4183 16.4183 20 12 20C7.58172 20 4 16.4183 4 12ZM15 12C15 13.6569 13.6568 15 12 15C10.3431 15 9 13.6569 9 12C9 10.3431 10.3431 9 12 9C13.6568 9 15 10.3431 15 12Z"/></svg>
                                </button>
                                <button onclick='window.deleteLink(${link.id})' class="p-3 bg-red-500/5 hover:bg-red-500/10 text-red-500/50 hover:text-red-500 rounded-2xl transition-all border border-red-500/10">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                </button>
                            </div>
                        </div>
                    </div>
                </li>
            `).join('');
        } else {
            linksList.innerHTML = '<div class="py-24 text-center text-gray-500 font-bold uppercase tracking-widest text-xs">Start by creating your first QR code generator URL above.</div>';
        }
    }

    async function fetchTokens() {
        const tokensRes = await fetch('/api/tokens', { credentials: 'same-origin',  headers: globalHeaders,  });
        const tokensData = await tokensRes.json();
        const tokensList = document.getElementById('tokens-list');
        
        if (tokensData.tokens && tokensData.tokens.length) {
            tokensList.innerHTML = tokensData.tokens.map(token => `
                <li class="p-5 bg-white/5 border border-white/5 rounded-2xl group hover:border-indigo-500/30 transition-all duration-300">
                    <div class="flex items-start justify-between gap-4 mb-4 text-left">
                        <div class="min-w-0">
                            <div class="flex items-center gap-2 mb-1">
                                <div class="w-1.5 h-1.5 rounded-full bg-indigo-500 shadow-[0_0_8px_rgba(99,102,241,0.5)]"></div>
                                <span class="text-[11px] font-black text-white uppercase tracking-wider truncate text-left">${token.name}</span>
                            </div>
                            <p class="text-[10px] text-gray-500 font-medium truncate text-left">${token.description || 'No system notes'}</p>
                        </div>
                        <button onclick="revokeToken(${token.id})" class="p-2 text-gray-600 hover:text-red-400 transition-colors">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                        </button>
                    </div>
                    <div class="flex items-center gap-2 bg-black/40 p-3 rounded-xl border border-white/5">
                        <input type="password" value="${token.plain_token || ''}" readonly 
                            class="bg-transparent border-none outline-none w-full text-[10px] font-mono text-indigo-400 font-bold tracking-tighter" 
                            id="token-input-${token.id}">
                        <button class="text-gray-500 hover:text-indigo-400 transition" onclick="window.toggleTokenRow(${token.id})">
                            <svg class="w-3.5 h-3.5" id="token-eye-${token.id}" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                        </button>
                    </div>
                    <div class="mt-2 text-[8px] font-bold text-gray-600 uppercase tracking-widest text-right">
                        Last sync: ${token.last_used_at ? new Date(token.last_used_at).toLocaleDateString() : 'Never'}
                    </div>
                </li>
            `).join('');
        } else {
            tokensList.innerHTML = '<li class="text-center py-8 text-gray-600 text-[10px] font-bold uppercase tracking-widest">No API keys found.</li>';
        }
    }

    await Promise.all([fetchLinks(), fetchTokens()]);

    const fgInput = document.querySelector('input[name="color"]');
    const bgInput = document.querySelector('input[name="background_color"]');
    if (fgInput) fgInput.oninput = (e) => document.getElementById('fg-preview').style.backgroundColor = e.target.value;
    if (bgInput) bgInput.oninput = (e) => document.getElementById('bg-preview').style.backgroundColor = e.target.value;

    document.getElementById('create-link-form').addEventListener('submit', async function(e) {
        e.preventDefault();
        const formData = new FormData(this);
        const data = Object.fromEntries(formData.entries());
        const res = await fetch('/api/links', { credentials: 'same-origin', 
            method: 'POST',
            headers: { ...globalHeaders, 'Content-Type': 'application/json' },
            body: JSON.stringify(data),
            
        });
        if (res.ok) {
            showToast('Deployment Successful');
            setTimeout(() => location.reload(), 800);
        } else {
            showToast('Deployment Failed', 'bg-red-500');
        }
    });

    document.getElementById('create-token-form').addEventListener('submit', async function(e) {
        e.preventDefault();
        const res = await fetch('/api/tokens', { credentials: 'same-origin', 
            method: 'POST',
            headers: { ...globalHeaders, 'Content-Type': 'application/json' },
            body: JSON.stringify({ name: this.name.value, description: this.description.value }),
            
        });
        if (res.ok) {
            const data = await res.json();
            const tokenModal = document.getElementById('token-modal');
            document.getElementById('new-token-value').value = data.access_token;
            tokenModal.classList.remove('hidden');
            
            document.getElementById('toggle-token-visibility').onclick = () => {
                const input = document.getElementById('new-token-value');
                input.style.webkitTextSecurity = (input.style.webkitTextSecurity === 'disc' || !input.style.webkitTextSecurity) ? 'none' : 'disc';
            };
            document.getElementById('copy-token-btn').onclick = () => {
                navigator.clipboard.writeText(document.getElementById('new-token-value').value);
                showToast('Key copied to clipboard');
            };
            document.getElementById('close-token-modal').onclick = () => location.reload();
        }
    });

    document.getElementById('search-links').addEventListener('input', function() {
        const query = this.value.toLowerCase();
        document.querySelectorAll('#links-list li').forEach(li => {
            li.style.display = li.textContent.toLowerCase().includes(query) ? '' : 'none';
        });
    });
});

window.revokeToken = async function(id) {
    if (!confirm('Deauthorize this system key?')) return;
    const res = await fetch(`/api/tokens/${id}`, { credentials: 'same-origin',  method: 'DELETE', headers: window.globalHeaders });
    if (res.ok) { showToast('Key Revoked'); location.reload(); }
};

window.deleteLink = async function(id) {
    if (!confirm('Erase this vector data?')) return;
    const res = await fetch(`/api/links/${id}`, { credentials: 'same-origin',  method: 'DELETE', headers: window.globalHeaders });
    if (res.ok) { showToast('Vector Purged'); location.reload(); }
};

window.showToast = function(message, color = 'bg-blue-600') {
    const toast = document.getElementById('toast');
    if (!toast) return;
    toast.textContent = message;
    toast.className = `fixed bottom-8 right-8 text-white px-8 py-4 rounded-2xl shadow-2xl font-black uppercase tracking-widest text-[10px] z-[100] border border-white/10 backdrop-blur-xl ${color}`;
    toast.style.opacity = 1;
    toast.classList.remove('hidden');
    setTimeout(() => {
        toast.style.opacity = 0;
        setTimeout(() => toast.classList.add('hidden'), 500);
    }, 3000);
};

window.showQrModal = async function(url) {
    const modal = document.getElementById('qr-modal');
    const content = document.getElementById('qr-modal-content');
    modal.classList.remove('hidden');
    content.innerHTML = '<div class="animate-pulse text-blue-500 font-black">SCANNING...</div>';
    
    try {
        const res = await fetch(url, { credentials: 'same-origin',  headers: window.globalHeaders });
        const svg = await res.text();
        content.innerHTML = svg;
        document.getElementById('qr-download-link').href = 'data:image/svg+xml;base64,' + btoa(svg);
    } catch (err) {
        content.innerHTML = '<span class="text-red-500 font-bold">ERROR LOADING SCANNER</span>';
    }
};

document.addEventListener('DOMContentLoaded', () => {
    const closeQr = document.getElementById('close-qr-modal');
    if (closeQr) closeQr.onclick = () => document.getElementById('qr-modal').classList.add('hidden');
});
