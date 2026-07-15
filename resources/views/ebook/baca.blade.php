<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=5.0, user-scalable=yes">
    <title>Baca: {{ $ebook->judul }}</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css" rel="stylesheet">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/pdf.js/3.11.174/pdf.min.js"></script>
    <style>
    * { margin:0; padding:0; box-sizing:border-box; }
    html, body { height:100%; overflow:hidden; }
    body { background:#1a1a1a; font-family:'Segoe UI',sans-serif; }

    .top-bar {
        background:#222; padding:10px 16px;
        display:flex; align-items:center; justify-content:space-between;
        position:fixed; width:100%; top:0; z-index:1000; height:56px;
    }
    .top-bar .left { display:flex; align-items:center; gap:12px; min-width:0; flex:1; }
    .top-bar .title { color:white; font-weight:600; font-size:14px; white-space:nowrap; overflow:hidden; text-overflow:ellipsis; max-width:180px; }
    .top-bar .author { color:#888; font-size:11px; }
    .top-bar .center { display:flex; align-items:center; gap:6px; flex-shrink:0; }
    .top-bar .right { display:flex; align-items:center; gap:10px; flex-shrink:0; }
    .btn-back { color:#aaa; text-decoration:none; font-size:18px; padding:4px 8px; }
    .btn-back:hover { color:white; }
    .btn-download { background:#1a6e35; color:white; padding:6px 14px; border-radius:8px; font-size:12px; font-weight:600; text-decoration:none; white-space:nowrap; }
    .btn-download:hover { background:#155c2a; color:white; }
    .badge-vip { background:#f59e0b; color:white; font-size:10px; padding:3px 10px; border-radius:8px; font-weight:600; white-space:nowrap; }

    .zoom-controls {
        display:flex; align-items:center; gap:4px;
        background:#333; border-radius:8px; padding:2px;
    }
    .zoom-btn {
        background:transparent; color:#ccc; border:none; padding:5px 8px;
        font-size:14px; cursor:pointer; border-radius:6px; line-height:1;
    }
    .zoom-btn:hover { background:#444; color:white; }
    .zoom-level {
        color:#aaa; font-size:11px; min-width:40px; text-align:center;
        padding:4px 4px; background:#2a2a2a; border-radius:4px;
    }

    .pdf-container {
        position:fixed; top:56px; left:0; right:0; bottom:0;
        overflow-y:auto; overflow-x:auto; -webkit-overflow-scrolling:touch;
        background:#2a2a2a; scroll-behavior:auto;
    }
    .pdf-container::-webkit-scrollbar { width:8px; height:8px; }
    .pdf-container::-webkit-scrollbar-track { background:#1a1a1a; }
    .pdf-container::-webkit-scrollbar-thumb { background:#444; border-radius:4px; }
    .pdf-container::-webkit-scrollbar-thumb:hover { background:#555; }

    .pdf-pages {
        display:flex; flex-direction:column; align-items:center;
        padding:16px 8px; gap:12px; min-height:100%;
    }

    .pdf-page-wrapper {
        position:relative; background:white;
        box-shadow:0 2px 12px rgba(0,0,0,0.4); border-radius:4px;
        overflow:hidden;
    }
    .pdf-page-wrapper canvas {
        display:block; max-width:100%; height:auto;
    }
    .pdf-page-wrapper .page-number {
        position:absolute; bottom:8px; right:12px;
        background:rgba(0,0,0,0.6); color:white;
        padding:2px 8px; border-radius:4px; font-size:11px;
    }
    .pdf-page-wrapper.rendering {
        background:#333; min-height:200px;
        display:flex; align-items:center; justify-content:center;
    }
    .pdf-page-wrapper.rendering::after {
        content:''; width:30px; height:30px;
        border:3px solid #555; border-top-color:#7c3aed;
        border-radius:50%; animation:spin 0.8s linear infinite;
    }

    .page-indicator {
        position:fixed; bottom:16px; right:16px;
        background:rgba(0,0,0,0.8); color:white; padding:8px 16px;
        border-radius:20px; font-size:13px; z-index:1000;
        backdrop-filter:blur(4px); display:flex; align-items:center; gap:8px;
    }
    .page-indicator i { color:#7c3aed; }

    .loading {
        position:fixed; inset:0; top:56px;
        display:flex; align-items:center; justify-content:center;
        flex-direction:column; gap:16px; color:#aaa; font-size:14px;
    }
    .spinner {
        width:40px; height:40px; border:3px solid #444;
        border-top-color:#7c3aed; border-radius:50%;
        animation:spin 0.8s linear infinite;
    }
    @keyframes spin { to { transform:rotate(360deg); } }

    .error-msg {
        text-align:center; color:#e74c3c; padding:40px 20px;
    }
    .error-msg a { color:#7c3aed; }

    .page-skeleton {
        background:linear-gradient(90deg, #2a2a2a 25%, #333 50%, #2a2a2a 75%);
        background-size:200% 100%; animation:shimmer 1.5s infinite;
        border-radius:4px;
    }
    @keyframes shimmer { to { background-position:-200% 0; } }

    @media (max-width:600px) {
        .top-bar { padding:8px 12px; height:50px; }
        .top-bar .title { font-size:12px; max-width:120px; }
        .top-bar .author { display:none; }
        .btn-download { padding:5px 10px; font-size:11px; }
        .badge-vip { font-size:9px; padding:2px 8px; }
        .pdf-container { top:50px; }
        .zoom-controls { display:none; }
        .page-indicator { bottom:12px; right:12px; padding:6px 12px; font-size:12px; }
        .pdf-pages { padding:10px 4px; gap:8px; }
    }

    @media (max-width:400px) {
        .top-bar .title { max-width:80px; }
    }
    </style>
</head>
<body>

<div class="top-bar">
    <div class="left">
        <a href="{{ route('ebook.show', $ebook->id) }}" class="btn-back" title="Kembali">
            <i class="bi bi-arrow-left"></i>
        </a>
        <div>
            <div class="title">{{ $ebook->judul }}</div>
            <div class="author">{{ $ebook->penulis }}</div>
        </div>
    </div>
    <div class="center">
        <div class="zoom-controls">
            <button class="zoom-btn" onclick="adjustZoom(-0.25)" title="Zoom Out"><i class="bi bi-dash"></i></button>
            <span class="zoom-level" id="zoomLevel">100%</span>
            <button class="zoom-btn" onclick="adjustZoom(0.25)" title="Zoom In"><i class="bi bi-plus"></i></button>
            <button class="zoom-btn" onclick="fitWidth()" title="Sesuaikan Lebar"><i class="bi bi-arrows-fullscreen"></i></button>
        </div>
    </div>
    <div class="right">
        @if($ebook->is_vip)
            <span class="badge-vip">VIP</span>
        @endif
        <a href="{{ $pdfUrl }}" download class="btn-download" id="btnDownload" onclick="this.innerHTML='<i class=\'bi bi-hourglass-split\'></i>...'">
            <i class="bi bi-download"></i> Download
        </a>
    </div>
</div>

<div class="loading" id="loadingEl">
    <div class="spinner"></div>
    <div>Memuat dokumen...</div>
</div>

<div class="pdf-container" id="pdfContainer" style="display:none">
    <div class="pdf-pages" id="pdfPages"></div>
</div>

<div class="page-indicator" id="pageIndicator" style="display:none">
    <i class="bi bi-file-earmark-text"></i>
    <span id="currentPageLabel">0 / 0</span>
</div>

<script>
pdfjsLib.GlobalWorkerOptions.workerSrc = 'https://cdnjs.cloudflare.com/ajax/libs/pdf.js/3.11.174/pdf.worker.min.js';

const pdfUrl = '{{ $pdfUrl }}';
const container = document.getElementById('pdfContainer');
const pagesContainer = document.getElementById('pdfPages');
const loadingEl = document.getElementById('loadingEl');
const pageIndicator = document.getElementById('pageIndicator');
const zoomLevelEl = document.getElementById('zoomLevel');
const currentPageLabel = document.getElementById('currentPageLabel');

let pdfDoc = null;
let currentScale = 1.5;
let renderedPages = new Map();
let pageContainers = [];
let numPages = 0;
let isRendering = false;
let pendingRenders = [];
let scrollTimeout = null;
let resizeTimeout = null;
let lastScrollTop = 0;
let initialized = false;

function showError(msg) {
    loadingEl.innerHTML =
        '<div class="error-msg">' +
        '<p style="font-size:48px;margin-bottom:16px">&#128221;</p>' +
        '<p style="font-size:16px;font-weight:600;margin-bottom:8px">Gagal memuat file</p>' +
        '<p>' + msg + '</p>' +
        '<a href="' + pdfUrl + '" download style="display:inline-block;margin-top:20px;padding:12px 24px;background:#1a6e35;color:white;border-radius:10px;font-weight:600;text-decoration:none">' +
        '<i class="bi bi-download"></i> Download PDF</a></div>';
}

function updateZoomDisplay() {
    zoomLevelEl.textContent = Math.round(currentScale * 100) + '%';
}

async function renderPage(pageNum) {
    if (renderedPages.has(pageNum) || pendingRenders.includes(pageNum)) return;
    if (pageNum < 1 || pageNum > numPages) return;

    const wrapper = pageContainers[pageNum - 1];
    if (!wrapper) return;

    pendingRenders.push(pageNum);
    wrapper.classList.add('rendering');

    try {
        const page = await pdfDoc.getPage(pageNum);
        const viewport = page.getViewport({ scale: currentScale });

        const canvas = document.createElement('canvas');
        const ctx = canvas.getContext('2d');
        canvas.width = viewport.width;
        canvas.height = viewport.height;
        canvas.dataset.pageNum = pageNum;

        wrapper.innerHTML = '';
        wrapper.appendChild(canvas);

        const pageNumEl = document.createElement('div');
        pageNumEl.className = 'page-number';
        pageNumEl.textContent = pageNum;
        wrapper.appendChild(pageNumEl);

        await page.render({ canvasContext: ctx, viewport: viewport }).promise;
        renderedPages.set(pageNum, true);
    } catch (err) {
        console.error('Error rendering page', pageNum, err);
    } finally {
        const idx = pendingRenders.indexOf(pageNum);
        if (idx > -1) pendingRenders.splice(idx, 1);
        wrapper.classList.remove('rendering');
    }
}

function renderVisiblePages() {
    if (isRendering) return;
    isRendering = true;

    const scrollTop = container.scrollTop;
    const viewportHeight = container.clientHeight;
    const buffer = 500;

    const startY = scrollTop - buffer;
    const endY = scrollTop + viewportHeight + buffer;

    let pagesToRender = [];

    for (let i = 0; i < pageContainers.length; i++) {
        const wrapper = pageContainers[i];
        const rect = wrapper.getBoundingClientRect();
        const wrapperTop = rect.top + scrollTop;
        const wrapperBottom = wrapperTop + rect.height;

        if ((wrapperBottom >= startY && wrapperTop <= endY) && !renderedPages.has(i + 1)) {
            pagesToRender.push(i + 1);
        }
    }

    const batchSize = 3;
    let index = 0;

    function processBatch() {
        const batch = pagesToRender.slice(index, index + batchSize);
        if (batch.length === 0) {
            isRendering = false;
            return;
        }

        batch.forEach(pageNum => renderPage(pageNum));
        index += batchSize;

        if (index < pagesToRender.length) {
            requestAnimationFrame(processBatch);
        } else {
            isRendering = false;
        }
    }

    processBatch();
}

function updateCurrentPage() {
    const scrollTop = container.scrollTop;
    const viewportHeight = container.clientHeight;
    const centerY = scrollTop + viewportHeight / 2;

    let activePage = 1;
    let minDistance = Infinity;

    for (let i = 0; i < pageContainers.length; i++) {
        const wrapper = pageContainers[i];
        const rect = wrapper.getBoundingClientRect();
        const pageCenter = rect.top + rect.height / 2;

        const distance = Math.abs(pageCenter - centerY - container.getBoundingClientRect().top);
        if (distance < minDistance) {
            minDistance = distance;
            activePage = i + 1;
        }
    }

    currentPageLabel.textContent = activePage + ' / ' + numPages;
}

function onScroll() {
    if (scrollTimeout) cancelAnimationFrame(scrollTimeout);
    scrollTimeout = requestAnimationFrame(() => {
        renderVisiblePages();
        updateCurrentPage();
    });
}

function adjustZoom(delta) {
    const newScale = Math.max(0.5, Math.min(3, currentScale + delta));
    if (newScale === currentScale) return;
    
    const scrollTop = container.scrollTop;
    const viewportHeight = container.clientHeight;
    const scrollRatio = (scrollTop + viewportHeight / 2) / (pagesContainer.scrollHeight || 1);

    currentScale = newScale;
    updateZoomDisplay();
    invalidateAllPages();
    
    requestAnimationFrame(() => {
        const newScrollTop = scrollRatio * pagesContainer.scrollHeight - viewportHeight / 2;
        container.scrollTop = Math.max(0, newScrollTop);
        renderVisiblePages();
    });
}

function fitWidth() {
    if (!pdfDoc || pageContainers.length === 0) return;

    const firstPage = pageContainers[0];
    if (!firstPage) return;

    const containerWidth = container.clientWidth - 16;
    
    pdfDoc.getPage(1).then(page => {
        const viewport = page.getViewport({ scale: 1 });
        const newScale = containerWidth / viewport.width;
        const clampedScale = Math.max(0.5, Math.min(3, newScale));

        const scrollTop = container.scrollTop;
        const viewportHeight = container.clientHeight;
        const scrollRatio = (scrollTop + viewportHeight / 2) / (pagesContainer.scrollHeight || 1);

        currentScale = clampedScale;
        updateZoomDisplay();
        invalidateAllPages();

        requestAnimationFrame(() => {
            const newScrollTop = scrollRatio * pagesContainer.scrollHeight - viewportHeight / 2;
            container.scrollTop = Math.max(0, newScrollTop);
            renderVisiblePages();
        });
    });
}

function invalidateAllPages() {
    renderedPages.clear();
    pendingRenders = [];
    
    for (let i = 0; i < pageContainers.length; i++) {
        const wrapper = pageContainers[i];
        wrapper.innerHTML = '';
        wrapper.style.minHeight = '200px';
        wrapper.classList.add('rendering');
    }
}

function createPageContainers() {
    pagesContainer.innerHTML = '';
    pageContainers = [];
    renderedPages.clear();
    pendingRenders = [];

    for (let i = 1; i <= numPages; i++) {
        const wrapper = document.createElement('div');
        wrapper.className = 'pdf-page-wrapper rendering';
        wrapper.dataset.pageNum = i;
        pagesContainer.appendChild(wrapper);
        pageContainers.push(wrapper);
    }
}

function preserveScrollPosition() {
    if (!initialized || pageContainers.length === 0) return;

    const scrollTop = container.scrollTop;
    const viewportHeight = container.clientHeight;
    const scrollRatio = scrollTop / (pagesContainer.scrollHeight || 1);

    renderVisiblePages();

    requestAnimationFrame(() => {
        const newScrollTop = scrollRatio * pagesContainer.scrollHeight;
        container.scrollTop = Math.max(0, Math.min(newScrollTop, pagesContainer.scrollHeight - viewportHeight));
    });
}

pdfjsLib.getDocument(pdfUrl).promise.then(async function(pdf) {
    pdfDoc = pdf;
    numPages = pdf.numPages;
    initialized = false;

    loadingEl.style.display = 'none';
    container.style.display = 'block';
    pageIndicator.style.display = 'flex';

    createPageContainers();

    initialized = true;

    fitWidth();

    container.addEventListener('scroll', onScroll, { passive: true });

    updateCurrentPage();

    renderVisiblePages();

}).catch(function(err) {
    console.error('PDF load error:', err);
    showError('File PDF tidak dapat dimuat. Coba download langsung.');
});

let resizeObserver = new ResizeObserver(() => {
    if (resizeTimeout) clearTimeout(resizeTimeout);
    resizeTimeout = setTimeout(preserveScrollPosition, 100);
});
resizeObserver.observe(container);

window.addEventListener('orientationchange', () => {
    setTimeout(preserveScrollPosition, 200);
});
</script>

</body>
</html>
