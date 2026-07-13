@extends('layouts.admin')

@section('header_title', 'Tambah Background')

@section('content')
<x-admin-page-header title="Tambah Background" icon="bi bi-plus-circle" :backUrl="route('background.index')" />

<div class="card-admin">
    <div class="card-admin-body">
        <form action="{{ route('background.store') }}" method="POST" enctype="multipart/form-data">
            @csrf
            <div class="row">
                <div class="col-md-6 mb-3">
                    <label class="form-label fw-600">Nama Background <span style="color:red">*</span></label>
                    <input type="text" name="nama" class="form-control @error('nama') is-invalid @enderror" value="{{ old('nama') }}" required>
                    @error('nama') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>
                <div class="col-md-3 mb-3">
                    <label class="form-label">Harga (coin)</label>
                    <input type="number" name="harga" class="form-control @error('harga') is-invalid @enderror" value="{{ old('harga') }}">
                    @error('harga') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    <small class="text-muted">Warna: &lt;50 • Foto: 50-99 • Video: 100+</small>
                </div>
                <div class="col-md-3 mb-3">
                    <label class="form-label">Tipe</label>
                    <select name="type" id="type" class="form-control @error('type') is-invalid @enderror" onchange="toggleTipe()">
                        <option value="color" {{ old('type') == 'color' ? 'selected' : '' }}>Warna</option>
                        <option value="image" {{ old('type') == 'image' ? 'selected' : '' }}>Foto</option>
                        <option value="video" {{ old('type') == 'video' ? 'selected' : '' }}>Video</option>
                    </select>
                    @error('type') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>

                <div class="col-md-12" id="fieldWarna">
                    <div class="row">
                        <div class="col-md-3 mb-3">
                            <label class="form-label">Warna 1</label>
                            <input type="color" name="warna1" class="form-control" value="{{ old('warna1', '#1a6e35') }}" style="height:45px">
                        </div>
                        <div class="col-md-3 mb-3">
                            <label class="form-label">Warna 2</label>
                            <input type="color" name="warna2" class="form-control" value="{{ old('warna2', '#27ae60') }}" style="height:45px">
                        </div>
                    </div>
                </div>

                <div class="col-md-12 mb-3" id="fieldFile" style="display:none">
                    <label class="form-label">Upload File</label>
                    <input type="file" name="file" class="form-control @error('file') is-invalid @enderror">
                    @error('file') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    <small class="text-muted">Foto: jpg/png/webp maks 5MB. Video: mp4/webm maks 20MB.</small>
                </div>
            </div>
            <button type="submit" class="btn" style="background:linear-gradient(135deg,#1a6e35,#27ae60);color:white;border-radius:10px;padding:10px 30px;font-weight:600">
                <i class="bi bi-save"></i> Simpan
            </button>
        </form>
    </div>
</div>

<script>
function toggleTipe() {
    const type = document.getElementById('type').value;
    document.getElementById('fieldWarna').style.display = type === 'color' ? 'block' : 'none';
    document.getElementById('fieldFile').style.display = type === 'color' ? 'none' : 'block';
}
toggleTipe();
</script>
@endsection