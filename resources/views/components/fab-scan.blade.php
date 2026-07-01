@auth
<meta name="csrf-token" content="{{ csrf_token() }}">
<style>
#fab-scan-btn{position:fixed;bottom:24px;right:24px;width:60px;height:60px;border-radius:50%;background:linear-gradient(135deg,#1a6e35,#27ae60);border:none;box-shadow:0 4px 15px rgba(26,110,53,0.4);cursor:pointer;display:flex;align-items:center;justify-content:center;z-index:10000;transition:all .3s}
#fab-scan-btn:hover{transform:scale(1.1);box-shadow:0 6px 20px rgba(26,110,53,.5)}
#fab-scan-btn i{font-size:24px;color:#fff}
.fab-tooltip{position:absolute;bottom:70px;right:0;background:#333;color:#fff;padding:6px 12px;border-radius:6px;font-size:12px;white-space:nowrap;opacity:0;visibility:hidden;transition:all .3s}
#fab-scan-btn:hover .fab-tooltip{opacity:1;visibility:visible}
.fab-modal{display:none;position:fixed;inset:0;background:rgba(0,0,0,.6);z-index:1000;align-items:center;justify-content:center}
.fab-modal.show{display:flex}
.fab-modal-box{background:#fff;border-radius:16px;padding:24px;width:90%;max-width:420px;max-height:90vh;overflow-y:auto}
.fab-modal-box h4{font-weight:700;color:#1a6e35;margin-bottom:4px}
.fab-modal-box .fab-subtitle{font-size:13px;color:#888;margin-bottom:20px}
.fab-modal-info{background:#f8f9fa;border-radius:10px;padding:14px;margin-bottom:20px}
.fab-modal-info p{font-size:13px;margin-bottom:6px;display:flex;gap:8px}
.fab-modal-info p:last-child{margin-bottom:0}
.fab-modal-info i{color:#1a6e35;width:16px}
.fab-cover{width:60px;height:85px;object-fit:cover;border-radius:6px;border:1px solid #eee}
.fab-btn-primary{width:100%;padding:12px;background:linear-gradient(135deg,#1a6e35,#27ae60);color:#fff;border:none;border-radius:10px;font-weight:600;font-size:15px;cursor:pointer}
.fab-btn-primary:hover{opacity:.9}
.fab-btn-secondary{width:100%;padding:10px;background:#f0f0f0;color:#333;border:none;border-radius:10px;font-weight:500;margin-top:10px;cursor:pointer}
.fab-scan-reader{width:100%;border-radius:12px;overflow:hidden;margin-bottom:16px}
.fab-scan-error{text-align:center;padding:20px;color:#e74c3c}
.fab-scan-error i{font-size:48px;margin-bottom:10px}
.fab-manual-input{display:flex;gap:8px}
.fab-manual-input input{flex:1;padding:10px 14px;border:1px solid #ddd;border-radius:8px;font-size:14px}
.fab-manual-input button{padding:10px 16px;background:#1a6e35;color:#fff;border:none;border-radius:8px;font-weight:600}
.fab-toast{position:fixed;top:80px;left:50%;transform:translateX(-50%);background:#fff;border-radius:12px;padding:14px 20px;box-shadow:0 4px 20px rgba(0,0,0,.15);z-index:2000;display:none;min-width:280px;text-align:center}
.fab-toast.show{display:block}
.fab-toast.success{border-left:4px solid #27ae60}
.fab-toast.error{border-left:4px solid #e74c3c}
.fab-label{color:#555}
.fab-date-input{background:#fff;color:#222}

body.dark-mode .fab-modal-box{background:#1e1e1e;color:#e0e0e0}
body.dark-mode .fab-modal-box h4{color:#4ade80}
body.dark-mode .fab-subtitle{color:#aaa}
body.dark-mode .fab-modal-info{background:#2a2a2a}
body.dark-mode .fab-modal-info p{color:#e0e0e0}
body.dark-mode .fab-modal-info i{color:#4ade80}
body.dark-mode .fab-cover{border-color:#444}
body.dark-mode .fab-btn-secondary{background:#2a2a2a;color:#e0e0e0}
body.dark-mode .fab-manual-input input{background:#2a2a2a;border-color:#444;color:#fff}
body.dark-mode .fab-manual-input input::placeholder{color:#888}
body.dark-mode .fab-scan-error{color:#e74c3c}
body.dark-mode .fab-toast{background:#1e1e1e;color:#e0e0e0}
body.dark-mode .fab-toast.success{border-left-color:#4ade80}
body.dark-mode .fab-toast.error{border-left-color:#e74c3c}
body.dark-mode .fab-label{color:#ccc}
body.dark-mode .fab-date-input{background:#2a2a2a;border-color:#444;color:#fff}
</style>

<button id="fab-scan-btn" type="button">
    <span class="fab-tooltip">Scan Barcode</span>
    <i class="bi bi-qr-code-scan"></i>
</button>

<div class="fab-modal" id="fab-scan-modal">
    <div class="fab-modal-box">
        <h4><i class="bi bi-qr-code-scan"></i> Scan Barcode Buku</h4>
        <p class="fab-subtitle">Arahkan kamera ke barcode ISBN buku</p>
        <div id="fab-scan-reader" class="fab-scan-reader"></div>
        <div id="fab-scan-error" class="fab-scan-error" style="display:none">
            <i class="bi bi-exclamation-triangle"></i>
            <p id="fab-scan-error-title">Kamera tidak tersedia atau ditolak</p>
            <p id="fab-scan-error-detail" style="font-size:11px;color:#999;margin-top:8px;word-break:break-all"></p>
        </div>
        <div class="fab-manual-input">
            <input type="text" id="fab-manual-isbn" placeholder="Masukkan ISBN manual">
            <button id="fab-manual-btn" type="button">Cari</button>
        </div>
        <button class="fab-btn-secondary" id="fab-scan-close" type="button">Batal</button>
    </div>
</div>

<div class="fab-modal" id="fab-pinjam-modal">
    <div class="fab-modal-box">
        <h4><i class="bi bi-bookmark-plus"></i> Form Peminjaman</h4>
        <p class="fab-subtitle">Ajukan peminjaman buku</p>
        <div id="fab-pinjam-content"></div>
    </div>
</div>

<div class="fab-modal" id="fab-kembali-modal">
    <div class="fab-modal-box">
        <h4><i class="bi bi-arrow-return-left"></i> Ajukan Pengembalian</h4>
        <p class="fab-subtitle">Kembalikan buku yang sedang dipinjam</p>
        <div id="fab-kembali-content"></div>
    </div>
</div>

<div class="fab-toast" id="fab-toast"></div>

<script src="https://unpkg.com/html5-qrcode@2.3.8/html5-qrcode.min.js"></script>
<script>
(function() {
    let scanner = null;
    const csrf = document.querySelector('meta[name="csrf-token"]')?.content || '';
    const isVip = {{ auth()->user()->is_vip && auth()->user()->vip_expired_at && now()->lt(auth()->user()->vip_expired_at) ? 'true' : 'false' }};

    document.getElementById('fab-scan-btn').addEventListener('click', openScan);
    document.getElementById('fab-scan-close').addEventListener('click', closeScan);
    document.getElementById('fab-manual-btn').addEventListener('click', manualSearch);

    function openScan() {
        document.getElementById('fab-scan-modal').classList.add('show');
        document.getElementById('fab-scan-error').style.display = 'none';
        document.getElementById('fab-scan-reader').style.display = 'block';
        document.getElementById('fab-manual-isbn').value = '';
        startScanner();
    }

    function closeScan() {
        stopScanner();
        document.getElementById('fab-scan-modal').classList.remove('show');
    }

    function startScanner() {
        if (scanner) return;
        if (typeof Html5Qrcode === 'undefined') {
            console.error('[FabScanner] Html5Qrcode library belum dimuat');
            showScanError('Library scanner gagal dimuat.', 'Pastikan koneksi internet stabil dan muat ulang halaman.');
            return;
        }

        if (!window.isSecureContext) {
            console.error('[FabScanner] Halaman tidak berjalan di secure context (HTTPS)');
            showScanError('Halaman harus dibuka dengan HTTPS.', 'Kamera memerlukan secure context. Pastikan URL menggunakan https:// atau localhost.');
            return;
        }

        if (!navigator.mediaDevices || !navigator.mediaDevices.getUserMedia) {
            console.error('[FabScanner] Browser tidak mendukung getUserMedia');
            showScanError('Browser tidak mendukung akses kamera.', 'Gunakan browser modern seperti Chrome, Firefox, atau Edge.');
            return;
        }

        console.log('[FabScanner] Memulai inisialisasi...');
        scanner = new Html5Qrcode("fab-scan-reader");

        var isMobile = /Android|iPhone|iPad|iPod/i.test(navigator.userAgent);
        console.log('[FabScanner] Device:', isMobile ? 'Mobile' : 'Desktop');

        if (isMobile) {
            console.log('[FabScanner] Menggunakan facingMode: environment (kamera belakang)');
            startWithCamera({ facingMode: "environment" });
        } else {
            console.log('[FabScanner] Melakukan enumerasi kamera...');
            Html5Qrcode.getCameras().then(function(cameras) {
                console.log('[FabScanner] Kamera ditemukan:', cameras.length, cameras);
                var config;
                if (cameras && cameras.length > 0) {
                    var selected = cameras.find(function(c) {
                        return c.label && /back|rear|environment/i.test(c.label);
                    }) || cameras[0];
                    console.log('[FabScanner] Kamera dipilih:', selected.label || selected.id);
                    config = { deviceId: { exact: selected.id } };
                } else {
                    console.warn('[FabScanner] Tidak ada kamera via enumerasi, fallback facingMode');
                    config = { facingMode: "environment" };
                }
                startWithCamera(config);
            }).catch(function(err) {
                console.warn('[FabScanner] Gagal enumerasi kamera:', err.message || err);
                startWithCamera({ facingMode: "environment" });
            });
        }
    }

    function startWithCamera(cameraConfig) {
        console.log('[FabScanner] Memulai kamera dengan config:', JSON.stringify(cameraConfig));
        scanner.start(
            cameraConfig,
            { fps: 10, qrbox: 250 },
            code => { stopScanner(); closeScan(); processIsbn(code); },
            () => {}
        ).catch(err => {
            console.error('[FabScanner] Gagal memulai kamera:', err);
            stopScanner();
            var msg = String(err.message || err || '');
            var detail = msg;
            if (msg.indexOf('NotAllowedError') !== -1 || msg.indexOf('Permission') !== -1 || msg.indexOf('permission') !== -1 || msg.indexOf('denied') !== -1) {
                showScanError('Izin kamera ditolak.', 'Berikan izin kamera di pengaturan browser, lalu muat ulang halaman.');
            } else if (msg.indexOf('NotFoundError') !== -1 || msg.indexOf('not found') !== -1 || msg.indexOf('could not be found') !== -1) {
                showScanError('Kamera tidak ditemukan.', 'Pastikan perangkat memiliki kamera dan tidak sedang digunakan aplikasi lain.');
            } else if (msg.indexOf('NotReadableError') !== -1 || msg.indexOf('in use') !== -1 || msg.indexOf('Could not start') !== -1) {
                showScanError('Kamera sedang digunakan.', 'Tutup aplikasi lain yang menggunakan kamera (Zoom, Meet, dll), lalu coba lagi.');
            } else if (msg.indexOf('OverconstrainedError') !== -1) {
                showScanError('Kamera tidak memenuhi syarat.', 'Kamera yang dipilih tidak mendukung resolusi yang diminta.');
            } else {
                showScanError('Gagal mengakses kamera.', detail);
            }
        });
    }

    function showScanError(title, detail) {
        var errEl = document.getElementById('fab-scan-error');
        var titleEl = document.getElementById('fab-scan-error-title');
        var detailEl = document.getElementById('fab-scan-error-detail');
        var readerEl = document.getElementById('fab-scan-reader');
        if (errEl) errEl.style.display = 'block';
        if (titleEl) titleEl.textContent = title;
        if (detailEl) detailEl.textContent = detail || '';
        if (readerEl) readerEl.style.display = 'none';
    }

    function stopScanner() {
        if (scanner && scanner.isScanning) {
            scanner.stop().then(() => { scanner = null; }).catch(() => { scanner = null; });
        } else {
            scanner = null;
        }
    }

    function manualSearch() {
        const isbn = document.getElementById('fab-manual-isbn').value.trim();
        if (!isbn) return;
        stopScanner();
        closeScan();
        processIsbn(isbn);
    }

    function processIsbn(isbn) {
        fetch('{{ route("barcode.cekBuku") }}', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': csrf },
            body: JSON.stringify({ kode: isbn })
        })
        .then(r => r.json())
        .then(data => {
            if (data.status === 'not_found') {
                toast('error', 'Barcode buku tidak ditemukan.');
                return;
            }
            if (data.peminjaman_aktif && data.peminjaman_aktif.status === 'dipinjam') {
                showKembali(data);
            } else {
                showPinjam(data);
            }
        })
        .catch(() => toast('error', 'Terjadi kesalahan koneksi.'));
    }

    function showPinjam(data) {
        const buku = data.buku;
        const batasText = isVip ? 'VIP - maks. 6 buku, 14 hari' : 'Reguler - maks. 3 buku, 7 hari';
        const maxDurasi = isVip ? 14 : 7;
        const today = new Date().toISOString().split('T')[0];
        const minKembali = new Date(Date.now() + 86400000).toISOString().split('T')[0];
        const maxDate = new Date(Date.now() + maxDurasi * 86400000).toISOString().split('T')[0];

        const stokBadge = buku.stok > 0
            ? `<span style="display:inline-block;padding:2px 8px;border-radius:12px;font-size:11px;font-weight:600;background:#d4edda;color:#1a6e35">✓ Tersedia (${buku.stok})</span>`
            : `<span style="display:inline-block;padding:2px 8px;border-radius:12px;font-size:11px;font-weight:600;background:#f8d7da;color:#721c24">✕ Stok habis</span>`;

        document.getElementById('fab-pinjam-content').innerHTML = `
            <div style="display:flex;gap:12px;align-items:start;margin-bottom:16px">
                ${buku.sampul ? `<img src="${buku.sampul}" class="fab-cover">` : `<div style="width:60px;height:85px;background:#eee;border-radius:6px;display:flex;align-items:center;justify-content:center;font-size:24px">📚</div>`}
                <div style="flex:1;min-width:0">
                    <div style="font-weight:700;font-size:15px;color:#222;line-height:1.3">${buku.judul}</div>
                    <div style="font-size:13px;color:#666;margin-top:2px">${buku.pengarang}</div>
                    <div style="margin-top:6px;display:flex;flex-wrap:wrap;gap:4px">${stokBadge}${buku.genre ? `<span style="display:inline-block;padding:2px 8px;border-radius:12px;font-size:11px;font-weight:600;background:#f3f4f6;color:#666">${buku.genre}</span>` : ''}</div>
                </div>
            </div>
            <div class="fab-modal-info" style="margin-bottom:16px">
                ${buku.kode_buku ? `<p><i class="bi bi-upc-scan"></i> Kode Buku: <strong>${buku.kode_buku}</strong></p>` : ''}
                <p><i class="bi bi-upc"></i> ISBN: <strong>${buku.isbn || '-'}</strong></p>
                ${buku.lokasi ? `<p><i class="bi bi-map"></i> Lokasi Rak: <strong>Rak ${buku.lokasi}</strong></p>` : ''}
                <p><i class="bi bi-stack"></i> Stok: <strong>${buku.stok}</strong></p>
                <p><i class="bi bi-person"></i> Peminjam: <strong>{{ auth()->user()->name }}</strong></p>
                <p><i class="bi bi-star-fill"></i> Status: <strong>${batasText}</strong></p>
            </div>
            <div style="margin-bottom:12px">
                <label style="font-size:13px;font-weight:600;margin-bottom:4px;display:block" class="fab-label">Tanggal Pinjam</label>
                <input type="date" id="fab-tgl-pinjam" min="${today}" value="${today}" max="${maxDate}" style="width:100%;padding:10px;border:1px solid #ddd;border-radius:8px;font-size:14px" class="fab-date-input" onchange="updateMinKembali()">
            </div>
            <div style="margin-bottom:16px">
                <label style="font-size:13px;font-weight:600;margin-bottom:4px;display:block" class="fab-label">Tanggal Kembali</label>
                <input type="date" id="fab-tgl-kembali" min="${minKembali}" max="${maxDate}" style="width:100%;padding:10px;border:1px solid #ddd;border-radius:8px;font-size:14px" class="fab-date-input">
            </div>
            <div id="fab-validasi-msg" style="color:#e74c3c;font-size:12px;margin-bottom:12px;display:none"></div>
            <button class="fab-btn-primary" onclick="fabSubmitPinjam(${buku.id})">
                <i class="bi bi-check-circle"></i> Ajukan Peminjaman
            </button>
            <button class="fab-btn-secondary" onclick="fabClosePinjam()">Batal</button>
        `;
        document.getElementById('fab-pinjam-modal').classList.add('show');
    }

    window.updateMinKembali = function() {
        const tglPinjam = document.getElementById('fab-tgl-pinjam').value;
        if (tglPinjam) {
            const minDate = new Date(tglPinjam);
            minDate.setDate(minDate.getDate() + 1);
            document.getElementById('fab-tgl-kembali').min = minDate.toISOString().split('T')[0];
        }
    };

    window.fabClosePinjam = function() {
        document.getElementById('fab-pinjam-modal').classList.remove('show');
    };

    window.fabSubmitPinjam = function(bukuId) {
        const tglPinjam = document.getElementById('fab-tgl-pinjam').value;
        const tglKembali = document.getElementById('fab-tgl-kembali').value;
        const maxDurasi = isVip ? 14 : 7;
        const validasiMsg = document.getElementById('fab-validasi-msg');

        if (!tglPinjam || !tglKembali) {
            validasiMsg.textContent = 'Tanggal Pinjam dan Tanggal Kembali wajib diisi.';
            validasiMsg.style.display = 'block';
            return;
        }

        const pinjam = new Date(tglPinjam);
        const kembali = new Date(tglKembali);
        const hariIni = new Date();
        hariIni.setHours(0,0,0,0);
        pinjam.setHours(0,0,0,0);
        kembali.setHours(0,0,0,0);

        if (pinjam < hariIni) {
            validasiMsg.textContent = 'Tanggal Pinjam tidak boleh kurang dari hari ini.';
            validasiMsg.style.display = 'block';
            return;
        }

        if (kembali <= pinjam) {
            validasiMsg.textContent = 'Tanggal Kembali harus lebih besar dari Tanggal Pinjam.';
            validasiMsg.style.display = 'block';
            return;
        }

        const diffDays = Math.ceil((kembali - pinjam) / (1000 * 60 * 60 * 24));
        if (diffDays > maxDurasi) {
            validasiMsg.textContent = `Durasi peminjaman maksimal ${maxDurasi} hari untuk status Anda.`;
            validasiMsg.style.display = 'block';
            return;
        }

        validasiMsg.style.display = 'none';

        console.log('CSRF Token:', csrf);
        console.log('Submitting to:', '{{ route("barcode.pinjam") }}');

        fetch('{{ route("barcode.pinjam") }}', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': csrf },
            body: JSON.stringify({ buku_id: bukuId, tanggal_pinjam: tglPinjam, tanggal_kembali: tglKembali })
        })
        .then(r => {
            console.log('Response status:', r.status);
            return r.json().then(data => {
                console.log('Response body:', data);
                if (r.status === 422 && data.errors) {
                    const msgs = Object.values(data.errors).flat().join(', ');
                    toast('error', msgs);
                } else {
                    fabClosePinjam();
                    toast(data.status === 'success' ? 'success' : 'error', data.pesan || 'Terjadi kesalahan');
                }
            });
        })
        .catch(err => {
            console.error('Fetch error:', err);
            toast('error', 'Terjadi kesalahan: ' + err.message);
        });
    };

    function showKembali(data) {
        const buku = data.buku;
        const pinjam = data.peminjaman_aktif;
        const tglPinjam = pinjam.tanggal_pinjam ? new Date(pinjam.tanggal_pinjam).toLocaleDateString('id-ID', { day: 'numeric', month: 'long', year: 'numeric' }) : '-';
        const tglKembali = pinjam.tanggal_kembali ? new Date(pinjam.tanggal_kembali).toLocaleDateString('id-ID', { day: 'numeric', month: 'long', year: 'numeric' }) : '-';

        document.getElementById('fab-kembali-content').innerHTML = `
            <div style="display:flex;gap:12px;align-items:start;margin-bottom:16px">
                ${buku.sampul ? `<img src="${buku.sampul}" class="fab-cover">` : `<div style="width:60px;height:85px;background:#eee;border-radius:6px;display:flex;align-items:center;justify-content:center;font-size:24px">📚</div>`}
                <div style="flex:1;min-width:0">
                    <div style="font-weight:700;font-size:15px;color:#222;line-height:1.3">${buku.judul}</div>
                    <div style="font-size:13px;color:#666;margin-top:2px">${buku.pengarang}</div>
                </div>
            </div>
            <div class="fab-modal-info" style="margin-bottom:16px">
                ${buku.kode_buku ? `<p><i class="bi bi-upc-scan"></i> Kode Buku: <strong>${buku.kode_buku}</strong></p>` : ''}
                <p><i class="bi bi-calendar"></i> Tanggal Pinjam: <strong>${tglPinjam}</strong></p>
                <p><i class="bi bi-calendar-check"></i> Tanggal Harus Kembali: <strong>${tglKembali}</strong></p>
                <p><i class="bi bi-info-circle"></i> Status: <strong style="color:#2563eb">Dipinjam</strong></p>
            </div>
            <button class="fab-btn-primary" style="background:linear-gradient(135deg,#2563eb,#3b82f6)" onclick="fabSubmitKembali(${pinjam.id})">
                <i class="bi bi-arrow-return-left"></i> Ajukan Pengembalian
            </button>
            <button class="fab-btn-secondary" onclick="fabCloseKembali()">Batal</button>
        `;
        document.getElementById('fab-kembali-modal').classList.add('show');
    }

    window.fabCloseKembali = function() {
        document.getElementById('fab-kembali-modal').classList.remove('show');
    };

    window.fabSubmitKembali = function(peminjamanId) {
        fetch('{{ route("barcode.kembali") }}', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': csrf },
            body: JSON.stringify({ peminjaman_id: peminjamanId })
        })
        .then(r => r.json())
        .then(data => {
            fabCloseKembali();
            toast(data.status === 'success' ? 'success' : 'error', data.pesan);
        })
        .catch(() => toast('error', 'Terjadi kesalahan.'));
    };

    function toast(type, msg) {
        const t = document.getElementById('fab-toast');
        t.className = 'fab-toast ' + type;
        t.innerHTML = `<i class="bi bi-${type === 'success' ? 'check-circle' : 'exclamation-circle'}"></i> ${msg}`;
        t.classList.add('show');
        setTimeout(() => t.classList.remove('show'), 4000);
    }

    document.addEventListener('click', e => {
        if (e.target.classList.contains('fab-modal')) {
            e.target.classList.remove('show');
            stopScanner();
        }
    });
})();
</script>
@endauth
