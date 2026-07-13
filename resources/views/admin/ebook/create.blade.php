@extends('layouts.admin')

@section('title', 'Tambah E-book')
@section('page-title', 'Tambah E-book')

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

toggleKoin('{{ old('is_vip', 0) }}');
</script>
@endpush

@section('content')
<x-admin-page-header title="Tambah E-book" icon="bi bi-plus-circle" :backUrl="route('admin.ebook.index')" />

<div class="card-admin">
    <div class="card-admin-body">
        <form action="{{ route('admin.ebook.store') }}" method="POST" enctype="multipart/form-data">
            @csrf
            <div class="row">
                <div class="col-md-6 mb-3">
                    <label class="form-label fw-600">Judul E-book <span style="color:red">*</span></label>
                    <input type="text" name="judul" class="form-control @error('judul') is-invalid @enderror" value="{{ old('judul') }}" required>
                    @error('judul') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>
                <div class="col-md-6 mb-3">
                    <label class="form-label fw-600">Penulis <span style="color:red">*</span></label>
                    <input type="text" name="penulis" class="form-control @error('penulis') is-invalid @enderror" value="{{ old('penulis') }}" required>
                    @error('penulis') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>
                <div class="col-md-6 mb-3">
                    <label class="form-label">Tipe Akses</label>
                    <select name="is_vip" id="tipeAkses" class="form-control" onchange="toggleKoin(this.value)">
                        <option value="0" {{ old('is_vip') == 0 ? 'selected' : '' }}>Gratis / Bayar Koin</option>
                        <option value="1" {{ old('is_vip') == 1 ? 'selected' : '' }}>VIP (akses unlimited)</option>
                    </select>
                </div>
                <div class="col-md-6 mb-3" id="koinField">
                    <label class="form-label">Harga Koin <small class="text-muted">(isi 0 jika gratis)</small></label>
                    <input type="number" name="harga_koin" class="form-control @error('harga_koin') is-invalid @enderror" value="{{ old('harga_koin', 0) }}" min="0">
                    @error('harga_koin') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>
                <div class="col-md-12 mb-3">
                    <label class="form-label">Sinopsis</label>
                    <textarea name="sinopsis" class="form-control @error('sinopsis') is-invalid @enderror" rows="4">{{ old('sinopsis') }}</textarea>
                    @error('sinopsis') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>
                <div class="col-md-6 mb-3">
                    <label class="form-label fw-600">File PDF <span style="color:red">*</span> <small class="text-muted">(maks. 100MB)</small></label>
                    <input type="file" name="file_pdf" class="form-control @error('file_pdf') is-invalid @enderror" accept=".pdf" required>
                    @error('file_pdf') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>
                <div class="col-md-6 mb-3">
                    <label class="form-label">Cover <small class="text-muted">(jpg/png, maks. 2MB)</small></label>
                    <input type="file" name="cover" class="form-control @error('cover') is-invalid @enderror" accept="image/*" id="coverInput" onchange="previewCover(this)">
                    <img id="coverPreview" src="" alt="Preview" style="display:none;margin-top:10px;height:100px;border-radius:8px;object-fit:cover">
                    @error('cover') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>
            </div>
            <button type="submit"
                    style="padding:10px 20px;background:linear-gradient(135deg,#1a6e35,#27ae60);color:white;border:none;border-radius:10px;font-size:13px;font-weight:600;cursor:pointer">
                <i class="bi bi-upload"></i> Upload E-book
            </button>
        </form>
    </div>
</div>
@endsection
