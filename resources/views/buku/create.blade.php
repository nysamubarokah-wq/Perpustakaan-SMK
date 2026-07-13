@extends('layouts.admin')

@section('title', 'Tambah Buku')
@section('page-title', 'Tambah Buku')

@php
$genreListData = $genres->map(fn($g) => ['id' => $g->id, 'nama' => $g->nama])->toArray();
$penerbitListData = $penerbitList->map(fn($p) => ['id' => $p->id, 'nama' => $p->nama])->toArray();
@endphp

@push('scripts')
<script>
function toggleNew(field) {
    const input = document.getElementById(field + 'Baru');
    if (input.style.display === 'none') {
        input.style.display = 'block';
        input.focus();
    } else {
        input.style.display = 'none';
        input.value = '';
    }
}
</script>
@endpush

@section('content')
<x-admin-page-header title="Tambah Buku" icon="bi bi-plus-circle" :backUrl="route('buku.index')" />

<div class="card-admin">
    <div class="card-admin-body" style="position:relative">
        <form action="{{ route('buku.store') }}" method="POST" enctype="multipart/form-data">
            @csrf
            <div class="row">
                <div class="col-md-6 mb-3">
                    <label class="form-label fw-600">Judul <span style="color:red">*</span></label>
                    <input type="text" name="judul" class="form-control @error('judul') is-invalid @enderror" value="{{ old('judul') }}" required>
                    @error('judul') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>
                <div class="col-md-6 mb-3">
                    <label class="form-label fw-600">Pengarang <span style="color:red">*</span></label>
                    <input type="text" name="pengarang" class="form-control @error('pengarang') is-invalid @enderror" value="{{ old('pengarang') }}" required>
                    @error('pengarang') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>
                <div class="col-md-6 mb-3">
                    <label class="form-label fw-600">Penerbit <span style="color:red">*</span></label>
                    <div class="input-group">
                        <select name="penerbit_id" class="form-control @error('penerbit_id') is-invalid @enderror">
                            <option value="">-- Pilih --</option>
                            @foreach($penerbitList as $p)
                                <option value="{{ $p->id }}" {{ old('penerbit_id') == $p->id ? 'selected' : '' }}>{{ $p->nama }}</option>
                            @endforeach
                        </select>
                        <button type="button" class="btn btn-outline-secondary" onclick="toggleNew('penerbit')">+ Baru</button>
                    </div>
                    <input type="text" name="penerbit_baru" id="penerbitBaru" class="form-control mt-2 @error('penerbit_baru') is-invalid @enderror" placeholder="Ketik nama penerbit baru..." style="display:none">
                    @error('penerbit_baru') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>
                <div class="col-md-3 mb-3">
                    <label class="form-label fw-600">Tahun Terbit <span style="color:red">*</span></label>
                    <input type="number" name="tahun_terbit" class="form-control @error('tahun_terbit') is-invalid @enderror" value="{{ old('tahun_terbit') }}" required>
                    @error('tahun_terbit') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>
                <div class="col-md-3 mb-3">
                    <label class="form-label fw-600">Jumlah Eksemplar <span style="color:red">*</span></label>
                    <input type="number" name="jumlah_eksemplar" class="form-control @error('jumlah_eksemplar') is-invalid @enderror" value="{{ old('jumlah_eksemplar', 1) }}" min="1" required>
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
                    <label class="form-label fw-600">ISBN <span style="color:red">*</span></label>
                    <input type="text" name="isbn" class="form-control @error('isbn') is-invalid @enderror" value="{{ old('isbn') }}" required>
                    @error('isbn') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>
                <div class="col-md-6 mb-3">
                    <label class="form-label">Genre</label>
                    <div class="input-group">
                        <select name="genre_id" class="form-control @error('genre_id') is-invalid @enderror">
                            <option value="">-- Pilih --</option>
                            @foreach($genres as $g)
                                <option value="{{ $g->id }}" {{ old('genre_id') == $g->id ? 'selected' : '' }}>{{ $g->nama }}</option>
                            @endforeach
                        </select>
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
            <button type="submit"
                    style="padding:10px 20px;background:linear-gradient(135deg,#1a6e35,#27ae60);color:white;border:none;border-radius:10px;font-size:13px;font-weight:600;cursor:pointer">
                <i class="bi bi-save"></i> Simpan
            </button>
        </form>
    </div>
</div>
@endsection
