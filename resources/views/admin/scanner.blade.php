@extends('layouts.admin')

@section('title', 'Scan Barcode Buku')

@section('content')
<style>
    #reader { width: 100%; max-width: 500px; border-radius: 12px; overflow: hidden; border: 2px dashed #d1d5db; background: #f9fafb; margin: 0 auto; }
    #reader video { width: 100% !important; border-radius: 12px; }
    #reader__dashboard_section_swaplink { display: none !important; }
    #reader__dashboard_section_csr { display: none !important; }
    #reader__camera_permission_button { background: #1a6e35 !important; color: white !important; border-radius: 8px !important; padding: 8px 16px !important; border: none !important; font-size: 14px !important; cursor: pointer !important; }

    .scan-sampul { width: 70px; height: 95px; object-fit: cover; border-radius: 8px; border: 1px solid #eee; }
    .scan-sampul-ph { width: 70px; height: 95px; background: #f3f4f6; border-radius: 8px; display: flex; align-items: center; justify-content: center; font-size: 28px; }

    #alert-box { border-radius: 10px; padding: 12px 16px; font-size: 14px; font-weight: 500; margin-bottom: 16px; display: none; }
    #alert-box.show { display: block; }
    #alert-box.success { background: #f0fdf4; border: 1px solid #bbf7d0; color: #166534; }
    #alert-box.error { background: #fef2f2; border: 1px solid #fecaca; color: #991b1b; }
    #alert-box.info { background: #eff6ff; border: 1px solid #bfdbfe; color: #1e40af; }

    .scan-badge-stok { display: inline-block; padding: 3px 10px; border-radius: 20px; font-size: 11px; font-weight: 600; }
    .scan-badge-stok.ada { background: #d4edda; color: #1a6e35; }
    .scan-badge-stok.habis { background: #f8d7da; color: #721c24; }

    .scan-mode-wrap { display: flex; gap: 0; background: #f3f4f6; border-radius: 10px; padding: 4px; margin-bottom: 16px; }
    .scan-mode-btn { flex: 1; padding: 10px; border: none; border-radius: 8px; font-size: 13px; font-weight: 600; cursor: pointer; transition: all 0.2s; background: transparent; color: #999; text-align: center; }
    .scan-mode-btn.active { background: white; box-shadow: 0 2px 8px rgba(0,0,0,0.08); color: #1a6e35; }

    .scan-aksi-btn { width: 100%; padding: 13px; border: none; border-radius: 10px; font-size: 15px; font-weight: 700; cursor: pointer; transition: opacity 0.2s; color: white; }
    .scan-aksi-btn:hover { opacity: 0.9; }
    .scan-aksi-btn:disabled { background: #ccc !important; cursor: not-allowed; }
    .scan-btn-pinjam { background: linear-gradient(135deg, #1a6e35, #27ae60); }
    .scan-btn-kembali { background: linear-gradient(135deg, #2563eb, #3b82f6); }

    .scan-form-input { width: 100%; padding: 10px 12px; border: 2px solid #e5e7eb; border-radius: 8px; font-size: 14px; outline: none; transition: border 0.2s; }
    .scan-form-input:focus { border-color: #27ae60; }

    .scan-info-box { border-radius: 8px; padding: 10px 12px; font-size: 13px; line-height: 1.7; }
    .scan-info-box.amber { background: #fffbeb; color: #92400e; }

    .scan-hasil-card { background: white; border-radius: 16px; overflow: hidden; box-shadow: 0 4px 20px rgba(0,0,0,0.08); animation: fadeUp 0.3s ease; }
    @keyframes fadeUp { from { opacity: 0; transform: translateY(10px); } to { opacity: 1; transform: translateY(0); } }

    .scan-btn-lain { width: 100%; padding: 11px; background: #f3f4f6; color: #555; border: none; border-radius: 10px; font-size: 14px; font-weight: 600; cursor: pointer; margin-top: 10px; transition: background 0.2s; }
    .scan-btn-lain:hover { background: #e5e7eb; }

    .scan-section { max-width: 500px; margin: 0 auto; }
</style>

<div class="scan-section">

    <div class="text-center mb-4">
        <div style="font-size:36px;margin-bottom:8px">📷</div>
        <p style="font-size:13px;color:#888">Arahkan kamera ke QR Code pada buku, atau input kode manual</p>
    </div>

    <div id="alert-box"></div>

    {{-- Mode toggle --}}
    <div class="scan-mode-wrap">
        <button id="btn-mode-pinjam" class="scan-mode-btn active" onclick="setMode('pinjam')">
            📥 Pinjam Buku
        </button>
        <button id="btn-mode-kembali" class="scan-mode-btn" onclick="setMode('kembali')">
            📤 Kembalikan Buku
        </button>
    </div>

    <div id="reader" class="mb-3"></div>

    <div class="text-center mb-3">
        <button onclick="document.getElementById('manual-box').classList.toggle('d-none')" style="background:none;border:none;color:#999;font-size:12px;text-decoration:underline;cursor:pointer">
            Input Kode Buku / ISBN manual
        </button>
    </div>
    <div id="manual-box" class="d-none mb-4">
        <div class="d-flex gap-2">
            <input id="kode-manual" type="text" class="scan-form-input" placeholder="Masukkan kode buku (BK0001) atau ISBN" onkeydown="if(event.key==='Enter'){event.preventDefault();prosesKode(this.value.trim());}">
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
    var CEK_URL = '{{ route("admin.scanner.cek") }}';
    var PINJAM_URL = '{{ route("admin.scanner.pinjam") }}';
    var KEMBALI_URL = '{{ route("admin.scanner.kembali") }}';

    var mode = 'pinjam';
    var bukuData = null;
    var peminjamanData = null;
    var anggotaList = [];
    var dataGlobal = null;
    var scanner = null;
    var scannerAktif = false;
    var sedangMemproses = false;
    var lastScanned = null;

    document.addEventListener('DOMContentLoaded', mulaiScanner);

    // ── Mode toggle ──
    window.setMode = function(m) {
        mode = m;
        var btnPinjam = document.getElementById('btn-mode-pinjam');
        var btnKembali = document.getElementById('btn-mode-kembali');
        if (m === 'pinjam') {
            btnPinjam.classList.add('active');
            btnPinjam.style.color = '#1a6e35';
            btnKembali.classList.remove('active');
            btnKembali.style.color = '#999';
        } else {
            btnKembali.classList.add('active');
            btnKembali.style.color = '#1a6e35';
            btnPinjam.classList.remove('active');
            btnPinjam.style.color = '#999';
        }
        resetCard();
    };

    // ── Start kamera ──
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
        resetCard();
        showAlert('info', 'Mencari buku...');
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
            ? '<img src="' + b.sampul + '" class="scan-sampul" alt="">'
            : '<div class="scan-sampul-ph">📚</div>';

        var stokBadge = stokHabis
            ? '<span class="scan-badge-stok habis">✕ Stok habis</span>'
            : '<span class="scan-badge-stok ada">✓ Tersedia (' + b.stok + ')</span>';

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

        if (mode === 'kembali') {
            // ── Mode kembali ──
            if (!p) {
                aksiHtml = '<div style="border-top:1px solid #eee;padding-top:16px;margin-top:16px">'
                    + '<div class="scan-info-box amber text-center"><i class="bi bi-info-circle"></i> Buku ini tidak sedang dipinjam.</div>'
                    + '<button class="scan-btn-lain" onclick="resetScanner()"><i class="bi bi-qr-code-scan"></i> Scan Buku Lain</button>'
                    + '</div>';
            } else if (p.status === 'menunggu_konfirmasi') {
                aksiHtml = '<div style="border-top:1px solid #eee;padding-top:16px;margin-top:16px">'
                    + '<div class="scan-info-box amber text-center"><i class="bi bi-hourglass-split"></i> Peminjaman sedang menunggu konfirmasi admin.</div>'
                    + '<button class="scan-btn-lain" onclick="resetScanner()"><i class="bi bi-qr-code-scan"></i> Scan Buku Lain</button>'
                    + '</div>';
            } else if (p.status === 'menunggu_pengembalian') {
                aksiHtml = '<div style="border-top:1px solid #eee;padding-top:16px;margin-top:16px">'
                    + '<div class="scan-info-box amber text-center"><i class="bi bi-hourglass-split"></i> Permintaan pengembalian sudah dikirim, tunggu konfirmasi admin.</div>'
                    + '<button class="scan-btn-lain" onclick="resetScanner()"><i class="bi bi-qr-code-scan"></i> Scan Buku Lain</button>'
                    + '</div>';
            } else if (p.status === 'dipinjam') {
                aksiHtml = '<div style="border-top:1px solid #eee;padding-top:16px;margin-top:16px">'
                    + '<h5 class="fw-bold mb-3" style="font-size:15px"><i class="bi bi-arrow-counterclockwise text-primary"></i> Konfirmasi Pengembalian</h5>'
                    + '<div class="scan-info-box" style="background:#eff6ff;color:#1e40af;margin-bottom:12px">'
                    + '<p class="mb-1"><i class="bi bi-person"></i> Peminjam: <strong>' + esc(p.anggota_nama) + '</strong></p>'
                    + '<p class="mb-1"><i class="bi bi-calendar"></i> Pinjam: <strong>' + fmtDate(p.tanggal_pinjam) + '</strong></p>'
                    + '<p class="mb-0"><i class="bi bi-calendar-check"></i> Kembali: <strong>' + fmtDate(p.tanggal_kembali) + '</strong></p>'
                    + '</div>'
                    + '<form method="POST" action="' + KEMBALI_URL + '">'
                    + '<input type="hidden" name="_token" value="' + CSRF + '">'
                    + '<input type="hidden" name="buku_id" value="' + b.id + '">'
                    + '<button type="submit" class="scan-aksi-btn scan-btn-kembali" onclick="return confirm(\'Kembalikan buku ini?\')"><i class="bi bi-arrow-return-left"></i> Kembalikan Buku</button>'
                    + '</form>'
                    + '<button class="scan-btn-lain" onclick="resetScanner()"><i class="bi bi-qr-code-scan"></i> Scan Buku Lain</button>'
                    + '</div>';
            }
        } else {
            // ── Mode pinjam ──
            if (p) {
                aksiHtml = '<div style="border-top:1px solid #eee;padding-top:16px;margin-top:16px">'
                    + '<p style="font-size:14px;color:#d97706;text-align:center;font-weight:600;padding:8px 0"><i class="bi bi-exclamation-triangle"></i> Buku ini sudah dipinjam oleh: <strong>' + esc(p.anggota_nama) + '</strong></p>'
                    + '<button class="scan-btn-lain" onclick="resetScanner()"><i class="bi bi-qr-code-scan"></i> Scan Buku Lain</button>'
                    + '</div>';
            } else if (stokHabis) {
                aksiHtml = '<div style="border-top:1px solid #eee;padding-top:16px;margin-top:16px">'
                    + '<div class="scan-info-box" style="background:#fef2f2;color:#991b1b;text-align:center"><i class="bi bi-x-circle"></i> Stok buku habis. Tidak bisa dipinjamkan.</div>'
                    + '<button class="scan-btn-lain" onclick="resetScanner()"><i class="bi bi-qr-code-scan"></i> Scan Buku Lain</button>'
                    + '</div>';
            } else {
                var itemHtml = '';
                for (var i = 0; i < anggotaList.length; i++) {
                    var a = anggotaList[i];
                    var initials = a.nama.substring(0, 2).toUpperCase();
                    var nisLabel = a.nis ? 'NIS: ' + a.nis : '';
                    itemHtml += '<div class="anggota-item" data-id="' + a.id + '" data-nama="' + esc(a.nama) + '" onclick="pilihAnggota(this)" style="padding:10px 12px;font-size:13px;cursor:pointer;border-bottom:1px solid #f3f4f6;display:flex;align-items:center;gap:10px;transition:background 0.15s">'
                        + '<div style="width:32px;height:32px;border-radius:50%;background:#1a6e35;color:white;display:flex;align-items:center;justify-content:center;font-size:12px;font-weight:700;flex-shrink:0">' + esc(initials) + '</div>'
                        + '<div style="flex:1;min-width:0">'
                        + '<div style="font-weight:600;color:#222">' + esc(a.nama) + '</div>'
                        + (nisLabel ? '<div style="font-size:11px;color:#999">' + esc(nisLabel) + '</div>' : '')
                        + '</div></div>';
                }

                var today = new Date().toISOString().split('T')[0];
                var nextWeek = new Date(Date.now() + 7 * 86400000).toISOString().split('T')[0];

                aksiHtml = '<div style="border-top:1px solid #eee;padding-top:16px;margin-top:16px">'
                    + '<h5 class="fw-bold mb-3" style="font-size:15px"><i class="bi bi-bookmark-plus text-success"></i> Form Peminjaman</h5>'
                    + '<form method="POST" action="' + PINJAM_URL + '" onsubmit="return validateAnggota()">'
                    + '<input type="hidden" name="_token" value="' + CSRF + '">'
                    + '<input type="hidden" name="buku_id" value="' + b.id + '">'
                    + '<input type="hidden" name="anggota_id" id="anggota-id-hidden" value="">'
                    + '<div class="mb-3">'
                    + '<label class="form-label fw-semibold" style="font-size:13px">Pilih Anggota</label>'
                    + '<div style="position:relative" id="anggota-wrap">'
                    + '<input type="text" class="scan-form-input" id="anggota-search" placeholder="Ketik untuk cari, lalu klik nama..." autocomplete="off">'
                    + '<button type="button" id="anggota-clear" onclick="clearAnggota()" style="position:absolute;right:10px;top:50%;transform:translateY(-50%);background:none;border:none;color:#999;cursor:pointer;font-size:16px;padding:4px;display:none">&times;</button>'
                    + '<div id="anggota-dropdown" style="position:absolute;top:100%;left:0;right:0;max-height:220px;overflow-y:auto;background:white;border:2px solid #e5e7eb;border-top:none;border-radius:0 0 8px 8px;z-index:100;display:none;box-shadow:0 8px 16px rgba(0,0,0,0.1)">'
                    + itemHtml
                    + '<div id="anggota-empty" style="padding:14px;text-align:center;color:#999;font-size:13px;display:none">Anggota tidak ditemukan</div>'
                    + '</div></div>'
                    + '</div>'
                    + '<div class="mb-3">'
                    + '<label class="form-label fw-semibold" style="font-size:13px">Tanggal Pinjam</label>'
                    + '<input type="date" name="tanggal_pinjam" class="scan-form-input" required value="' + today + '">'
                    + '</div>'
                    + '<div class="mb-3">'
                    + '<label class="form-label fw-semibold" style="font-size:13px">Tanggal Kembali</label>'
                    + '<input type="date" name="tanggal_kembali" class="scan-form-input" required value="' + nextWeek + '">'
                    + '</div>'
                    + '<div class="mb-3">'
                    + '<label class="form-label fw-semibold" style="font-size:13px">Catatan <span class="text-muted fw-normal">(opsional)</span></label>'
                    + '<input type="text" name="catatan" class="scan-form-input" placeholder="Contoh: Sudah diperiksa kondisinya">'
                    + '</div>'
                    + '<button type="submit" class="scan-aksi-btn scan-btn-pinjam" onclick="return confirm(\'Pinjamkan buku ini?\')"><i class="bi bi-check-circle"></i> Pinjamkan Buku</button>'
                    + '</form>'
                    + '<button class="scan-btn-lain" onclick="resetScanner()"><i class="bi bi-qr-code-scan"></i> Scan Buku Lain</button>'
                    + '</div>';
            }
        }

        area.innerHTML = '<div class="scan-hasil-card p-4">'
            + '<div class="d-flex gap-3 align-items-start">'
            + '<div class="flex-shrink-0">' + sampulHtml + '</div>'
            + '<div class="flex-grow-1" style="min-width:0">'
            + '<h5 class="fw-bold mb-1" style="font-size:16px">' + esc(b.judul) + '</h5>'
            + '<p class="text-muted mb-0" style="font-size:13px">' + esc(b.pengarang) + '</p>'
            + eksemplarBadge
            + '</div>'
            + '</div>'
            + '<div class="mt-3">' + infoHtml + '</div>'
            + aksiHtml
            + '</div>';

        area.style.display = 'block';
    }

    // ── Anggota search ──
    window.pilihAnggota = function(el) {
        var id = el.getAttribute('data-id');
        var nama = el.getAttribute('data-nama');
        document.getElementById('anggota-id-hidden').value = id;
        var inp = document.getElementById('anggota-search');
        inp.value = nama;
        inp.style.borderColor = '#27ae60';
        inp.style.fontWeight = '600';
        document.getElementById('anggota-clear').style.display = 'block';
        document.getElementById('anggota-dropdown').style.display = 'none';
    };

    window.clearAnggota = function() {
        document.getElementById('anggota-id-hidden').value = '';
        var inp = document.getElementById('anggota-search');
        inp.value = '';
        inp.style.borderColor = '#e5e7eb';
        inp.style.fontWeight = 'normal';
        inp.focus();
        document.getElementById('anggota-clear').style.display = 'none';
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
            document.getElementById('anggota-dropdown').style.display = 'none';
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
            document.getElementById('anggota-dropdown').style.display = 'block';
            if (document.getElementById('anggota-id-hidden').value) {
                document.getElementById('anggota-id-hidden').value = '';
                var inp = document.getElementById('anggota-search');
                inp.style.borderColor = '#e5e7eb';
                inp.style.fontWeight = 'normal';
                document.getElementById('anggota-clear').style.display = 'none';
            }
        }
    });

    document.addEventListener('focus', function(e) {
        if (e.target && e.target.id === 'anggota-search') {
            document.getElementById('anggota-dropdown').style.display = 'block';
        }
    }, true);

    // ── Reset ──
    function resetCard() {
        bukuData = null;
        peminjamanData = null;
        anggotaList = [];
        dataGlobal = null;
        lastScanned = null;
        sedangMemproses = false;
        document.getElementById('hasil-area').style.display = 'none';
        document.getElementById('hasil-area').innerHTML = '';
        hideAlert();
    }

    window.resetScanner = function() {
        resetCard();
        hentikanScanner().then(function() {
            document.getElementById('reader').innerHTML = '';
            document.getElementById('reader').style.display = 'block';
            mulaiScanner();
        });
    };

    // ── Alerts ──
    function showAlert(type, msg) {
        var box = document.getElementById('alert-box');
        box.className = type + ' show';
        box.textContent = msg;
    }

    function hideAlert() {
        document.getElementById('alert-box').className = '';
    }

    // ── Helpers ──
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
