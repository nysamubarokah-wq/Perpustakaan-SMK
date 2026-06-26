@extends('layouts.admin')

@section('header_title', 'Edit Anggota')

@section('content')
<div class="card-admin">
    <div class="card-admin-header" style="display:flex;align-items:center;justify-content:space-between">
        <h5 style="margin:0"><i class="bi bi-person-gear" style="color:#1a6e35"></i> Edit Anggota</h5>
        <a href="{{ route('anggota.index') }}" class="btn btn-sm btn-secondary">
            <i class="bi bi-arrow-left"></i> Kembali
        </a>
    </div>
    <div class="card-admin-body" style="padding:25px">
        <form action="{{ route('anggota.update', $anggota->id) }}" method="POST">
            @csrf
            @method('PUT')
            <div class="row">
                <div class="col-md-6 mb-3">
                    <label class="form-label">Nama</label>
                    <input type="text" name="nama" class="form-control @error('nama') is-invalid @enderror" value="{{ old('nama', $anggota->nama) }}">
                    @error('nama') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>
                <div class="col-md-6 mb-3">
                    <label class="form-label">Email</label>
                    <input type="email" name="email" class="form-control @error('email') is-invalid @enderror" value="{{ old('email', $anggota->email) }}">
                    @error('email') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>
                @php $userTerkait = \App\Models\User::where('email', $anggota->email)->first(); @endphp
<div class="col-md-6 mb-3">
    <label class="form-label">NIS</label>
    <input type="text" name="nis" class="form-control @error('nis') is-invalid @enderror" value="{{ old('nis', $userTerkait?->nis) }}" placeholder="Nomor Induk Siswa">
    @error('nis') <div class="invalid-feedback">{{ $message }}</div> @enderror
</div>
                <div class="col-md-6 mb-3">
                    <label class="form-label">No. Telepon</label>
                    <input type="text" name="no_telepon" class="form-control @error('no_telepon') is-invalid @enderror" value="{{ old('no_telepon', $anggota->no_telepon) }}">
                    @error('no_telepon') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>
                <div class="col-md-6 mb-3">
                    <label class="form-label">Tanggal Daftar</label>
                    <input type="date" name="tanggal_daftar" class="form-control @error('tanggal_daftar') is-invalid @enderror" value="{{ old('tanggal_daftar', $anggota->tanggal_daftar) }}">
                    @error('tanggal_daftar') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>
                <div class="col-md-12 mb-3">
                    <label class="form-label">Alamat</label>
                    <textarea name="alamat" class="form-control @error('alamat') is-invalid @enderror" rows="3">{{ old('alamat', $anggota->alamat) }}</textarea>
                    @error('alamat') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>
            </div>
            <button type="submit" class="btn" style="background:linear-gradient(135deg,#1a6e35,#27ae60);color:white;border-radius:10px;padding:10px 30px;font-weight:600">
                <i class="bi bi-save"></i> Update
            </button>
        </form>
    </div>
</div>
@endsection