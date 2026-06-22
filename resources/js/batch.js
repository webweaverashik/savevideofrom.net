const csrf = document.querySelector('meta[name="csrf-token"]')?.content || '';
const el = (id) => document.getElementById(id);

// ── tab switching ────────────────────────────────────────────────
const tabs = { single: el('tabSingle'), batch: el('tabBatch') };
const panes = { single: el('singleMode'), batch: el('batchMode') };

function activate(name) {
    Object.entries(panes).forEach(([k, node]) => node?.classList.toggle('hidden', k !== name));
    Object.entries(tabs).forEach(([k, btn]) => {
        const on = k === name;
        btn?.classList.toggle('bg-violet-600', on);
        btn?.classList.toggle('text-white', on);
        btn?.classList.toggle('text-gray-600', !on);
        btn?.classList.toggle('dark:text-gray-300', !on);
    });
}
tabs.single?.addEventListener('click', () => activate('single'));
tabs.batch?.addEventListener('click', () => activate('batch'));

// ── helpers ──────────────────────────────────────────────────────
const b = {
    form: el('batchForm'), url: el('batchUrl'), loadBtn: el('batchLoadBtn'), loadText: el('batchLoadText'),
    error: el('batchError'), loading: el('batchLoading'),
    picker: el('batchPicker'), title: el('batchTitle'), truncated: el('batchTruncated'),
    entries: el('batchEntries'), selectAll: el('batchSelectAll'),
    quality: el('batchQuality'), downloadBtn: el('batchDownloadBtn'),
    progress: el('batchProgress'), progressText: el('batchProgressText'), bar: el('batchBar'),
    done: el('batchDone'), zipLink: el('batchZipLink'),
};

let polling = null;
let playlist = { url: '', title: '' };

function show(node, on = true) { node?.classList.toggle('hidden', !on); }
function fmtDuration(s) {
    if (!s) return '';
    const m = Math.floor(s / 60), sec = s % 60;
    return `${m}:${String(sec).padStart(2, '0')}`;
}
function bFail(msg) {
    clearTimeout(polling);
    [b.loading, b.progress].forEach((n) => show(n, false));
    b.error.textContent = msg;
    show(b.error, true);
    b.loadBtn.disabled = false; b.loadText.textContent = 'Load';
}
function resetBatch() {
    clearTimeout(polling);
    [b.error, b.loading, b.picker, b.progress, b.done].forEach((n) => show(n, false));
    b.entries.innerHTML = '';
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

// ── load playlist ────────────────────────────────────────────────
async function onLoad(e) {
    e.preventDefault();
    const url = b.url.value.trim();
    resetBatch();
    if (!url) return bFail('Please paste a playlist URL.');

    b.loadBtn.disabled = true; b.loadText.textContent = 'Loading…';
    show(b.loading, true);

    try {
        const data = await api('/api/batch/extract', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({ url }),
        });
        renderEntries(url, data);
    } catch (err) {
        bFail(err.message);
    }
}

function renderEntries(url, data) {
    show(b.loading, false);
    b.loadBtn.disabled = false; b.loadText.textContent = 'Load';

    playlist = { url, title: data.title || 'Playlist' };
    b.title.textContent = `${playlist.title} · ${data.count} item${data.count === 1 ? '' : 's'}`;

    if (data.truncated) {
        b.truncated.textContent = `Showing the first ${data.count} items.`;
        show(b.truncated, true);
    } else {
        show(b.truncated, false);
    }

    b.entries.innerHTML = '';
    (data.entries || []).forEach((entry) => b.entries.appendChild(entryRow(entry)));
    b.selectAll.checked = true;
    show(b.picker, true);
}

function entryRow(entry) {
    const label = document.createElement('label');
    label.className = 'flex items-center gap-3 p-2 rounded-lg hover:bg-gray-100 dark:hover:bg-gray-800 cursor-pointer';

    const cb = document.createElement('input');
    cb.type = 'checkbox'; cb.className = 'batch-item'; cb.checked = true; cb.dataset.url = entry.url;

    const img = document.createElement('img');
    img.src = entry.thumbnail || '';
    img.className = 'w-16 h-10 object-cover rounded bg-gray-200 dark:bg-gray-700 shrink-0';
    img.onerror = () => { img.style.visibility = 'hidden'; };

    const title = document.createElement('span');
    title.className = 'flex-1 min-w-0 text-sm truncate';
    title.textContent = entry.title;

    const dur = document.createElement('span');
    dur.className = 'text-xs text-gray-500 shrink-0';
    dur.textContent = fmtDuration(entry.duration);

    label.append(cb, img, title, dur);
    return label;
}

b.selectAll?.addEventListener('change', () => {
    b.entries.querySelectorAll('.batch-item').forEach((cb) => { cb.checked = b.selectAll.checked; });
});

// ── start download ───────────────────────────────────────────────
async function onDownload() {
    const urls = Array.from(b.entries.querySelectorAll('.batch-item:checked')).map((cb) => cb.dataset.url);
    if (urls.length === 0) return bFail('Select at least one item.');

    const [media_type, quality, format] = b.quality.value.split('|');

    show(b.picker, false);
    show(b.progress, true);
    b.progressText.textContent = 'Starting…';
    b.bar.style.width = '0%';

    try {
        const { uuid, total } = await api('/api/batch/download', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({ url: playlist.url, title: playlist.title, urls, media_type, quality, format }),
        });
        pollBatch(uuid, total, Date.now() + 1_800_000);
    } catch (err) {
        bFail(err.message);
    }
}

function pollBatch(uuid, total, deadline) {
    polling = setTimeout(async () => {
        try {
            const d = await api(`/api/batch/status/${uuid}`);
            if (d.status === 'completed') return batchDone(d);
            if (d.status === 'failed' || d.status === 'expired') return bFail(d.error || 'The batch failed.');

            const done = d.completed || 0;
            const t = d.total || total || 1;
            b.progressText.textContent = `Downloaded ${done} of ${t}…`;
            b.bar.style.width = `${Math.round((done / t) * 100)}%`;

            if (Date.now() > deadline) return bFail('This is taking too long. Please try again.');
            pollBatch(uuid, t, deadline);
        } catch (err) { bFail(err.message); }
    }, 2500);
}

function batchDone(d) {
    show(b.progress, false);
    b.zipLink.href = d.download_url;
    show(b.done, true);
}

b.form?.addEventListener('submit', onLoad);
b.downloadBtn?.addEventListener('click', onDownload);