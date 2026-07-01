@extends('layouts.admin')

@section('header_title', 'Tambah Peminjaman')

@section('content')

<x-admin-page-header title="Tambah Peminjaman" icon="bi bi-plus-circle" :backUrl="route('peminjaman.index')" />

<div class="card-admin">
    <div class="card-admin-body">
        <form action="{{ route('peminjaman.store') }}" method="POST">
            @csrf

            <div class="row">
                <div class="col-md-6 mb-3">
                    <label class="form-label fw-600">Anggota</label>
                    <select name="anggota_id" class="form-control @error('anggota_id') is-invalid @enderror">
                        <option value="">-- Pilih Anggota --</option>
                        @foreach($anggota as $item)
                        <option value="{{ $item->id }}" {{ old('anggota_id') == $item->id ? 'selected' : '' }}>
                            {{ $item->nama }}
                        </option>
                        @endforeach
                    </select>
                    @error('anggota_id') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>

                <div class="col-md-6 mb-3">
                    <label class="form-label fw-600">Buku</label>
                    <select name="buku_id" class="form-control @error('buku_id') is-invalid @enderror">
                        <option value="">-- Pilih Buku --</option>
                        @foreach($buku as $item)
                        <option value="{{ $item->id }}" {{ old('buku_id') == $item->id ? 'selected' : '' }}>
                            {{ $item->judul }} (Eksemplar tersedia: {{ $item->eksemplarTersedia()->count() }})
                        </option>
                        @endforeach
                    </select>
                    @error('buku_id') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>

                <div class="col-md-6 mb-3">
                    <label class="form-label">Tanggal Pinjam</label>
                    <input type="date" name="tanggal_pinjam"
                        class="form-control @error('tanggal_pinjam') is-invalid @enderror"
                        value="{{ old('tanggal_pinjam') }}">
                    @error('tanggal_pinjam') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>

                <div class="col-md-6 mb-3">
                    <label class="form-label">Tanggal Kembali</label>
                    <input type="date" name="tanggal_kembali"
                        class="form-control @error('tanggal_kembali') is-invalid @enderror"
                        value="{{ old('tanggal_kembali') }}">
                    @error('tanggal_kembali') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>

                <div class="col-md-6 mb-3">
                    <label class="form-label">Status</label>
                    <select name="status" class="form-control">
                        <option value="menunggu_konfirmasi">Menunggu Konfirmasi</option>
                        <option value="dipinjam">Dipinjam</option>
                    </select>
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