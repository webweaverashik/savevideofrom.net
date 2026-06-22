const csrf = document.querySelector('meta[name="csrf-token"]')?.content || '';

const el = (id) => document.getElementById(id);
const els = {
    form: el('dlForm'), url: el('urlInput'), btn: el('submitBtn'), btnText: el('btnText'),
    error: el('errorBox'), loading: el('loadingBox'), loadingText: el('loadingText'),
    card: el('resultCard'), thumb: el('thumb'), title: el('title'), meta: el('meta'),
    formats: el('formats'), downloadBox: el('downloadBox'), downloadLink: el('downloadLink'),
    themeToggle: el('themeToggle'),
};

let polling = null;

function show(node, on = true) { node?.classList.toggle('hidden', !on); }
function reset() {
    clearTimeout(polling);
    [els.error, els.loading, els.card, els.downloadBox].forEach((n) => show(n, false));
    els.formats.innerHTML = '';
}
function setBusy(on) {
    els.btn.disabled = on;
    els.btnText.textContent = on ? 'Working…' : 'Download';
}
function loading(text) { els.loadingText.textContent = text; show(els.loading, true); }

// Extract-phase failure (uses the big fetch loader)
function fail(msg) {
    clearTimeout(polling);
    show(els.loading, false);
    els.error.textContent = msg;
    show(els.error, true);
    setBusy(false);
}
// Download-phase failure (keeps the format grid visible)
function showError(msg) {
    clearTimeout(polling);
    els.error.textContent = msg;
    show(els.error, true);
}

function fmtBytes(b) {
    if (!b) return '';
    const u = ['B', 'KB', 'MB', 'GB']; let i = 0;
    while (b >= 1024 && i < u.length - 1) { b /= 1024; i++; }
    return `${b.toFixed(b < 10 && i > 0 ? 1 : 0)} ${u[i]}`;
}
function fmtDuration(s) {
    if (!s) return '';
    const m = Math.floor(s / 60), sec = s % 60;
    return `${m}:${String(sec).padStart(2, '0')}`;
}

async function api(url, options = {}) {
    const res = await fetch(url, {
        ...options,
        headers: { 'Accept': 'application/json', 'X-CSRF-TOKEN': csrf, ...(options.headers || {}) },
    });
    const data = await res.json().catch(() => ({}));
    if (!res.ok && res.status !== 409) {
        throw new Error(data.error || data.message || 'Request failed. Please try again.');
    }
    return data;
}

// ── Phase 1: extract ─────────────────────────────────────────────
async function onSubmit(e) {
    e.preventDefault();
    const url = els.url.value.trim();
    reset();
    if (!url) return fail('Please paste a video link.');

    setBusy(true);
    loading('Fetching video…');

    try {
        const { uuid } = await api('/api/extract', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({ url }),
        });
        pollExtract(uuid, Date.now() + 90_000);
    } catch (err) {
        fail(err.message);
    }
}

function pollExtract(uuid, deadline) {
    polling = setTimeout(async () => {
        try {
            const d = await api(`/api/status/${uuid}`);
            if (d.status === 'ready') return renderFormats(uuid, d);
            if (d.status === 'failed' || d.status === 'expired') return fail(d.error);
            if (Date.now() > deadline) return fail('This is taking too long. Please try again.');
            pollExtract(uuid, deadline);
        } catch (err) { fail(err.message); }
    }, 1500);
}

function renderFormats(uuid, data) {
    show(els.loading, false);
    setBusy(false);

    if (data.thumbnail) { els.thumb.src = data.thumbnail; show(els.thumb, true); }
    els.title.textContent = data.title || 'Untitled';
    els.meta.textContent = [data.uploader, data.platform, fmtDuration(data.duration)].filter(Boolean).join(' · ');

    const videos = (data.formats || []).filter((f) => f.type === 'video');
    const audio = (data.formats || []).find((f) => f.type === 'audio');

    els.formats.innerHTML = '';
    videos.forEach((f) => els.formats.appendChild(
        formatButton(`${f.quality}`, fmtBytes(f.filesize), (btn) =>
            requestDownload(uuid, { media_type: 'video', quality: f.quality, format: 'mp4' }, btn))
    ));
    if (audio) {
        ['mp3', 'm4a', 'wav'].forEach((codec) => els.formats.appendChild(
            formatButton(codec.toUpperCase(), 'Audio only', (btn) =>
                requestDownload(uuid, { media_type: 'audio', format: codec }, btn))
        ));
    }

    show(els.card, true);
}

function formatButton(label, sub, onClick) {
    const b = document.createElement('button');
    b.type = 'button';
    b.className = 'rounded-xl border border-gray-300 dark:border-gray-700 px-3 py-2 text-sm hover:border-violet-500 hover:bg-violet-50 dark:hover:bg-violet-950/40 transition text-left disabled:cursor-not-allowed';
    b.innerHTML = `<span class="font-semibold block">${label}</span><span class="text-xs text-gray-500">${sub}</span>`;
    b.addEventListener('click', () => onClick(b));
    return b;
}

// ── Phase 2: download (in-button loader) ─────────────────────────
function lockFormats(activeBtn) {
    els.formats.querySelectorAll('button').forEach((b) => {
        b.disabled = true;
        if (b !== activeBtn) b.classList.add('opacity-40');
    });
}
function unlockFormats() {
    els.formats.querySelectorAll('button').forEach((b) => {
        b.disabled = false;
        b.classList.remove('opacity-40');
    });
}
function btnLoading(btn) {
    btn.dataset.prev = btn.innerHTML;
    btn.classList.add('bg-violet-600', 'text-white', 'border-violet-600');
    btn.innerHTML = '<span class="flex items-center gap-2"><span class="h-4 w-4 rounded-full border-2 border-white/40 border-t-white animate-spin"></span>Preparing…</span>';
}
function btnRestore(btn) {
    if (btn.dataset.prev !== undefined) { btn.innerHTML = btn.dataset.prev; delete btn.dataset.prev; }
    btn.classList.remove('bg-violet-600', 'text-white', 'border-violet-600');
}

async function requestDownload(uuid, payload, btn) {
    show(els.error, false);
    show(els.downloadBox, false);
    lockFormats(btn);
    btnLoading(btn);

    try {
        await api('/api/download', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({ uuid, ...payload }),
        });
        pollDownload(uuid, Date.now() + 600_000, btn);
    } catch (err) {
        btnRestore(btn);
        unlockFormats();
        showError(err.message);
    }
}

function pollDownload(uuid, deadline, btn) {
    polling = setTimeout(async () => {
        try {
            const d = await api(`/api/status/${uuid}`);
            if (d.status === 'completed') {
                btnRestore(btn);
                unlockFormats();
                triggerDownload(d.download_url);
                els.downloadLink.href = d.download_url;
                show(els.downloadBox, true);
                return;
            }
            if (d.status === 'failed' || d.status === 'expired') {
                btnRestore(btn); unlockFormats();
                return showError(d.error);
            }
            if (Date.now() > deadline) {
                btnRestore(btn); unlockFormats();
                return showError('The download took too long. Please try again.');
            }
            pollDownload(uuid, deadline, btn);
        } catch (err) {
            btnRestore(btn); unlockFormats();
            showError(err.message);
        }
    }, 2000);
}

function triggerDownload(url) {
    const a = document.createElement('a');
    a.href = url;
    a.download = '';
    a.rel = 'noopener';
    document.body.appendChild(a);
    a.click();
    a.remove();
}

els.themeToggle?.addEventListener('click', () => {
    const dark = document.documentElement.classList.toggle('dark');
    localStorage.setItem('theme', dark ? 'dark' : 'light');
});

els.form?.addEventListener('submit', onSubmit);