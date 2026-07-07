<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Scan QR Code - Perpustakaan SMK Maarif</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css" rel="stylesheet">
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: 'Segoe UI', sans-serif; background: #f5f7fa; }

        .navbar {
            background: white;
            box-shadow: 0 2px 15px rgba(0,0,0,0.1);
            padding: 12px 0;
            position: fixed;
            width: 100%;
            top: 0;
            z-index: 1000;
        }

        .main-container {
            max-width: 480px;
            margin: 90px auto 40px;
            padding: 0 16px;
        }

        #reader {
            width: 100%;
            max-width: 100%;
            border-radius: 12px;
            overflow: hidden;
            border: 2px dashed #d1d5db;
            background: #f9fafb;
        }
        #reader video { width: 100% !important; border-radius: 12px; }
        #reader__dashboard_section_swaplink { display: none !important; }
        #reader__dashboard_section_csr { display: none !important; }
        #reader__camera_permission_button {
            background: #2563eb !important;
            color: white !important;
            border-radius: 8px !important;
            padding: 8px 16px !important;
            border: none !important;
            font-size: 14px !important;
            cursor: pointer !important;
        }

        .hasil-card {
            background: white;
            border-radius: 16px;
            overflow: hidden;
            box-shadow: 0 4px 20px rgba(0,0,0,0.08);
            animation: fadeUp 0.3s ease;
        }
        @keyframes fadeUp {
            from { opacity: 0; transform: translateY(10px); }
            to   { opacity: 1; transform: translateY(0); }
        }

        .btn-scan-lain {
            width: 100%;
            padding: 11px;
            background: #f3f4f6;
            color: #555;
            border: none;
            border-radius: 12px;
            font-size: 14px;
            font-weight: 600;
            cursor: pointer;
            margin-top: 10px;
            transition: background 0.2s;
        }
        .btn-scan-lain:hover { background: #e5e7eb; }

        .btn-aksi {
            width: 100%;
            padding: 13px;
            border: none;
            border-radius: 12px;
            font-size: 15px;
            font-weight: 700;
            cursor: pointer;
            transition: opacity 0.2s;
        }
        .btn-aksi:hover { opacity: 0.9; }
        .btn-aksi:disabled { background: #ccc !important; cursor: not-allowed; }

        .btn-pinjam { background: linear-gradient(135deg, #1a6e35, #27ae60); color: white; }
        .btn-kembali { background: linear-gradient(135deg, #2563eb, #3b82f6); color: white; }

        .info-box {
            border-radius: 10px;
            padding: 12px 14px;
            font-size: 13px;
            line-height: 1.8;
        }
        .info-box.green { background: #f0fdf4; color: #166534; }
        .info-box.blue { background: #eff6ff; color: #1e40af; }
        .info-box.amber { background: #fffbeb; color: #92400e; }
        .info-box.red { background: #fef2f2; color: #991b1b; }
        .info-box strong { font-weight: 700; }

        .form-input {
            width: 100%;
            padding: 11px 14px;
            border: 2px solid #e5e7eb;
            border-radius: 10px;
            font-size: 14px;
            outline: none;
            transition: border 0.2s;
        }
        .form-input:focus { border-color: #27ae60; }

        .badge-stok {
            display: inline-block;
            padding: 3px 10px;
            border-radius: 20px;
            font-size: 11px;
            font-weight: 600;
        }
        .badge-stok.ada { background: #d4edda; color: #1a6e35; }
        .badge-stok.habis { background: #f8d7da; color: #721c24; }

        .badge-status {
            display: inline-block;
            padding: 3px 10px;
            border-radius: 20px;
            font-size: 11px;
            font-weight: 600;
        }

        #alert-box {
            border-radius: 12px;
            padding: 12px 16px;
            font-size: 14px;
            font-weight: 500;
            margin-bottom: 16px;
            display: none;
        }
        #alert-box.show { display: block; }
        #alert-box.success { background: #f0fdf4; border: 1px solid #bbf7d0; color: #166534; }
        #alert-box.error { background: #fef2f2; border: 1px solid #fecaca; color: #991b1b; }
        #alert-box.info { background: #eff6ff; border: 1px solid #bfdbfe; color: #1e40af; }

        .sampul-img {
            width: 80px;
            height: 110px;
            object-fit: cover;
            border-radius: 8px;
            border: 1px solid #eee;
        }
        .sampul-placeholder {
            width: 80px;
            height: 110px;
            background: #f3f4f6;
            border-radius: 8px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 32px;
        }

        body.dark-mode { background: #121212; color: white; }
        body.dark-mode .navbar { background: #1e1e1e; }
        body.dark-mode .navbar a, body.dark-mode .navbar span { color: white !important; }
        body.dark-mode .hasil-card { background: #1e1e1e; }
        body.dark-mode .info-box.green { background: #14311a; color: #86efac; }
        body.dark-mode .info-box.blue { background: #172554; color: #93c5fd; }
        body.dark-mode .info-box.amber { background: #451a03; color: #fcd34d; }
        body.dark-mode .info-box.red { background: #450a0a; color: #fca5a5; }
        body.dark-mode .form-input { background: #2a2a2a; border-color: #444; color: white; }
        body.dark-mode .btn-scan-lain { background: #2a2a2a; color: #ddd; }
        body.dark-mode .sampul-placeholder { background: #2a2a2a; }
        body.dark-mode .sampul-img { border-color: #444; }
    </style>
</head>
<body>

<nav class="navbar">
    <div class="container-fluid px-4">
        <div class="d-flex align-items-center justify-content-between w-100">
            <a href="{{ route('dashboard') }}" class="d-flex align-items-center gap-2 text-decoration-none">
                <img src="{{ asset('images/logo.jpg') }}" style="width:45px;height:45px;border-radius:50%;object-fit:cover" alt="Logo">
                <span style="font-size:13px;font-weight:700;color:#1a6e35;text-transform:uppercase;line-height:1.3">SMK Maarif<br>Walisongo Kajoran</span>
            </a>
            <a href="{{ route('dashboard') }}" style="color:#1a6e35;text-decoration:none;font-size:14px;font-weight:500">
                <i class="bi bi-arrow-left"></i> Kembali
            </a>
        </div>
    </div>
</nav>

<div class="main-container">

    <div class="text-center mb-4">
        <div style="font-size:40px;margin-bottom:8px">📷</div>
        <h1 style="font-size:20px;font-weight:700;color:#222">Scan QR Code Buku</h1>
        <p style="font-size:13px;color:#888;margin-top:4px">Arahkan kamera ke QR Code pada buku</p>
    </div>

    <div id="alert-box"></div>

    <div id="reader"></div>

    <div class="text-center mt-3 mb-3">
        <button onclick="toggleManual()" style="background:none;border:none;color:#999;font-size:12px;text-decoration:underline;cursor:pointer">
            Input Kode Buku / ISBN manual
        </button>
    </div>
    <div id="manual-box" style="display:none;margin-bottom:20px">
        <div style="display:flex;gap:8px">
            <input id="kode-manual" type="text" placeholder="Masukkan kode buku (BK0001) atau ISBN"
                style="flex:1;padding:10px 14px;border:2px solid #e5e7eb;border-radius:10px;font-size:14px;outline:none">
            <button onclick="prosesKode(document.getElementById('kode-manual').value.trim())"
                style="padding:10px 20px;background:#2563eb;color:white;border:none;border-radius:10px;font-size:14px;font-weight:600;cursor:pointer">
                Cari
            </button>
        </div>
    </div>

    <div id="hasil-area" style="display:none"></div>

</div>

<script src="https://unpkg.com/html5-qrcode@2.3.8/html5-qrcode.min.js"></script>
<script>
(function() {
    var CSRF = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
    var CEK_URL = '{{ route("barcode.cekBuku") }}';
    var PINJAM_URL = '{{ route("barcode.pinjam") }}';
    var KEMBALI_URL = '{{ route("barcode.kembali") }}';
    var IS_VIP = {{ $isVip ? 'true' : 'false' }};
    var USER_NAME = '{{ addslashes(auth()->user()->name) }}';

    var lastScanned = null;
    var bukuData = null;
    var pinjamData = null;
    var scanner = null;
    var scannerAktif = false;
    var sedangMemproses = false;

    document.addEventListener('DOMContentLoaded', function() {
        mulaiScanner();
    });

    function mulaiScanner() {
        if (scannerAktif) return;

        var readerEl = document.getElementById('reader');
        readerEl.innerHTML = '';
        readerEl.style.display = 'block';

        if (!window.isSecureContext) {
            readerEl.innerHTML = '<div style="text-align:center;padding:30px 16px">'
                + '<div style="font-size:40px;margin-bottom:12px">🔒</div>'
                + '<p style="color:#dc2626;font-size:14px;font-weight:600;margin-bottom:8px">Kamera butuh HTTPS</p>'
                + '<p style="color:#888;font-size:12px">Buka halaman ini via HTTPS atau localhost agar kamera bisa diakses.</p>'
                + '</div>';
            document.getElementById('manual-box').style.display = 'block';
            return;
        }

        if (!navigator.mediaDevices || !navigator.mediaDevices.getUserMedia) {
            readerEl.innerHTML = '<div style="text-align:center;padding:30px 16px">'
                + '<div style="font-size:40px;margin-bottom:12px">📷</div>'
                + '<p style="color:#dc2626;font-size:14px;font-weight:600;margin-bottom:8px">Browser tidak support kamera</p>'
                + '<p style="color:#888;font-size:12px">Gunakan Chrome, Safari, atau Edge terbaru.</p>'
                + '</div>';
            document.getElementById('manual-box').style.display = 'block';
            return;
        }

        scanner = new Html5Qrcode('reader');

        scanner.start(
            { facingMode: 'environment' },
            { fps: 10, qrbox: { width: 250, height: 100 } },
            onScanSuccess,
            function() {}
        ).then(function() {
            scannerAktif = true;
            console.log('[Scanner] Kamera aktif');
        }).catch(function(err) {
            console.error('[Scanner] Gagal mulai:', err);
            var msg = 'Kamera tidak dapat diakses.';
            if (err && err.toString().indexOf('NotAllowedError') !== -1) {
                msg = 'Izin kamera ditolak. Silakan izinkan akses kamera di pengaturan browser.';
            } else if (err && err.toString().indexOf('NotFoundError') !== -1) {
                msg = 'Kamera tidak ditemukan di perangkat ini.';
            }
            readerEl.innerHTML = '<div style="text-align:center;padding:30px 16px">'
                + '<div style="font-size:40px;margin-bottom:12px">📷</div>'
                + '<p style="color:#dc2626;font-size:13px;margin-bottom:8px">' + msg + '</p>'
                + '<p style="color:#888;font-size:12px">Gunakan input manual di bawah.</p>'
                + '</div>';
            document.getElementById('manual-box').style.display = 'block';
        });
    }

    function onScanSuccess(decodedText) {
        if (sedangMemproses) return;
        if (decodedText === lastScanned) return;

        lastScanned = decodedText;
        sedangMemproses = true;

        console.log('[Scanner] QR terbaca:', decodedText);
        showAlert('info', 'QR terbaca! Memproses: ' + decodedText);

        prosesKode(decodedText).then(function() {
            sedangMemproses = false;
            setTimeout(function() { lastScanned = null; }, 5000);
        }).catch(function() {
            sedangMemproses = false;
            setTimeout(function() { lastScanned = null; }, 5000);
        });
    }

    function hentikanScanner() {
        return new Promise(function(resolve) {
            if (scanner && scannerAktif) {
                scanner.stop().then(function() {
                    scanner = null;
                    scannerAktif = false;
                    resolve();
                }).catch(function() {
                    scanner = null;
                    scannerAktif = false;
                    resolve();
                });
            } else {
                scanner = null;
                scannerAktif = false;
                resolve();
            }
        });
    }

    function prosesKode(kode) {
        if (!kode) return Promise.resolve();

        return fetch(CEK_URL, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': CSRF,
                'Accept': 'application/json',
            },
            body: JSON.stringify({ kode: kode }),
        })
        .then(function(res) {
            if (res.status === 419) {
                showAlert('error', 'Sesi habis. Silakan refresh halaman.');
                return;
            }
            return res.json().then(function(data) {
                if (data.status === 'not_found') {
                    showAlert('error', 'Buku tidak ditemukan untuk kode: ' + kode);
                    return;
                }

                bukuData = data.buku;
                pinjamData = data.peminjaman_aktif;

                showAlert('success', 'Buku ditemukan: ' + data.buku.judul);

                return hentikanScanner().then(function() {
                    var readerEl = document.getElementById('reader');
                    readerEl.style.display = 'none';
                    tampilkanHasil(data);
                });
            });
        })
        .catch(function(err) {
            console.error('[Scanner] Fetch error:', err);
            showAlert('error', 'Gagal terhubung ke server.');
        });
    }

    function tampilkanHasil(data) {
        var area = document.getElementById('hasil-area');
        var buku = data.buku;
        var pinjam = data.peminjaman_aktif;
        var stokHabis = buku.stok < 1;

        var sampulHtml = buku.sampul
            ? '<img src="' + buku.sampul + '" class="sampul-img" alt="' + escHtml(buku.judul) + '">'
            : '<div class="sampul-placeholder">📚</div>';

        var stokBadge = stokHabis
            ? '<span class="badge-stok habis">✕ Stok habis</span>'
            : '<span class="badge-stok ada">✓ Tersedia (' + buku.stok + ')</span>';

        var statusBadge = '';
        if (pinjam) {
            if (pinjam.status === 'dipinjam') statusBadge = '<span class="badge-status" style="background:#dbeafe;color:#1d4ed8">📖 Sedang dipinjam</span>';
            else if (pinjam.status === 'menunggu_konfirmasi') statusBadge = '<span class="badge-status" style="background:#fef3c7;color:#92400e">⏳ Menunggu konfirmasi</span>';
            else if (pinjam.status === 'menunggu_pengembalian') statusBadge = '<span class="badge-status" style="background:#ede9fe;color:#6d28d9">⏳ Menunggu pengembalian</span>';
        }

        var genreBadge = buku.genre ? '<span class="badge-status" style="background:#f3f4f6;color:#666">' + escHtml(buku.genre) + '</span>' : '';

        var infoHtml = '<div style="display:grid;grid-template-columns:1fr 1fr;gap:10px;font-size:13px">';
        if (buku.kode_buku) infoHtml += '<div><span style="color:#999;font-size:11px">Kode Buku</span><p style="font-weight:600;color:#333;margin:2px 0 0">' + escHtml(buku.kode_buku) + '</p></div>';
        infoHtml += '<div><span style="color:#999;font-size:11px">ISBN</span><p style="font-weight:600;color:#333;margin:2px 0 0">' + escHtml(buku.isbn || '-') + '</p></div>';
        if (buku.lokasi) infoHtml += '<div><span style="color:#999;font-size:11px">Lokasi Rak</span><p style="font-weight:600;color:#333;margin:2px 0 0">📍 Rak ' + escHtml(buku.lokasi) + '</p></div>';
        infoHtml += '<div><span style="color:#999;font-size:11px">Stok</span><p style="font-weight:600;color:#333;margin:2px 0 0">' + buku.stok + '</p></div>';
        infoHtml += '</div>';

        var aksiHtml = '';

        if (pinjam && pinjam.status === 'dipinjam') {
            var tglPinjam = formatDate(pinjam.tanggal_pinjam);
            var tglKembali = formatDate(pinjam.tanggal_kembali);
            var eksKode = pinjam.eksemplar_kode ? ' (' + pinjam.eksemplar_kode + ')' : '';
            aksiHtml = ''
                + '<div style="border-top:1px solid #eee;padding-top:16px;margin-top:16px">'
                + '<h3 style="font-size:15px;font-weight:700;color:#222;margin-bottom:12px"><i class="bi bi-arrow-counterclockwise" style="color:#2563eb"></i> Konfirmasi Pengembalian</h3>'
                + '<div class="info-box blue" style="margin-bottom:16px">'
                + '<p><i class="bi bi-calendar"></i> Tanggal Pinjam: <strong>' + tglPinjam + '</strong></p>'
                + '<p><i class="bi bi-calendar-check"></i> Tanggal Harus Kembali: <strong>' + tglKembali + '</strong></p>'
                + '<p><i class="bi bi-info-circle"></i> Status: <strong>Dipinjam</strong></p>'
                + '</div>'
                + '<button id="btnKembali" class="btn-aksi btn-kembali" onclick="submitKembali()"><i class="bi bi-arrow-return-left"></i> Ajukan Pengembalian</button>'
                + '<button class="btn-scan-lain" onclick="resetDanMulaiLagi()">Scan Buku Lain</button>'
                + '</div>';
        } else if (pinjam && (pinjam.status === 'menunggu_konfirmasi' || pinjam.status === 'menunggu_pengembalian')) {
            var msg = pinjam.status === 'menunggu_konfirmasi' ? 'Peminjaman sedang menunggu konfirmasi admin.' : 'Pengembalian sedang menunggu konfirmasi admin.';
            aksiHtml = ''
                + '<div style="border-top:1px solid #eee;padding-top:16px;margin-top:16px">'
                + '<div class="info-box amber" style="text-align:center"><i class="bi bi-hourglass-split"></i> ' + msg + '</div>'
                + '<button class="btn-scan-lain" onclick="resetDanMulaiLagi()" style="margin-top:12px">Scan Buku Lain</button>'
                + '</div>';
        } else if (stokHabis) {
            aksiHtml = ''
                + '<div style="border-top:1px solid #eee;padding-top:16px;margin-top:16px">'
                + '<div class="info-box red" style="text-align:center"><i class="bi bi-x-circle"></i> Stok buku habis.</div>'
                + '<button class="btn-scan-lain" onclick="resetDanMulaiLagi()" style="margin-top:12px">Scan Buku Lain</button>'
                + '</div>';
        } else {
            var today = new Date().toISOString().split('T')[0];
            var minKembali = new Date(Date.now() + 86400000).toISOString().split('T')[0];
            var maxDays = IS_VIP ? 14 : 7;
            var maxDate = new Date(Date.now() + maxDays * 86400000).toISOString().split('T')[0];
            var batasText = IS_VIP ? '⭐ VIP — maks. 6 buku, 14 hari' : 'maks. 3 buku, 7 hari';

            aksiHtml = ''
                + '<div style="border-top:1px solid #eee;padding-top:16px;margin-top:16px">'
                + '<h3 style="font-size:15px;font-weight:700;color:#222;margin-bottom:12px"><i class="bi bi-bookmark-plus" style="color:#1a6e35"></i> Form Peminjaman</h3>'
                + '<div class="info-box green" style="margin-bottom:14px">'
                + '<p><i class="bi bi-person"></i> Peminjam: <strong>' + escHtml(USER_NAME) + '</strong></p>'
                + '<p><i class="bi bi-info-circle"></i> Batas pinjam: <strong>' + batasText + '</strong></p>'
                + '</div>'
                + '<form id="formPinjam" onsubmit="submitPinjam(event)">'
                + '<div style="margin-bottom:12px">'
                + '<label style="font-size:13px;font-weight:600;color:#444;display:block;margin-bottom:4px">Tanggal Pinjam</label>'
                + '<input type="date" id="form-tgl-pinjam" class="form-input" required min="' + today + '" value="' + today + '" max="' + maxDate + '" onchange="updateMinKembali()">'
                + '</div>'
                + '<div style="margin-bottom:14px">'
                + '<label style="font-size:13px;font-weight:600;color:#444;display:block;margin-bottom:4px">Tanggal Kembali</label>'
                + '<input type="date" id="form-tgl-kembali" class="form-input" required min="' + minKembali + '" max="' + maxDate + '">'
                + '</div>'
                + '<div id="form-validasi" style="display:none;color:#dc2626;font-size:12px;margin-bottom:10px"></div>'
                + '<button type="submit" id="btnPinjam" class="btn-aksi btn-pinjam"><i class="bi bi-check-circle"></i> Ajukan Peminjaman</button>'
                + '</form>'
                + '<button class="btn-scan-lain" onclick="resetDanMulaiLagi()">Scan Buku Lain</button>'
                + '</div>';
        }

        area.innerHTML = ''
            + '<div class="hasil-card" style="padding:20px">'
            + '<div style="display:flex;gap:14px;align-items:flex-start">'
            + '<div style="flex-shrink:0">' + sampulHtml + '</div>'
            + '<div style="flex:1;min-width:0">'
            + '<h2 style="font-size:16px;font-weight:700;color:#222;line-height:1.3">' + escHtml(buku.judul) + '</h2>'
            + '<p style="font-size:13px;color:#888;margin-top:3px">' + escHtml(buku.pengarang) + '</p>'
            + '<div style="margin-top:8px;display:flex;flex-wrap:wrap;gap:4px">' + stokBadge + ' ' + statusBadge + ' ' + genreBadge + '</div>'
            + '</div>'
            + '</div>'
            + '<div style="margin-top:16px">' + infoHtml + '</div>'
            + aksiHtml
            + '</div>';

        area.style.display = 'block';
    }

    window.updateMinKembali = function() {
        var tglPinjam = document.getElementById('form-tgl-pinjam').value;
        if (tglPinjam) {
            var d = new Date(tglPinjam);
            d.setDate(d.getDate() + 1);
            document.getElementById('form-tgl-kembali').min = d.toISOString().split('T')[0];
        }
    };

    window.submitPinjam = function(e) {
        e.preventDefault();
        if (!bukuData) return;

        var tglPinjam = document.getElementById('form-tgl-pinjam').value;
        var tglKembali = document.getElementById('form-tgl-kembali').value;
        var validasiEl = document.getElementById('form-validasi');

        if (!tglPinjam || !tglKembali) {
            validasiEl.textContent = 'Harap isi tanggal pinjam dan tanggal kembali.';
            validasiEl.style.display = 'block';
            return;
        }
        if (tglKembali <= tglPinjam) {
            validasiEl.textContent = 'Tanggal kembali harus setelah tanggal pinjam.';
            validasiEl.style.display = 'block';
            return;
        }
        validasiEl.style.display = 'none';

        var btn = document.getElementById('btnPinjam');
        btn.disabled = true;
        btn.innerHTML = 'Mengirim...';

        fetch(PINJAM_URL, {
            method: 'POST',
            headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': CSRF, 'Accept': 'application/json' },
            body: JSON.stringify({ buku_id: bukuData.id, tanggal_pinjam: tglPinjam, tanggal_kembali: tglKembali }),
        })
        .then(function(r) { return r.json(); })
        .then(function(data) {
            if (data.status === 'success') {
                showAlert('success', data.pesan);
                resetDanMulaiLagi();
            } else {
                showAlert('error', data.pesan || 'Terjadi kesalahan.');
                btn.disabled = false;
                btn.innerHTML = '<i class="bi bi-check-circle"></i> Ajukan Peminjaman';
            }
        })
        .catch(function() {
            showAlert('error', 'Terjadi kesalahan. Coba lagi.');
            btn.disabled = false;
            btn.innerHTML = '<i class="bi bi-check-circle"></i> Ajukan Peminjaman';
        });
    };

    window.submitKembali = function() {
        if (!pinjamData) return;
        var btn = document.getElementById('btnKembali');
        btn.disabled = true;
        btn.innerHTML = 'Mengirim...';

        fetch(KEMBALI_URL, {
            method: 'POST',
            headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': CSRF, 'Accept': 'application/json' },
            body: JSON.stringify({ peminjaman_id: pinjamData.id }),
        })
        .then(function(r) { return r.json(); })
        .then(function(data) {
            if (data.status === 'success') {
                showAlert('success', data.pesan);
                resetDanMulaiLagi();
            } else {
                showAlert('error', data.pesan || 'Terjadi kesalahan.');
                btn.disabled = false;
                btn.innerHTML = '<i class="bi bi-arrow-return-left"></i> Ajukan Pengembalian';
            }
        })
        .catch(function() {
            showAlert('error', 'Terjadi kesalahan. Coba lagi.');
            btn.disabled = false;
            btn.innerHTML = '<i class="bi bi-arrow-return-left"></i> Ajukan Pengembalian';
        });
    };

    window.resetDanMulaiLagi = function() {
        bukuData = null;
        pinjamData = null;
        lastScanned = null;
        sedangMemproses = false;
        document.getElementById('hasil-area').style.display = 'none';
        document.getElementById('hasil-area').innerHTML = '';
        hideAlert();

        hentikanScanner().then(function() {
            document.getElementById('reader').innerHTML = '';
            document.getElementById('reader').style.display = 'block';
            mulaiScanner();
        });
    };

    window.toggleManual = function() {
        var box = document.getElementById('manual-box');
        box.style.display = box.style.display === 'none' ? 'block' : 'none';
    };

    function showAlert(type, msg) {
        var box = document.getElementById('alert-box');
        box.className = type + ' show';
        box.textContent = msg;
    }

    function hideAlert() {
        document.getElementById('alert-box').className = '';
    }

    function formatDate(str) {
        if (!str) return '-';
        var d = new Date(str);
        var months = ['Januari','Februari','Maret','April','Mei','Juni','Juli','Agustus','September','Oktober','November','Desember'];
        return d.getDate() + ' ' + months[d.getMonth()] + ' ' + d.getFullYear();
    }

    function escHtml(str) {
        if (!str) return '';
        var div = document.createElement('div');
        div.textContent = str;
        return div.innerHTML;
    }
})();
</script>

@if(auth()->user()->is_on_duty)
<script>
    if(localStorage.getItem('darkMode') === 'enabled'){
        document.body.classList.add('dark-mode');
    }
</script>
@endif

</body>
</html>
