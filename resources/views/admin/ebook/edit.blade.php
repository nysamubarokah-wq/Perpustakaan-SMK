@extends('layouts.admin')

@section('title', 'Edit E-book')
@section('page-title', 'Edit E-book')

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

function previewCover(input) {
    const preview = document.getElementById('coverPreview');
    if (input.files && input.files[0]) {
        const reader = new FileReader();
        reader.onload = e => {
            preview.src = e.target.result;
            preview.style.display = 'block';
        };
        reader.readAsDataURL(input.files[0]);
    }
}

function toggleKoin(val) {
    document.getElementById('koinField').style.display = val == 1 ? 'none' : 'block';
}

toggleKoin('{{ old('is_vip', $ebook->is_vip) }}');
</script>
@endpush

@section('content')
<x-admin-page-header title="Edit E-book" icon="bi bi-pencil-square" :backUrl="route('admin.ebook.index')" />

<div class="card-admin">
    <div class="card-admin-body">
        <form action="{{ route('admin.ebook.update', $ebook->id) }}" method="POST" enctype="multipart/form-data">
            @csrf
            @method('PUT')
            <div class="row">
                <div class="col-md-6 mb-3">
                    <label class="form-label">Judul E-book</label>
                    <input type="text" name="judul" class="form-control @error('judul') is-invalid @enderror" value="{{ old('judul', $ebook->judul) }}">
                    @error('judul') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>
                <div class="col-md-6 mb-3">
                    <label class="form-label">Penulis</label>
                    <input type="text" name="penulis" class="form-control @error('penulis') is-invalid @enderror" value="{{ old('penulis', $ebook->penulis) }}">
                    @error('penulis') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>
                <div class="col-md-6 mb-3">
                    <label class="form-label">Tipe Akses</label>
                    <select name="is_vip" id="tipeAkses" class="form-control" onchange="toggleKoin(this.value)">
                        <option value="0" {{ old('is_vip', $ebook->is_vip) == 0 ? 'selected' : '' }}>Gratis / Bayar Koin</option>
                        <option value="1" {{ old('is_vip', $ebook->is_vip) == 1 ? 'selected' : '' }}>VIP (akses unlimited)</option>
                    </select>
                </div>
                <div class="col-md-6 mb-3" id="koinField">
                    <label class="form-label">Harga Koin <small class="text-muted">(isi 0 jika gratis)</small></label>
                    <input type="number" name="harga_koin" class="form-control @error('harga_koin') is-invalid @enderror" value="{{ old('harga_koin', $ebook->harga_koin) }}" min="0">
                    @error('harga_koin') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>
                <div class="col-md-12 mb-3">
                    <label class="form-label">Sinopsis</label>
                    <textarea name="sinopsis" class="form-control @error('sinopsis') is-invalid @enderror" rows="4">{{ old('sinopsis', $ebook->sinopsis) }}</textarea>
                    @error('sinopsis') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>
                <div class="col-md-6 mb-3">
                    <label class="form-label">File PDF <small class="text-muted">(kosongkan jika tidak ingin mengganti)</small></label>
                    @if($ebook->file_pdf)
                        <div style="padding:8px 12px;background:#f0fdf4;border-radius:8px;margin-bottom:8px;font-size:13px;color:#1a6e35">
                            <i class="bi bi-file-earmark-pdf"></i> File saat ini tersimpan
                        </div>
                    @endif
                    <input type="file" name="file_pdf" class="form-control @error('file_pdf') is-invalid @enderror" accept=".pdf">
                    @error('file_pdf') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>
                <div class="col-md-6 mb-3">
                    <label class="form-label">Cover <small class="text-muted">(kosongkan jika tidak ingin mengganti)</small></label>
                    @if($ebook->cover)
                        <div class="mb-2">
                            <img src="{{ asset('storage/'.$ebook->cover) }}" id="coverPreview" style="height:100px;border-radius:8px;object-fit:cover">
                        </div>
                    @else
                        <img id="coverPreview" src="" alt="Preview" style="display:none;height:100px;border-radius:8px;object-fit:cover">
                    @endif
                    <input type="file" name="cover" class="form-control @error('cover') is-invalid @enderror" accept="image/*" onchange="previewCover(this)">
                    @error('cover') <div class="invalid-feedback">{{ $message }}</div> @enderror
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
