<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Baca: {{ $ebook->judul }}</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css" rel="stylesheet">
    <style>
    * { margin:0; padding:0; box-sizing:border-box; }
    body { 
        background:#1a1a1a; 
        font-family:'Segoe UI',sans-serif;
        overflow: hidden;
    }
    .top-bar { 
        background:#222; 
        padding:10px 20px; 
        display:flex; 
        align-items:center; 
        justify-content:space-between; 
        position:fixed; 
        width:100%; 
        top:0; 
        z-index:100;
        height: 56px;
    }
    .pdf-container { 
        position:fixed;
        top:56px;
        left:0;
        right:0;
        bottom:0;
        overflow-y:auto;
        -webkit-overflow-scrolling: touch;
    }
    .pdf-container iframe { 
        width:100%; 
        height:100%;
        min-height: calc(100vh - 56px);
        border:none; 
        display:block;
    }
</style>
</head>
<body>

<div class="top-bar">
    <div style="display:flex;align-items:center;gap:12px">
        <a href="{{ route('ebook.show', $ebook->id) }}" style="color:#aaa;text-decoration:none;font-size:14px">
            <i class="bi bi-arrow-left"></i>
        </a>
        <div>
            <div style="color:white;font-weight:600;font-size:14px">{{ Str::limit($ebook->judul, 40) }}</div>
            <div style="color:#888;font-size:11px">{{ $ebook->penulis }}</div>
        </div>
    </div>
    <div style="display:flex;align-items:center;gap:10px">
        @if($ebook->is_vip)
            <span style="background:#f59e0b;color:white;font-size:10px;padding:3px 10px;border-radius:8px;font-weight:600;white-space:nowrap">⭐ VIP</span>
        @endif
        <a href="{{ $pdfUrl }}" download
           style="background:#1a6e35;color:white;padding:6px 14px;border-radius:8px;font-size:12px;font-weight:600;text-decoration:none">
            <i class="bi bi-download"></i> Download
        </a>
    </div>
</div>

<div class="pdf-container">
    <iframe src="{{ $pdfUrl }}#toolbar=0&navpanes=0" allowfullscreen></iframe>
</div>

</body>
</html>