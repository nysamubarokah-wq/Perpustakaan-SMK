@extends('layouts.admin')

@section('title', 'Edit Buku')
@section('page-title', 'Edit Buku')

@section('content')
<div class="card-admin">
    <div class="card-admin-header">
        <h5><i class="bi bi-pencil-square" style="color:#1a6e35"></i> Edit Buku</h5>
        <a href="{{ route('buku.index') }}" class="btn btn-sm btn-secondary">
            <i class="bi bi-arrow-left"></i> Kembali
        </a>
    </div>
    <div class="card-admin-body">
        <form action="{{ route('buku.update', $buku->id) }}" method="POST" enctype="multipart/form-data">
            @csrf
            @method('PUT')
            <div class="row">
                <div class="col-md-6 mb-3">
                    <label class="form-label">Judul</label>
                    <input type="text" name="judul" class="form-control @error('judul') is-invalid @enderror" value="{{ old('judul', $buku->judul) }}">
                    @error('judul') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>
                <div class="col-md-6 mb-3">
                    <label class="form-label">Pengarang</label>
                    <input type="text" name="pengarang" class="form-control @error('pengarang') is-invalid @enderror" value="{{ old('pengarang', $buku->pengarang) }}">
                    @error('pengarang') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>
                <div class="col-md-6 mb-3">
                    <label class="form-label">Penerbit</label>
                    <input type="text" name="penerbit" class="form-control @error('penerbit') is-invalid @enderror" value="{{ old('penerbit', $buku->penerbit) }}">
                    @error('penerbit') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>
                <div class="col-md-3 mb-3">
                    <label class="form-label">Tahun Terbit</label>
                    <input type="number" name="tahun_terbit" class="form-control @error('tahun_terbit') is-invalid @enderror" value="{{ old('tahun_terbit', $buku->tahun_terbit) }}">
                    @error('tahun_terbit') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>
                <div class="col-md-3 mb-3">
                    <label class="form-label">Stok</label>
                    <input type="number" name="stok" class="form-control @error('stok') is-invalid @enderror" value="{{ old('stok', $buku->stok) }}">
                    @error('stok') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>
                <div class="col-md-6 mb-3">
                    <label class="form-label">ISBN</label>
                    <input type="text" name="isbn" class="form-control @error('isbn') is-invalid @enderror" value="{{ old('isbn', $buku->isbn) }}">
                    @error('isbn') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>
                <div class="col-md-6 mb-3">
                    <label class="form-label">Genre</label>
                    <select name="genre" class="form-control @error('genre') is-invalid @enderror">
                        <option value="">-- Pilih Genre --</option>
                        @foreach(['Fiksi','Non-Fiksi','Kejuruan','Sains & Teknologi','Sejarah','Romance','Pendidikan','Seni & Budaya'] as $g)
                            <option value="{{ $g }}" {{ old('genre', $buku->genre) == $g ? 'selected' : '' }}>{{ $g }}</option>
                        @endforeach
                    </select>
                    @error('genre') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>
                <div class="col-md-12 mb-3">
                    <label class="form-label">Deskripsi Buku</label>
                    <textarea name="deskripsi" class="form-control @error('deskripsi') is-invalid @enderror" rows="4">{{ old('deskripsi', $buku->deskripsi) }}</textarea>
                    @error('deskripsi') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>
                <div class="col-md-12 mb-3">
                    <label class="form-label">Sampul Buku</label>
                    @if($buku->sampul)
                        <div class="mb-2">
                            <img src="{{ asset($buku->sampul) }}" style="height:100px;border-radius:8px;object-fit:cover">
                            <p style="font-size:12px;color:#888;margin-top:5px">Sampul saat ini</p>
                        </div>
                    @endif
                    <input type="file" name="sampul" class="form-control @error('sampul') is-invalid @enderror" accept="image/*">
                    <small class="text-muted">Kosongkan jika tidak ingin mengubah sampul</small>
                    @error('sampul') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>
            </div>
            <div class="col-md-6 mb-3">
    <label class="form-label">Lokasi Rak</label>
    <select name="lokasi" class="form-control @error('lokasi') is-invalid @enderror">
        <option value="">-- Pilih Lokasi --</option>
        @foreach(['A1','A2','A3','B1','B2','B3','C1','C2','C3','D1','D2','D3'] as $lok)
            <option value="{{ $lok }}" {{ old('lokasi', $buku->lokasi) == $lok ? 'selected' : '' }}>Rak {{ $lok }}</option>
        @endforeach
    </select>
    @error('lokasi') <div class="invalid-feedback">{{ $message }}</div> @enderror
</div>
            <button type="submit" class="btn" style="background:linear-gradient(135deg,#1a6e35,#27ae60);color:white;border-radius:10px;padding:10px 30px;font-weight:600">
                <i class="bi bi-save"></i> Update
            </button>
        </form>
    </div>
</div>
@endsection