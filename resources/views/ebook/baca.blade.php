<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Baca: {{ $ebook->judul }}</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css" rel="stylesheet">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/pdf.js/3.11.174/pdf.min.js"></script>
    <style>
    * { margin:0; padding:0; box-sizing:border-box; }
    body { background:#1a1a1a; font-family:'Segoe UI',sans-serif; overflow:hidden; }

    .top-bar {
        background:#222; padding:10px 16px;
        display:flex; align-items:center; justify-content:space-between;
        position:fixed; width:100%; top:0; z-index:100; height:56px;
    }
    .top-bar .left { display:flex; align-items:center; gap:12px; min-width:0; }
    .top-bar .title { color:white; font-weight:600; font-size:14px; white-space:nowrap; overflow:hidden; text-overflow:ellipsis; max-width:200px; }
    .top-bar .author { color:#888; font-size:11px; }
    .top-bar .right { display:flex; align-items:center; gap:10px; flex-shrink:0; }
    .btn-back { color:#aaa; text-decoration:none; font-size:16px; }
    .btn-download { background:#1a6e35; color:white; padding:6px 14px; border-radius:8px; font-size:12px; font-weight:600; text-decoration:none; white-space:nowrap; }
    .badge-vip { background:#f59e0b; color:white; font-size:10px; padding:3px 10px; border-radius:8px; font-weight:600; white-space:nowrap; }

    .pdf-viewer {
        position:fixed; top:56px; left:0; right:0; bottom:0;
        overflow-y:auto; -webkit-overflow-scrolling:touch;
        display:flex; flex-direction:column; align-items:center;
        padding:10px; gap:8px; background:#2a2a2a;
    }

    .pdf-viewer canvas {
        max-width:100%; height:auto;
        box-shadow:0 2px 12px rgba(0,0,0,0.4); border-radius:4px;
    }

    .page-info {
        position:fixed; bottom:16px; left:50%; transform:translateX(-50%);
        background:rgba(0,0,0,0.75); color:white; padding:8px 20px;
        border-radius:20px; font-size:13px; z-index:100;
        display:flex; align-items:center; gap:12px;
    }
    .page-info button {
        background:#444; color:white; border:none; border-radius:6px;
        padding:4px 12px; font-size:14px; cursor:pointer;
    }
    .page-info button:disabled { opacity:0.3; cursor:default; }

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

    @media (max-width:480px) {
        .top-bar { padding:8px 12px; height:50px; }
        .top-bar .title { font-size:12px; max-width:130px; }
        .top-bar .author { font-size:10px; }
        .btn-download { padding:5px 10px; font-size:11px; }
        .pdf-viewer { top:50px; padding:6px; }
        .page-info { bottom:12px; padding:6px 14px; font-size:12px; }
    }
    </style>
</head>
<body>

<div class="top-bar">
    <div class="left">
        <a href="{{ route('ebook.show', $ebook->id) }}" class="btn-back"><i class="bi bi-arrow-left"></i></a>
        <div>
            <div class="title">{{ $ebook->judul }}</div>
            <div class="author">{{ $ebook->penulis }}</div>
        </div>
    </div>
    <div class="right">
        @if($ebook->is_vip)
            <span class="badge-vip">VIP</span>
        @endif
        <a href="{{ $pdfUrl }}" download class="btn-download" id="btnDownload" onclick="this.innerHTML='<i class=\'bi bi-hourglass-split\'></i> Mengunduh...'">
            <i class="bi bi-download"></i> Download
        </a>
    </div>
</div>

<div class="loading" id="loadingEl">
    <div class="spinner"></div>
    <div>Memuat dokumen...</div>
</div>

<div class="pdf-viewer" id="pdfViewer" style="display:none"></div>

<div class="page-info" id="pageInfo" style="display:none">
    <button id="prevBtn" onclick="goPage(currentPage-1)">&#8249;</button>
    <span id="pageLabel">1 / 1</span>
    <button id="nextBtn" onclick="goPage(currentPage+1)">&#8250;</button>
</div>

<script>
pdfjsLib.GlobalWorkerOptions.workerSrc = 'https://cdnjs.cloudflare.com/ajax/libs/pdf.js/3.11.174/pdf.worker.min.js';

var pdfUrl = '{{ $pdfUrl }}';
var pdfDoc = null;
var currentPage = 1;
var container = document.getElementById('pdfViewer');
var loadingEl = document.getElementById('loadingEl');
var pageInfo = document.getElementById('pageInfo');

function showError(msg) {
    loadingEl.innerHTML =
        '<div class="error-msg">' +
        '<p style="font-size:48px;margin-bottom:16px">&#128221;</p>' +
        '<p style="font-size:16px;font-weight:600;margin-bottom:8px">Gagal memuat file</p>' +
        '<p>' + msg + '</p>' +
        '<a href="' + pdfUrl + '" download style="display:inline-block;margin-top:20px;padding:12px 24px;background:#1a6e35;color:white;border-radius:10px;font-weight:600;text-decoration:none">' +
        '<i class="bi bi-download"></i> Download PDF</a></div>';
}

function renderPage(num) {
    if (!pdfDoc) return;
    if (num < 1 || num > pdfDoc.numPages) return;
    currentPage = num;

    pdfDoc.getPage(num).then(function(page) {
        var scale = 1.5;
        var viewport = page.getViewport({ scale: scale });

        // Sesuaikan lebar dengan container
        var containerWidth = container.clientWidth - 20;
        if (viewport.width > containerWidth) {
            scale = containerWidth / viewport.width;
            viewport = page.getViewport({ scale: scale });
        }

        var canvas = document.createElement('canvas');
        var ctx = canvas.getContext('2d');
        canvas.width = viewport.width;
        canvas.height = viewport.height;

        // Hapus canvas lama
        container.innerHTML = '';
        container.appendChild(canvas);

        page.render({ canvasContext: ctx, viewport: viewport });

        document.getElementById('pageLabel').textContent = num + ' / ' + pdfDoc.numPages;
        document.getElementById('prevBtn').disabled = (num <= 1);
        document.getElementById('nextBtn').disabled = (num >= pdfDoc.numPages);
    });
}

function goPage(n) {
    renderPage(n);
}

// Load PDF
pdfjsLib.getDocument(pdfUrl).promise.then(function(pdf) {
    pdfDoc = pdf;
    loadingEl.style.display = 'none';
    container.style.display = 'flex';
    pageInfo.style.display = 'flex';
    renderPage(1);
}).catch(function(err) {
    console.error('PDF load error:', err);
    showError('File PDF tidak dapat dimuat. Coba download langsung.');
});
</script>

</body>
</html>
