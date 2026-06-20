@extends('layouts.app')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-3">
    <h2><i class="bi bi-plus-circle"></i> Tambah Peminjaman</h2>
    <a href="{{ route('peminjaman.index') }}" class="btn btn-secondary">
        <i class="bi bi-arrow-left"></i> Kembali
    </a>
</div>

<div class="card">
    <div class="card-body">
        <form action="{{ route('peminjaman.store') }}" method="POST">
            @csrf
            <div class="mb-3">
                <label class="form-label">Anggota</label>
                <select name="anggota_id" class="form-select @error('anggota_id') is-invalid @enderror">
                    <option value="">-- Pilih Anggota --</option>
                    @foreach($anggota as $item)
                    <option value="{{ $item->id }}" {{ old('anggota_id') == $item->id ? 'selected' : '' }}>
                        {{ $item->nama }}
                    </option>
                    @endforeach
                </select>
                @error('anggota_id') <div class="invalid-feedback">{{ $message }}</div> @enderror
            </div>
            <div class="mb-3">
                <label class="form-label">Buku</label>
                <select name="buku_id" class="form-select @error('buku_id') is-invalid @enderror">
                    <option value="">-- Pilih Buku --</option>
                    @foreach($buku as $item)
                    <option value="{{ $item->id }}" {{ old('buku_id') == $item->id ? 'selected' : '' }}>
                        {{ $item->judul }} (Stok: {{ $item->stok }})
                    </option>
                    @endforeach
                </select>
                @error('buku_id') <div class="invalid-feedback">{{ $message }}</div> @enderror
            </div>
            <div class="row">
                <div class="col-md-6 mb-3">
                    <label class="form-label">Tanggal Pinjam</label>
                    <input type="date" name="tanggal_pinjam" class="form-control @error('tanggal_pinjam') is-invalid @enderror" value="{{ old('tanggal_pinjam') }}">
                    @error('tanggal_pinjam') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>
                <div class="col-md-6 mb-3">
                    <label class="form-label">Tanggal Kembali</label>
                    <input type="date" name="tanggal_kembali" class="form-control @error('tanggal_kembali') is-invalid @enderror" value="{{ old('tanggal_kembali') }}">
                    @error('tanggal_kembali') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>
            </div>
            <button type="submit" class="btn btn-primary">
                <i class="bi bi-save"></i> Simpan
            </button>
        </form>
    </div>
</div>
@endsection