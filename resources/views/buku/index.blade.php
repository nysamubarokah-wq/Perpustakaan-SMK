@extends('layouts.admin')

@section('header_title', 'Kelola Buku')

@section('content')

<style>
    .kelola-search-bar .d-flex { gap: 8px; }
    @media (max-width: 768px) {
        .kelola-search-bar {
            padding: 12px 16px !important;
        }
        .kelola-search-bar .d-flex {
            flex-direction: column;
        }
        .kelola-search-bar .form-control { font-size: 14px; }
        .kelola-search-bar button,
        .kelola-search-bar a {
            width: 100%;
            text-align: center;
            justify-content: center;
        }
        .kelola-table-wrap { overflow-x: auto; -webkit-overflow-scrolling: touch; }
        .kelola-table-wrap table { min-width: 950px; }
        .kelola-table-wrap td, .kelola-table-wrap th {
            padding: 8px 10px !important;
            font-size: 12px;
        }
        .kelola-table-wrap .aksi-btn {
            padding: 4px 6px !important;
            font-size: 10px !important;
            margin-right: 2px !important;
        }
        .kelola-toolbar { flex-direction: column; align-items: flex-start !important; }
        .kelola-toolbar > div { width: 100%; }
        .kelola-toolbar button { flex: 1; }
    }
    @media (max-width: 480px) {
        .kelola-table-wrap table { min-width: 750px; }
        .kelola-table-wrap td, .kelola-table-wrap th {
            padding: 6px 8px !important;
            font-size: 11px;
        }
    }
</style>

@if(session('success'))
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        <i class="bi bi-check-circle"></i> {{ session('success') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
@endif

@if(session('error'))
    <div class="alert alert-danger alert-dismissible fade show" role="alert">
        <i class="bi bi-exclamation-circle"></i> {{ session('error') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
@endif

<div class="card-admin">
    <x-admin-card-header title="Daftar Buku" icon="bi bi-book">
        <x-slot:action>
            <form method="POST" action="{{ route('buku.generateAllQr') }}" style="display:inline">
                @csrf
                <button type="submit"
                        style="padding:8px 18px;background:linear-gradient(135deg,#6c5ce7,#a29bfe);color:white;border:none;border-radius:10px;font-size:13px;font-weight:600;cursor:pointer">
                    <i class="bi bi-qr-code"></i> Generate Semua QR
                </button>
            </form>
            <a href="{{ route('buku.downloadAllQr') }}"
               style="padding:8px 18px;background:linear-gradient(135deg,#e17055,#fdcb6e);color:white;border:none;border-radius:10px;font-size:13px;font-weight:600;cursor:pointer;text-decoration:none">
                <i class="bi bi-download"></i> Download Semua QR
            </a>
            <a href="{{ route('buku.cetakSemuaQr') }}"
               style="padding:8px 18px;background:linear-gradient(135deg,#00b894,#55efc4);color:white;border:none;border-radius:10px;font-size:13px;font-weight:600;cursor:pointer;text-decoration:none">
                <i class="bi bi-printer"></i> Cetak Semua QR
            </a>
            <button type="button" onclick="document.getElementById('modalImport').style.display='flex'"
                style="padding:8px 18px;background:#2c3e50;color:white;border:none;border-radius:10px;font-size:13px;font-weight:600;cursor:pointer">
                <i class="bi bi-upload"></i> Import CSV
            </button>
            <a href="{{ route('buku.create') }}"
               style="padding:8px 18px;background:linear-gradient(135deg,#1a6e35,#27ae60);color:white;border:none;border-radius:10px;font-size:13px;font-weight:600;cursor:pointer;text-decoration:none">
                <i class="bi bi-plus-lg"></i> Tambah Buku
            </a>
        </x-slot:action>
    </x-admin-card-header>
    <div class="card-admin-body">
        <div style="padding:15px 25px;border-bottom:1px solid #eee;background:#fafafa" class="kelola-search-bar">
            <form method="GET" action="{{ route('buku.index') }}">
                <div class="d-flex gap-2">
                    <input type="text" name="search"
                           value="{{ $search ?? '' }}"
                           placeholder="Cari judul, pengarang, ISBN, kode buku, genre..."
                           class="form-control" style="border-radius:10px;flex:1">
                    <button type="submit"
                        style="padding:8px 20px;background:linear-gradient(135deg,#1a6e35,#27ae60);color:white;border:none;border-radius:10px;font-size:13px;font-weight:600;cursor:pointer;white-space:nowrap">
                        <i class="bi bi-search"></i> Cari
                    </button>
                    @if($search)
                    <a href="{{ route('buku.index') }}"
                       style="padding:8px 16px;background:#f0f0f0;color:#555;border:none;border-radius:10px;font-size:13px;font-weight:600;cursor:pointer;text-decoration:none;white-space:nowrap">
                        <i class="bi bi-x"></i> Reset
                    </a>
                    @endif
                </div>
            </form>
        </div>

    <div id="toolbarHapus" class="kelola-toolbar" style="display:none;padding:10px 20px;background:#fff3cd;border-bottom:1px solid #eee;align-items:center;justify-content:space-between;gap:10px;flex-wrap:wrap">
        <span id="jumlahDipilih" style="font-size:13px;font-weight:600;color:#856404">0 buku dipilih</span>
        <div style="display:flex;gap:8px">
            <button type="button" onclick="pilihSemua()" style="padding:6px 14px;background:#856404;color:white;border:none;border-radius:8px;font-size:12px;font-weight:600;cursor:pointer">
                <i class="bi bi-check-all"></i> Pilih Semua
            </button>
            <button type="button" onclick="submitHapusBanyak()" style="padding:6px 14px;background:#e74c3c;color:white;border:none;border-radius:8px;font-size:12px;font-weight:600;cursor:pointer">
                <i class="bi bi-trash"></i> Hapus yang Dipilih
            </button>
        </div>
    </div>

    <div class="card-admin-body">
        <div class="kelola-table-wrap">
        <table class="table table-hover">
            <thead style="background:#f8f9fa">
                <tr>
                    <th style="width:40px">
                        <input type="checkbox" id="checkAll" onchange="toggleCheckAll(this)"
                            style="width:15px;height:15px;cursor:pointer">
                    </th>
                    <th style="width:35px">#</th>
                    <th style="width:50px">Sampul</th>
                    <th>Judul</th>
                    <th style="min-width:90px">Kode Buku</th>
                    <th style="min-width:100px">ISBN</th>
                    <th>Pengarang</th>
                    <th>Genre</th>
                    <th style="min-width:80px">Lokasi</th>
                    <th style="width:70px">Eksemplar</th>
                    <th style="min-width:170px">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse($buku as $item)
                <tr>
                    <td>
                        <input type="checkbox" name="ids[]" value="{{ $item->id }}"
                            class="checkbox-buku" onchange="updateToolbar()"
                            style="width:15px;height:15px;cursor:pointer">
                    </td>
                    <td>{{ $loop->iteration }}</td>
                    <td>
                        @if($item->sampul)
                            <img src="{{ asset($item->sampul) }}" style="width:40px;height:55px;object-fit:cover;border-radius:5px">
                        @else
                            <div style="width:40px;height:55px;background:linear-gradient(135deg,#1a6e35,#27ae60);border-radius:5px;display:flex;align-items:center;justify-content:center;color:white;font-size:16px">
                                <i class="bi bi-book"></i>
                            </div>
                        @endif
                    </td>
                    <td>{{ $item->judul }}</td>
                    <td style="font-size:11px;font-family:monospace;color:#1a6e35;font-weight:600;white-space:nowrap">{{ $item->kode_buku ?? '-' }}</td>
                    <td style="font-size:11px;font-family:monospace;color:#555;white-space:nowrap">{{ $item->isbn ?? '-' }}</td>
                    <td>{{ $item->pengarang }}</td>
                    <td>
                        @if($item->genre)
                            <span style="padding:3px 10px;background:#e8f5e9;color:#1a6e35;border-radius:20px;font-size:11px;font-weight:600">{{ $item->genre }}</span>
                        @else
                            <span style="color:#aaa">-</span>
                        @endif
                    </td>
                    <td style="white-space:nowrap">
                        @if($item->lokasi)
                            <span style="display:inline-block;max-width:80px;overflow:hidden;text-overflow:ellipsis;white-space:nowrap;padding:3px 10px;background:#e8f4fd;color:#2c3e50;border-radius:20px;font-size:11px;font-weight:600;vertical-align:middle" title="{{ $item->lokasi }}">
                                {{ $item->lokasi }}
                            </span>
                        @else
                            <span style="color:#aaa">-</span>
                        @endif
                    </td>
                    <td>
                        <span style="padding:3px 10px;border-radius:20px;font-size:11px;font-weight:600;background:{{ ($item->eksemplar_tersedia_count ?? $item->stok) > 0 ? '#d4edda' : '#f8d7da' }};color:{{ ($item->eksemplar_tersedia_count ?? $item->stok) > 0 ? '#1a6e35' : '#721c24' }}">
                            {{ $item->eksemplar_tersedia_count ?? $item->stok }}/{{ $item->eksemplar_count ?? $item->stok }}
                        </span>
                    </td>
                    <td style="white-space:nowrap">
                        <a href="{{ route('buku.show', $item->id) }}"
                           class="aksi-btn"
                           style="padding:5px 10px;background:#e0f2fe;color:#0369a1;border:none;border-radius:6px;font-size:11px;font-weight:600;cursor:pointer;text-decoration:none;display:inline-flex;align-items:center;gap:3px;margin-right:3px">
                            <i class="bi bi-eye"></i> Detail
                        </a>
                        <a href="{{ route('buku.edit', $item->id) }}"
                           class="aksi-btn"
                           style="padding:5px 10px;background:#dbeafe;color:#2563eb;border:none;border-radius:6px;font-size:11px;font-weight:600;cursor:pointer;text-decoration:none;display:inline-flex;align-items:center;gap:3px;margin-right:3px">
                            <i class="bi bi-pencil"></i>
                        </a>
                        <form action="{{ route('buku.destroy', $item->id) }}" method="POST" class="d-inline">
                            @csrf
                            @method('DELETE')
                            <button type="submit"
                                    class="aksi-btn"
                                    onclick="return confirm('Yakin hapus buku ini?')"
                                    style="padding:5px 10px;background:#fee2e2;color:#dc2626;border:none;border-radius:6px;font-size:11px;font-weight:600;cursor:pointer">
                                <i class="bi bi-trash"></i>
                            </button>
                        </form>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="12" class="text-center text-muted py-4">Belum ada data buku</td>
                </tr>
                @endforelse
            </tbody>
        </table>
        </div>
    </div>
</div>

{{-- MODAL IMPORT --}}
<div id="modalImport" style="display:none;position:fixed;inset:0;background:rgba(0,0,0,0.5);z-index:2000;align-items:center;justify-content:center;padding:15px">
    <div style="background:white;border-radius:20px;padding:25px;width:100%;max-width:480px;box-shadow:0 20px 60px rgba(0,0,0,0.3);max-height:90vh;overflow-y:auto">
        <h5 style="font-weight:700;color:#222;margin-bottom:5px"><i class="bi bi-upload" style="color:#1a6e35"></i> Import Buku dari CSV</h5>
        <p style="font-size:12px;color:#888;margin-bottom:20px">Format kolom: <code>judul, pengarang, penerbit, tahun_terbit, isbn, stok, genre, lokasi, deskripsi, url_sampul</code><br>
        <span style="font-size:11px;color:#aaa">* url_sampul opsional</span></p>

        <form method="POST" action="{{ route('buku.import') }}" enctype="multipart/form-data">
            @csrf
            <div style="margin-bottom:15px">
                <input type="file" name="file" accept=".csv,.txt"
                    class="form-control" style="border-radius:10px">
            </div>
            <div style="background:#f8f9fa;border-radius:10px;padding:12px;font-size:12px;color:#666;margin-bottom:15px">
                <strong>Contoh:</strong><br>
                <code style="font-size:10px">Matematika,Pak Budi,Erlangga,2023,9786021000001,10,Sains & Teknologi,A1,Deskripsi,https://url-gambar.jpg</code>
            </div>
            <div style="display:flex;gap:10px;flex-wrap:wrap">
                <button type="submit"
                        style="flex:1;min-width:140px;padding:12px;background:linear-gradient(135deg,#1a6e35,#27ae60);color:white;border:none;border-radius:10px;font-size:14px;font-weight:600;cursor:pointer">
                    <i class="bi bi-upload"></i> Import Sekarang
                </button>
                <button type="button" onclick="document.getElementById('modalImport').style.display='none'"
                        style="flex:1;min-width:140px;padding:12px;background:#f0f0f0;color:#555;border:none;border-radius:10px;font-size:14px;font-weight:600;cursor:pointer">
                    Batal
                </button>
            </div>
        </form>
    </div>
</div>

{{-- MODAL QR CODE --}}
<div id="modalQR" style="display:none;position:fixed;inset:0;background:rgba(0,0,0,0.5);z-index:2000;align-items:center;justify-content:center;padding:15px">
    <div style="background:white;border-radius:20px;padding:25px;width:100%;max-width:420px;box-shadow:0 20px 60px rgba(0,0,0,0.3);text-align:center;max-height:90vh;overflow-y:auto">
        <h5 style="font-weight:700;color:#222;margin-bottom:5px"><i class="bi bi-qr-code" style="color:#6c5ce7"></i> QR Code Buku</h5>
        <p id="modalQR-judul" style="font-size:14px;color:#333;font-weight:600;margin-bottom:2px"></p>
        <p id="modalQR-kode" style="font-size:12px;color:#1a6e35;font-family:monospace;font-weight:700;margin-bottom:2px"></p>
        <p id="modalQR-isbn" style="font-size:11px;color:#888;margin-bottom:15px"></p>
        <div id="modalQR-gambar" style="margin-bottom:15px"></div>
        <div style="display:flex;gap:10px;justify-content:center;flex-wrap:wrap">
            <a id="modalQR-download" href="#"
               style="padding:10px 20px;background:linear-gradient(135deg,#2563eb,#3b82f6);color:white;border:none;border-radius:10px;font-size:13px;font-weight:600;cursor:pointer;text-decoration:none;display:inline-flex;align-items:center;gap:5px">
                <i class="bi bi-download"></i> Download
            </a>
            <a id="modalQR-print" href="#" target="_blank"
               style="padding:10px 20px;background:linear-gradient(135deg,#1a6e35,#27ae60);color:white;border:none;border-radius:10px;font-size:13px;font-weight:600;cursor:pointer;text-decoration:none;display:inline-flex;align-items:center;gap:5px">
                <i class="bi bi-printer"></i> Print
            </a>
            <button type="button" onclick="document.getElementById('modalQR').style.display='none'"
                    style="padding:10px 20px;background:#f0f0f0;color:#555;border:none;border-radius:10px;font-size:13px;font-weight:600;cursor:pointer">
                Tutup
            </button>
        </div>
    </div>
</div>

@push('scripts')
<script>
function updateToolbar() {
    const checked = document.querySelectorAll('.checkbox-buku:checked');
    const toolbar = document.getElementById('toolbarHapus');
    const jumlah = document.getElementById('jumlahDipilih');

    if (checked.length > 0) {
        toolbar.style.display = 'flex';
        jumlah.textContent = checked.length + ' buku dipilih';
    } else {
        toolbar.style.display = 'none';
    }

    const semua = document.querySelectorAll('.checkbox-buku');
    document.getElementById('checkAll').checked = checked.length === semua.length && semua.length > 0;
}

function toggleCheckAll(el) {
    document.querySelectorAll('.checkbox-buku').forEach(cb => cb.checked = el.checked);
    updateToolbar();
}

function pilihSemua() {
    document.querySelectorAll('.checkbox-buku').forEach(cb => cb.checked = true);
    updateToolbar();
}

function submitHapusBanyak() {
    const checked = document.querySelectorAll('.checkbox-buku:checked');
    if (checked.length === 0) {
        alert('Tidak ada buku yang dipilih.');
        return;
    }
    if (!confirm('Hapus ' + checked.length + ' buku yang dipilih? Tindakan ini tidak bisa dibatalkan.')) return;

    const form = document.createElement('form');
    form.method = 'POST';
    form.action = '{{ route("buku.hapusBanyak") }}';

    const csrf = document.createElement('input');
    csrf.type = 'hidden';
    csrf.name = '_token';
    csrf.value = '{{ csrf_token() }}';
    form.appendChild(csrf);

    checked.forEach(cb => {
        const input = document.createElement('input');
        input.type = 'hidden';
        input.name = 'ids[]';
        input.value = cb.value;
        form.appendChild(input);
    });

    document.body.appendChild(form);
    form.submit();
}

async function lihatQR(bukuId) {
    try {
        const res = await fetch('/buku/' + bukuId + '/qrcode', {
            headers: { 'Accept': 'application/json' }
        });
        const data = await res.json();

        if (data.status === 'success') {
            document.getElementById('modalQR-judul').textContent = data.judul;
            document.getElementById('modalQR-kode').textContent = data.kode_buku;
            document.getElementById('modalQR-isbn').textContent = 'ISBN: ' + (data.isbn || '-');
            document.getElementById('modalQR-gambar').innerHTML = '<img src="' + data.qrcode_url + '" style="width:200px;height:200px;border:1px solid #eee;border-radius:10px">';
            document.getElementById('modalQR-download').href = '/buku/' + bukuId + '/qrcode/download';
            document.getElementById('modalQR-print').href = '/buku/' + bukuId + '/qrcode/print';
            document.getElementById('modalQR').style.display = 'flex';
        } else {
            alert('Gagal memuat QR Code.');
        }
    } catch (e) {
        alert('Gagal terhubung ke server.');
    }
}
</script>
@endpush

@endsection
