<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Perpustakaan SMK Maarif Walisongo Kajoran</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/cropperjs@1.6.1/dist/cropper.min.css" rel="stylesheet">
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }

        body { font-family: 'Segoe UI', sans-serif; }

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

        .navbar-brand {
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .navbar-brand img {
            width: 45px;
            height: 45px;
            border-radius: 50%;
            object-fit: cover;
        }

        .navbar-brand span {
            font-size: 13px;
            font-weight: 700;
            color: #1a6e35;
            line-height: 1.3;
            text-transform: uppercase;
        }

        .nav-link {
            color: #333 !important;
            font-weight: 500;
            font-size: 14px;
            padding: 8px 15px !important;
            transition: color 0.3s;
        }

        .nav-link:hover { color: #1a6e35 !important; }

        .nav-link.active { color: #1a6e35 !important; }

        /* Dropdown */
        .layanan-dropdown {
            position: absolute;
            top: calc(100% + 40px);
            left: -70px;
            background: white;
            border-radius: 12px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.15);
            padding: 20px;
            min-width: 280px;
            display: none;
            z-index: 999;
        }

        .layanan-dropdown.show { display: block; }

        /* Profil Popup */
        .profil-popup {
            position: absolute;
            top: calc(100% + 40px);
            right: -10px;
            background: white;
            border-radius: 15px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.15);
            padding: 20px;
            min-width: 260px;
            display: none;
            z-index: 999;
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

            <!-- Brand -->
            <a class="navbar-brand" href="#">
                <img src="<?php echo e(asset('images/logo.jpg')); ?>" alt="Logo">
                <span>SMK Maarif<br>Walisongo Kajoran</span>
            </a>

            <!-- Menu -->
            <div class="d-flex align-items-center gap-2">
                <ul class="navbar-nav flex-row gap-1 mb-0">
                    <li class="nav-item">
                      <a class="nav-link active" href="#home"><i class="bi bi-house"></i> <span class="nav-text">Home</span></a>
                    </li>
                    <li class="nav-item">
                       <a class="nav-link" href="#" onclick="toggleLayanan(event)">
    <i class="bi bi-person-workspace"></i> <span class="nav-text">Layanan</span> <i class="bi bi-chevron-down" style="font-size:10px"></i>
</a>
                        <div class="layanan-dropdown" id="layananDropdown">
                            <p style="font-size:12px;color:#999;margin-bottom:15px">Penjaga Perpustakaan</p>
                           <?php
    $adminAktif = \App\Models\User::where('role', 'admin')
        ->where('is_on_duty', true)
        ->first();

    if (!$adminAktif) {
        $adminAktif = \App\Models\User::where('role', 'admin')->first();
    }
?>
<div class="penjaga-card">
    <?php if($adminAktif?->foto): ?>
        <img src="<?php echo e(asset($adminAktif->foto)); ?>" alt="Penjaga" style="width:65px;height:65px;border-radius:50%;object-fit:cover;border:3px solid #1a6e35">
    <?php else: ?>
        <img src="https://ui-avatars.com/api/?name=<?php echo e(urlencode($adminAktif?->name ?? 'Admin')); ?>&background=1a6e35&color=fff" alt="Penjaga">
    <?php endif; ?>
    <div>
        <h6><?php echo e($adminAktif?->name ?? 'Nama Penjaga'); ?></h6>
      <?php
    $anggotaAdmin = \App\Models\Anggota::where('email', $adminAktif?->email)->first();
?>
<?php
    $noHpAdmin = $anggotaAdmin?->no_telepon ?? $adminAktif?->no_hp ?? '';
    $waLink = $noHpAdmin ? 'https://wa.me/62' . ltrim(preg_replace('/[^0-9]/', '', $noHpAdmin), '0') : '#';
?>
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
                </ul>

                <!-- Dark Mode Toggle -->
                 <button id="darkModeToggle"
        class="btn btn-sm btn-outline-secondary rounded-circle">
    <i class="bi bi-moon-fill"></i>
</button>

                <!-- Profil -->
                <div class="nav-item">
                    <button onclick="toggleProfil(event)" style="background:none;border:none;cursor:pointer">
    <?php if(auth()->user()->foto): ?>
        <img src="<?php echo e(asset(auth()->user()->foto)); ?>"
             class="profil-avatar"
             style="width:38px;height:38px;border-radius:50%;border:2px solid #1a6e35;object-fit:cover">
    <?php else: ?>
        <img src="https://ui-avatars.com/api/?name=<?php echo e(auth()->user()->name); ?>&background=1a6e35&color=fff"
             class="profil-avatar"
             style="width:38px;height:38px;border-radius:50%;border:2px solid #1a6e35">
    <?php endif; ?>
</button>
                    <div class="profil-popup" id="profilPopup">
    <?php if(auth()->user()->foto): ?>
        <img src="<?php echo e(asset(auth()->user()->foto)); ?>"
             class="profil-avatar profil-foto" alt="Foto Profil" style="object-fit:cover">
    <?php else: ?>
        <img src="https://ui-avatars.com/api/?name=<?php echo e(auth()->user()->name); ?>&background=1a6e35&color=fff&size=200"
             class="profil-avatar profil-foto" alt="Foto Profil">
    <?php endif; ?>
    <h5><?php echo e(auth()->user()->name); ?></h5>
    <div class="profil-info">
        <p><i class="bi bi-person-badge"></i> NIS: <?php echo e(auth()->user()->nis); ?></p>
        <p><i class="bi bi-envelope"></i> <?php echo e(auth()->user()->email); ?></p>
    </div>
    <a href="<?php echo e(route('profil.index')); ?>" class="btn-lihat-profil">
        <i class="bi bi-person"></i> Lihat Profil
    </a>
    <form method="POST" action="<?php echo e(route('logout')); ?>" onsubmit="return confirm('Yakin ingin logout?');">
        <?php echo csrf_field(); ?>
        <button type="submit" class="btn-logout">
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
function toggleLayanan(e) {
    e.preventDefault();
    const el = document.getElementById('layananDropdown');
    el.classList.toggle('show');
    document.getElementById('profilPopup').classList.remove('show');
}

function toggleProfil(e) {
    e.preventDefault();
    const el = document.getElementById('profilPopup');
    el.classList.toggle('show');
    document.getElementById('layananDropdown').classList.remove('show');
}

document.addEventListener('click', function(e) {
    if (!e.target.closest('.nav-item')) {
        document.getElementById('layananDropdown').classList.remove('show');
        document.getElementById('profilPopup').classList.remove('show');
    }
});
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
</html><?php /**PATH C:\laragon\www\PerpustakaanDigital\resources\views/dashboard.blade.php ENDPATH**/ ?>