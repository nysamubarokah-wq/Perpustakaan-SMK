<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="Cache-Control" content="no-cache, no-store, must-revalidate">
    <meta http-equiv="Pragma" content="no-cache">
    <meta http-equiv="Expires" content="0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Notifikasi - Perpustakaan SMK Maarif</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css" rel="stylesheet">
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: 'Segoe UI', sans-serif; background: #f5f7fa; overflow-x: hidden; }

        .navbar {
            background: white;
            box-shadow: 0 2px 15px rgba(0,0,0,0.1);
            padding: 12px 0;
            position: fixed;
            width: 100%;
            top: 0;
            z-index: 1000;
        }

        .profil-avatar { width: 38px; height: 38px; border-radius: 50%; border: 2px solid #1a6e35; cursor: pointer; object-fit: cover; }
        .nav-item { position: relative; }

        .main-container {
            max-width: 900px;
            margin: 90px auto 60px;
            padding: 0 20px;
        }

        .page-header {
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin-bottom: 24px;
            flex-wrap: wrap;
            gap: 12px;
        }

        .page-title {
            font-size: 24px;
            font-weight: 700;
            color: #222;
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .page-title i { color: #1a6e35; }

        .filter-tabs {
            display: flex;
            gap: 8px;
            margin-bottom: 20px;
            flex-wrap: wrap;
        }

        .filter-tab {
            padding: 8px 16px;
            border-radius: 20px;
            font-size: 13px;
            font-weight: 500;
            cursor: pointer;
            border: 1px solid #e5e7eb;
            background: white;
            color: #666;
            text-decoration: none;
            transition: all 0.2s;
            display: inline-flex;
            align-items: center;
            gap: 6px;
        }

        .filter-tab:hover {
            background: #f3f4f6;
            color: #333;
        }

        .filter-tab.active {
            background: linear-gradient(135deg, #1a6e35, #27ae60);
            color: white;
            border-color: #1a6e35;
        }

        .action-btn {
            padding: 8px 16px;
            border-radius: 8px;
            font-size: 13px;
            font-weight: 600;
            cursor: pointer;
            border: none;
            display: inline-flex;
            align-items: center;
            gap: 6px;
            transition: all 0.2s;
        }

        .action-btn.primary {
            background: linear-gradient(135deg, #1a6e35, #27ae60);
            color: white;
        }

        .action-btn.primary:hover {
            opacity: 0.9;
            transform: scale(1.02);
        }

        .action-btn.danger {
            background: #fee2e2;
            color: #dc2626;
        }

        .action-btn.danger:hover {
            background: #fecaca;
        }

        .notif-card {
            background: white;
            border-radius: 12px;
            box-shadow: 0 2px 8px rgba(0,0,0,0.06);
            overflow: hidden;
            margin-bottom: 16px;
            transition: all 0.2s;
        }

        .notif-card:hover {
            box-shadow: 0 4px 16px rgba(0,0,0,0.1);
        }

        .notif-card.unread {
            border-left: 4px solid #1a6e35;
        }

        .notif-card-content {
            padding: 16px;
            display: flex;
            align-items: flex-start;
            gap: 14px;
        }

        .notif-icon-wrap {
            width: 44px;
            height: 44px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            flex-shrink: 0;
        }

        .notif-icon-wrap i {
            font-size: 20px;
            color: white;
        }

        .notif-body {
            flex: 1;
            min-width: 0;
        }

        .notif-title {
            font-size: 14px;
            font-weight: 700;
            color: #222;
            margin-bottom: 4px;
            display: flex;
            align-items: center;
            gap: 8px;
            flex-wrap: wrap;
        }

        .notif-message {
            font-size: 13px;
            color: #666;
            line-height: 1.5;
            margin-bottom: 6px;
        }

        .notif-meta {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 12px;
            flex-wrap: wrap;
        }

        .notif-time {
            font-size: 12px;
            color: #999;
        }

        .notif-time i {
            margin-right: 4px;
        }

        .unread-badge {
            display: inline-flex;
            align-items: center;
            padding: 2px 8px;
            border-radius: 10px;
            font-size: 10px;
            font-weight: 700;
            background: linear-gradient(135deg, #1a6e35, #27ae60);
            color: white;
        }

        .count-badge {
            background: #e74c3c;
            color: white;
            font-size: 11px;
            font-weight: 700;
            padding: 2px 8px;
            border-radius: 10px;
            margin-left: 4px;
        }

        .notif-actions {
            display: flex;
            gap: 8px;
        }

        .action-icon {
            background: none;
            border: none;
            cursor: pointer;
            padding: 6px;
            border-radius: 6px;
            color: #999;
            transition: all 0.2s;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .action-icon:hover {
            background: #f3f4f6;
            color: #666;
        }

        .action-icon.delete:hover {
            color: #dc2626;
            background: #fee2e2;
        }

        .empty-state {
            text-align: center;
            padding: 60px 20px;
        }

        .empty-state i {
            font-size: 64px;
            color: #ddd;
            margin-bottom: 16px;
            display: block;
        }

        .empty-state h3 {
            font-size: 18px;
            font-weight: 600;
            color: #666;
            margin-bottom: 8px;
        }

        .empty-state p {
            font-size: 14px;
            color: #aaa;
        }

        .pagination .page-link {
            color: #1a6e35;
            border-color: #dee2e6;
        }

        .pagination .page-item.active .page-link {
            background: linear-gradient(135deg, #1a6e35, #27ae60);
            border-color: #1a6e35;
            color: white;
        }

        .pagination .page-link:hover {
            background: #f0faf4;
            border-color: #1a6e35;
        }

        .alert {
            border-radius: 10px;
            padding: 12px 16px;
            font-size: 14px;
        }

        .alert-success {
            background: #d4edda;
            border-color: #c3e6cb;
            color: #155724;
        }

        @media (max-width: 768px) {
            .main-container { margin-top: 80px; padding: 0 12px; margin-bottom: 30px; }
            .page-header { flex-direction: column; align-items: flex-start; }
            .page-title { font-size: 20px; }
            .notif-card-content { flex-direction: column; }
            .notif-icon-wrap { width: 40px; height: 40px; }
            .notif-actions { margin-top: 8px; }
        }

        @media (max-width: 480px) {
            .main-container { margin-top: 75px; padding: 0 10px; margin-bottom: 25px; }
            .page-title { font-size: 18px; }
            .filter-tab { padding: 6px 12px; font-size: 12px; }
            .action-btn { padding: 6px 12px; font-size: 12px; }
        }
    </style>
    @auth
        @if(auth()->user()->role !== 'admin')
    <style>
        body.dark-mode { background: #121212; color: #e0e0e0; }
        body.dark-mode .navbar { background: #1e1e1e; transition: all .3s ease; }
        body.dark-mode .navbar span { color: #1a6e35 !important; }
        body.dark-mode .notif-card { background: #1e1e1e; }
        body.dark-mode .notif-title { color: #ffffff; }
        body.dark-mode .notif-message { color: #b0b0b0; }
        body.dark-mode .notif-time { color: #888; }
        body.dark-mode .filter-tab { background: #2a2a2a; border-color: #444; color: #ccc; }
        body.dark-mode .filter-tab:hover { background: #333; color: #fff; }
        body.dark-mode .filter-tab.active { background: linear-gradient(135deg, #1a6e35, #27ae60); border-color: #1a6e35; }
        body.dark-mode .action-icon:hover { background: #2a2a2a; color: #fff; }
        body.dark-mode .action-icon.delete:hover { background: rgba(220, 38, 38, 0.2); color: #f87171; }
        body.dark-mode .empty-state i { color: #444; }
        body.dark-mode .empty-state h3 { color: #ccc; }
        body.dark-mode .empty-state p { color: #888; }
        body.dark-mode .pagination .page-link { background: #2a2a2a; border-color: #444; color: #e0e0e0; }
        body.dark-mode .pagination .page-item.active .page-link { background: linear-gradient(135deg, #1a6e35, #27ae60); border-color: #1a6e35; }
        body.dark-mode .pagination .page-link:hover { background: #3a3a3a; }
        body.dark-mode .alert-success { background: #1a3d1a; border-color: #2d5c2d; color: #81c784; }
        body.dark-mode .action-btn.primary { background: #2d6a3e; }
        body.dark-mode .action-btn.danger { background: rgba(220, 38, 38, 0.15); color: #f87171; }
        body.dark-mode .action-btn.danger:hover { background: rgba(220, 38, 38, 0.25); }
        body.dark-mode .count-badge { background: #e74c3c; }
    </style>
        @endif
    @endauth
</head>
<body>
    @php
        session()->forget('notifikasi_from_notif');
        $isAdmin = auth()->check() && auth()->user()->role === 'admin';
        $backUrl = session('notifikasi_back_url')
            ?? ($isAdmin ? route('admin.dashboard') : route('koleksi.index'));
    @endphp

<nav class="navbar">
    <div class="container-fluid px-4">
        <div class="d-flex align-items-center justify-content-between w-100">
            <a href="{{ $backUrl }}" class="d-flex align-items-center gap-2 text-decoration-none">
                <img src="{{ asset('images/logo.jpg') }}" style="width:45px;height:45px;border-radius:50%;object-fit:cover" alt="Logo">
                <span style="font-size:13px;font-weight:700;color:#1a6e35;text-transform:uppercase;line-height:1.3">SMK Maarif<br>Walisongo Kajoran</span>
            </a>
            <div class="d-flex align-items-center gap-2">
                @if(!$isAdmin)
                <button id="darkModeToggle" class="btn btn-sm btn-outline-secondary rounded-circle"><i class="bi bi-moon-fill"></i></button>
                @endif
                <a href="{{ $backUrl }}" style="color:#1a6e35;text-decoration:none;font-size:14px;font-weight:500">
                    <i class="bi bi-arrow-left"></i> Kembali
                </a>
            </div>
        </div>
    </div>
</nav>

<div class="main-container">
    <div class="page-header">
        <h1 class="page-title">
            <i class="bi bi-bell-fill"></i>
            Notifikasi
            @if($belumDibaca > 0)
                <span class="count-badge">{{ $belumDibaca }}</span>
            @endif
        </h1>
        <div class="d-flex gap-2 flex-wrap" id="actionButtons">
            @if($belumDibaca > 0)
                <button type="button" class="action-btn primary" onclick="markAllRead()">
                    <i class="bi bi-check-all"></i> Tandai Semua Dibaca
                </button>
            @endif
            <form action="{{ route('notifikasi.destroyAll') }}" method="POST" id="deleteAllForm" style="display:inline">
                @csrf
                <button type="submit" class="action-btn danger">
                    <i class="bi bi-trash"></i> Hapus Semua
                </button>
            </form>
        </div>
    </div>

    <div class="filter-tabs">
        <a href="{{ route('notifikasi.index') }}" class="filter-tab {{ !request('filter') ? 'active' : '' }}">
            Semua
        </a>
        <a href="{{ route('notifikasi.index', ['filter' => 'belum_dibaca']) }}" class="filter-tab {{ request('filter') === 'belum_dibaca' ? 'active' : '' }}">
            <i class="bi bi-envelope"></i> Belum Dibaca
            @if($belumDibaca > 0)
                <span class="count-badge">{{ $belumDibaca }}</span>
            @endif
        </a>
        <a href="{{ route('notifikasi.index', ['filter' => 'dibaca']) }}" class="filter-tab {{ request('filter') === 'dibaca' ? 'active' : '' }}">
            <i class="bi bi-envelope-open"></i> Dibaca
        </a>
    </div>

    @if(session('success'))
        <div class="alert alert-success mb-3">{{ session('success') }}</div>
    @endif

    @if($notifikasi->count() > 0)
        @foreach($notifikasi as $item)
            @php
                $iconBg = match($item->icon) {
                    'book' => '#27ae60',
                    'arrow-repeat' => '#3498db',
                    'clock' => '#f39c12',
                    'exclamation-triangle' => '#e74c3c',
                    'coin' => '#f1c40f',
                    'star' => '#f1c40f',
                    'check-circle' => '#27ae60',
                    'x-circle' => '#e74c3c',
                    'arrow-counterclockwise' => '#3498db',
                    default => '#6c757d'
                };
            @endphp
            <div class="notif-card {{ !$item->is_read ? 'unread' : '' }}" id="notif-card-{{ $item->id }}" onclick="window.location.href='/notifikasi/{{ $item->id }}/baca?from=notif'" style="cursor:pointer;">
                <div class="notif-card-content">
                    <div class="notif-icon-wrap" style="background: {{ $iconBg }}">
                        <i class="bi bi-{{ $item->icon ?? 'bell' }}"></i>
                    </div>
                    <div class="notif-body">
                        <div class="notif-title">
                            {{ $item->judul }}
                            @if(!$item->is_read)
                                <span class="unread-badge">Baru</span>
                            @endif
                        </div>
                        <div class="notif-message">{{ $item->pesan }}</div>
                        <div class="notif-meta">
                            <span class="notif-time">
                                <i class="bi bi-clock"></i> {{ $item->created_at->diffForHumans() }}
                            </span>
                            <div class="notif-actions">
                                @if($item->link)
                                    <a href="{{ $item->link }}?from=notif" class="action-icon" title="Buka link" onclick="event.stopPropagation()">
                                        <i class="bi bi-box-arrow-up-right"></i>
                                    </a>
                                @endif
                                @if(!$item->is_read)
                                    <button type="button" class="action-icon" onclick="event.stopPropagation(); markSingleRead({{ $item->id }})" title="Tandai dibaca">
                                        <i class="bi bi-check-circle"></i>
                                    </button>
                                @endif
                                <button type="button" class="action-icon delete" onclick="event.stopPropagation(); deleteSingle({{ $item->id }})" title="Hapus">
                                    <i class="bi bi-trash"></i>
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        @endforeach

        <div class="d-flex justify-content-center mt-4">
            {{ $notifikasi->appends(request()->query())->links() }}
        </div>
    @else
        <div class="empty-state">
            <i class="bi bi-bell-slash"></i>
            <h3>Tidak Ada Notifikasi</h3>
            <p>Semua notifikasi akan muncul di sini.</p>
        </div>
    @endif
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    const csrfToken = document.querySelector('meta[name="csrf-token"]')?.content || '';

    document.getElementById('deleteAllForm')?.addEventListener('submit', function(e) {
        e.preventDefault();
        if (!confirm('Hapus semua notifikasi? Tindakan ini tidak dapat dibatalkan.')) return;
        const form = this;
        fetch(form.action, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'Accept': 'application/json',
                'X-CSRF-TOKEN': csrfToken
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                document.querySelectorAll('.notif-card').forEach(card => {
                    card.style.transition = 'opacity 0.3s';
                    card.style.opacity = '0';
                });
                setTimeout(() => {
                    location.reload();
                }, 300);
            }
        })
        .catch(error => console.error('Error:', error));
    });
});

function markAllRead() {
    fetch('{{ route("notifikasi.bacaSemua") }}', {
        credentials: 'include',
        headers: {
            'X-Requested-With': 'XMLHttpRequest',
            'Accept': 'application/json',
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            document.querySelectorAll('.notif-card.unread').forEach(card => {
                card.classList.remove('unread');
                const badge = card.querySelector('.unread-badge');
                if (badge) badge.remove();
                const actions = card.querySelector('.notif-actions');
                const readBtn = actions?.querySelector('[title="Tandai dibaca"]');
                if (readBtn) readBtn.remove();
            });
            document.getElementById('markAllReadBtn')?.remove();
            updatePageCount();
        }
    })
    .catch(error => console.error('Error:', error));
}

function markSingleRead(id) {
    fetch(`/notifikasi/${id}/baca`, {
        credentials: 'include',
        headers: {
            'X-Requested-With': 'XMLHttpRequest',
            'Accept': 'application/json',
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            const card = document.getElementById(`notif-card-${id}`);
            if (card) {
                card.classList.remove('unread');
                const badge = card.querySelector('.unread-badge');
                if (badge) badge.remove();
                const actions = card.querySelector('.notif-actions');
                const readBtn = actions?.querySelector('[title="Tandai dibaca"]');
                if (readBtn) readBtn.remove();
            }
            updatePageCount();
        }
    })
    .catch(error => console.error('Error:', error));
}

function deleteSingle(id) {
    if (!confirm('Hapus notifikasi ini?')) return;
    
    fetch(`/notifikasi/${id}`, {
        method: 'DELETE',
        headers: {
            'Content-Type': 'application/json',
            'Accept': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content || ''
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            const card = document.getElementById(`notif-card-${id}`);
            if (card) {
                card.style.transition = 'opacity 0.3s';
                card.style.opacity = '0';
                setTimeout(() => {
                    card.remove();
                    updatePageCount();
                }, 300);
            }
        }
    })
    .catch(error => console.error('Error:', error));
}

function updatePageCount() {
    const url = new URL(window.location.href);
    const currentFilter = url.searchParams.get('filter');
    
    fetch('{{ route("notifikasi.unreadCount") }}', {
        headers: {
            'Content-Type': 'application/json',
            'Accept': 'application/json',
        }
    })
    .then(response => response.json())
    .then(data => {
        const titleBadge = document.querySelector('.page-title .count-badge');
        const filterBadge = document.querySelector('.filter-tab.active .count-badge');
        
        if (data.count > 0) {
            if (titleBadge) {
                titleBadge.textContent = data.count;
            } else {
                const newTitleBadge = document.createElement('span');
                newTitleBadge.className = 'count-badge';
                newTitleBadge.textContent = data.count;
                document.querySelector('.page-title').appendChild(newTitleBadge);
            }
            
            if (filterBadge) {
                filterBadge.textContent = data.count;
            } else if (currentFilter === 'belum_dibaca') {
                const activeTab = document.querySelector('.filter-tab.active');
                if (activeTab) {
                    const newFilterBadge = document.createElement('span');
                    newFilterBadge.className = 'count-badge';
                    newFilterBadge.textContent = data.count;
                    activeTab.appendChild(newFilterBadge);
                }
            }
        } else {
            if (titleBadge) titleBadge.remove();
            if (filterBadge) filterBadge.remove();
            
            if (currentFilter === 'belum_dibaca' && document.querySelectorAll('.notif-card').length === 0) {
                location.reload();
            }
        }
    })
    .catch(error => console.error('Error:', error));
}

setInterval(updatePageCount, 30000);

// Dark mode toggle - hanya untuk user
(function initDarkMode() {
    const btn = document.getElementById('darkModeToggle');
    if (!btn) return;
    
    if (localStorage.getItem('darkMode') === 'enabled') {
        document.body.classList.add('dark-mode');
        btn.innerHTML = '<i class="bi bi-sun-fill"></i>';
    }
    
    btn.addEventListener('click', function() {
        document.body.classList.toggle('dark-mode');
        if (document.body.classList.contains('dark-mode')) {
            localStorage.setItem('darkMode', 'enabled');
            btn.innerHTML = '<i class="bi bi-sun-fill"></i>';
        } else {
            localStorage.setItem('darkMode', 'disabled');
            btn.innerHTML = '<i class="bi bi-moon-fill"></i>';
        }
    });
})();
</script>

</body>
</html>
