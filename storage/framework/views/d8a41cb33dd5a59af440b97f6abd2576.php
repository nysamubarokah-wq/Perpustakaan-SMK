<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Perpustakaan SMK Maarif</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css" rel="stylesheet">
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }

        body {
            font-family: 'Segoe UI', sans-serif;
            min-height: 100vh;
            background: url('https://images.unsplash.com/photo-1481627834876-b7833e8f5570?w=1920') center/cover no-repeat fixed;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        body::before {
            content: '';
            position: fixed;
            inset: 0;
            background: rgba(0,0,0,0.55);
        }

        .container {
            position: relative;
            width: 820px;
            height: 500px;
            background: white;
            border-radius: 20px;
            overflow: hidden;
            box-shadow: 0 25px 60px rgba(0,0,0,0.5);
            display: flex;
        }

        .logo-panel {
            width: 42%;
            background: linear-gradient(160deg, #1a6e35, #27ae60);
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            padding: 40px 30px;
            position: absolute;
            top: 0; left: 0;
            height: 100%;
            z-index: 2;
        }

        .logo-panel img {
            width: 110px;
            height: 110px;
            border-radius: 50%;
            margin-bottom: 18px;
            border: 4px solid rgba(255,255,255,0.8);
            object-fit: cover;
        }

        .logo-panel h2 {
            color: white;
            font-size: 15px;
            text-align: center;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 1px;
            line-height: 1.5;
        }

        .logo-panel p {
            color: rgba(255,255,255,0.8);
            font-size: 12px;
            text-align: center;
            margin-top: 8px;
        }

        .login-panel {
            width: 58%;
            padding: 50px 45px;
            display: flex;
            flex-direction: column;
            justify-content: center;
            position: absolute;
            right: 0; top: 0;
            height: 100%;
            background: white;
            z-index: 1;
        }

        h3 {
            font-size: 24px;
            font-weight: 700;
            color: #222;
            margin-bottom: 4px;
        }

        .subtitle {
            color: #999;
            font-size: 13px;
            margin-bottom: 30px;
        }

        .form-group {
            margin-bottom: 18px;
        }

        .form-group label {
            display: block;
            font-size: 13px;
            font-weight: 600;
            color: #555;
            margin-bottom: 8px;
        }

        .form-group input {
            width: 100%;
            padding: 13px 16px;
            border: 2px solid #eee;
            border-radius: 10px;
            font-size: 14px;
            outline: none;
            transition: border 0.3s;
            background: #f9f9f9;
        }

        .form-group input:focus {
            border-color: #27ae60;
            background: white;
        }

        .btn-submit {
            width: 100%;
            padding: 14px;
            background: linear-gradient(135deg, #1a6e35, #27ae60);
            color: white;
            border: none;
            border-radius: 10px;
            font-size: 15px;
            font-weight: 600;
            cursor: pointer;
            transition: opacity 0.3s;
            margin-top: 8px;
            letter-spacing: 1px;
        }

        .btn-submit:hover { opacity: 0.88; }

        .info-box {
            margin-top: 16px;
            background: #fff3cd;
            border-left: 4px solid #f0ad4e;
            border-radius: 8px;
            padding: 12px 15px;
        }

        .info-box p {
            font-size: 12px;
            color: #856404;
            margin: 0;
            line-height: 1.7;
        }

        .info-box strong { font-weight: 700; }

        @media (max-width: 768px) {
            .container {
                width: 92vw;
                height: auto;
                flex-direction: column;
                position: relative;
            }
            .logo-panel {
                position: relative;
                width: 100%;
                height: auto;
                padding: 15px 20px;
                top: auto; left: auto;
                flex-direction: row;
                gap: 12px;
                justify-content: center;
            }
            .logo-panel img { width: 45px; height: 45px; margin-bottom: 0; }
            .logo-panel h2 { font-size: 12px; text-align: left; }
            .logo-panel p { text-align: left; margin-top: 2px; }
            .login-panel {
                position: relative;
                width: 100%;
                height: auto;
                padding: 30px 25px;
                right: auto; top: auto;
            }
        }
    </style>
</head>
<body>
<div class="container">

    <div class="logo-panel">
        <img src="<?php echo e(asset('images/logo.jpg')); ?>" alt="Logo SMK Maarif">
        <h2>SMK Maarif<br>Walisongo Kajoran</h2>
        <p>Perpustakaan Digital</p>
    </div>

    <div class="login-panel">
        <h3>Selamat Datang!</h3>
        <p class="subtitle">Masuk ke akun perpustakaan kamu</p>

        <?php if($errors->any()): ?>
            <div style="background:#fee2e2;border-left:4px solid #e74c3c;border-radius:8px;padding:12px 15px;margin-bottom:20px">
                <?php $__currentLoopData = $errors->all(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $e): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <p style="color:#dc2626;font-size:13px;margin:0"><?php echo e($e); ?></p>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </div>
        <?php endif; ?>

        <form method="POST" action="<?php echo e(route('login')); ?>">
            <?php echo csrf_field(); ?>
            <div class="form-group">
                <label><i class="bi bi-person"></i> Nama Lengkap</label>
                <input type="text" name="nama_login" placeholder="Masukkan nama lengkap kamu"
                       value="<?php echo e(old('nama_login')); ?>" required autofocus>
            </div>
            <div class="form-group">
                <label><i class="bi bi-person-badge"></i> NIS (Nomor Induk Siswa)</label>
                <input type="text" name="nis_login" placeholder="Masukkan NIS kamu"
                       value="<?php echo e(old('nis_login')); ?>" required>
            </div>
            <button type="submit" class="btn-submit">MASUK</button>
        </form>

        <div class="info-box">
            <p>
                <strong>Catatan:</strong> Masukkan nama dan NIS sesuai data yang didaftarkan oleh admin perpustakaan.
            </p>
        </div>
    </div>

</div>
</body>
</html>
<?php /**PATH C:\laragon\www\PerpustakaanDigital\resources\views/auth/login.blade.php ENDPATH**/ ?>