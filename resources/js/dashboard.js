document.addEventListener('DOMContentLoaded', async function() {
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
    const linksRes = await fetch('/api/links', { headers: authHeaders() });
    const linksData = await linksRes.json();
    const linksList = document.getElementById('links-list');
    linksList.innerHTML = '';
    if (linksData.data && linksData.data.length) {
        linksData.data.forEach(link => {
            const li = document.createElement('li');
            li.className = 'list-group-item';
            li.innerHTML = `
                <input type='checkbox' class='bulk-link-checkbox mr-2' data-link-id='${link.id}'>
                <strong>${link.short_code}</strong> &rarr; <a href="${link.url}" target="_blank">${link.url}</a>
                <button class='btn btn-sm btn-outline-secondary ml-2' onclick='copyToClipboard("${link.short_url}")'>Copy</button>
                <button class='btn btn-sm btn-outline-info ml-2' onclick='showQrModal("${link.qr_code_download_url}", "${link.qr_code_download_url}")' type='button'>QR</button>
                <button class='btn btn-sm btn-outline-primary ml-2' onclick='editLink(${link.id})'>Edit</button>
                <button class='btn btn-sm btn-outline-danger ml-2' onclick='deleteLink(${link.id})'>Delete</button>
                <br>
                <span class='text-sm text-gray-500'>${link.expires_at ? `Expires: ${link.expires_at}` : ''}</span>
                <span class='text-sm text-gray-500 ml-2'>Scans: <span id='scan-count-${link.id}'>-</span></span>
                <span class='text-sm text-gray-500 ml-2'>Last: <span id='last-scan-${link.id}'>-</span></span>
            `;
            if (link.expires_at) {
                li.innerHTML += `<br><span class='text-sm text-gray-500'>Expires: ${link.expires_at}</span>`;
            }
            linksList.appendChild(li);
        });
    } else {
        linksList.innerHTML = '<li class="list-group-item">No links found.</li>';
    }
    // Fetch tokens
    const tokensRes = await fetch('/api/tokens', { headers: authHeaders() });
    const tokensData = await tokensRes.json();
    const tokensList = document.getElementById('tokens-list');
    tokensList.innerHTML = '';
    if (tokensData.tokens && tokensData.tokens.length) {
        tokensData.tokens.forEach(token => {
            const li = document.createElement('li');
            li.className = 'list-group-item flex justify-between items-center';
            li.innerHTML = `<span>${token.name}</span>`;
            if (token.description) {
                li.innerHTML += `<span class='ml-2 text-xs text-gray-500'>${token.description}</span>`;
            }
            if (token.last_used_at) {
                li.innerHTML += `<span class='ml-2 text-xs text-gray-500'>(Last used: ${token.last_used_at})</span>`;
            }
            li.innerHTML += ` <button class='btn btn-danger btn-sm ml-2' onclick='revokeToken(${token.id})'>Revoke</button>`;
            tokensList.appendChild(li);
        });
    } else {
        tokensList.innerHTML = '<li class="list-group-item">No API keys found.</li>';
    }
    // Handle create token
    document.getElementById('create-token-form').addEventListener('submit', async function(e) {
        e.preventDefault();
        const name = this.name.value.trim();
        if (!name) {
            showToast('Token name is required.', 'bg-red-500');
            this.name.focus();
            return;
        }
        const res = await fetch('/api/tokens', {
            method: 'POST',
            headers: authHeaders({
                'Content-Type': 'application/json',
                ...(csrfToken ? { 'X-CSRF-TOKEN': csrfToken } : {})
            }),
            body: JSON.stringify({ name })
        });
        if (res.ok) location.reload();
        else showToast('Failed to create token.', 'bg-red-500');
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
        const res = await fetch('/api/links', {
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
async function revokeToken(id) {
    if (!confirm('Revoke this API key?')) return;
    await fetch(`/api/tokens/${id}`, { method: 'DELETE', headers: authHeaders() });
    location.reload();
}
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
