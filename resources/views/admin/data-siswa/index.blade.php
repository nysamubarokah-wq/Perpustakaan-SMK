@extends('layouts.admin')

@section('header_title', 'Kelola Data Siswa')

@section('content')

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

<div class="row g-4">

    {{-- FORM TAMBAH + IMPORT --}}
    <div class="col-12 col-lg-4">

        {{-- Tambah Manual --}}
        <div class="card border-0 shadow-sm mb-4" style="border-radius:15px">
            <div class="card-body p-4">
                <h6 style="font-weight:700;color:#222;margin-bottom:18px">
                    <i class="bi bi-person-plus" style="color:#1a6e35"></i> Tambah Siswa
                </h6>
                <form method="POST" action="{{ route('admin.siswa.store') }}">
                    @csrf
                    <div class="mb-3">
                        <label style="font-size:13px;font-weight:600;color:#444;margin-bottom:5px;display:block">NIS</label>
                        <input type="text" name="nis" value="{{ old('nis') }}"
                            class="form-control @error('nis') is-invalid @enderror"
                            placeholder="Contoh: 20240001" style="border-radius:10px;font-size:13px">
                        @error('nis')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="mb-3">
                        <label style="font-size:13px;font-weight:600;color:#444;margin-bottom:5px;display:block">Nama Lengkap</label>
                        <input type="text" name="nama" value="{{ old('nama') }}"
                            class="form-control @error('nama') is-invalid @enderror"
                            placeholder="Contoh: Anisa Rahmawati" style="border-radius:10px;font-size:13px">
                        @error('nama')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="mb-3">
                        <label style="font-size:13px;font-weight:600;color:#444;margin-bottom:5px;display:block">Kelas <span style="color:#aaa;font-weight:400">(opsional)</span></label>
                        <input type="text" name="kelas" value="{{ old('kelas') }}"
                            class="form-control"
                            placeholder="Contoh: X RPL 1" style="border-radius:10px;font-size:13px">
                    </div>
                    <button type="submit" style="width:100%;padding:10px;background:linear-gradient(135deg,#1a6e35,#27ae60);color:white;border:none;border-radius:10px;font-size:13px;font-weight:600;cursor:pointer">
                        <i class="bi bi-plus-circle"></i> Tambahkan
                    </button>
                </form>
            </div>
        </div>

        {{-- Import CSV --}}
        <div class="card border-0 shadow-sm" style="border-radius:15px">
            <div class="card-body p-4">
                <h6 style="font-weight:700;color:#222;margin-bottom:6px">
                    <i class="bi bi-file-earmark-spreadsheet" style="color:#1a6e35"></i> Import CSV
                </h6>
                <p style="font-size:12px;color:#888;margin-bottom:14px">Format CSV: <code>nis, nama, kelas</code> (baris pertama = header)</p>
                <form method="POST" action="{{ route('admin.siswa.import') }}" enctype="multipart/form-data">
                    @csrf
                    <div class="mb-3">
                        <input type="file" name="file" accept=".csv,.txt"
                            class="form-control @error('file') is-invalid @enderror"
                            style="border-radius:10px;font-size:13px">
                        @error('file')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <button type="submit" style="width:100%;padding:10px;background:#2c3e50;color:white;border:none;border-radius:10px;font-size:13px;font-weight:600;cursor:pointer">
                        <i class="bi bi-upload"></i> Import Sekarang
                    </button>
                </form>
                <div style="margin-top:12px;background:#f8f9fa;border-radius:10px;padding:12px;font-size:12px;color:#666">
                    <strong>Contoh format CSV:</strong><br>
                    <code>nis,nama,kelas</code><br>
                    <code>20240001,Anisa Rahmawati,X RPL 1</code><br>
                    <code>20240002,Budi Santoso,XI TKJ 2</code>
                </div>
            </div>
        </div>

    </div>

    {{-- TABEL DATA SISWA --}}
    <div class="col-12 col-lg-8">
            <div class="card border-0 shadow-sm" style="border-radius:15px">
                <div class="card-body p-0">

                    {{-- Header --}}
                    <div style="padding:20px 25px;border-bottom:1px solid #eee;display:flex;align-items:center;justify-content:space-between;flex-wrap:wrap;gap:10px">
    <h6 style="font-weight:700;color:#222;margin:0">
        <i class="bi bi-people" style="color:#1a6e35"></i> Daftar Siswa
        <span style="font-size:12px;color:#888;font-weight:400">({{ $siswa->total() }} siswa)</span>
    </h6>
    <div style="display:flex;gap:8px">
        <input type="text" id="searchInput" value="{{ request('search') }}"
            placeholder="Cari NIS / nama / kelas..."
            style="padding:7px 14px;border:1.5px solid #eee;border-radius:10px;font-size:12px;outline:none;width:200px"
            onkeydown="if(event.key==='Enter'){cariSiswa()}">
        <button type="button" onclick="cariSiswa()" style="padding:7px 14px;background:#1a6e35;color:white;border:none;border-radius:10px;font-size:12px;cursor:pointer">
            <i class="bi bi-search"></i>
        </button>
    </div>
</div>

                    {{-- Toolbar hapus banyak --}}
                   {{-- Toolbar hapus banyak --}}
<div id="toolbarHapus" style="display:none;padding:10px 20px;background:#fff3cd;border-bottom:1px solid #eee;align-items:center;justify-content:space-between;gap:10px;flex-wrap:wrap">
    <span id="jumlahDipilih" style="font-size:13px;font-weight:600;color:#856404">
        0 data dipilih
    </span>
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
                        <table class="table table-hover mb-0" style="font-size:13px">
                            <thead style="background:#f8f9fa">
                                <tr>
                                    <th style="padding:12px 15px;font-weight:600;color:#555;border:none;width:40px">
                                        <input type="checkbox" id="checkAll" onchange="toggleCheckAll(this)"
                                            style="width:15px;height:15px;cursor:pointer">
                                    </th>
                                    <th style="padding:12px 15px;font-weight:600;color:#555;border:none">#</th>
                                    <th style="padding:12px 15px;font-weight:600;color:#555;border:none">NIS</th>
                                    <th style="padding:12px 15px;font-weight:600;color:#555;border:none">Nama</th>
                                    <th style="padding:12px 15px;font-weight:600;color:#555;border:none">Kelas</th>
                                    <th style="padding:12px 15px;font-weight:600;color:#555;border:none">Status</th>
                                    <th style="padding:12px 15px;font-weight:600;color:#555;border:none">Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($siswa as $index => $s)
                                @php
                                    $sudahDaftar = \App\Models\User::where('nis', $s->nis)->exists();
                                @endphp
                                <tr>
                                    <td style="padding:12px 15px;vertical-align:middle">
                                        @if(!$sudahDaftar)
                                        <input type="checkbox" name="ids[]" value="{{ $s->id }}"
                                            class="checkbox-siswa"
                                            onchange="updateToolbar()"
                                            style="width:15px;height:15px;cursor:pointer">
                                        @endif
                                    </td>
                                    <td style="padding:12px 15px;vertical-align:middle;color:#888">{{ $siswa->firstItem() + $index }}</td>
                                    <td style="padding:12px 15px;vertical-align:middle">
                                        <span style="background:#e8f5e9;color:#1a6e35;padding:3px 10px;border-radius:20px;font-size:11px;font-weight:600">{{ $s->nis }}</span>
                                    </td>
                                    <td style="padding:12px 15px;vertical-align:middle;font-weight:600;color:#222">{{ $s->nama }}</td>
                                    <td style="padding:12px 15px;vertical-align:middle;color:#666">{{ $s->kelas ?? '-' }}</td>
                                    <td style="padding:12px 15px;vertical-align:middle">
                                      @if($sudahDaftar)
    <span style="background:#d4edda;color:#1a6e35;padding:3px 10px;border-radius:20px;font-size:11px;font-weight:600;white-space:nowrap">
        <i class="bi bi-check-circle"></i> Sudah Daftar
    </span>
@else
    <span style="background:#fff3cd;color:#856404;padding:3px 10px;border-radius:20px;font-size:11px;font-weight:600;white-space:nowrap">
        <i class="bi bi-clock"></i> Belum Daftar
    </span>
@endif
                                    </td>
                                    <td style="padding:12px 15px;vertical-align:middle">
                                        @if(!$sudahDaftar)
                                        <form method="POST" action="{{ route('admin.siswa.destroy', $s->id) }}"
                                            onsubmit="return confirm('Hapus data siswa {{ $s->nama }}?')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" style="padding:5px 12px;background:#e74c3c;color:white;border:none;border-radius:8px;font-size:12px;cursor:pointer">
                                                <i class="bi bi-trash"></i>
                                            </button>
                                        </form>
                                        @else
                                            <span style="font-size:11px;color:#aaa">Tidak bisa dihapus</span>
                                        @endif
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="7" style="text-align:center;padding:40px;color:#aaa">
                                        <i class="bi bi-people" style="font-size:40px;display:block;margin-bottom:10px"></i>
                                        <p style="font-size:13px">Belum ada data siswa.</p>
                                    </td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    @if($siswa->hasPages())
                    <div style="padding:15px 20px;border-top:1px solid #eee">
                        {{ $siswa->appends(request()->query())->links() }}
                    </div>
                    @endif

                </div>
            </div>
        </form>
    </div>

</div>

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
.pagination .page-link:hover {
    background: #e8f5e9;
    color: #1a6e35;
}
</style>
<script>
function updateToolbar() {
    const checked = document.querySelectorAll('.checkbox-siswa:checked');
    const toolbar = document.getElementById('toolbarHapus');
    const jumlah = document.getElementById('jumlahDipilih');

    if (checked.length > 0) {
        toolbar.style.display = 'flex';
        jumlah.textContent = checked.length + ' data dipilih';
    } else {
        toolbar.style.display = 'none';
    }

    // update state checkAll
    const semua = document.querySelectorAll('.checkbox-siswa');
    document.getElementById('checkAll').checked = checked.length === semua.length && semua.length > 0;
}

function toggleCheckAll(el) {
    document.querySelectorAll('.checkbox-siswa').forEach(cb => cb.checked = el.checked);
    updateToolbar();
}

function pilihSemua() {
    document.querySelectorAll('.checkbox-siswa').forEach(cb => cb.checked = true);
    updateToolbar();
}
function cariSiswa() {
    const keyword = document.getElementById('searchInput').value;
    window.location.href = '{{ route('admin.siswa.index') }}?search=' + encodeURIComponent(keyword);
}
function submitHapusBanyak() {
    const checked = document.querySelectorAll('.checkbox-siswa:checked');
    if (checked.length === 0) {
        alert('Tidak ada data yang dipilih.');
        return;
    }
    if (!confirm('Hapus ' + checked.length + ' data yang dipilih?')) return;

    const form = document.createElement('form');
    form.method = 'POST';
    form.action = '{{ route("admin.siswa.hapusBanyak") }}';

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
@endsection