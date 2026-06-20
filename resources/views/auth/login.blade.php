<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Perpustakaan SMK Maarif</title>
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
            height: 520px;
            background: white;
            border-radius: 20px;
            overflow: hidden;
            box-shadow: 0 25px 60px rgba(0,0,0,0.5);
            display: flex;
        }

        /* Panel Kiri - Logo */
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
            transition: all 0.6s cubic-bezier(0.77,0,0.175,1);
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

        /* Panel Login */
        .login-panel {
            width: 58%;
            padding: 50px 45px;
            display: flex;
            flex-direction: column;
            justify-content: center;
            position: absolute;
            right: 0; top: 0;
            height: 100%;
            transition: all 0.6s cubic-bezier(0.77,0,0.175,1);
            background: white;
            z-index: 1;
        }

        /* Panel Register */
        .register-panel {
            width: 58%;
            padding: 45px;
            display: flex;
            flex-direction: column;
            justify-content: center;
            position: absolute;
            left: -58%; top: 0;
            height: 100%;
            transition: all 0.6s cubic-bezier(0.77,0,0.175,1);
            background: white;
            z-index: 1;
        }

        /* Mode Register */
        .container.register-mode .logo-panel {
            left: 58%;
        }

        .container.register-mode .login-panel {
            right: -58%;
        }

        .container.register-mode .register-panel {
            left: 0;
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
            margin-bottom: 25px;
        }

        .form-group {
            margin-bottom: 15px;
        }

        .form-group input {
            width: 100%;
            padding: 12px 16px;
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
            padding: 13px;
            background: linear-gradient(135deg, #1a6e35, #27ae60);
            color: white;
            border: none;
            border-radius: 10px;
            font-size: 15px;
            font-weight: 600;
            cursor: pointer;
            transition: opacity 0.3s;
            margin-top: 5px;
            letter-spacing: 1px;
        }

        .btn-submit:hover { opacity: 0.88; }

        .switch-link {
            text-align: center;
            margin-top: 18px;
            font-size: 13px;
            color: #999;
        }

        .switch-link a {
            color: #1a6e35;
            font-weight: 700;
            text-decoration: none;
            cursor: pointer;
        }

        .switch-link a:hover { text-decoration: underline; }

        .error-msg {
            color: #e74c3c;
            font-size: 12px;
            margin-top: 4px;
        }

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

    .register-panel {
        position: relative;
        width: 100%;
        height: auto;
        padding: 30px 25px;
        left: auto; top: auto;
        display: none;
    }

    /* Mode register di HP */
    .container.register-mode .logo-panel {
        left: auto;
        order: 0;
    }

    .container.register-mode .login-panel {
        right: auto;
        display: none;
    }

    .container.register-mode .register-panel {
        left: auto;
        display: flex;
        order: 1;
    }
}
    </style>
</head>
<body>
<div class="container" id="mainContainer">

    <!-- Logo Panel -->
    <div class="logo-panel">
     <img src="{{ asset('images/logo.jpg') }}"
     alt="Logo SMK Maarif">
            
        <h2>SMK Maarif<br>Walisongo Kajoran</h2>
        <p>Perpustakaan Digital</p>
    </div>

    <!-- Login Panel -->
    <div class="login-panel">
        <h3>Selamat Datang!</h3>
        <p class="subtitle">Masuk ke akun perpustakaan kamu</p>

        <form method="POST" action="{{ route('login') }}">
            @csrf
            <div class="form-group">
                <input type="email" name="email" placeholder="Email"
                       value="{{ old('email') }}" required autofocus>
                @error('email')
                    <p class="error-msg">{{ $message }}</p>
                @enderror
            </div>
            <div class="form-group">
                <input type="text" name="nis_display" placeholder="NIS"
                       oninput="document.getElementById('nis_as_pass').value=this.value">
                <input type="hidden" name="password" id="nis_as_pass">
                @error('password')
                    <p class="error-msg">{{ $message }}</p>
                @enderror
            </div>
            <button type="submit" class="btn-submit">MASUK</button>
        </form>

        <div class="switch-link">
            Belum punya akun? <a onclick="showRegister()">Daftar</a>
        </div>
    </div>

    <!-- Register Panel -->
    <div class="register-panel">
        <h3>Buat Akun</h3>
        <p class="subtitle">Daftarkan diri kamu sekarang</p>

        <form method="POST" action="{{ route('register') }}">
            @csrf
            <div class="form-group">
                <input type="text" name="name" placeholder="Nama Lengkap"
                       value="{{ old('name') }}" required>
                @error('name')
                    <p class="error-msg">{{ $message }}</p>
                @enderror
            </div>
            <div class="form-group">
                <input type="email" name="email" placeholder="Email"
                       value="{{ old('email') }}" required>
                @error('email')
                    <p class="error-msg">{{ $message }}</p>
                @enderror
            </div>
            <div class="form-group">
                <input type="text" name="nis" placeholder="NIS"
                       value="{{ old('nis') }}" required
                       oninput="document.getElementById('pass').value=this.value;document.getElementById('pass_confirm').value=this.value">
                <input type="hidden" name="password" id="pass">
                <input type="hidden" name="password_confirmation" id="pass_confirm">
                @error('nis')
                    <p class="error-msg">{{ $message }}</p>
                @enderror
            </div>
            <div class="form-group">
    <input type="text" name="no_hp" placeholder="No. HP" required>
</div>
<div class="form-group">
    <textarea name="alamat" placeholder="Alamat" required style="width:100%;padding:12px 16px;border:2px solid #eee;border-radius:10px;font-size:14px;outline:none;resize:none" rows="2"></textarea>
</div>
            <button type="submit" class="btn-submit">DAFTAR</button>
        </form>

        <div class="switch-link">
            Sudah punya akun? <a onclick="showLogin()">Masuk</a>
        </div>
    </div>

</div>

<script>
    function showRegister() {
        document.getElementById('mainContainer').classList.add('register-mode');
    }
    function showLogin() {
        document.getElementById('mainContainer').classList.remove('register-mode');
    }

    document.querySelector('.register-panel form').addEventListener('submit', function() {
        const nis = document.querySelector('input[name="nis"]').value;
        document.getElementById('pass').value = nis;
        document.getElementById('pass_confirm').value = nis;
    });
</script>
</body>
</html>