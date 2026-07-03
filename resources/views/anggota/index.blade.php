@extends('layouts.admin')

@section('title', 'Kelola Anggota')
@section('page-title', 'Kelola Anggota')

@section('content')

<style>
    @media (max-width: 768px) {
        .tbl-wrap { overflow-x: auto; -webkit-overflow-scrolling: touch; }
        .tbl-wrap table { min-width: 700px; font-size: 12px; }
        .tbl-wrap td, .tbl-wrap th { padding: 8px 10px !important; }
        .search-row { flex-direction: column !important; }
        .search-row .form-control { width: 100% !important; }
        .search-row .btn { width: 100% !important; }
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

{{-- TOOLBAR ATAS --}}
<div style="background:white;border-radius:16px;padding:20px 24px;border:1px solid #eee;margin-bottom:20px;display:flex;align-items:center;justify-content:space-between;flex-wrap:wrap;gap:16px">

    <div>
        <h4 style="font-weight:800;color:#222;margin:0 0 4px;font-size:18px">
            <i class="bi bi-people" style="color:#1a6e35"></i> Data Anggota
        </h4>
        <p style="font-size:13px;color:#888;margin:0">
            Total: <strong style="color:#1a6e35">{{ $anggota->count() }}</strong> anggota
        </p>
    </div>

    <div style="display:flex;gap:10px;align-items:center;flex-wrap:wrap">
        {{-- SEARCH --}}
        <div style="display:flex;gap:8px;align-items:center">
            <input type="text" id="searchInput" value="{{ request('search') }}"
                placeholder="Cari nama / NIS / email..."
                style="padding:8px 14px;border:1.5px solid #eee;border-radius:10px;font-size:13px;outline:none;width:220px"
                onkeydown="if(event.key==='Enter'){cariAnggota()}">
            <button type="button" onclick="cariAnggota()"
                    style="padding:8px 16px;background:linear-gradient(135deg,#1a6e35,#27ae60);color:white;border:none;border-radius:10px;font-size:13px;font-weight:600;cursor:pointer">
                <i class="bi bi-search"></i> Cari
            </button>
            @if(request('search'))
                <a href="{{ route('anggota.index') }}"
                   style="padding:8px 14px;background:#f3f4f6;color:#666;border:none;border-radius:10px;font-size:13px;font-weight:600;text-decoration:none">
                    <i class="bi bi-x"></i>
                </a>
            @endif
        </div>

        {{-- IMPORT --}}
        <button onclick="document.getElementById('importModal').style.display='flex'"
                style="padding:8px 16px;background:#f3f4f6;color:#666;border:none;border-radius:10px;font-size:13px;font-weight:600;cursor:pointer">
            <i class="bi bi-upload"></i> Import
        </button>

        {{-- TOMBOL TAMBAH --}}
        <a href="{{ route('anggota.create') }}"
           style="padding:8px 20px;background:linear-gradient(135deg,#1a6e35,#27ae60);color:white;border:none;border-radius:10px;font-size:13px;font-weight:600;text-decoration:none;display:inline-flex;align-items:center;gap:6px">
            <i class="bi bi-plus-lg"></i> Tambah
        </a>
    </div>
</div>

{{-- BULK DELETE TOOLBAR --}}
<div id="toolbarHapus" style="display:none;padding:14px 24px;background:#fff3cd;border:1.5px solid #ffe69c;border-radius:14px;margin-bottom:16px;align-items:center;justify-content:space-between;flex-wrap:wrap;gap:12px">
    <span style="font-size:14px;font-weight:700;color:#856404">
        <i class="bi bi-check-square"></i> <span id="jumlahDipilih">0</span> anggota dipilih
    </span>
    <div style="display:flex;gap:8px">
        <button type="button" onclick="pilihSemua()"
                style="padding:7px 16px;background:#856404;color:white;border:none;border-radius:8px;font-size:12px;font-weight:600;cursor:pointer">
            <i class="bi bi-check-all"></i> Pilih Semua
        </button>
        <button type="button" onclick="submitHapusBanyak()"
                style="padding:7px 16px;background:#dc2626;color:white;border:none;border-radius:8px;font-size:12px;font-weight:600;cursor:pointer">
            <i class="bi bi-trash"></i> Hapus Terpilih
        </button>
        <button type="button" onclick="batalPilih()"
                style="padding:7px 16px;background:#f3f4f6;color:#666;border:none;border-radius:8px;font-size:12px;font-weight:600;cursor:pointer">
            Batal
        </button>
    </div>
</div>

{{-- ============================================================
     TABEL SISWA
     ============================================================ --}}
<div class="card-admin" style="margin-bottom:20px">
    <div style="padding:16px 24px;border-bottom:1px solid #eee;display:flex;align-items:center;justify-content:space-between;flex-wrap:wrap;gap:10px;background:#f8faff">
        <h5 style="font-weight:700;color:#2563eb;margin:0;font-size:15px">
            <i class="bi bi-mortarboard"></i> Daftar Siswa
            <span style="font-size:12px;font-weight:400;color:#888">({{ $anggota->where('role','siswa')->count() }} siswa)</span>
        </h5>
    </div>
    <div class="card-admin-body tbl-wrap">
        <table class="table table-hover mb-0" style="font-size:13px">
            <thead style="background:#f8f9fa">
                <tr>
                    <th style="padding:12px 16px;font-weight:600;color:#555;border:none;width:40px">
                        <input type="checkbox" id="checkAllSiswa" onchange="toggleCheckAll('siswa')"
                            style="width:16px;height:16px;cursor:pointer">
                    </th>
                    <th style="padding:12px 16px;font-weight:600;color:#555;border:none">#</th>
                    <th style="padding:12px 16px;font-weight:600;color:#555;border:none">Nama</th>
                    <th style="padding:12px 16px;font-weight:600;color:#555;border:none">NIS</th>
                    <th style="padding:12px 16px;font-weight:600;color:#555;border:none">Kelas</th>
                    <th style="padding:12px 16px;font-weight:600;color:#555;border:none">Jurusan</th>
                    <th style="padding:12px 16px;font-weight:600;color:#555;border:none">Email</th>
                    <th style="padding:12px 16px;font-weight:600;color:#555;border:none">No. HP</th>
                    <th style="padding:12px 16px;font-weight:600;color:#555;border:none;text-align:center">Aksi</th>
                </tr>
            </thead>
            <tbody>
            @forelse($anggota->where('role','siswa') as $item)
                <tr id="row-{{ $item->id }}" class="row-item" data-role="siswa">
                    <td style="padding:12px 16px;vertical-align:middle">
                        <input type="checkbox" name="ids[]" value="{{ $item->id }}"
                            class="cb-siswa"
                            onchange="updateToolbar('siswa')"
                            style="width:16px;height:16px;cursor:pointer">
                    </td>
                    <td style="padding:12px 16px;vertical-align:middle;color:#aaa;font-size:12px">{{ $loop->iteration }}</td>
                    <td style="padding:12px 16px;vertical-align:middle">
                        <div style="font-weight:600;color:#222">{{ $item->nama }}</div>
                        @if($item->alamat)
                            <div style="font-size:11px;color:#aaa">{{ Str::limit($item->alamat, 30) }}</div>
                        @endif
                    </td>
                    <td style="padding:12px 16px;vertical-align:middle">
                        @if($item->nis)
                            <span style="background:#e8f5e9;color:#1a6e35;padding:3px 10px;border-radius:20px;font-size:11px;font-weight:600;white-space:nowrap">{{ $item->nis }}</span>
                        @else
                            <span style="color:#ccc">-</span>
                        @endif
                    </td>
                    <td style="padding:12px 16px;vertical-align:middle;color:#555">{{ $item->kelas ?? '-' }}</td>
                    <td style="padding:12px 16px;vertical-align:middle;color:#555">{{ $item->jurusan ?? '-' }}</td>
                    <td style="padding:12px 16px;vertical-align:middle">
                        <span style="color:#555;font-size:12px;word-break:break-all">{{ $item->email }}</span>
                    </td>
                    <td style="padding:12px 16px;vertical-align:middle;color:#555">{{ $item->no_telepon ?? '-' }}</td>
                    <td style="padding:12px 16px;vertical-align:middle;text-align:center">
                        <div style="display:flex;gap:5px;justify-content:center;flex-wrap:nowrap">

                            {{-- Jadikan Admin --}}
                            <form action="{{ route('admin.anggota.role', [$item->id, 'admin']) }}" method="POST" style="margin:0">
                                @csrf @method('PUT')
                                <button type="submit" title="Jadikan Admin"
                                    style="padding:6px 10px;background:#ede9fe;color:#7c3aed;border:none;border-radius:7px;font-size:12px;font-weight:600;cursor:pointer"
                                    onclick="return confirm('Jadikan admin?')">
                                    <i class="bi bi-shield-check"></i>
                                </button>
                            </form>

                            {{-- Edit --}}
                            <a href="{{ route('anggota.edit', $item->id) }}"
                               title="Edit"
                               style="padding:6px 10px;background:#dbeafe;color:#2563eb;border:none;border-radius:7px;font-size:12px;font-weight:600;text-decoration:none;display:inline-flex;align-items:center">
                                <i class="bi bi-pencil"></i>
                            </a>

                            {{-- Hapus --}}
                            <form action="{{ route('anggota.destroy', $item->id) }}" method="POST" style="margin:0">
                                @csrf @method('DELETE')
                                <button type="submit" title="Hapus"
                                    style="padding:6px 10px;background:#fee2e2;color:#dc2626;border:none;border-radius:7px;font-size:12px;font-weight:600;cursor:pointer"
                                    onclick="return confirm('Hapus {{ $item->nama }}?')">
                                    <i class="bi bi-trash"></i>
                                </button>
                            </form>
                        </div>
                    </td>
                </tr>
            @empty
                <tr><td colspan="10" style="text-align:center;padding:40px;color:#aaa">
                    <i class="bi bi-people" style="font-size:40px;display:block;margin-bottom:8px"></i>
                    <p style="font-size:13px">Belum ada data siswa</p>
                </td></tr>
            @endforelse
            </tbody>
        </table>
    </div>
</div>

{{-- ============================================================
     MODAL IMPORT
     ============================================================ --}}
<div id="importModal" style="display:none;position:fixed;top:0;left:0;width:100%;height:100%;background:rgba(0,0,0,0.5);z-index:9999;align-items:center;justify-content:center">
    <div style="background:white;border-radius:20px;padding:30px;max-width:500px;width:90%;box-shadow:0 20px 60px rgba(0,0,0,0.3)">
        <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:24px">
            <h5 style="font-weight:700;color:#222;margin:0">
                <i class="bi bi-upload" style="color:#1a6e35"></i> Import CSV
            </h5>
            <button onclick="document.getElementById('importModal').style.display='none'"
                    style="background:none;border:none;font-size:22px;color:#aaa;cursor:pointer">&times;</button>
        </div>

        <form method="POST" action="{{ route('admin.anggota.import') }}" enctype="multipart/form-data">
            @csrf
            <div class="mb-3">
                <label style="font-size:13px;font-weight:600;color:#444;margin-bottom:8px;display:block">Pilih File CSV</label>
                <input type="file" name="file" accept=".csv,.txt"
                    class="form-control @error('file') is-invalid @enderror"
                    style="border-radius:10px;font-size:13px">
                @error('file')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <div style="background:#f8f9fa;border-radius:12px;padding:16px;font-size:12px;color:#666;margin-bottom:20px;line-height:1.8">
                <strong style="color:#333">Format kolom CSV:</strong><br>
                <code>nis, nama, kelas, jurusan, jk(L/P), no_hp, email</code><br><br>
                <strong style="color:#333">Contoh baris:</strong><br>
                <code>2024001,Anisa Rahmawati,X RPL 1,RPL,L,081234567890,anisa@siswa.sch.id</code>
            </div>

            <div style="display:flex;gap:10px">
                <button type="button" onclick="document.getElementById('importModal').style.display='none'"
                        style="flex:1;padding:12px;background:#f3f4f6;color:#666;border:none;border-radius:10px;font-size:14px;font-weight:600;cursor:pointer">
                    Batal
                </button>
                <button type="submit"
                        style="flex:1;padding:12px;background:linear-gradient(135deg,#1a6e35,#27ae60);color:white;border:none;border-radius:10px;font-size:14px;font-weight:600;cursor:pointer">
                    <i class="bi bi-upload"></i> Import
                </button>
            </div>
        </form>
    </div>
</div>

@endsection

@push('scripts')
<style>
.pagination { gap: 5px; }
.pagination .page-link {
    border-radius: 8px !important;
    font-size: 13px;
    color: #1a6e35;
    border: 1.5px solid #eee;
    padding: 6px 12px;
}
.pagination .page-item.active .page-link {
    background: linear-gradient(135deg, #1a6e35, #27ae60);
    border-color: #1a6e35;
    color: white;
}
.pagination .page-link:hover { background: #e8f5e9; color: #1a6e35; }
</style>
<script>
function cariAnggota() {
    const keyword = document.getElementById('searchInput').value;
    window.location.href = '{{ route('anggota.index') }}?search=' + encodeURIComponent(keyword);
}

function toggleCheckAll(role) {
    const checkboxes = document.querySelectorAll('.cb-' + role);
    const master = document.getElementById('checkAll' + (role === 'siswa' ? 'Siswa' : 'Admin'));
    checkboxes.forEach(cb => cb.checked = master.checked);
    updateToolbar(role);
}

function updateToolbar(role) {
    const checkboxes = document.querySelectorAll('.cb-' + role + ':checked');
    const total = document.querySelectorAll('.cb-' + role);
    const master = document.getElementById('checkAll' + (role === 'siswa' ? 'Siswa' : 'Admin'));

    master.checked = checkboxes.length === total.length && total.length > 0;

    // Count ALL checked across both tables
    const allChecked = document.querySelectorAll('.cb-siswa:checked, .cb-admin:checked');
    const toolbar = document.getElementById('toolbarHapus');
    const jumlah = document.getElementById('jumlahDipilih');

    if (allChecked.length > 0) {
        toolbar.style.display = 'flex';
        jumlah.textContent = allChecked.length;
    } else {
        toolbar.style.display = 'none';
    }
}

function pilihSemua() {
    document.querySelectorAll('.cb-siswa, .cb-admin').forEach(cb => cb.checked = true);
    updateToolbar('siswa');
    updateToolbar('admin');
}

function batalPilih() {
    document.querySelectorAll('.cb-siswa, .cb-admin').forEach(cb => cb.checked = false);
    document.getElementById('checkAllSiswa').checked = false;
    document.getElementById('checkAllAdmin').checked = false;
    updateToolbar('siswa');
    updateToolbar('admin');
}

function submitHapusBanyak() {
    const checked = document.querySelectorAll('.cb-siswa:checked, .cb-admin:checked');
    if (checked.length === 0) {
        alert('Tidak ada data yang dipilih.');
        return;
    }
    if (!confirm('Hapus ' + checked.length + ' anggota yang dipilih?')) return;

    const form = document.createElement('form');
    form.method = 'POST';
    form.action = '{{ route("admin.anggota.hapusBanyak") }}';

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
</script>
@endpush
