@extends('layouts.admin')

@section('header_title', 'Edit Peminjaman')

@section('content')

<x-admin-page-header title="Edit Peminjaman" icon="bi bi-pencil-square" :backUrl="route('peminjaman.index')" />

<div class="card-admin">
    <div class="card-admin-body">
        <form action="{{ route('peminjaman.update', $peminjaman->id) }}" method="POST">
            @csrf
            @method('PUT')

            <div class="row">
                <div class="col-md-6 mb-3">
                    <label class="form-label fw-600">Anggota <span style="color:red">*</span></label>
                    <select name="anggota_id" class="form-control @error('anggota_id') is-invalid @enderror" required>
                        <option value="">-- Pilih Anggota --</option>
                        @foreach($anggota as $item)
                        <option value="{{ $item->id }}" {{ old('anggota_id', $peminjaman->anggota_id) == $item->id ? 'selected' : '' }}>
                            {{ $item->nama }}
                        </option>
                        @endforeach
                    </select>
                    @error('anggota_id') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>

                <div class="col-md-6 mb-3">
                    <label class="form-label fw-600">Buku <span style="color:red">*</span></label>
                    <select name="buku_id" class="form-control @error('buku_id') is-invalid @enderror" required>
                        <option value="">-- Pilih Buku --</option>
                        @foreach($buku as $item)
                        <option value="{{ $item->id }}" {{ old('buku_id', $peminjaman->buku_id) == $item->id ? 'selected' : '' }}>
                            {{ $item->judul }} (Stok: {{ $item->stok }})
                        </option>
                        @endforeach
                    </select>
                    @error('buku_id') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>

                <div class="col-md-6 mb-3">
                    <label class="form-label fw-600">Tanggal Pinjam <span style="color:red">*</span></label>
                    <input type="date" name="tanggal_pinjam"
                        class="form-control @error('tanggal_pinjam') is-invalid @enderror"
                        value="{{ old('tanggal_pinjam', $peminjaman->tanggal_pinjam) }}" required>
                    @error('tanggal_pinjam') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>

                <div class="col-md-6 mb-3">
                    <label class="form-label fw-600">Tanggal Kembali <span style="color:red">*</span></label>
                    <input type="date" name="tanggal_kembali"
                        class="form-control @error('tanggal_kembali') is-invalid @enderror"
                        value="{{ old('tanggal_kembali', $peminjaman->tanggal_kembali) }}" required>
                    @error('tanggal_kembali') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>

                <div class="col-md-6 mb-3">
                    <label class="form-label">Status</label>
                    <select name="status" class="form-control @error('status') is-invalid @enderror">
                        <option value="menunggu_konfirmasi" {{ old('status', $peminjaman->status) == 'menunggu_konfirmasi' ? 'selected' : '' }}>Menunggu Konfirmasi</option>
                        <option value="dipinjam" {{ old('status', $peminjaman->status) == 'dipinjam' ? 'selected' : '' }}>Dipinjam</option>
                        <option value="dikembalikan" {{ old('status', $peminjaman->status) == 'dikembalikan' ? 'selected' : '' }}>Dikembalikan</option>
                    </select>
                    @error('status') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>
            </div>

            <button type="submit"
                    style="padding:10px 20px;background:linear-gradient(135deg,#1a6e35,#27ae60);color:white;border:none;border-radius:10px;font-size:13px;font-weight:600;cursor:pointer">
                <i class="bi bi-save"></i> Update
            </button>
        </form>
    </div>
</div>

@endsection