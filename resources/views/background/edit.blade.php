@extends('layouts.admin')

@section('header_title', 'Edit Background')

@section('content')
<div class="card-admin">
    <div class="card-admin-header">
        <h5><i class="bi bi-pencil" style="color:#1a6e35"></i> Edit Background</h5>
        <a href="{{ route('background.index') }}" class="btn btn-sm btn-secondary">
            <i class="bi bi-arrow-left"></i> Kembali
        </a>
    </div>
    <div class="card-admin-body">
        <form action="{{ route('background.update', $background->id) }}" method="POST" enctype="multipart/form-data">
            @csrf
            @method('PUT')
            <div class="row">
                <div class="col-md-6 mb-3">
                    <label class="form-label">Nama Background</label>
                    <input type="text" name="nama" class="form-control @error('nama') is-invalid @enderror" value="{{ old('nama', $background->nama) }}">
                    @error('nama') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>
                <div class="col-md-3 mb-3">
                    <label class="form-label">Harga (coin)</label>
                    <input type="number" name="harga" class="form-control @error('harga') is-invalid @enderror" value="{{ old('harga', $background->harga) }}">
                    @error('harga') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>
                <div class="col-md-3 mb-3">
                    <label class="form-label">Tipe</label>
                    <select name="type" id="type" class="form-control @error('type') is-invalid @enderror" onchange="toggleTipe()">
                        <option value="color" {{ old('type', $background->type) == 'color' ? 'selected' : '' }}>Warna</option>
                        <option value="image" {{ old('type', $background->type) == 'image' ? 'selected' : '' }}>Foto</option>
                        <option value="video" {{ old('type', $background->type) == 'video' ? 'selected' : '' }}>Video</option>
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
                    <label class="form-label">Upload File Baru (opsional, kosongkan kalau ga ganti)</label>
                    <input type="file" name="file" class="form-control @error('file') is-invalid @enderror">
                    @error('file') <div class="invalid-feedback">{{ $message }}</div> @enderror

                    @if($background->type !== 'color')
                        <div class="mt-2" style="width:120px;height:70px;border-radius:8px;overflow:hidden">
                            @if($background->type === 'image')
                                <img src="{{ asset($background->value) }}" style="width:100%;height:100%;object-fit:cover">
                            @else
                                <video muted controls style="width:100%;height:100%;object-fit:cover">
                                    <source src="{{ asset($background->value) }}">
                                </video>
                            @endif
                        </div>
                    @endif
                </div>
            </div>
            <button type="submit" class="btn" style="background:linear-gradient(135deg,#1a6e35,#27ae60);color:white;border-radius:10px;padding:10px 30px;font-weight:600">
                <i class="bi bi-save"></i> Update
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