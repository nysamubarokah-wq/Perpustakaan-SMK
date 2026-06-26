@extends('layouts.admin')

@section('header_title', 'Tambah Peminjaman')

@section('content')

<div class="card border-0 shadow-sm" style="border-radius:15px;max-width:700px">
    <div class="card-body p-4">
        <div class="d-flex align-items-center gap-2 mb-4">
            <i class="bi bi-plus-circle fs-4 text-success"></i>
            <h5 class="fw-bold m-0" style="color:#222">Tambah Peminjaman Baru</h5>
        </div>

        <form action="{{ route('peminjaman.store') }}" method="POST">
            @csrf

            <div class="mb-3">
                <label style="font-size:13px;font-weight:600;color:#444;margin-bottom:6px;display:block">Anggota</label>
                <select name="anggota_id" class="form-select @error('anggota_id') is-invalid @enderror" style="border-radius:10px;font-size:13px">
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
                <label style="font-size:13px;font-weight:600;color:#444;margin-bottom:6px;display:block">Buku</label>
                <select name="buku_id" class="form-select @error('buku_id') is-invalid @enderror" style="border-radius:10px;font-size:13px">
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
                    <label style="font-size:13px;font-weight:600;color:#444;margin-bottom:6px;display:block">Tanggal Pinjam</label>
                    <input type="date" name="tanggal_pinjam"
                        class="form-control @error('tanggal_pinjam') is-invalid @enderror"
                        style="border-radius:10px;font-size:13px"
                        value="{{ old('tanggal_pinjam') }}">
                    @error('tanggal_pinjam') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>
                <div class="col-md-6 mb-3">
                    <label style="font-size:13px;font-weight:600;color:#444;margin-bottom:6px;display:block">Tanggal Kembali</label>
                    <input type="date" name="tanggal_kembali"
                        class="form-control @error('tanggal_kembali') is-invalid @enderror"
                        style="border-radius:10px;font-size:13px"
                        value="{{ old('tanggal_kembali') }}">
                    @error('tanggal_kembali') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>
            </div>

            <div class="mb-4">
                <label style="font-size:13px;font-weight:600;color:#444;margin-bottom:6px;display:block">Status</label>
                <select name="status" class="form-select" style="border-radius:10px;font-size:13px">
                    <option value="menunggu_konfirmasi">Menunggu Konfirmasi</option>
                    <option value="dipinjam">Dipinjam</option>
                </select>
            </div>

            <div style="display:flex;gap:10px">
                <button type="submit" style="padding:10px 25px;background:linear-gradient(135deg,#1a6e35,#27ae60);color:white;border:none;border-radius:10px;font-size:13px;font-weight:600;cursor:pointer">
                    <i class="bi bi-save"></i> Simpan
                </button>
                <a href="{{ route('peminjaman.index') }}" style="padding:10px 25px;background:#f8f9fa;color:#555;border:none;border-radius:10px;font-size:13px;font-weight:600;text-decoration:none;display:inline-flex;align-items:center;gap:5px">
                    <i class="bi bi-arrow-left"></i> Kembali
                </a>
            </div>
        </form>
    </div>
</div>

@endsection