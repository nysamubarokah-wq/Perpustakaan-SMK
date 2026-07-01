@extends('layouts.admin')

@section('title', 'Scan Buku')

@section('content')
<style>
    #reader { width: 100%; max-width: 500px; border-radius: 12px; overflow: hidden; border: 2px dashed #d1d5db; background: #f9fafb; margin: 0 auto; }
    #reader video { width: 100% !important; border-radius: 12px; }
    #reader__dashboard_section_swaplink { display: none !important; }
    #reader__dashboard_section_csr { display: none !important; }
    #reader__camera_permission_button { background: #1a6e35 !important; color: white !important; border-radius: 8px !important; padding: 8px 16px !important; border: none !important; font-size: 14px !important; cursor: pointer !important; }

    .sampul-img { width: 80px; height: 110px; object-fit: cover; border-radius: 8px; border: 1px solid #eee; }
    .sampul-placeholder { width: 80px; height: 110px; background: #f3f4f6; border-radius: 8px; display: flex; align-items: center; justify-content: center; font-size: 32px; }

    #alert-box { border-radius: 10px; padding: 12px 16px; font-size: 14px; font-weight: 500; margin-bottom: 16px; display: none; }
    #alert-box.show { display: block; }
    #alert-box.success { background: #f0fdf4; border: 1px solid #bbf7d0; color: #166534; }
    #alert-box.error { background: #fef2f2; border: 1px solid #fecaca; color: #991b1b; }
    #alert-box.info { background: #eff6ff; border: 1px solid #bfdbfe; color: #1e40af; }

    .badge-stok { display: inline-block; padding: 3px 10px; border-radius: 20px; font-size: 11px; font-weight: 600; }
    .badge-stok.ada { background: #d4edda; color: #1a6e35; }
    .badge-stok.habis { background: #f8d7da; color: #721c24; }

    .btn-scan-lain { width: 100%; padding: 11px; background: #f3f4f6; color: #555; border: none; border-radius: 10px; font-size: 14px; font-weight: 600; cursor: pointer; margin-top: 10px; transition: background 0.2s; }
    .btn-scan-lain:hover { background: #e5e7eb; }

    .btn-aksi { width: 100%; padding: 13px; border: none; border-radius: 10px; font-size: 15px; font-weight: 700; cursor: pointer; transition: opacity 0.2s; color: white; }
    .btn-aksi:hover { opacity: 0.9; }
    .btn-aksi:disabled { background: #ccc !important; cursor: not-allowed; }
    .btn-pinjam { background: linear-gradient(135deg, #1a6e35, #27ae60); }
    .btn-kembali { background: linear-gradient(135deg, #2563eb, #3b82f6); }

    .form-input { width: 100%; padding: 10px 12px; border: 2px solid #e5e7eb; border-radius: 8px; font-size: 14px; outline: none; transition: border 0.2s; }
    .form-input:focus { border-color: #27ae60; }

    .info-box { border-radius: 8px; padding: 10px 12px; font-size: 13px; line-height: 1.7; }
    .info-box.green { background: #f0fdf4; color: #166534; }
    .info-box.blue { background: #eff6ff; color: #1e40af; }
    .info-box.red { background: #fef2f2; color: #991b1b; }
    .info-box.amber { background: #fffbeb; color: #92400e; }

    .hasil-card { background: white; border-radius: 16px; overflow: hidden; box-shadow: 0 4px 20px rgba(0,0,0,0.08); animation: fadeUp 0.3s ease; }
    @keyframes fadeUp { from { opacity: 0; transform: translateY(10px); } to { opacity: 1; transform: translateY(0); } }

    .anggota-wrap { position: relative; }
    .anggota-input { width: 100%; padding: 10px 12px; border: 2px solid #e5e7eb; border-radius: 8px; font-size: 14px; outline: none; transition: border 0.2s; background: white; cursor: text; }
    .anggota-input:focus, .anggota-input.selected { border-color: #27ae60; }
    .anggota-input.has-value { color: #222; font-weight: 600; }
    .anggota-dropdown { position: absolute; top: 100%; left: 0; right: 0; max-height: 220px; overflow-y: auto; background: white; border: 2px solid #e5e7eb; border-top: none; border-radius: 0 0 8px 8px; z-index: 100; display: none; box-shadow: 0 8px 16px rgba(0,0,0,0.1); }
    .anggota-dropdown.show { display: block; }
    .anggota-item { padding: 10px 12px; font-size: 13px; cursor: pointer; border-bottom: 1px solid #f3f4f6; display: flex; align-items: center; gap: 10px; transition: background 0.15s; }
    .anggota-item:last-child { border-bottom: none; }
    .anggota-item:hover { background: #f0fdf4; }
    .anggota-item .anggota-avatar { width: 32px; height: 32px; border-radius: 50%; background: #1a6e35; color: white; display: flex; align-items: center; justify-content: center; font-size: 12px; font-weight: 700; flex-shrink: 0; }
    .anggota-item .anggota-info { flex: 1; min-width: 0; }
    .anggota-item .anggota-nama { font-weight: 600; color: #222; }
    .anggota-item .anggota-nis { font-size: 11px; color: #999; }
    .anggota-empty { padding: 14px; text-align: center; color: #999; font-size: 13px; }
    .anggota-clear { position: absolute; right: 10px; top: 50%; transform: translateY(-50%); background: none; border: none; color: #999; cursor: pointer; font-size: 16px; padding: 4px; display: none; }
    .anggota-clear.show { display: block; }

    .scanner-section { max-width: 500px; margin: 0 auto; }
</style>

<div class="scanner-section">

    <div class="text-center mb-4">
        <div style="font-size:36px;margin-bottom:8px">📷</div>
        <p style="font-size:13px;color:#888">Arahkan kamera ke QR Code pada buku, atau input kode manual</p>
    </div>

    <div id="alert-box"></div>

    <div id="reader" class="mb-3"></div>

    <div class="text-center mb-3">
        <button onclick="document.getElementById('manual-box').classList.toggle('d-none')" style="background:none;border:none;color:#999;font-size:12px;text-decoration:underline;cursor:pointer">
            Input Kode Buku / ISBN manual
        </button>
    </div>
    <div id="manual-box" class="d-none mb-4">
        <div class="d-flex gap-2">
            <input id="kode-manual" type="text" class="form-input" placeholder="Masukkan kode buku (BK0001) atau ISBN" onkeydown="if(event.key==='Enter'){event.preventDefault();prosesKode(this.value.trim());}">
            <button onclick="prosesKode(document.getElementById('kode-manual').value.trim())" class="btn btn-success" style="white-space:nowrap;border-radius:8px">Cari</button>
        </div>
    </div>

    <div id="hasil-area" style="display:none"></div>

</div>
@endsection

@section('script')
<script src="https://unpkg.com/html5-qrcode@2.3.8/html5-qrcode.min.js"></script>
<script>
(function() {
    var CSRF = '{{ csrf_token() }}';
    var CEK_URL = '{{ route("admin.scanner.cek") }}'.replace(/^https?:\/\/[^/]+/, '');

    var bukuData = null;
    var peminjamanData = null;
    var anggotaList = [];
    var dataGlobal = null;
    var scanner = null;
    var scannerAktif = false;
    var sedangMemproses = false;
    var lastScanned = null;

    document.addEventListener('DOMContentLoaded', mulaiScanner);

    function mulaiScanner() {
        if (scannerAktif) return;
        var el = document.getElementById('reader');
        el.innerHTML = '';
        el.style.display = 'block';

        scanner = new Html5Qrcode('reader');
        scanner.start(
            { facingMode: 'environment' },
            { fps: 10, qrbox: { width: 250, height: 250 } },
            onScanSuccess,
            function() {}
        ).then(function() {
            scannerAktif = true;
        }).catch(function() {
            el.innerHTML = '<p class="text-center text-muted py-4 mb-0" style="font-size:13px">Kamera tidak dapat diakses.<br>Gunakan input manual di bawah.</p>';
            document.getElementById('manual-box').classList.remove('d-none');
        });
    }

    function onScanSuccess(text) {
        if (sedangMemproses) return;
        if (text === lastScanned) return;
        lastScanned = text;
        sedangMemproses = true;
        showAlert('info', 'QR terbaca! Memproses: ' + text);
        prosesKode(text).finally(function() {
            sedangMemproses = false;
            setTimeout(function() { lastScanned = null; }, 5000);
        });
    }

    function hentikanScanner() {
        return new Promise(function(resolve) {
            if (scanner && scannerAktif) {
                scanner.stop().then(function() { scanner = null; scannerAktif = false; resolve(); })
                    .catch(function() { scanner = null; scannerAktif = false; resolve(); });
            } else {
                scanner = null; scannerAktif = false; resolve();
            }
        });
    }

    function prosesKode(kode) {
        if (!kode) return Promise.resolve();
        return fetch(CEK_URL, {
            method: 'POST',
            headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': CSRF, 'Accept': 'application/json' },
            body: JSON.stringify({ kode: kode }),
        })
        .then(function(res) {
            if (res.status === 419) {
                showAlert('error', 'Sesi habis. Silakan refresh halaman.');
                return;
            }
            var ct = res.headers.get('content-type') || '';
            if (!ct.includes('json')) {
                return res.text().then(function(t) {
                    console.error('Non-JSON response (' + res.status + '):', t);
                    showAlert('error', 'Server error ' + res.status + '. Buka Console (F12) untuk detail.');
                });
            }
            return res.json().then(function(data) {
                if (res.ok && data.status === 'found') {
                    bukuData = data.buku;
                    peminjamanData = data.peminjaman_aktif;
                    anggotaList = data.anggota || [];
                    dataGlobal = data;
                    showAlert('success', 'Buku ditemukan: ' + data.buku.judul + (data.eksemplar ? ' (' + data.eksemplar.kode_buku + ')' : ''));
                    return hentikanScanner().then(function() {
                        document.getElementById('reader').style.display = 'none';
                        tampilkanHasil();
                    });
                }
                showAlert('error', data.pesan || data.message || 'Terjadi kesalahan.');
            });
        })
        .catch(function(err) {
            console.error('Fetch error:', err);
            showAlert('error', 'Gagal terhubung ke server. Cek Console (F12) untuk detail.');
        });
    }
    window.prosesKode = prosesKode;

    function tampilkanHasil() {
        var area = document.getElementById('hasil-area');
        var b = bukuData;
        var p = peminjamanData;
        var stokHabis = b.stok < 1;
        var eksemplarInfo = dataGlobal && dataGlobal.eksemplar;

        var sampulHtml = b.sampul
            ? '<img src="' + b.sampul + '" class="sampul-img" alt="">'
            : '<div class="sampul-placeholder">📚</div>';

        var stokBadge = stokHabis
            ? '<span class="badge-stok habis">✕ Stok habis</span>'
            : '<span class="badge-stok ada">✓ Tersedia (' + b.stok + ')</span>';

        var eksemplarBadge = eksemplarInfo
            ? '<div style="margin-top:5px"><span style="padding:3px 10px;border-radius:20px;font-size:11px;font-weight:600;background:#e8f5e9;color:#1a6e35;font-family:monospace">Eksemplar: ' + esc(eksemplarInfo.kode_buku) + ' (' + esc(eksemplarInfo.status) + ')</span></div>'
            : '';

        var infoHtml = '<div class="row" style="font-size:13px">'
            + '<div class="col-6 mb-2"><span class="text-muted" style="font-size:11px">Kode Buku</span><p class="fw-bold mb-0">' + esc(b.kode_buku) + '</p></div>'
            + '<div class="col-6 mb-2"><span class="text-muted" style="font-size:11px">ISBN</span><p class="fw-bold mb-0">' + esc(b.isbn || '-') + '</p></div>'
            + '<div class="col-6 mb-2"><span class="text-muted" style="font-size:11px">Lokasi Rak</span><p class="fw-bold mb-0">📍 ' + esc(b.lokasi || '-') + '</p></div>'
            + '<div class="col-6 mb-2"><span class="text-muted" style="font-size:11px">Status</span><p class="mb-0">' + stokBadge + '</p></div>'
            + '</div>';

        var aksiHtml = '';

        if (p) {
            var tglPinjam = fmtDate(p.tanggal_pinjam);
            var tglKembali = fmtDate(p.tanggal_kembali);
            var statusLabel = p.status === 'dipinjam' ? 'Sedang dipinjam' : 'Menunggu pengembalian';
            var statusColor = p.status === 'dipinjam' ? '#1d4ed8' : '#6d28d9';

            aksiHtml = '<div style="border-top:1px solid #eee;padding-top:16px;margin-top:16px">'
                + '<h5 class="fw-bold mb-3" style="font-size:15px"><i class="bi bi-arrow-counterclockwise text-primary"></i> Form Pengembalian</h5>'
                + '<div class="info-box blue mb-3">'
                + '<p class="mb-1"><i class="bi bi-person"></i> Peminjam: <strong>' + esc(p.anggota_nama) + '</strong></p>'
                + '<p class="mb-1"><i class="bi bi-calendar"></i> Tanggal Pinjam: <strong>' + tglPinjam + '</strong></p>'
                + '<p class="mb-1"><i class="bi bi-calendar-check"></i> Tanggal Kembali: <strong>' + tglKembali + '</strong></p>'
                + '<p class="mb-0"><i class="bi bi-info-circle"></i> Status: <strong style="color:' + statusColor + '">' + statusLabel + '</strong></p>'
                + '</div>'
                + '<form method="POST" action="{{ route("admin.scanner.kembali") }}">'
                + '@csrf'
                + '<input type="hidden" name="buku_id" value="' + b.id + '">'
                + '<button type="submit" class="btn-aksi btn-kembali" onclick="return confirm(\'Kembalikan buku ini?\')"><i class="bi bi-arrow-return-left"></i> Kembalikan Buku</button>'
                + '</form>'
                + '<button class="btn-scan-lain" onclick="resetScanner()"><i class="bi bi-qr-code-scan"></i> Scan Buku Lain</button>'
                + '</div>';

        } else if (stokHabis) {
            aksiHtml = '<div style="border-top:1px solid #eee;padding-top:16px;margin-top:16px">'
                + '<div class="info-box red text-center"><i class="bi bi-x-circle"></i> Stok buku habis. Tidak bisa dipinjamkan.</div>'
                + '<button class="btn-scan-lain" onclick="resetScanner()"><i class="bi bi-qr-code-scan"></i> Scan Buku Lain</button>'
                + '</div>';

        } else {
            var itemHtml = '';
            for (var i = 0; i < anggotaList.length; i++) {
                var a = anggotaList[i];
                var initials = a.nama.substring(0, 2).toUpperCase();
                var nisLabel = a.nis ? 'NIS: ' + a.nis : '';
                itemHtml += '<div class="anggota-item" data-id="' + a.id + '" data-nama="' + esc(a.nama) + '" onclick="pilihAnggota(this)">'
                    + '<div class="anggota-avatar">' + esc(initials) + '</div>'
                    + '<div class="anggota-info">'
                    + '<div class="anggota-nama">' + esc(a.nama) + '</div>'
                    + (nisLabel ? '<div class="anggota-nis">' + esc(nisLabel) + '</div>' : '')
                    + '</div></div>';
            }

            var today = new Date().toISOString().split('T')[0];
            var nextWeek = new Date(Date.now() + 7 * 86400000).toISOString().split('T')[0];

            aksiHtml = '<div style="border-top:1px solid #eee;padding-top:16px;margin-top:16px">'
                + '<h5 class="fw-bold mb-3" style="font-size:15px"><i class="bi bi-bookmark-plus text-success"></i> Form Peminjaman</h5>'
                + '<form method="POST" action="{{ route("admin.scanner.pinjam") }}" onsubmit="return validateAnggota()">'
                + '@csrf'
                + '<input type="hidden" name="buku_id" value="' + b.id + '">'
                + '<input type="hidden" name="anggota_id" id="anggota-id-hidden" value="">'
                + '<div class="mb-3">'
                + '<label class="form-label fw-semibold" style="font-size:13px">Pilih Anggota</label>'
                + '<div class="anggota-wrap" id="anggota-wrap">'
                + '<input type="text" class="anggota-input" id="anggota-search" placeholder="Ketik untuk cari, lalu klik nama..." autocomplete="off">'
                + '<button type="button" class="anggota-clear" id="anggota-clear" onclick="clearAnggota()">&times;</button>'
                + '<div class="anggota-dropdown" id="anggota-dropdown">'
                + itemHtml
                + '<div class="anggota-empty" id="anggota-empty" style="display:none">Anggota tidak ditemukan</div>'
                + '</div></div>'
                + '</div>'
                + '<div class="mb-3">'
                + '<label class="form-label fw-semibold" style="font-size:13px">Tanggal Pinjam</label>'
                + '<input type="date" name="tanggal_pinjam" class="form-input" required value="' + today + '">'
                + '</div>'
                + '<div class="mb-3">'
                + '<label class="form-label fw-semibold" style="font-size:13px">Tanggal Kembali</label>'
                + '<input type="date" name="tanggal_kembali" class="form-input" required value="' + nextWeek + '">'
                + '</div>'
                + '<div class="mb-3">'
                + '<label class="form-label fw-semibold" style="font-size:13px">Catatan <span class="text-muted fw-normal">(opsional)</span></label>'
                + '<input type="text" name="catatan" class="form-input" placeholder="Contoh: Sudah diperiksa kondisinya">'
                + '</div>'
                + '<button type="submit" class="btn-aksi btn-pinjam" onclick="return confirm(\'Pinjamkan buku ini?\')"><i class="bi bi-check-circle"></i> Pinjamkan Buku</button>'
                + '</form>'
                + '<button class="btn-scan-lain" onclick="resetScanner()"><i class="bi bi-qr-code-scan"></i> Scan Buku Lain</button>'
                + '</div>';
        }

        area.innerHTML = '<div class="hasil-card p-4">'
            + '<div class="d-flex gap-3 align-items-start">'
            + '<div class="flex-shrink-0">' + sampulHtml + '</div>'
            + '<div class="flex-grow-1" style="min-width:0">'
            + '<h5 class="fw-bold mb-1" style="font-size:16px">' + esc(b.judul) + '</h5>'
            + '<p class="text-muted mb-0" style="font-size:13px">' + esc(b.pengarang) + '</p>'
            + '</div>'
            + '</div>'
            + '<div class="mt-3">' + infoHtml + '</div>'
            + aksiHtml
            + '</div>';

        area.style.display = 'block';
    }

    window.pilihAnggota = function(el) {
        var id = el.getAttribute('data-id');
        var nama = el.getAttribute('data-nama');
        document.getElementById('anggota-id-hidden').value = id;
        var inp = document.getElementById('anggota-search');
        inp.value = nama;
        inp.classList.add('has-value', 'selected');
        document.getElementById('anggota-clear').classList.add('show');
        document.getElementById('anggota-dropdown').classList.remove('show');
    };

    window.clearAnggota = function() {
        document.getElementById('anggota-id-hidden').value = '';
        var inp = document.getElementById('anggota-search');
        inp.value = '';
        inp.classList.remove('has-value', 'selected');
        inp.focus();
        document.getElementById('anggota-clear').classList.remove('show');
        var items = document.querySelectorAll('#anggota-dropdown .anggota-item');
        for (var i = 0; i < items.length; i++) items[i].style.display = '';
        document.getElementById('anggota-empty').style.display = 'none';
    };

    window.validateAnggota = function() {
        if (!document.getElementById('anggota-id-hidden').value) {
            alert('Pilih anggota terlebih dahulu.');
            return false;
        }
        return true;
    };

    document.addEventListener('click', function(e) {
        var wrap = document.getElementById('anggota-wrap');
        if (!wrap) return;
        if (!wrap.contains(e.target)) {
            document.getElementById('anggota-dropdown').classList.remove('show');
        }
    });

    document.addEventListener('input', function(e) {
        if (e.target && e.target.id === 'anggota-search') {
            var q = e.target.value.toLowerCase().trim();
            var items = document.querySelectorAll('#anggota-dropdown .anggota-item');
            var any = false;
            for (var i = 0; i < items.length; i++) {
                var ds = (items[i].getAttribute('data-nama') || '').toLowerCase();
                var show = !q || ds.includes(q);
                items[i].style.display = show ? '' : 'none';
                if (show) any = true;
            }
            document.getElementById('anggota-empty').style.display = any ? 'none' : 'block';
            document.getElementById('anggota-dropdown').classList.add('show');
            if (document.getElementById('anggota-id-hidden').value) {
                document.getElementById('anggota-id-hidden').value = '';
                document.getElementById('anggota-search').classList.remove('has-value', 'selected');
                document.getElementById('anggota-clear').classList.remove('show');
            }
        }
    });

    document.addEventListener('focus', function(e) {
        if (e.target && e.target.id === 'anggota-search') {
            document.getElementById('anggota-dropdown').classList.add('show');
        }
    }, true);

    window.resetScanner = function() {
        bukuData = null;
        peminjamanData = null;
        anggotaList = [];
        dataGlobal = null;
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

    function showAlert(type, msg) {
        var box = document.getElementById('alert-box');
        box.className = type + ' show';
        box.textContent = msg;
    }

    function hideAlert() {
        document.getElementById('alert-box').className = '';
    }

    function fmtDate(str) {
        if (!str) return '-';
        var d = new Date(str);
        var bln = ['Januari','Februari','Maret','April','Mei','Juni','Juli','Agustus','September','Oktober','November','Desember'];
        return d.getDate() + ' ' + bln[d.getMonth()] + ' ' + d.getFullYear();
    }

    function esc(s) {
        if (!s) return '';
        var d = document.createElement('div');
        d.textContent = s;
        return d.innerHTML;
    }
})();
</script>
@endsection
