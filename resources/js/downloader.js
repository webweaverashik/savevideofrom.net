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
function fail(msg) {
    clearTimeout(polling);
    show(els.loading, false);
    els.error.textContent = msg;
    show(els.error, true);
    setBusy(false);
}
function setBusy(on) {
    els.btn.disabled = on;
    els.btnText.textContent = on ? 'Working…' : 'Download';
}
function loading(text) { els.loadingText.textContent = text; show(els.loading, true); }

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

    videos.forEach((f) => els.formats.appendChild(
        formatButton(`${f.quality}`, fmtBytes(f.filesize), () =>
            requestDownload(uuid, { media_type: 'video', quality: f.quality, format: 'mp4' }))
    ));

    if (audio) {
        ['mp3', 'm4a', 'wav'].forEach((codec) => els.formats.appendChild(
            formatButton(codec.toUpperCase(), 'Audio only', () =>
                requestDownload(uuid, { media_type: 'audio', format: codec }))
        ));
    }

    show(els.card, true);
}

function formatButton(label, sub, onClick) {
    const b = document.createElement('button');
    b.type = 'button';
    b.className = 'rounded-xl border border-gray-300 dark:border-gray-700 px-3 py-2 text-sm hover:border-violet-500 hover:bg-violet-50 dark:hover:bg-violet-950/40 transition text-left';
    b.innerHTML = `<span class="font-semibold block">${label}</span><span class="text-xs text-gray-500">${sub}</span>`;
    b.addEventListener('click', onClick);
    return b;
}

async function requestDownload(uuid, payload) {
    show(els.card, false);
    loading('Preparing your file… this can take a moment.');
    try {
        await api('/api/download', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({ uuid, ...payload }),
        });
        pollDownload(uuid, Date.now() + 600_000);
    } catch (err) { fail(err.message); }
}

function pollDownload(uuid, deadline) {
    polling = setTimeout(async () => {
        try {
            const d = await api(`/api/status/${uuid}`);
            if (d.status === 'completed') return renderComplete(d);
            if (d.status === 'failed' || d.status === 'expired') return fail(d.error);
            if (Date.now() > deadline) return fail('The download took too long. Please try again.');
            pollDownload(uuid, deadline);
        } catch (err) { fail(err.message); }
    }, 2000);
}

function renderComplete(data) {
    show(els.loading, false);
    els.downloadLink.href = data.download_url;
    show(els.downloadBox, true);
}

els.themeToggle?.addEventListener('click', () => {
    const dark = document.documentElement.classList.toggle('dark');
    localStorage.setItem('theme', dark ? 'dark' : 'light');
});

els.form?.addEventListener('submit', onSubmit);