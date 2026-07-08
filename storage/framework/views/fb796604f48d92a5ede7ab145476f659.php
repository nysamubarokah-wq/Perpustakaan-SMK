<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="<?php echo e(csrf_token()); ?>">
    <title>Perpustakaan SMK Maarif Walisongo Kajoran</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/cropperjs@1.6.1/dist/cropper.min.css" rel="stylesheet">
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: 'Segoe UI', sans-serif; background: #f5f7fa; overflow-x: hidden; }

        /* NAVBAR */
        .navbar {
            background: white;
            box-shadow: 0 2px 15px rgba(0,0,0,0.1);
            padding: 12px 0;
            position: fixed;
            width: 100%;
            top: 0;
            z-index: 1000;
            overflow: visible;
        }
        .navbar-brand img { width: 45px; height: 45px; border-radius: 50%; object-fit: cover; }
        .navbar-brand span { font-size: 13px; font-weight: 700; color: #1a6e35; text-transform: uppercase; line-height: 1.3; }
        .nav-link { color: #333 !important; font-weight: 500; font-size: 14px; padding: 8px 15px !important; transition: color 0.3s; }
        .nav-link:hover { color: #1a6e35 !important; }
        .nav-link.active { color: #1a6e35 !important; }

        /* Dropdown ngambang - Genre */
        .floating-dropdown { position: absolute; top: calc(100% + 10px); left: 0; background: white; border-radius: 15px; box-shadow: 0 10px 40px rgba(0,0,0,0.15); padding: 20px; min-width: 220px; display: none; z-index: 999; animation: fadeDown 0.2s ease; }
        @keyframes fadeDown { from { opacity: 0; transform: translateY(-10px); } to { opacity: 1; transform: translateY(0); } }
        .floating-dropdown.show { display: block; }
        .floating-dropdown h6 { font-size: 11px; color: #aaa; text-transform: uppercase; letter-spacing: 1px; margin-bottom: 12px; }
        .genre-list-item { display: flex; align-items: center; gap: 10px; padding: 8px 10px; border-radius: 8px; cursor: pointer; transition: background 0.2s; font-size: 13px; color: #333; text-decoration: none; }
        .genre-list-item:hover { background: #f0faf4; color: #1a6e35; }
        .genre-list-item i { color: #1a6e35; width: 18px; }

        /* Genre Dropdown */
        .genre-dropdown-wrapper { min-width: 400px; max-width: 500px; }
        @media (max-width: 576px) { .genre-dropdown-wrapper { min-width: 280px; max-width: 95vw; } }
        .genre-dropdown-header { display: flex; align-items: center; justify-content: space-between; padding-bottom: 12px; border-bottom: 1px solid #e9ecef; margin-bottom: 12px; }
        .genre-dropdown-header h6 { font-size: 12px; color: #6c757d; text-transform: uppercase; letter-spacing: 1px; margin: 0; font-weight: 600; }
        .genre-dropdown-header .badge { font-size: 10px; padding: 4px 8px; border-radius: 20px; background: #e8f5e9; color: #1a6e35; }
        .genre-search-box { position: relative; margin-bottom: 12px; }
        .genre-search-box input { width: 100%; padding: 10px 12px 10px 36px; border: 1px solid #e9ecef; border-radius: 10px; font-size: 13px; transition: border-color 0.2s, box-shadow 0.2s; background: #f8f9fa; }
        .genre-search-box input:focus { outline: none; border-color: #1a6e35; box-shadow: 0 0 0 3px rgba(26, 110, 53, 0.1); background: white; }
        .genre-search-box i { position: absolute; left: 12px; top: 50%; transform: translateY(-50%); color: #adb5bd; font-size: 14px; }
        .genre-dropdown-body { max-height: 50vh; overflow-y: auto; scrollbar-width: thin; scrollbar-color: #c1dbc8 transparent; }
        .genre-dropdown-body::-webkit-scrollbar { width: 6px; }
        .genre-dropdown-body::-webkit-scrollbar-track { background: transparent; }
        .genre-dropdown-body::-webkit-scrollbar-thumb { background: #c1dbc8; border-radius: 3px; }
        .genre-dropdown-body::-webkit-scrollbar-thumb:hover { background: #1a6e35; }
        .genre-grid { display: grid; grid-template-columns: repeat(2, 1fr); gap: 6px; }
        @media (max-width: 576px) { .genre-grid { grid-template-columns: 1fr; } }
        .genre-grid-item { display: flex; align-items: center; gap: 10px; padding: 10px 12px; border-radius: 10px; cursor: pointer; transition: all 0.2s; font-size: 13px; color: #495057; text-decoration: none; background: #f8f9fa; border: 1px solid transparent; }
        .genre-grid-item:hover { background: #e8f5e9; border-color: #c8e6c9; color: #1a6e35; transform: translateX(3px); }
        .genre-grid-item.active { background: linear-gradient(135deg, #1a6e35, #27ae60); color: white; border-color: transparent; box-shadow: 0 4px 12px rgba(26, 110, 53, 0.3); }
        .genre-grid-item.active i { color: white; }
        .genre-grid-item i { color: #1a6e35; font-size: 16px; width: 20px; text-align: center; }
        .genre-dropdown-footer { padding-top: 12px; margin-top: 12px; border-top: 1px solid #e9ecef; text-align: center; }
        .genre-dropdown-footer a { font-size: 12px; color: #6c757d; text-decoration: none; transition: color 0.2s; }
        .genre-dropdown-footer a:hover { color: #1a6e35; }

        /* Dark mode support */
        body.dark-mode .genre-dropdown-header { border-color: #444; }
        body.dark-mode .genre-search-box input { background: #2d2d2d; border-color: #444; color: #e0e0e0; }
        body.dark-mode .genre-search-box input:focus { border-color: #27ae60; }
        body.dark-mode .genre-search-box i { color: #888; }
        body.dark-mode .genre-grid-item { background: #2d2d2d; color: #e0e0e0; }
        body.dark-mode .genre-grid-item:hover { background: #1a3d1a; border-color: #2d5c2d; color: #4ade80; }
        body.dark-mode .genre-dropdown-body { scrollbar-color: #444 transparent; }
        body.dark-mode .genre-dropdown-footer { border-color: #444; }

        /* Layanan dropdown */
        .layanan-dropdown { position: absolute; top: calc(100% + 10px); left: 0; background: white; border-radius: 15px; box-shadow: 0 10px 40px rgba(0,0,0,0.15); padding: 20px; min-width: 260px; display: none; z-index: 999; animation: fadeDown 0.2s ease; }
        .layanan-dropdown.show { display: block; }
        .penjaga-card { display: flex; align-items: center; gap: 15px; }
        .penjaga-card img { width: 65px; height: 65px; border-radius: 50%; object-fit: cover; border: 3px solid #1a6e35; }
        .penjaga-card h6 { font-size: 14px; font-weight: 600; color: #222; margin-bottom: 4px; }
        .penjaga-card p { font-size: 12px; color: #666; margin-bottom: 0; }
        .penjaga-card p i { color: #1a6e35; margin-right: 6px; }

        /* Profil Popup */
        .profil-popup {
            position: absolute;
            top: calc(100% + 10px);
            right: 0;
            background: white;
            border-radius: 15px;
            box-shadow: 0 10px 40px rgba(0,0,0,0.15);
            padding: 20px;
            min-width: 260px;
            display: none;
            z-index: 999;
            animation: fadeDown 0.2s ease;
        }
        .profil-popup.show { display: block; }
        .profil-popup .profil-foto {
            width: 70px;
            height: 70px;
            border-radius: 50%;
            object-fit: cover;
            border: 3px solid #1a6e35;
            margin: 0 auto 12px;
            display: block;
        }

        .profil-popup h5 {
            text-align: center;
            font-weight: 700;
            color: #222;
            margin-bottom: 12px;
        }

        .profil-info {
            background: #f9f9f9;
            border-radius: 10px;
            padding: 10px;
            margin-bottom: 12px;
        }

        .profil-info p {
            font-size: 12px;
            color: #555;
            margin-bottom: 5px;
            display: flex;
            gap: 8px;
        }

        .profil-info p:last-child { margin-bottom: 0; }

        .profil-info i { color: #1a6e35; width: 16px; }

        .btn-lihat-profil {
            width: 100%;
            padding: 9px;
            background: linear-gradient(135deg, #1a6e35, #27ae60);
            color: white;
            border: none;
            border-radius: 8px;
            font-size: 13px;
            font-weight: 600;
            cursor: pointer;
            text-decoration: none;
            display: block;
            text-align: center;
        }

        .btn-lihat-profil:hover {
            background: linear-gradient(135deg, #155c2c, #1e8f4e);
            color: white;
        }

        .btn-logout {
            width: 100%;
            padding: 9px;
            background: #f1f1f1;
            color: #333;
            border: none;
            border-radius: 8px;
            font-size: 13px;
            font-weight: 600;
            cursor: pointer;
            text-decoration: none;
            display: block;
            text-align: center;
            margin-top: 8px;
        }

        .btn-logout:hover {
            background: #e0e0e0;
            color: #333;
        }

        @media (min-width: 769px) {
            .profil-popup {
                position: absolute;
                top: 100%;
                right: 0;
                left: auto;
                transform: none;
            }
        }

        .penjaga-card {
            display: flex;
            align-items: center;
            gap: 15px;
        }

        .penjaga-card img {
            width: 70px;
            height: 70px;
            border-radius: 50%;
            object-fit: cover;
            border: 3px solid #1a6e35;
        }

        .penjaga-card h6 {
            font-weight: 700;
            color: #222;
            margin-bottom: 3px;
        }

        .penjaga-card p {
            font-size: 12px;
            color: #666;
            margin: 0;
        }

        /* HERO */
        .hero {
            height: 100vh;
            min-height: 500px;
            background: url('/images/sekolah.jpg') center/cover no-repeat;
            position: relative;
            display: flex;
            align-items: flex-end;
            padding-bottom: 80px;
        }

        .hero::before {
            content: '';
            position: absolute;
            inset: 0;
            background: rgba(0,0,0,0.45);
        }

        .hero-content {
            position: relative;
            max-width: 500px;
            padding-left: 80px;
            padding-right: 40px;
        }

        .hero-box {
            background: rgba(26, 110, 53, 0.88);
            backdrop-filter: blur(5px);
            border-radius: 12px;
            padding: 30px 35px;
            color: white;
        }

        .hero-box h1 {
            font-size: 22px;
            font-weight: 700;
            margin-bottom: 10px;
            line-height: 1.4;
        }

        .hero-box p {
            font-size: 13px;
            opacity: 0.9;
            margin-bottom: 20px;
        }

        .btn-kunjungi {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            background: white;
            color: #1a6e35;
            padding: 10px 24px;
            border-radius: 8px;
            font-weight: 700;
            font-size: 14px;
            text-decoration: none;
            transition: all 0.3s;
        }

        .btn-kunjungi:hover {
            background: #1a6e35;
            color: white;
        }

        /* STATS */
        .stats-section {
            padding: 60px 0;
            background: #f8f9fa;
        }

        .stat-card {
            background: white;
            border-radius: 15px;
            padding: 25px;
            text-align: center;
            box-shadow: 0 5px 20px rgba(0,0,0,0.08);
            transition: transform 0.3s;
        }

        .stat-card:hover { transform: translateY(-5px); }

        .stat-card .icon {
            width: 60px;
            height: 60px;
            border-radius: 50%;
            background: linear-gradient(135deg, #1a6e35, #27ae60);
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 15px;
        }

        .stat-card .icon i {
            font-size: 24px;
            color: white;
        }

        .stat-card h3 {
            font-size: 32px;
            font-weight: 700;
            color: #1a6e35;
        }

        .stat-card p {
            color: #888;
            font-size: 14px;
            margin: 0;
        }

        /* Relative position untuk dropdown */
        .nav-item { position: relative; }

        /* Tablet */
        @media (max-width: 768px) {
            .navbar .container-fluid > div {
                flex-wrap: wrap;
                gap: 8px;
            }

            .nav-link {
                padding: 8px 10px !important;
                font-size: 13px;
            }

            .nav-text { display: none; }

            .layanan-dropdown,
            .profil-popup {
                position: fixed;
                top: 70px;
                left: 50%;
                transform: translateX(-50%);
                width: 90vw;
                max-width: 320px;
                max-height: calc(100vh - 100px);
                overflow-y: auto;
            }

            .hero {
                align-items: center;
                padding-bottom: 0;
                height: 100vh;
                min-height: 100svh;
            }

            .hero-content {
                padding-left: 24px;
                padding-right: 24px;
                max-width: 100%;
                width: 100%;
            }

            .hero-box {
                padding: 22px 20px;
            }

            .hero-box h1 { font-size: 19px; }
            .hero-box p { font-size: 13px; }

            .stats-section { padding: 40px 0; }

            .stat-card { margin-bottom: 16px; }
        }

        /* Phone */
        @media (max-width: 480px) {
            .navbar-brand span { font-size: 11px; }
            .navbar-brand img { width: 36px; height: 36px; }

            .nav-link {
                padding: 6px 8px !important;
                font-size: 12px;
            }

            .layanan-dropdown,
            .profil-popup {
                width: 95vw;
                padding: 18px;
                border-radius: 12px;
            }

            .hero {
                align-items: center;
                padding-bottom: 0;
                height: 100vh;
                min-height: 100svh;
            }

            .hero-content {
                padding-left: 16px;
                padding-right: 16px;
            }

            .hero-box {
                padding: 18px 16px;
                border-radius: 10px;
            }

            .hero-box h1 { font-size: 17px; }
            .hero-box p { font-size: 12px; margin-bottom: 14px; }

            .btn-kunjungi {
                padding: 8px 18px;
                font-size: 13px;
            }

            .stat-card { padding: 18px; }
            .stat-card h3 { font-size: 26px; }
            .stat-card .icon { width: 48px; height: 48px; }
            .stat-card .icon i { font-size: 20px; }
        }

/* DARK MODE */
body.dark-mode {
    background: #121212;
    color: #fff;
}

body.dark-mode .navbar {
    background: #1e1e1e;
    box-shadow: 0 2px 15px rgba(255,255,255,0.05);
}

body.dark-mode .nav-link {
    color: #fff !important;
}

body.dark-mode .navbar-brand span {
    color: #4ade80;
}

body.dark-mode .layanan-dropdown {
    background: #1e1e1e;
    color: white;
}

body.dark-mode .penjaga-card p {
    color: #ccc;
}

body.dark-mode .penjaga-card h6 {
    color: white;
}

body.dark-mode .profil-popup {
    background: #1e1e1e;
    color: white;
}

body.dark-mode .profil-popup h5 {
    color: white;
}

body.dark-mode .profil-info {
    background: #2a2a2a;
}

body.dark-mode .profil-info p {
    color: #ccc;
}

body.dark-mode .btn-logout {
    background: #2a2a2a;
    color: #ccc;
}

body.dark-mode .btn-logout:hover {
    background: #3a3a3a;
    color: white;
}

body.dark-mode .hero-box {
    background: rgba(20, 20, 20, 0.9);
}

body.dark-mode .stats-section {
    background: #181818;
}

body.dark-mode .stat-card {
    background: #1e1e1e;
    color: white;
}
    </style>
</head>
<body>

<!-- NAVBAR -->
<nav class="navbar">
    <div class="container-fluid px-4">
        <div class="d-flex align-items-center justify-content-between w-100">
            <a href="<?php echo e(route('koleksi.index')); ?>" class="d-flex align-items-center gap-2 text-decoration-none">
                <img src="<?php echo e(asset('images/logo.jpg')); ?>" style="width:45px;height:45px;border-radius:50%;object-fit:cover" alt="Logo">
                <span style="font-size:13px;font-weight:700;color:#1a6e35;text-transform:uppercase;line-height:1.3">SMK Maarif<br>Walisongo Kajoran</span>
            </a>
            <div class="d-flex align-items-center gap-2">
                <ul class="navbar-nav flex-row gap-1 mb-0">
                    <li class="nav-item">
                        <a class="nav-link" href="#" onclick="toggleLayanan(event)">
                            <i class="bi bi-person-workspace"></i> <span class="nav-text">Layanan</span> <i class="bi bi-chevron-down" style="font-size:10px"></i>
                        </a>
                        <div class="layanan-dropdown" id="layananDropdown">
                            <p style="font-size:11px;color:#aaa;text-transform:uppercase;letter-spacing:1px;margin-bottom:12px">Penjaga Perpustakaan</p>
                            <?php
                                $adminAktif = \App\Models\User::where('role', 'admin')->where('is_on_duty', true)->first();
                                if (!$adminAktif) { $adminAktif = \App\Models\User::where('role', 'admin')->first(); }
                                $anggotaAdmin = \App\Models\Anggota::where('email', $adminAktif?->email)->first();
                                $noHpAdmin = $anggotaAdmin?->no_telepon ?? $adminAktif?->no_hp ?? '';
                                $waLink = $noHpAdmin ? 'https://wa.me/62' . ltrim(preg_replace('/[^0-9]/', '', $noHpAdmin), '0') : '#';
                            ?>
                            <div class="penjaga-card">
                                <?php if($adminAktif?->foto): ?>
                                    <img src="<?php echo e(asset($adminAktif->foto)); ?>" alt="Penjaga" style="width:65px;height:65px;border-radius:50%;object-fit:cover;border:3px solid #1a6e35">
                                <?php else: ?>
                                    <img src="https://ui-avatars.com/api/?name=<?php echo e(urlencode($adminAktif?->name ?? 'Admin')); ?>&background=1a6e35&color=fff" alt="Penjaga">
                                <?php endif; ?>
                                <div>
                                    <h6><?php echo e($adminAktif?->name ?? 'Nama Penjaga'); ?></h6>
                                    <p><i class="bi bi-telephone"></i> <?php echo e($anggotaAdmin?->no_telepon ?? $adminAktif?->no_hp ?? '-'); ?></p>
                                    <p><i class="bi bi-clock"></i> <?php echo e(now()->locale('id')->isoFormat('dddd')); ?>, <?php echo e(now()->format('H:i')); ?> WIB</p>
                                </div>
                            </div>
                            <?php if($noHpAdmin): ?>
                            <a href="<?php echo e($waLink); ?>" target="_blank" rel="noopener noreferrer" style="display:flex;align-items:center;justify-content:center;gap:8px;margin-top:12px;padding:8px 16px;background:#25D366;color:#fff;border-radius:8px;text-decoration:none;font-size:13px;font-weight:600;transition:background 0.2s" onmouseover="this.style.background='#1ebe57'" onmouseout="this.style.background='#25D366'">
                                <i class="bi bi-whatsapp" style="font-size:16px"></i> Chat Admin
                            </a>
                            <?php endif; ?>
                        </div>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="#" onclick="toggleGenre(event)">
                            <i class="bi bi-collection-fill"></i> <span class="nav-text">Genre Buku</span> <i class="bi bi-chevron-down" style="font-size:10px"></i>
                        </a>
                        <div class="floating-dropdown genre-dropdown-wrapper" id="genreDropdown">
                            <div class="genre-dropdown-header">
                                <h6><i class="bi bi-collection-fill"></i> Pilih Genre</h6>
                                <span class="badge"><?php echo e($genreList->count()); ?> genre</span>
                            </div>
                            <div class="genre-search-box">
                                <i class="bi bi-search"></i>
                                <input type="text" id="genreSearchInput" placeholder="Cari genre..." autocomplete="off">
                            </div>
                            <div class="genre-dropdown-body">
                                <div class="genre-grid" id="genreGrid">
                                    <a href="<?php echo e(route('koleksi.index')); ?>" class="genre-grid-item <?php echo e(!$genre ? 'active' : ''); ?>" data-genre="">
                                        <i class="bi bi-grid-fill"></i>
                                        <span>Semua</span>
                                    </a>
                                    <?php $__currentLoopData = $genreList; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $g): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <a href="<?php echo e(route('koleksi.index', ['genre' => $g->id])); ?>" class="genre-grid-item" data-genre="<?php echo e(strtolower($g->nama)); ?>">
                                        <i class="bi bi-tag-fill"></i>
                                        <span><?php echo e($g->nama); ?></span>
                                    </a>
                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                </div>
                            </div>
                        </div>
                    </li>
                    <li class="nav-item">
                       <a class="nav-link" href="<?php echo e(route('favorit.index', ['from' => 'koleksi'])); ?>"><i class="bi bi-heart-fill"></i> <span class="nav-text">Favorit</span></a>
                    </li>
                    <li class="nav-item">
                       <a class="nav-link" href="#" onclick="openScanModal(event)"><i class="bi bi-qr-code-scan"></i> <span class="nav-text">Scan</span></a>
                    </li>
                </ul>
                <button id="darkModeToggle" class="btn btn-sm btn-outline-secondary rounded-circle"><i class="bi bi-moon-fill"></i></button>
                <?php
                    $dashUnreadCount = \App\Models\Notification::where('user_id', auth()->id())->where('is_read', false)->count();
                ?>
                <a href="<?php echo e(route('notifikasi.go')); ?>" class="btn btn-sm position-relative" title="Notifikasi" style="padding: 6px 10px; background: rgba(26, 110, 53, 0.1); border: none; border-radius: 8px;">
                    <i class="bi bi-bell" style="color: #1a6e35; font-size: 18px;"></i>
                    <?php if($dashUnreadCount > 0): ?>
                        <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger" style="font-size: 9px;">
                            <?php echo e($dashUnreadCount > 99 ? '99+' : $dashUnreadCount); ?>

                        </span>
                    <?php endif; ?>
                </a>
                <div class="nav-item">
                    <a href="#" onclick="toggleProfil(event)">
                      <?php if(auth()->user()->foto): ?>
                        <img src="<?php echo e(asset(auth()->user()->foto)); ?>" class="profil-avatar" alt="Profil">
                      <?php else: ?>
                        <img src="https://ui-avatars.com/api/?name=<?php echo e(auth()->user()->name); ?>&background=1a6e35&color=fff" class="profil-avatar" alt="Profil">
                      <?php endif; ?>
                    </a>
                    <div class="floating-dropdown" id="profilDropdown" style="right:0;left:auto;min-width:250px">
                        <div style="text-align:center;margin-bottom:15px">
                           <?php if(auth()->user()->foto): ?>
                            <img src="<?php echo e(asset(auth()->user()->foto)); ?>" style="width:70px;height:70px;border-radius:50%;border:3px solid #1a6e35;margin-bottom:10px">
                           <?php else: ?>
                            <img src="https://ui-avatars.com/api/?name=<?php echo e(auth()->user()->name); ?>&background=1a6e35&color=fff&size=200" style="width:70px;height:70px;border-radius:50%;border:3px solid #1a6e35;margin-bottom:10px">
                           <?php endif; ?>
                            <h6 style="font-weight:700;color:#222;margin:0"><?php echo e(auth()->user()->name); ?></h6>
                        </div>
                        <div style="background:#f9f9f9;border-radius:10px;padding:12px;margin-bottom:12px">
                            <p style="font-size:12px;color:#555;margin-bottom:6px"><i class="bi bi-person-badge" style="color:#1a6e35"></i> NIS: <?php echo e(auth()->user()->nis); ?></p>
                            <p style="font-size:12px;color:#555;margin:0"><i class="bi bi-envelope" style="color:#1a6e35"></i> <?php echo e(auth()->user()->email); ?></p>
                        </div>
                        <?php
                            $vipAktif = auth()->user()->is_vip && auth()->user()->vip_expired_at && now()->lt(auth()->user()->vip_expired_at);
                            $sisaHari = $vipAktif ? (int) now()->diffInDays(auth()->user()->vip_expired_at) + 1 : 0;
                        ?>
                        <button onclick="document.getElementById('profilDropdown').classList.remove('show');document.getElementById('modalVip').style.display='flex'"
                            style="width:100%;padding:10px;background:<?php echo e($vipAktif ? 'linear-gradient(135deg,#f59e0b,#d97706)' : 'linear-gradient(135deg,#374151,#1f2937)'); ?>;color:white;border:none;border-radius:10px;font-size:13px;font-weight:600;cursor:pointer;margin-bottom:8px;text-align:left">
                            <?php echo e($vipAktif ? 'VIP Aktif · '.$sisaHari.' hari lagi' : 'Upgrade VIP'); ?>

                        </button>
                       <a href="<?php echo e(route('profil.index')); ?>" style="display:block;width:100%;padding:10px;background:linear-gradient(135deg,#1a6e35,#27ae60);color:white;border:none;border-radius:10px;font-size:13px;font-weight:600;text-align:center;text-decoration:none">
                       <i class="bi bi-person"></i> Lihat Profil
                       </a>
                          <form method="POST" action="<?php echo e(route('logout')); ?>" style="margin-top:8px" onsubmit="return confirm('Yakin ingin logout?');">
                              <?php echo csrf_field(); ?>
                              <button type="submit" style="width:100%;padding:10px;background:#f8f9fa;color:#555;border:none;border-radius:10px;font-size:13px;font-weight:600;cursor:pointer">
                                  <i class="bi bi-box-arrow-right"></i> Logout
                              </button>
                          </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</nav>

<!-- HERO -->
<section class="hero" id="home">
    <div class="hero-content">
        <div class="hero-box">
            <h1>Perpustakaan SMK Maarif Walisongo Kajoran</h1>
            <p>Temukan ribuan koleksi buku yang siap menambah wawasan dan pengetahuanmu.</p>
           <a href="<?php echo e(route('koleksi.index')); ?>" class="btn-kunjungi">
    Kunjungi <i class="bi bi-arrow-right"></i>
</a>
        </div>
    </div>
</section>




<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script>
function toggleLayanan(e) { e.preventDefault(); const el = document.getElementById('layananDropdown'); positionDropdown(el); el.classList.toggle('show'); document.getElementById('genreDropdown').classList.remove('show'); document.getElementById('profilDropdown').classList.remove('show'); }

function toggleGenre(e) { e.preventDefault(); const el = document.getElementById('genreDropdown'); positionDropdown(el); el.classList.toggle('show'); document.getElementById('layananDropdown').classList.remove('show'); document.getElementById('profilDropdown').classList.remove('show'); if (el.classList.contains('show')) { setTimeout(function() { document.getElementById('genreSearchInput').focus(); }, 100); } }

function toggleProfil(e) { e.preventDefault(); const el = document.getElementById('profilDropdown'); positionDropdown(el); el.classList.toggle('show'); document.getElementById('layananDropdown').classList.remove('show'); document.getElementById('genreDropdown').classList.remove('show'); }

function positionDropdown(el) { if (window.innerWidth < 768) { el.style.left = '16px'; el.style.right = '16px'; el.style.transform = 'none'; } else { el.style.left = ''; el.style.right = '0'; el.style.transform = ''; } }

document.addEventListener('click', function(e) { if (!e.target.closest('.nav-item') && !e.target.closest('.profil-avatar')) { document.getElementById('layananDropdown').classList.remove('show'); document.getElementById('genreDropdown').classList.remove('show'); document.getElementById('profilDropdown').classList.remove('show'); } });

if (document.getElementById('genreSearchInput')) {
    document.getElementById('genreSearchInput').addEventListener('input', function(e) {
        const searchValue = e.target.value.toLowerCase();
        const items = document.querySelectorAll('#genreGrid .genre-grid-item');
        items.forEach(function(item) {
            const genreName = item.getAttribute('data-genre') || '';
            const textContent = item.textContent.toLowerCase();
            if (genreName.includes(searchValue) || textContent.includes(searchValue)) {
                item.style.display = '';
            } else {
                item.style.display = 'none';
            }
        });
    });
    
    document.getElementById('genreSearchInput').addEventListener('click', function(e) { e.stopPropagation(); });
}
</script>

<script>
document.addEventListener('DOMContentLoaded', function () {
    if (localStorage.getItem('darkMode') === 'enabled') {
        document.body.classList.add('dark-mode');
    }
});
</script>
<script>
const darkToggle = document.getElementById('darkModeToggle');

if(localStorage.getItem('darkMode') === 'enabled'){
    document.body.classList.add('dark-mode');
    darkToggle.innerHTML = '<i class="bi bi-sun-fill"></i>';
}

darkToggle.addEventListener('click', () => {
    document.body.classList.toggle('dark-mode');

    if(document.body.classList.contains('dark-mode')){
        localStorage.setItem('darkMode','enabled');
        darkToggle.innerHTML = '<i class="bi bi-sun-fill"></i>';
    } else {
        localStorage.setItem('darkMode','disabled');
        darkToggle.innerHTML = '<i class="bi bi-moon-fill"></i>';
    }
});
</script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/cropperjs@1.6.1/dist/cropper.min.js"></script>
<?php if (isset($component)) { $__componentOriginalc341f611a63b85a7efee481957438a0f = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginalc341f611a63b85a7efee481957438a0f = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.crop-modal','data' => []] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('crop-modal'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes([]); ?>
<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginalc341f611a63b85a7efee481957438a0f)): ?>
<?php $attributes = $__attributesOriginalc341f611a63b85a7efee481957438a0f; ?>
<?php unset($__attributesOriginalc341f611a63b85a7efee481957438a0f); ?>
<?php endif; ?>
<?php if (isset($__componentOriginalc341f611a63b85a7efee481957438a0f)): ?>
<?php $component = $__componentOriginalc341f611a63b85a7efee481957438a0f; ?>
<?php unset($__componentOriginalc341f611a63b85a7efee481957438a0f); ?>
<?php endif; ?>


</body>
</html><?php /**PATH C:\laragon\www\PerpustakaanDigital\resources\views\dashboard.blade.php ENDPATH**/ ?>