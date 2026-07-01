@extends('layouts.admin')

@section('title', 'Tambah Buku')
@section('page-title', 'Tambah Buku')

@section('content')
<x-admin-page-header title="Tambah Buku" icon="bi bi-plus-circle" :backUrl="route('buku.index')" />

<div class="card-admin">
    <div class="card-admin-body">
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
                    <input type="text" name="penerbit" class="form-control @error('penerbit') is-invalid @enderror" value="{{ old('penerbit') }}">
                    @error('penerbit') <div class="invalid-feedback">{{ $message }}</div> @enderror
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
                    <select name="genre" class="form-control @error('genre') is-invalid @enderror">
                        <option value="">-- Pilih Genre --</option>
                        @foreach(['Fiksi','Non-Fiksi','Kejuruan','Sains & Teknologi','Sejarah','Romance','Pendidikan','Seni & Budaya'] as $g)
                            <option value="{{ $g }}" {{ old('genre') == $g ? 'selected' : '' }}>{{ $g }}</option>
                        @endforeach
                    </select>
                    @error('genre') <div class="invalid-feedback">{{ $message }}</div> @enderror
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
            <div style="background:#f0fdf4;border:1px solid #bbf7d0;border-radius:10px;padding:15px;margin-bottom:20px">
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
