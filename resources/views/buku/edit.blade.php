@extends('layouts.admin')

@section('title', 'Edit Buku')
@section('page-title', 'Edit Buku')

@php
$backParams = request()->only(['search', 'sort', 'direction', 'per_page']);
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
<x-admin-page-header title="Edit Buku" icon="bi bi-pencil-square" :backUrl="route('buku.index', $backParams)" />

<div class="card-admin">
    <div class="card-admin-body" style="position:relative">
        <form action="{{ route('buku.update', $buku->id) }}" method="POST" enctype="multipart/form-data">
            @csrf
            @method('PUT')
            <input type="hidden" name="search" value="{{ request('search') }}">
            <input type="hidden" name="sort" value="{{ request('sort') }}">
            <input type="hidden" name="direction" value="{{ request('direction') }}">
            <input type="hidden" name="per_page" value="{{ request('per_page') }}">
            <div class="row">
                <div class="col-md-6 mb-3">
                    <label class="form-label fw-600">Judul <span style="color:red">*</span></label>
                    <input type="text" name="judul" class="form-control @error('judul') is-invalid @enderror" value="{{ old('judul', $buku->judul) }}" required>
                    @error('judul') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>
                <div class="col-md-6 mb-3">
                    <label class="form-label fw-600">Pengarang <span style="color:red">*</span></label>
                    <input type="text" name="pengarang" class="form-control @error('pengarang') is-invalid @enderror" value="{{ old('pengarang', $buku->pengarang) }}" required>
                    @error('pengarang') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>
                <div class="col-md-6 mb-3">
                    <label class="form-label fw-600">Penerbit <span style="color:red">*</span></label>
                    <div class="input-group">
                        <select name="penerbit_id" class="form-control @error('penerbit_id') is-invalid @enderror">
                            <option value="">-- Pilih --</option>
                            @foreach($penerbitList as $p)
                                <option value="{{ $p->id }}" {{ old('penerbit_id', $buku->penerbit_id) == $p->id ? 'selected' : '' }}>{{ $p->nama }}</option>
                            @endforeach
                        </select>
                        <button type="button" class="btn btn-outline-secondary" onclick="toggleNew('penerbit')">+ Baru</button>
                    </div>
                    <input type="text" name="penerbit_baru" id="penerbitBaru" class="form-control mt-2" placeholder="Ketik nama penerbit baru..." style="display:none">
                </div>
                <div class="col-md-3 mb-3">
                    <label class="form-label fw-600">Tahun Terbit <span style="color:red">*</span></label>
                    <input type="number" name="tahun_terbit" class="form-control @error('tahun_terbit') is-invalid @enderror" value="{{ old('tahun_terbit', $buku->tahun_terbit) }}" required>
                    @error('tahun_terbit') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>
                <div class="col-md-3 mb-3">
                    <label class="form-label">Eksemplar</label>
                    <div style="padding:8px 12px;background:#f8f9fa;border-radius:8px;font-size:14px">
                        <strong>{{ $buku->eksemplar()->count() }}</strong> total &middot;
                        <span style="color:#1a6e35">{{ $buku->eksemplarTersedia()->count() }} tersedia</span>
                    </div>
                    <a href="{{ route('buku.show', $buku->id) }}" style="font-size:11px;color:#2563eb">Kelola Eksemplar &rarr;</a>
                </div>
                <div class="col-md-4 mb-3">
                    <label class="form-label">Kode Buku</label>
                    <input type="text" name="kode_buku" class="form-control @error('kode_buku') is-invalid @enderror" value="{{ old('kode_buku', $buku->kode_buku) }}">
                    @error('kode_buku') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>
                <div class="col-md-4 mb-3">
                    <label class="form-label fw-600">ISBN <span style="color:red">*</span></label>
                    <input type="text" name="isbn" class="form-control @error('isbn') is-invalid @enderror" value="{{ old('isbn', $buku->isbn) }}" required>
                    @error('isbn') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>
                <div class="col-md-6 mb-3">
                    <label class="form-label">Genre</label>
                    <div class="input-group">
                        <select name="genre_id" class="form-control @error('genre_id') is-invalid @enderror">
                            <option value="">-- Pilih --</option>
                            @foreach($genres as $g)
                                <option value="{{ $g->id }}" {{ old('genre_id', $buku->genre_id) == $g->id ? 'selected' : '' }}>{{ $g->nama }}</option>
                            @endforeach
                        </select>
                        <button type="button" class="btn btn-outline-secondary" onclick="toggleNew('genre')">+ Baru</button>
                    </div>
                    <input type="text" name="genre_baru" id="genreBaru" class="form-control mt-2" placeholder="Ketik genre baru..." style="display:none">
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
                <div class="col-md-12 mb-3">
                    <label class="form-label">Background Rekomendasi <small class="text-muted">(opsional)</small></label>
                    @if($buku->rekom_bg)
                        <div class="mb-2">
                            <img src="{{ asset($buku->rekom_bg) }}" style="height:80px;border-radius:8px;object-fit:cover">
                            <p style="font-size:12px;color:#888;margin-top:5px">Background saat ini</p>
                        </div>
                    @endif
                    <input type="file" name="rekom_bg" class="form-control @error('rekom_bg') is-invalid @enderror" accept="image/*">
                    <small class="text-muted">Kosongkan jika tidak ingin mengubah background rekomendasi</small>
                    @error('rekom_bg') <div class="invalid-feedback">{{ $message }}</div> @enderror
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
            </div>
            <button type="submit"
                    style="padding:10px 20px;background:linear-gradient(135deg,#1a6e35,#27ae60);color:white;border:none;border-radius:10px;font-size:13px;font-weight:600;cursor:pointer">
                <i class="bi bi-save"></i> Update
            </button>
        </form>
    </div>
</div>
@endsection
