document.addEventListener('DOMContentLoaded', async function() {
    // Modal close handlers
    document.getElementById('close-qr-modal')?.addEventListener('click', closeQrModal);
    
    // Token Modal handlers
    document.getElementById('close-token-modal')?.addEventListener('click', () => {
        document.getElementById('token-modal').classList.add('hidden');
        location.reload(); // Reload to update the generated list
    });
    
    document.getElementById('copy-token-btn')?.addEventListener('click', () => {
        const val = document.getElementById('new-token-value')?.value;
        if(val) {
            navigator.clipboard.writeText(val).then(() => {
                showToast('API Key copied to clipboard!', 'bg-green-500');
            }).catch(() => {
                showToast('Failed to copy API Key.', 'bg-red-500');
            });
        }
    });

    document.getElementById('toggle-token-visibility')?.addEventListener('click', () => {
        const input = document.getElementById('new-token-value');
        const icon = document.getElementById('token-eye-icon');
        if(!input) return;
        
        if(input.style.webkitTextSecurity === 'none') {
            input.style.webkitTextSecurity = 'disc';
            if(icon) icon.innerHTML = `<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>`;
        } else {
            input.style.webkitTextSecurity = 'none';
            if(icon) icon.innerHTML = `<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21" />`;
        }
    });
    
    // Get or prompt for API token
    let apiToken = localStorage.getItem('apiToken');
    if (!apiToken) {
        apiToken = prompt('Enter your API token (Bearer):');
        if (apiToken) localStorage.setItem('apiToken', apiToken);
    }

    function authHeaders(extra = {}) {
        const headers = { 'Accept': 'application/json', ...extra };
        if (apiToken) headers['Authorization'] = `Bearer ${apiToken}`;
        return headers;
    }
    // Fetch CSRF token from meta tag or cookie
    const csrfToken = document.querySelector('meta[name="csrf-token"]')?.content;
    // Fetch links
    const linksRes = await fetch('/api/links', { credentials: 'same-origin',  headers: authHeaders() });
    const linksData = await linksRes.json();
    const linksList = document.getElementById('links-list');
    linksList.innerHTML = '';
    if (linksData.data && linksData.data.length) {
        linksData.data.forEach(link => {
            const shortUrlPretty = link.short_url ? link.short_url.replace(/^https?:\/\//, '') : '';
            const li = document.createElement('li');
            li.className = 'bg-gray-800 rounded-xl p-4 md:p-6 mb-4 border border-gray-700 hover:border-blue-500/50 transition-all group shadow-sm flex flex-col md:flex-row justify-between md:items-center gap-4';
            let html = `
                <div class="flex-1 min-w-0 flex items-start space-x-3">
                    <div class="mt-1 flex items-center h-5">
                        <input type="checkbox" class="bulk-link-checkbox w-4 h-4 text-blue-600 bg-gray-700 border-gray-600 rounded focus:ring-blue-500 focus:ring-2" data-link-id="${link.id}">
                    </div>
                    <div class="flex-1 min-w-0">
                        <div class="flex items-center space-x-3 mb-1">
                            <span class="text-xs font-bold text-gray-400 uppercase tracking-widest">${link.short_code}</span>
                            <span class="px-2 py-0.5 rounded-full text-xs font-medium bg-emerald-500/10 text-emerald-400 border border-emerald-500/20">Active</span>
                        </div>
                        <h3 class="text-lg font-bold text-white mb-1 truncate flex items-center gap-2">
                            <a href="${link.short_url}" target="_blank" class="hover:text-blue-400 transition">${shortUrlPretty}</a>
                        </h3>
                        <p class="text-sm text-gray-500 truncate max-w-xl" title="${link.original_url}">
                            &rarr; ${link.original_url}
                        </p>
                        <div class="flex items-center gap-4 mt-3 text-xs text-gray-500">
                            <span class="flex items-center gap-1">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                                Scans: <span class="font-bold text-gray-300 ml-1" id="scan-count-${link.id}">${link.scans_count !== undefined ? link.scans_count : '-'}</span>
                            </span>
                            <span class="flex items-center gap-1">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                Last: <span class="text-gray-400 ml-1" id="last-scan-${link.id}">${link.last_scanned_at || '-'}</span>
                            </span>
                        </div>
                    </div>
                </div>
                <div class="flex items-center space-x-2 shrink-0 border-t border-gray-700 md:border-t-0 pt-4 md:pt-0 mt-4 md:mt-0">
                    <button class="bg-gray-700 hover:bg-gray-600 text-white p-2 rounded-lg transition-colors border border-gray-600" onclick="copyToClipboard('${link.short_url}')" title="Copy Short URL">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 5H6a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2v-1M8 5a2 2 0 002 2h2a2 2 0 002-2M8 5a2 2 0 012-2h2a2 2 0 012 2m0 0h2a2 2 0 012 2v3m2 4H10m0 0l3-3m-3 3l3 3"/></svg>
                    </button>
                    <button class="bg-indigo-600 hover:bg-indigo-500 text-white px-4 py-2 rounded-lg transition-colors border border-indigo-500 font-bold text-sm shadow-md" onclick="showQrModal('${link.qr_code_download_url}', '${link.qr_code_download_url}')" type="button">
                        QR Code
                    </button>
                    <button class="bg-red-500/10 hover:bg-red-500/20 text-red-400 p-2 rounded-lg transition-colors border border-red-500/20" onclick="deleteLink(${link.id})" title="Delete Link">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                    </button>
                </div>
            `;
            
            // Flex layout fix for expire date row
            if (link.expires_at) {
                html = `<div class="w-full flex flex-col md:flex-row justify-between md:items-center gap-4">${html}</div><div class='w-full mt-3 pt-3 border-t border-gray-700/50 text-xs text-orange-400 font-medium flex items-center'><svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>Expires: ${link.expires_at}</div>`;
                li.className = 'bg-gray-800 rounded-xl p-4 md:p-6 mb-4 border border-gray-700 hover:border-blue-500/50 transition-all group shadow-sm flex flex-col';
            }
            
            li.innerHTML = html;
            linksList.appendChild(li);
        });
    } else {
        linksList.innerHTML = '<li class="p-6 text-center text-gray-500 font-medium bg-gray-800 rounded-xl border border-gray-700">No links found. Create one above!</li>';
    }
    // Fetch tokens
    const tokensRes = await fetch('/api/tokens', { credentials: 'same-origin',  headers: authHeaders() });
    const tokensData = await tokensRes.json();
    const tokensList = document.getElementById('tokens-list');
        if (!tokensList) return;
    tokensList.innerHTML = '';
    if (tokensData.tokens && tokensData.tokens.length) {
        tokensData.tokens.forEach(token => {
            const li = document.createElement('li');
            li.className = 'bg-gray-800 rounded-xl p-4 mb-3 border border-gray-700 hover:border-gray-500 transition-colors flex flex-col md:flex-row justify-between md:items-center gap-3 shadow-sm';
            
            let html = `
                <div class="flex-1">
                    <div class="flex items-center gap-2">
                        <span class="font-bold text-white text-md">${token.name}</span>
                        ${token.last_used_at ? `<span class="px-2 py-0.5 rounded-full text-xs font-medium bg-blue-500/10 text-blue-400 border border-blue-500/20">Active</span>` : `<span class="px-2 py-0.5 rounded-full text-xs font-medium bg-gray-500/10 text-gray-400 border border-gray-500/20">Unused</span>`}
                    </div>
                    ${token.description ? `<p class="mt-1 text-sm text-gray-400">${token.description}</p>` : ''}
                    
                    ${token.plain_token ? `
                    <div class="mt-3 flex items-center gap-2 bg-gray-900/50 p-2 rounded border border-gray-700 w-full md:max-w-[200px]">
                        <input type="text" id="token-val-${token.id}" value="${token.plain_token}" readonly 
                            class="bg-transparent border-none outline-none w-full text-gray-300 font-mono text-xs tracking-widest cursor-text" 
                            style="text-security: none; -webkit-text-security: disc;">
                        <button type="button" class="text-gray-400 hover:text-white transition-colors shrink-0" 
                            onclick="
                                const input = document.getElementById('token-val-${token.id}');
                                if(input.style.webkitTextSecurity === 'none') {
                                    input.style.webkitTextSecurity = 'disc';
                                    this.querySelector('.icon-show').classList.remove('hidden');
                                    this.querySelector('.icon-hide').classList.add('hidden');
                                } else {
                                    input.style.webkitTextSecurity = 'none';
                                    this.querySelector('.icon-show').classList.add('hidden');
                                    this.querySelector('.icon-hide').classList.remove('hidden');
                                }
                            " title="Toggle Token Visibility">
                            <svg class="w-4 h-4 icon-show" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                            <svg class="w-4 h-4 icon-hide hidden" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21" /></svg>
                        </button>
                        <button type="button" class="text-gray-400 hover:text-blue-400 transition-colors shrink-0 ml-1" onclick="copyToClipboard('${token.plain_token}')" title="Copy Token">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 5H6a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2v-1M8 5a2 2 0 002 2h2a2 2 0 002-2M8 5a2 2 0 012-2h2a2 2 0 012 2m0 0h2a2 2 0 012 2v3m2 4H10m0 0l3-3m-3 3l3 3"/></svg>
                        </button>
                    </div>
                    ` : ''}

                    <div class="mt-3 text-xs text-gray-500 flex items-center gap-1">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                        Last used: <span class="text-gray-300 ml-1 font-medium">${token.last_used_at || 'Never'}</span>
                    </div>
                </div>
                <div class="flex items-center gap-2 mt-2 md:mt-0 pt-3 border-t border-gray-700 md:border-t-0 md:pt-0">
                    <button class="bg-red-500/10 hover:bg-red-500/20 text-red-400 px-4 py-2 rounded-lg transition-colors border border-red-500/20 font-bold text-sm" onclick="revokeToken(${token.id})">
                        Revoke Token
                    </button>
                </div>
            `;
            li.innerHTML = html;
            tokensList.appendChild(li);
        });
    } else {
        tokensList.innerHTML = '<li class="p-6 text-center text-gray-500 font-medium bg-gray-800 rounded-xl border border-gray-700">No API keys found.</li>';
    }
    // Handle create token
    const ctf = document.getElementById('create-token-form');
    if (ctf) ctf.addEventListener('submit', async function(e) {
        e.preventDefault();
        const name = this.name.value.trim();
        if (!name) {
            showToast('Token name is required.', 'bg-red-500');
            this.name.focus();
            return;
        }
        const res = await fetch('/api/tokens', { credentials: 'same-origin', 
            method: 'POST',
            headers: authHeaders({
                'Content-Type': 'application/json',
                ...(csrfToken ? { 'X-CSRF-TOKEN': csrfToken } : {})
            }),
            body: JSON.stringify({ name })
        });
        
        if (res.ok) {
            const data = await res.json();
            const tokenValue = document.getElementById('new-token-value');
            const tokenModal = document.getElementById('token-modal');
            
            if (tokenModal && tokenValue && data.access_token) {
                tokenValue.value = data.access_token;
                tokenValue.style.webkitTextSecurity = 'disc'; // Ensure it's hidden initially
                tokenModal.classList.remove('hidden');
                ctf.reset();
            } else {
                location.reload();
            }
        } else {
            showToast('Failed to create token.', 'bg-red-500');
        }
    });
    // Handle create link
    document.getElementById('create-link-form').addEventListener('submit', async function(e) {
        e.preventDefault();
        const formData = new FormData(this);
        const data = Object.fromEntries(formData.entries());
        // Client-side validation
        if (!data.original_url || !/^https?:\/\//.test(data.original_url.trim())) {
            showToast('A valid URL is required.', 'bg-red-500');
            this.original_url.focus();
            return;
        }
        if (data.custom_alias && !/^[a-zA-Z0-9_-]{3,32}$/.test(data.custom_alias)) {
            showToast('Alias must be 3-32 chars, alphanumeric, dash or underscore.', 'bg-red-500');
            this.custom_alias.focus();
            return;
        }
        if (data.expires_at && isNaN(Date.parse(data.expires_at))) {
            showToast('Expiration date is invalid.', 'bg-red-500');
            this.expires_at.focus();
            return;
        }
        const res = await fetch('/api/links', { credentials: 'same-origin', 
            method: 'POST',
            headers: authHeaders({
                'Content-Type': 'application/json',
                ...(csrfToken ? { 'X-CSRF-TOKEN': csrfToken } : {})
            }),
            body: JSON.stringify(data)
        });
        if (res.ok) {
            showToast('Link created!');
            setTimeout(() => location.reload(), 1000);
        } else {
            showToast('Failed to create link.', 'bg-red-500');
        }
    });
    // Search/filter links
    document.getElementById('search-links').addEventListener('input', function() {
        const query = this.value.toLowerCase();
        document.querySelectorAll('#links-list li').forEach(li => {
            const text = li.textContent.toLowerCase();
            li.style.display = text.includes(query) ? '' : 'none';
        });
    });
});
window.revokeToken = async function(id) {
    if (!confirm('Revoke this API key?')) return;
    
    let apiToken = localStorage.getItem('apiToken');
    const headers = { 'Accept': 'application/json' };
    const csrfToken = document.querySelector('meta[name="csrf-token"]')?.content;
    if(csrfToken) headers['X-CSRF-TOKEN'] = csrfToken;
    if (apiToken) headers['Authorization'] = `Bearer ${apiToken}`;
    
    try {
        const res = await fetch(`/api/tokens/${id}`, { 
            credentials: 'same-origin',  
            method: 'DELETE', 
            headers: headers 
        });
        
        if (res.ok) {
            showToast('API Key revoked!', 'bg-green-500');
            setTimeout(() => location.reload(), 1000);
        } else {
            showToast('Failed to revoke API Key.', 'bg-red-500');
        }
    } catch(e) {
        showToast('Error revoking API Key.', 'bg-red-500');
    }
};
// Toast notification function
function showToast(message, color = 'bg-green-500') {
    const toast = document.getElementById('toast');
    toast.textContent = message;
    toast.className = `fixed bottom-4 right-4 text-white px-4 py-2 rounded shadow-lg z-50 ${color}`;
    toast.style.opacity = 1;
    toast.classList.remove('hidden');
    setTimeout(() => {
        toast.style.opacity = 0;
        setTimeout(() => toast.classList.add('hidden'), 500);
    }, 2000);
}
// Update copy, delete, and create actions to use toast
window.copyToClipboard = function(text) {
    navigator.clipboard.writeText(text).then(function() {
        showToast('Copied to clipboard!');
    }, function() {
        showToast('Failed to copy.', 'bg-red-500');
    });
};

window.showQrModal = function(qrUrl, downloadUrl) {
    const modal = document.getElementById('qr-modal');
    const content = document.getElementById('qr-modal-content');
    const dlBtn = document.getElementById('qr-download-link');
    
    if(modal && content) {
        content.innerHTML = `<img src="${qrUrl}" class="max-w-full h-auto rounded-lg shadow-sm" alt="QR Code">`;
        if(dlBtn) dlBtn.href = downloadUrl;
        modal.classList.remove('hidden');
    } else {
        showToast("QR Modal not found in DOM.", "bg-red-500");
    }
};

window.closeQrModal = function() {
    const modal = document.getElementById('qr-modal');
    if(modal) modal.classList.add('hidden');
};

window.deleteLink = async function(id) {
    if (!confirm('Are you sure you want to delete this link?')) return;
    
    let apiToken = localStorage.getItem('apiToken');
    const headers = { 'Accept': 'application/json' };
    const csrfToken = document.querySelector('meta[name="csrf-token"]')?.content;
    if(csrfToken) headers['X-CSRF-TOKEN'] = csrfToken;
    if (apiToken) headers['Authorization'] = `Bearer ${apiToken}`;
    
    try {
        const res = await fetch(`/api/links/${id}`, { 
            method: 'DELETE', 
            credentials: 'same-origin',
            headers: headers 
        });
        
        if (res.ok) {
            showToast('Link deleted successfully!');
            setTimeout(() => location.reload(), 1000);
        } else {
            showToast('Failed to delete link.', 'bg-red-500');
        }
    } catch (e) {
        showToast('Error deleting link.', 'bg-red-500');
    }
};

// --- AI Assistant Logic ---
let currentAiMode = 'support'; 

window.switchAiMode = function(mode) {
    currentAiMode = mode;
    const tabSupport = document.getElementById('tab-support');
    const tabContent = document.getElementById('tab-content');
    const input = document.getElementById('ai-input');
    
    if (mode === 'support') {
        if(tabSupport) tabSupport.className = 'flex-1 py-3 text-xs font-bold text-white bg-white/10 transition';
        if(tabContent) tabContent.className = 'flex-1 py-3 text-xs font-bold text-gray-400 hover:text-white transition';
        if(input) input.placeholder = 'Type your question...';
    } else {
        if(tabContent) tabContent.className = 'flex-1 py-3 text-xs font-bold text-white bg-white/10 transition';
        if(tabSupport) tabSupport.className = 'flex-1 py-3 text-xs font-bold text-gray-400 hover:text-white transition';
        if(input) input.placeholder = 'Describe QR scenario (e.g. Real Estate Flyer)...';
    }
}

window.sendAiMessage = async function() {
    const inputField = document.getElementById('ai-input');
    if(!inputField) return;
    const message = inputField.value.trim();
    if (!message) return;
    
    inputField.value = '';
    appendAiMessage(message, 'user');
    
    // Add loading text
    const loadingId = Date.now();
    appendAiMessage('<span class="animate-pulse">Thinking...</span>', 'bot', loadingId, true);
    
    const endpoint = currentAiMode === 'support' ? '/api/ai/agent' : '/api/ai/generate-qr-content';
    const payload = currentAiMode === 'support' ? { question: message } : { scenario: message };
    
    try {
        let apiToken = localStorage.getItem('apiToken');
        const csrfToken = document.querySelector('meta[name="csrf-token"]')?.content;
        
        let headers = {
            'Content-Type': 'application/json',
            'Accept': 'application/json',
            ...(csrfToken ? { 'X-CSRF-TOKEN': csrfToken } : {})
        };
        
        if (apiToken) {
            headers['Authorization'] = `Bearer ${apiToken}`;
        }

        const res = await fetch(endpoint, {
            method: 'POST',
            credentials: 'same-origin',
            headers: headers,
            body: JSON.stringify(payload)
        });
        
        const data = await res.json();
        
        // Remove loading text
        document.getElementById('msg-' + loadingId)?.remove();
        
        if (data.success) {
            if (currentAiMode === 'support') {
                // Convert markdown-like response roughly
                const formattedAnswer = data.answer.replace(/\n/g, '<br>');
                appendAiMessage(formattedAnswer, 'bot', null, true);
            } else {
                let html = '<div class="space-y-2 mb-2 font-bold text-xs">Here are your CTAs. Click to copy:</div>';
                data.ctas.forEach(cta => {
                    const cleanCta = cta.replace(/'/g, "\\'").replace(/"/g, "&quot;");
                    html += `<button onclick="copyToClipboard('${cleanCta}')" class="block w-full text-left bg-blue-500/20 hover:bg-blue-500/40 text-blue-100 p-2 rounded-lg border border-blue-500/30 transition text-xs mb-2 text-center font-bold">"${cta}"</button>`;
                });
                appendAiMessage(html, 'bot', null, true);
            }
        } else {
            appendAiMessage('Error: ' + (data.message || 'Unknown error'), 'bot');
        }
    } catch (e) {
        document.getElementById('msg-' + loadingId)?.remove();
        appendAiMessage('Connection error. Please try again.', 'bot');
    }
}

function appendAiMessage(text, sender, id = null, isHtml = false) {
    const history = document.getElementById('ai-chat-history');
    if(!history) return;
    const msgDiv = document.createElement('div');
    if (id) msgDiv.id = 'msg-' + id;
    
    if (sender === 'user') {
        msgDiv.className = 'bg-blue-600 text-white p-3 rounded-2xl rounded-tr-sm ml-auto w-5/6 text-right text-sm shadow-md';
    } else {
        msgDiv.className = 'bg-white/10 text-white p-3 rounded-2xl rounded-tl-sm w-5/6 text-sm shadow-md';
    }
    
    if (isHtml) {
        msgDiv.innerHTML = text;
    } else {
        msgDiv.innerText = text;
    }
    
    history.appendChild(msgDiv);
    history.scrollTop = history.scrollHeight;
}
