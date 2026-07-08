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

    .scan-hasil-card { background: white; border-radius: 16px; overflow: visible; box-shadow: 0 4px 20px rgba(0,0,0,0.08); animation: fadeUp 0.3s ease; position: relative; z-index: 1; }
    @keyframes fadeUp { from { opacity: 0; transform: translateY(10px); } to { opacity: 1; transform: translateY(0); } }

    .scan-btn-lain { width: 100%; padding: 11px; background: #f3f4f6; color: #555; border: none; border-radius: 10px; font-size: 14px; font-weight: 600; cursor: pointer; margin-top: 10px; transition: background 0.2s; }
    .scan-btn-lain:hover { background: #e5e7eb; }

    .scan-section { max-width: 500px; margin: 0 auto; }

    @media (max-width: 576px) {
        .scan-section { padding: 0 12px; }
        .scan-hasil-card { padding: 16px !important; overflow: visible !important; }
        .scan-header-card { flex-direction: column; align-items: center !important; text-align: center; gap: 12px !important; }
        .scan-header-card .flex-shrink-0 { margin-bottom: 0 !important; }
        .scan-header-card .flex-grow-1 { width: 100% !important; }
        .scan-sampul, .scan-sampul-ph { width: 80px !important; height: 110px !important; }
        .scan-sampul-ph { font-size: 32px !important; }
        .scan-judul { font-size: 15px !important; text-align: center; }
        .scan-pengarang { text-align: center !important; }
        .scan-info-grid { gap: 8px !important; }
        .scan-info-item { padding: 10px !important; }
        .scan-aksi-form .mb-3 { margin-bottom: 12px !important; }
        .scan-form-input { font-size: 14px !important; padding: 12px !important; }
        .scan-aksi-btn { padding: 14px !important; font-size: 15px !important; }
        .scan-btn-lain { padding: 12px !important; font-size: 13px !important; }
        .scan-eksemplar-badge { font-size: 10px !important; }
    }

    .scan-header-card { display: flex; gap: 16px; align-items: flex-start; }
    .scan-judul { font-size: 18px; line-height: 1.3; }
    .scan-pengarang { font-size: 13px; }
    .scan-info-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 12px; margin-top: 16px; }
    .scan-info-item { background: #f8f9fa; border-radius: 8px; padding: 12px; }
    .scan-info-label { font-size: 11px; color: #888; text-transform: uppercase; letter-spacing: 0.5px; margin-bottom: 2px; }
    .scan-info-value { font-size: 13px; font-weight: 600; color: #333; }
    .scan-eksemplar-badge { display: inline-block; padding: 4px 10px; border-radius: 20px; font-size: 11px; font-weight: 600; background: #e8f5e9; color: #1a6e35; margin-top: 8px; font-family: monospace; }
    .scan-aksi-section { margin-top: 16px; padding-top: 16px; border-top: 1px solid #eee; }
    .scan-aksi-section h5 { font-size: 15px; margin-bottom: 12px; }
    .scan-anggota-avatar { width: 36px; height: 36px; border-radius: 50%; background: #1a6e35; color: white; display: flex; align-items: center; justify-content: center; font-size: 13px; font-weight: 700; flex-shrink: 0; }
    .anggota-item { padding: 10px 12px; font-size: 13px; cursor: pointer; border-bottom: 1px solid #f3f4f6; display: flex; align-items: center; gap: 10px; transition: background 0.15s; }
    .anggota-item:hover { background: #f0fdf4; }
    .scan-badge-stok { display: inline-flex; align-items: center; gap: 4px; }

    .scan-anggota-wrap { position: relative; }
    .scan-anggota-input-wrap { position: relative; }
    .scan-anggota-input {
        width: 100%;
        padding: 10px 36px 10px 12px;
        border: 2px solid #e5e7eb;
        border-radius: 8px;
        font-size: 14px;
        outline: none;
        transition: border-color 0.2s;
        background: #fff;
    }
    .scan-anggota-input:focus { border-color: #27ae60; }
    .scan-anggota-input.selected { border-color: #27ae60; font-weight: 600; }
    .scan-anggota-clear {
        position: absolute;
        right: 10px;
        top: 50%;
        transform: translateY(-50%);
        background: none;
        border: none;
        color: #999;
        cursor: pointer;
        font-size: 18px;
        padding: 4px;
        line-height: 1;
        display: none;
    }
    .scan-anggota-clear:hover { color: #666; }
    .scan-anggota-dropdown {
        position: absolute;
        top: calc(100% + 4px);
        left: 0;
        right: 0;
        max-height: 280px;
        overflow-y: auto;
        background: #fff;
        border: 1px solid #e5e7eb;
        border-radius: 10px;
        box-shadow: 0 8px 24px rgba(0,0,0,0.12);
        z-index: 1000;
        display: none;
        scrollbar-width: thin;
        scrollbar-color: #ccc #f9fafb;
    }
    .scan-anggota-dropdown::-webkit-scrollbar { width: 6px; }
    .scan-anggota-dropdown::-webkit-scrollbar-track { background: #f9fafb; border-radius: 3px; }
    .scan-anggota-dropdown::-webkit-scrollbar-thumb { background: #ccc; border-radius: 3px; }
    .scan-anggota-dropdown::-webkit-scrollbar-thumb:hover { background: #aaa; }
    .scan-anggota-dropdown.show { display: block; }
    .scan-anggota-item {
        padding: 12px 14px;
        cursor: pointer;
        border-bottom: 1px solid #f3f4f6;
        display: flex;
        align-items: center;
        gap: 12px;
        transition: background 0.15s ease;
    }
    .scan-anggota-item:last-child { border-bottom: none; }
    .scan-anggota-item:hover, .scan-anggota-item.active { background: #f0fdf4; }
    .scan-anggota-item.active { outline: 2px solid #27ae60; outline-offset: -2px; }
    .scan-anggota-avatar {
        width: 40px;
        height: 40px;
        border-radius: 50%;
        background: linear-gradient(135deg, #1a6e35, #27ae60);
        color: white;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 14px;
        font-weight: 700;
        flex-shrink: 0;
    }
    .scan-anggota-info { flex: 1; min-width: 0; }
    .scan-anggota-nama { font-weight: 600; color: #1a1a1a; font-size: 14px; }
    .scan-anggota-meta { font-size: 12px; color: #888; margin-top: 2px; }
    .scan-anggota-empty {
        padding: 20px 14px;
        text-align: center;
        color: #999;
        font-size: 13px;
    }

    .scan-loading {
        text-align: center;
        padding: 20px;
        color: #666;
        font-size: 14px;
    }
    .scan-loading .spin {
        display: inline-block;
        animation: spin 1s linear infinite;
        margin-right: 8px;
    }
    @keyframes spin { from { transform: rotate(0deg); } to { transform: rotate(360deg); } }

    .scan-status-info {
        padding: 12px 16px;
        border-radius: 10px;
        font-size: 14px;
        font-weight: 500;
        margin-bottom: 16px;
        display: flex;
        align-items: center;
        gap: 10px;
    }
    .scan-status-success { background: #f0fdf4; color: #166534; border: 1px solid #bbf7d0; }
    .scan-status-warning { background: #fffbeb; color: #92400e; border: 1px solid #fde68a; }
    .scan-status-info-blue { background: #eff6ff; color: #1e40af; border: 1px solid #bfdbfe; }

    @media (max-width: 576px) {
        .scan-aksi-section { padding-top: 12px !important; }
        #reader { border-radius: 8px !important; }
        .scan-mode-wrap { margin-bottom: 12px !important; }
        .scan-anggota-dropdown {
            position: fixed !important;
            top: auto !important;
            bottom: 0 !important;
            left: 0 !important;
            right: 0 !important;
            max-height: 60vh;
            border-radius: 16px 16px 0 0;
            box-shadow: 0 -4px 24px rgba(0,0,0,0.15);
        }
        .scan-anggota-dropdown::before {
            content: '';
            display: block;
            width: 40px;
            height: 4px;
            background: #ddd;
            border-radius: 2px;
            margin: 10px auto;
        }
    }
</style>

<div class="scan-section">

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
    var CEK_PEMINJAMAN_URL = '{{ route("admin.scanner.cek-peminjaman") }}';
    var PINJAM_URL = '{{ route("admin.scanner.pinjam") }}';
    var KEMBALI_URL = '{{ route("admin.scanner.kembali") }}';

    var mode = 'pinjam';
    var bukuData = null;
    var anggotaList = [];
    var dataGlobal = null;
    var scanner = null;
    var scannerAktif = false;
    var sedangMemproses = false;
    var lastScanned = null;
    var selectedAnggotaId = null;

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
                    anggotaList = data.anggota || [];
                    dataGlobal = data;
                    selectedAnggotaId = null;
                    showAlert('success', 'Buku ditemukan: ' + data.buku.judul);
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
        var stokHabis = b.stok < 1;

        var sampulHtml = b.sampul
            ? '<img src="' + b.sampul + '" class="scan-sampul" alt="">'
            : '<div class="scan-sampul-ph">📚</div>';

        var stokBadge = stokHabis
            ? '<span class="scan-badge-stok habis">✕ Stok habis</span>'
            : '<span class="scan-badge-stok ada">✓ Tersedia (' + b.stok + ')</span>';

        var itemHtml = '';
        for (var i = 0; i < anggotaList.length; i++) {
            var a = anggotaList[i];
            var initials = a.nama.substring(0, 2).toUpperCase();
            var nisLabel = a.nis ? 'NIS: ' + a.nis : '';
            var kelasLabel = a.kelas ? ' • ' + a.kelas : '';
            itemHtml += '<div class="scan-anggota-item" data-id="' + a.id + '" data-nama="' + esc(a.nama) + '" data-index="' + i + '" role="option">'
                + '<div class="scan-anggota-avatar">' + esc(initials) + '</div>'
                + '<div class="scan-anggota-info">'
                + '<div class="scan-anggota-nama">' + esc(a.nama) + '</div>'
                + '<div class="scan-anggota-meta">' + esc(nisLabel) + esc(kelasLabel) + '</div>'
                + '</div></div>';
        }

        var today = new Date().toISOString().split('T')[0];
        var nextWeek = new Date(Date.now() + 7 * 86400000).toISOString().split('T')[0];

        var aksiHtml = '<div class="scan-aksi-section scan-aksi-form" id="scan-form-section">'
            + '<h5 class="fw-bold mb-3"><i class="bi bi-person-plus text-success"></i> Pilih Anggota</h5>'
            + '<div class="mb-3">'
            + '<div class="scan-anggota-wrap" id="anggota-wrap">'
            + '<div class="scan-anggota-input-wrap">'
            + '<input type="text" class="scan-anggota-input" id="anggota-search" placeholder="Ketik nama anggota..." autocomplete="off" aria-autocomplete="list" aria-haspopup="listbox" aria-expanded="false">'
            + '<button type="button" class="scan-anggota-clear" id="anggota-clear" aria-label="Hapus pilihan">&times;</button>'
            + '</div>'
            + '<div class="scan-anggota-dropdown" id="anggota-dropdown" role="listbox">'
            + itemHtml
            + '<div class="scan-anggota-empty" id="anggota-empty">Tidak ada anggota ditemukan</div>'
            + '</div></div>'
            + '</div>'
            + '<div id="scan-aksi-container"></div>'
            + '<button class="scan-btn-lain" onclick="resetScanner()"><i class="bi bi-qr-code-scan"></i> Scan Buku Lain</button>'
            + '</div>';

        area.innerHTML = '<div class="scan-hasil-card p-4">'
            + '<div class="scan-header-card">'
            + '<div class="flex-shrink-0 mb-2">' + sampulHtml + '</div>'
            + '<div class="flex-grow-1" style="min-width:0">'
            + '<h5 class="fw-bold mb-1 scan-judul">' + esc(b.judul) + '</h5>'
            + '<p class="text-muted mb-0 scan-pengarang" style="font-size:13px">' + esc(b.pengarang) + '</p>'
            + '</div>'
            + '</div>'
            + '<div class="scan-info-grid">'
            + '<div class="scan-info-item"><div class="scan-info-label">Kode Buku</div><div class="scan-info-value">' + esc(b.kode_buku) + '</div></div>'
            + '<div class="scan-info-item"><div class="scan-info-label">ISBN</div><div class="scan-info-value">' + esc(b.isbn || '-') + '</div></div>'
            + '<div class="scan-info-item"><div class="scan-info-label">Lokasi Rak</div><div class="scan-info-value">📍 ' + esc(b.lokasi || '-') + '</div></div>'
            + '<div class="scan-info-item"><div class="scan-info-label">Status</div><div class="scan-info-value">' + stokBadge + '</div></div>'
            + '</div>'
            + aksiHtml
            + '</div>';

        area.style.display = 'block';
    }

    // ── Pilih anggota & cek peminjaman ──
    window.pilihAnggota = function(el) {
        var id = el.getAttribute('data-id');
        var nama = el.getAttribute('data-nama');
        selectedAnggotaId = id;

        var inp = document.getElementById('anggota-search');
        inp.value = nama;
        inp.classList.add('selected');
        document.getElementById('anggota-clear').style.display = 'block';
        document.getElementById('anggota-dropdown').classList.remove('show');
        inp.setAttribute('aria-expanded', 'false');
        activeIndex = -1;
        document.querySelectorAll('.scan-anggota-item').forEach(function(item) { item.classList.remove('active'); });

        var aksiContainer = document.getElementById('scan-aksi-container');
        aksiContainer.innerHTML = '<div class="scan-loading"><i class="bi bi-arrow-repeat spin"></i> Memeriksa transaksi...</div>';

        fetch(CEK_PEMINJAMAN_URL, {
            method: 'POST',
            headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': CSRF, 'Accept': 'application/json' },
            body: JSON.stringify({ buku_id: bukuData.id, anggota_id: id }),
        })
        .then(function(res) { return res.json(); })
        .then(function(data) {
            var html = '';
            if (data.status === 'tidak_memiliki') {
                if (data.stok < 1) {
                    html = '<div class="scan-status-info scan-status-warning">'
                        + '<i class="bi bi-info-circle"></i> Semua eksemplar buku sedang dipinjam.'
                        + '</div>'
                        + '<button type="button" class="scan-aksi-btn scan-btn-pinjam" disabled>'
                        + '<i class="bi bi-book-x"></i> Pinjamkan Buku'
                        + '</button>';
                } else {
                    var nextWeekPinjam = new Date(Date.now() + 7 * 86400000).toISOString().split('T')[0];
                    html = '<div class="scan-status-info scan-status-success">'
                        + '<i class="bi bi-check-circle"></i> Belum meminjam buku ini.'
                        + '</div>'
                        + '<form method="POST" action="' + PINJAM_URL + '">'
                        + '<input type="hidden" name="_token" value="' + CSRF + '">'
                        + '<input type="hidden" name="buku_id" value="' + bukuData.id + '">'
                        + '<input type="hidden" name="anggota_id" value="' + id + '">'
                        + '<div class="mb-3 mt-3">'
                        + '<label class="form-label fw-semibold" style="font-size:13px">Tanggal Pinjam</label>'
                        + '<input type="date" name="tanggal_pinjam" class="scan-form-input" required value="' + today() + '">'
                        + '</div>'
                        + '<div class="mb-3">'
                        + '<label class="form-label fw-semibold" style="font-size:13px">Tanggal Kembali</label>'
                        + '<input type="date" name="tanggal_kembali" class="scan-form-input" required value="' + nextWeekPinjam + '">'
                        + '</div>'
                        + '<div class="mb-3">'
                        + '<label class="form-label fw-semibold" style="font-size:13px">Jumlah Buku</label>'
                        + '<input type="number" name="jumlah" class="scan-form-input" required min="1" max="' + data.stok + '" value="1">'
                        + '<small style="font-size:11px;color:#888">Stok tersedia: <strong>' + data.stok + '</strong> eksemplar</small>'
                        + '</div>'
                        + '<div class="mb-3">'
                        + '<label class="form-label fw-semibold" style="font-size:13px">Catatan <span class="text-muted fw-normal">(opsional)</span></label>'
                        + '<input type="text" name="catatan" class="scan-form-input" placeholder="Contoh: Sudah diperiksa kondisinya">'
                        + '</div>'
                        + '<button type="submit" class="scan-aksi-btn scan-btn-pinjam" onclick="return confirm(\'Pinjamkan buku ini?\')">'
                        + '<i class="bi bi-check-circle"></i> Pinjamkan Buku'
                        + '</button>'
                        + '</form>';
                }
            } else if (data.status === 'sedang_memiliki') {
                var pList = Array.isArray(data.peminjaman) ? data.peminjaman : [data.peminjaman];
                var dipinjamList = pList.filter(function(p) { return p && p.status === 'dipinjam'; });

                if (dipinjamList.length > 0) {
                    var checkboxes = '';
                    dipinjamList.forEach(function(p, idx) {
                        var eksKode = p.eksemplar_kode ? p.eksemplar_kode : '-';
                        checkboxes += ''
                            + '<label style="display:flex;align-items:center;gap:10px;padding:10px 12px;background:#eff6ff;border-radius:8px;margin-bottom:8px;cursor:pointer">'
                            + '<input type="checkbox" class="admin-pinjam-check" value="' + p.id + '" style="width:18px;height:18px;accent-color:#2563eb">'
                            + '<div style="flex:1">'
                            + '<div style="font-size:13px;font-weight:600;color:#1e40af">📖 Eksemplar: <strong>' + eksKode + '</strong></div>'
                            + '<div style="font-size:11px;color:#666">Pinjam: ' + fmtDate(p.tanggal_pinjam) + ' | Kembali: ' + fmtDate(p.tanggal_kembali) + '</div>'
                            + '</div>'
                            + '</label>';
                    });

                    html = '<div class="scan-status-info scan-status-info-blue">'
                        + '<i class="bi bi-clock-history"></i> Sedang meminjam ' + dipinjamList.length + ' eksemplar buku ini.'
                        + '</div>'
                        + '<p style="font-size:12px;color:#666;margin:10px 0 8px">Centang buku yang ingin dikembalikan:</p>'
                        + '<div id="adminPinjamListContainer" style="margin-bottom:10px">' + checkboxes + '</div>'
                        + '<label style="display:flex;align-items:center;gap:10px;padding:8px 12px;cursor:pointer;font-size:13px;font-weight:600;color:#2563eb;margin-bottom:12px">'
                        + '<input type="checkbox" id="adminSelectAll" onchange="adminToggleAllPinjam(this)">'
                        + 'Pilih Semua (' + dipinjamList.length + ')'
                        + '</label>'
                        + '<form method="POST" action="' + KEMBALI_URL + '" id="adminKembaliForm">'
                        + '<input type="hidden" name="_token" value="' + CSRF + '">'
                        + '<input type="hidden" name="buku_id" value="' + bukuData.id + '">'
                        + '<input type="hidden" name="anggota_id" value="' + id + '">'
                        + '<div id="adminKembaliIdsContainer"></div>'
                        + '<button type="submit" class="scan-aksi-btn scan-btn-kembali" id="adminBtnKembali" onclick="return adminSubmitKembali(event)">'
                        + '<i class="bi bi-arrow-return-left"></i> Kembalikan (' + dipinjamList.length + ')'
                        + '</button>'
                        + '</form>';
                } else {
                    html = '<div class="scan-status-info scan-status-info-blue">'
                        + '<i class="bi bi-clock-history"></i> Sedang menunggu pengembalian.</div>';
                }
            } else {
                html = '<div class="scan-info-box amber text-center"><i class="bi bi-exclamation-triangle"></i> ' + esc(data.pesan || 'Terjadi kesalahan.') + '</div>';
            }
            aksiContainer.innerHTML = html;
        })
        .catch(function(err) {
            console.error('Fetch error:', err);
            aksiContainer.innerHTML = '<div class="scan-info-box" style="background:#fef2f2;color:#991b1b"><i class="bi bi-x-circle"></i> Gagal memeriksa transaksi.</div>';
        });
    };

    function today() {
        return new Date().toISOString().split('T')[0];
    }

    window.clearAnggota = function() {
        selectedAnggotaId = null;
        var inp = document.getElementById('anggota-search');
        inp.value = '';
        inp.classList.remove('selected');
        inp.focus();
        document.getElementById('anggota-clear').style.display = 'none';
        var items = document.querySelectorAll('.scan-anggota-item');
        for (var i = 0; i < items.length; i++) items[i].style.display = '';
        document.getElementById('anggota-empty').style.display = 'none';
        document.getElementById('scan-aksi-container').innerHTML = '';
        activeIndex = -1;
    };

    // ── Anggota search events ──
    var activeIndex = -1;

    document.addEventListener('click', function(e) {
        var wrap = document.getElementById('anggota-wrap');
        if (!wrap) return;
        if (!wrap.contains(e.target)) {
            document.getElementById('anggota-dropdown').classList.remove('show');
            var inp = document.getElementById('anggota-search');
            if (inp) inp.setAttribute('aria-expanded', 'false');
            activeIndex = -1;
        }
    });

    document.addEventListener('input', function(e) {
        if (e.target && e.target.id === 'anggota-search') {
            var q = e.target.value.toLowerCase().trim();
            var items = document.querySelectorAll('.scan-anggota-item');
            var any = false;
            for (var i = 0; i < items.length; i++) {
                var ds = (items[i].getAttribute('data-nama') || '').toLowerCase();
                var show = !q || ds.includes(q);
                items[i].style.display = show ? '' : 'none';
                if (show) any = true;
            }
            document.getElementById('anggota-empty').style.display = any ? 'none' : 'block';
            document.getElementById('anggota-dropdown').classList.add('show');
            e.target.setAttribute('aria-expanded', 'true');
            if (selectedAnggotaId) {
                selectedAnggotaId = null;
                var inp = document.getElementById('anggota-search');
                inp.classList.remove('selected');
                document.getElementById('anggota-clear').style.display = 'none';
                document.getElementById('scan-aksi-container').innerHTML = '';
            }
            activeIndex = -1;
        }
    });

    document.addEventListener('keydown', function(e) {
        var dropdown = document.getElementById('anggota-dropdown');
        var input = document.getElementById('anggota-search');
        if (!dropdown || !dropdown.classList.contains('show')) return;
        
        var items = Array.from(document.querySelectorAll('.scan-anggota-item')).filter(function(item) {
            return item.style.display !== 'none';
        });

        if (e.key === 'ArrowDown') {
            e.preventDefault();
            if (items.length === 0) return;
            if (activeIndex < items.length - 1) activeIndex++;
            items.forEach(function(item) { item.classList.remove('active'); });
            items[activeIndex].classList.add('active');
            items[activeIndex].scrollIntoView({ block: 'nearest' });
        } else if (e.key === 'ArrowUp') {
            e.preventDefault();
            if (items.length === 0) return;
            if (activeIndex > 0) activeIndex--;
            items.forEach(function(item) { item.classList.remove('active'); });
            items[activeIndex].classList.add('active');
            items[activeIndex].scrollIntoView({ block: 'nearest' });
        } else if (e.key === 'Enter') {
            e.preventDefault();
            if (activeIndex >= 0 && items[activeIndex]) {
                pilihAnggota(items[activeIndex]);
            }
        } else if (e.key === 'Escape') {
            dropdown.classList.remove('show');
            if (input) input.setAttribute('aria-expanded', 'false');
            activeIndex = -1;
        }
    });

    // ── Reset ──
    function resetCard() {
        bukuData = null;
        anggotaList = [];
        dataGlobal = null;
        lastScanned = null;
        sedangMemproses = false;
        selectedAnggotaId = null;
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

    window.adminToggleAllPinjam = function(el) {
        document.querySelectorAll('.admin-pinjam-check').forEach(function(c) { c.checked = el.checked; });
    };

    window.adminSubmitKembali = function(e) {
        var checks = document.querySelectorAll('.admin-pinjam-check:checked');
        if (checks.length === 0) {
            e.preventDefault();
            alert('Pilih setidaknya satu buku untuk dikembalikan.');
            return false;
        }
        var idsContainer = document.getElementById('adminKembaliIdsContainer');
        idsContainer.innerHTML = '';
        checks.forEach(function(c) {
            var inp = document.createElement('input');
            inp.type = 'hidden';
            inp.name = 'peminjaman_ids[]';
            inp.value = c.value;
            idsContainer.appendChild(inp);
        });
        return true;
    };
})();
</script>
@endsection
