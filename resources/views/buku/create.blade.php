@extends('layouts.admin')

@section('title', 'Tambah Buku')
@section('page-title', 'Tambah Buku')

@php
$genreListData = $genres->map(fn($g) => ['id' => $g->id, 'nama' => $g->nama])->toArray();
$penerbitListData = $penerbitList->map(fn($p) => ['id' => $p->id, 'nama' => $p->nama])->toArray();
@endphp

@push('scripts')
<script>
var genreData = @json($genreListData);
var penerbitData = @json($penerbitListData);

function toggleNew(field) {
    const input = document.getElementById(field + 'Baru');
    if (input.style.display === 'none') {
        input.style.display = 'block';
        input.focus();
        selectOption(field, '', '');
        closeDropdown(field);
    } else {
        input.style.display = 'none';
        input.value = '';
    }
}

function closeDropdown(field) {
    const dropdown = document.getElementById(field + 'Dropdown');
    if (dropdown) dropdown.style.display = 'none';
}

function toggleDropdown(field, event) {
    event.stopPropagation();
    const dropdown = document.getElementById(field + 'Dropdown');
    const input = document.getElementById(field + 'Baru');
    if (input && input.style.display !== 'none') return;
    
    document.querySelectorAll('.custom-dropdown-menu').forEach(el => {
        el.style.display = 'none';
    });
    dropdown.style.display = 'block';
}

function selectOption(field, id, nama) {
    const hidden = document.getElementById(field + 'Id');
    const display = document.getElementById(field + 'Display');
    const dropdown = document.getElementById(field + 'Dropdown');
    const input = document.getElementById(field + 'Baru');
    
    hidden.value = id;
    display.querySelector('span').textContent = nama || '-- Pilih / Tambah Baru --';
    display.querySelector('span').classList.toggle('text-muted', !nama);
    dropdown.style.display = 'none';
    
    if (input) {
        input.style.display = 'none';
        input.value = '';
    }
}

function filterDropdown(field, query) {
    const items = document.querySelectorAll('#' + field + 'Dropdown .dropdown-item[data-search]');
    items.forEach(item => {
        const text = item.getAttribute('data-search').toLowerCase();
        item.style.display = text.includes(query.toLowerCase()) ? '' : 'none';
    });
}

document.addEventListener('click', function(e) {
    if (!e.target.closest('.custom-select-wrapper')) {
        document.querySelectorAll('.custom-dropdown-menu').forEach(el => el.style.display = 'none');
    }
});
</script>
@endpush

<x-admin-page-header title="Tambah Buku" icon="bi bi-plus-circle" :backUrl="route('buku.index')" />

<div class="card-admin">
    <div class="card-admin-body" style="position:relative">
        <form action="{{ route('buku.store') }}" method="POST" enctype="multipart/form-data">
            @csrf
            <div class="row">
                <div class="col-md-6 mb-3">
                    <label class="form-label fw-600">Judul</label>
                    <input type="text" name="judul" class="form-control @error('judul') is-invalid @enderror" value="{{ old('judul') }}">
                    @error('judul') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>
                <div class="col-md-6 mb-3">
                    <label class="form-label">Pengarang</label>
                    <input type="text" name="pengarang" class="form-control @error('pengarang') is-invalid @enderror" value="{{ old('pengarang') }}">
                    @error('pengarang') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>
                <div class="col-md-6 mb-3">
                    <label class="form-label">Penerbit</label>
                    <div class="input-group">
                        <div class="custom-select-wrapper" style="flex:1">
                            <input type="hidden" name="penerbit_id" id="penerbitId" value="{{ old('penerbit_id') }}">
                            <div class="form-control custom-select-display @error('penerbit_id') is-invalid @enderror" id="penerbitDisplay" onclick="toggleDropdown('penerbit', event)" style="cursor:pointer;display:flex;align-items:center;justify-content:space-between">
                                <span class="{{ old('penerbit_id') ? '' : 'text-muted' }}">{{ old('penerbit_id') ? $penerbitList->firstWhere('id', old('penerbit_id'))?->nama : '-- Pilih / Tambah Baru --' }}</span>
                                <i class="bi bi-chevron-down" style="font-size:12px"></i>
                            </div>
                            <div class="custom-dropdown-menu" id="penerbitDropdown" style="display:none;position:absolute;top:100%;left:0;z-index:9999;background:white;border:1px solid #ced4da;border-radius:4px;width:100%;max-height:200px;overflow-y:auto;box-shadow:0 4px 6px rgba(0,0,0,0.1)">
                                <div style="padding:8px;border-bottom:1px solid #eee">
                                    <input type="text" class="form-control form-control-sm" placeholder="Cari..." onkeyup="filterDropdown('penerbit', this.value)" style="font-size:12px">
                                </div>
                                @foreach($penerbitList as $p)
                                <div class="dropdown-item" data-search="{{ strtolower($p->nama) }}" onclick="selectOption('penerbit', {{ $p->id }}, '{{ addslashes($p->nama) }}')" style="padding:8px 12px;cursor:pointer;font-size:13px;{{ $p->id == old('penerbit_id') ? 'background:#e9ecef;font-weight:600' : '' }}">{{ $p->nama }}</div>
                                @endforeach
                            </div>
                        </div>
                        <button type="button" class="btn btn-outline-secondary" onclick="toggleNew('penerbit')">+ Baru</button>
                    </div>
                    <input type="text" name="penerbit_baru" id="penerbitBaru" class="form-control mt-2 @error('penerbit_baru') is-invalid @enderror" placeholder="Ketik nama penerbit baru..." style="display:none">
                    @error('penerbit_baru') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>
                <div class="col-md-3 mb-3">
                    <label class="form-label">Tahun Terbit</label>
                    <input type="number" name="tahun_terbit" class="form-control @error('tahun_terbit') is-invalid @enderror" value="{{ old('tahun_terbit') }}">
                    @error('tahun_terbit') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>
                <div class="col-md-3 mb-3">
                    <label class="form-label">Jumlah Eksemplar</label>
                    <input type="number" name="jumlah_eksemplar" class="form-control @error('jumlah_eksemplar') is-invalid @enderror" value="{{ old('jumlah_eksemplar', 1) }}" min="1">
                    <small class="text-muted">Jumlah buku fisik yang dimiliki</small>
                    @error('jumlah_eksemplar') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>
                <div class="col-md-4 mb-3">
                    <label class="form-label">Kode Buku <small class="text-muted">(kosongkan = otomatis)</small></label>
                    <input type="text" name="kode_buku" class="form-control @error('kode_buku') is-invalid @enderror" value="{{ old('kode_buku') }}" placeholder="Contoh: BK001">
                    <small class="text-muted">Kode untuk judul buku ini (bukan eksemplar)</small>
                    @error('kode_buku') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>
                <div class="col-md-4 mb-3">
                    <label class="form-label">ISBN</label>
                    <input type="text" name="isbn" class="form-control @error('isbn') is-invalid @enderror" value="{{ old('isbn') }}">
                    @error('isbn') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>
                <div class="col-md-6 mb-3">
                    <label class="form-label">Genre</label>
                    <div class="input-group">
                        <div class="custom-select-wrapper" style="flex:1;position:relative">
                            <input type="hidden" name="genre_id" id="genreId" value="{{ old('genre_id') }}">
                            <div class="form-control custom-select-display @error('genre_id') is-invalid @enderror" id="genreDisplay" onclick="toggleDropdown('genre', event)" style="cursor:pointer;display:flex;align-items:center;justify-content:space-between">
                                <span class="{{ old('genre_id') ? '' : 'text-muted' }}">{{ old('genre_id') ? $genres->firstWhere('id', old('genre_id'))?->nama : '-- Pilih / Tambah Baru --' }}</span>
                                <i class="bi bi-chevron-down" style="font-size:12px"></i>
                            </div>
                            <div class="custom-dropdown-menu" id="genreDropdown" style="display:none;position:absolute;top:100%;left:0;z-index:9999;background:white;border:1px solid #ced4da;border-radius:4px;width:100%;max-height:200px;overflow-y:auto;box-shadow:0 4px 6px rgba(0,0,0,0.1)">
                                <div style="padding:8px;border-bottom:1px solid #eee">
                                    <input type="text" class="form-control form-control-sm" placeholder="Cari..." onkeyup="filterDropdown('genre', this.value)" style="font-size:12px">
                                </div>
                                @foreach($genres as $g)
                                <div class="dropdown-item" data-search="{{ strtolower($g->nama) }}" onclick="selectOption('genre', {{ $g->id }}, '{{ addslashes($g->nama) }}')" style="padding:8px 12px;cursor:pointer;font-size:13px;{{ $g->id == old('genre_id') ? 'background:#e9ecef;font-weight:600' : '' }}">{{ $g->nama }}</div>
                                @endforeach
                            </div>
                        </div>
                        <button type="button" class="btn btn-outline-secondary" onclick="toggleNew('genre')">+ Baru</button>
                    </div>
                    <input type="text" name="genre_baru" id="genreBaru" class="form-control mt-2 @error('genre_baru') is-invalid @enderror" placeholder="Ketik genre baru..." style="display:none">
                    @error('genre_baru') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>
                <div class="col-md-12 mb-3">
                    <label class="form-label">Deskripsi Buku</label>
                    <textarea name="deskripsi" class="form-control @error('deskripsi') is-invalid @enderror" rows="4" placeholder="Tulis deskripsi singkat...">{{ old('deskripsi') }}</textarea>
                    @error('deskripsi') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>
                <div class="col-md-12 mb-3">
                    <label class="form-label">Sampul Buku</label>
                    <input type="file" name="sampul" class="form-control @error('sampul') is-invalid @enderror" accept="image/*">
                    @error('sampul') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>
                <div class="col-md-12 mb-3">
                    <label class="form-label">Background Rekomendasi <small class="text-muted">(opsional)</small></label>
                    <input type="file" name="rekom_bg" class="form-control @error('rekom_bg') is-invalid @enderror" accept="image/*">
                    <small class="text-muted">Gambar latar belakang untuk banner Rekomendasi Hari Ini. Kosongkan jika tidak ingin menggunakan background kustom.</small>
                    @error('rekom_bg') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>
                <div class="col-md-6 mb-3">
                    <label class="form-label">Lokasi Rak</label>
                    <select name="lokasi" class="form-control @error('lokasi') is-invalid @enderror">
                        <option value="">-- Pilih Lokasi --</option>
                        @foreach(['A1','A2','A3','B1','B2','B3','C1','C2','C3','D1','D2','D3'] as $lok)
                            <option value="{{ $lok }}" {{ old('lokasi') == $lok ? 'selected' : '' }}>Rak {{ $lok }}</option>
                        @endforeach
                    </select>
                    @error('lokasi') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>
            </div>
            <div style="background:#f0df4f;border:1px solid #bbf7d0;border-radius:10px;padding:15px;margin-bottom:20px">
                <p style="font-size:13px;color:#166534;margin:0"><i class="bi bi-info-circle"></i> <strong>Info:</strong> Setiap eksemplar akan otomatis mendapatkan kode unik (BK000001, BK000002, dst) dan QR Code sendiri.</p>
            </div>
            <button type="submit"
                    style="padding:10px 20px;background:linear-gradient(135deg,#1a6e35,#27ae60);color:white;border:none;border-radius:10px;font-size:13px;font-weight:600;cursor:pointer">
                <i class="bi bi-save"></i> Simpan
            </button>
        </form>
    </div>
</div>
@endsection
