@extends('layouts.admin')

@section('header_title', 'Tambah E-book')

@section('content')

<div class="mb-4">
    <a href="{{ route('admin.ebook.index') }}" style="color:#888;text-decoration:none;font-size:14px">
        <i class="bi bi-arrow-left"></i> Kembali ke Kelola E-book
    </a>
</div>

<div style="background:white;border-radius:16px;box-shadow:0 3px 15px rgba(0,0,0,0.08);padding:30px;max-width:600px">

    @if($errors->any())
        <div class="alert alert-danger">
            <ul class="mb-0">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form action="{{ route('admin.ebook.store') }}" method="POST" enctype="multipart/form-data">
        @csrf

        <div class="mb-3">
            <label style="font-size:13px;font-weight:600;color:#555;margin-bottom:6px;display:block">
                Judul E-book <span style="color:red">*</span>
            </label>
            <input type="text" name="judul" value="{{ old('judul') }}" required
                   style="width:100%;padding:12px 15px;border:1.5px solid #e5e7eb;border-radius:10px;font-size:14px;outline:none"
                   placeholder="Contoh: Pemrograman Web Dasar">
        </div>

        <div class="mb-3">
            <label style="font-size:13px;font-weight:600;color:#555;margin-bottom:6px;display:block">
                Penulis <span style="color:red">*</span>
            </label>
            <input type="text" name="penulis" value="{{ old('penulis') }}" required
                   style="width:100%;padding:12px 15px;border:1.5px solid #e5e7eb;border-radius:10px;font-size:14px;outline:none"
                   placeholder="Nama penulis">
        </div>

        <div class="mb-3">
            <label style="font-size:13px;font-weight:600;color:#555;margin-bottom:6px;display:block">Sinopsis</label>
            <textarea name="sinopsis" rows="4"
                      style="width:100%;padding:12px 15px;border:1.5px solid #e5e7eb;border-radius:10px;font-size:14px;outline:none;resize:vertical"
                      placeholder="Deskripsi singkat e-book...">{{ old('sinopsis') }}</textarea>
        </div>

        <div class="mb-3">
            <label style="font-size:13px;font-weight:600;color:#555;margin-bottom:6px;display:block">
                File PDF <span style="color:red">*</span>
                <span style="color:#888;font-weight:400">(maks. 20MB)</span>
            </label>
            <input type="file" name="file_pdf" accept=".pdf" required
                   style="width:100%;padding:12px 15px;border:1.5px solid #e5e7eb;border-radius:10px;font-size:14px">
        </div>

        <div class="mb-3">
            <label style="font-size:13px;font-weight:600;color:#555;margin-bottom:6px;display:block">
                Cover <span style="color:#888;font-weight:400">(opsional, jpg/png, maks. 2MB)</span>
            </label>
            <input type="file" name="cover" accept="image/*" id="coverInput" onchange="previewCover(this)"
                   style="width:100%;padding:12px 15px;border:1.5px solid #e5e7eb;border-radius:10px;font-size:14px">
            <img id="coverPreview" src="" alt="Preview"
                 style="display:none;margin-top:10px;width:100px;height:130px;object-fit:cover;border-radius:10px;border:2px solid #e5e7eb">
        </div>

        <div class="mb-3">
            <label style="font-size:13px;font-weight:600;color:#555;margin-bottom:6px;display:block">
                Tipe Akses <span style="color:red">*</span>
            </label>
            <select name="is_vip" id="tipeAkses" onchange="toggleKoin(this.value)"
                    style="width:100%;padding:12px 15px;border:1.5px solid #e5e7eb;border-radius:10px;font-size:14px;outline:none;background:white">
                <option value="0" {{ old('is_vip') == '0' ? 'selected' : '' }}>Gratis / Bayar Koin</option>
                <option value="1" {{ old('is_vip') == '1' ? 'selected' : '' }}>⭐ VIP (akses unlimited)</option>
            </select>
        </div>

        <div class="mb-4" id="koinField">
            <label style="font-size:13px;font-weight:600;color:#555;margin-bottom:6px;display:block">
                Harga Koin <span style="color:#888;font-weight:400">(isi 0 jika gratis)</span>
            </label>
            <input type="number" name="harga_koin" value="{{ old('harga_koin', 0) }}" min="0"
                   style="width:100%;padding:12px 15px;border:1.5px solid #e5e7eb;border-radius:10px;font-size:14px;outline:none"
                   placeholder="0">
        </div>

        <button type="submit"
                style="width:100%;padding:14px;background:linear-gradient(135deg,#1a6e35,#27ae60);color:white;border:none;border-radius:12px;font-size:15px;font-weight:700;cursor:pointer">
            <i class="bi bi-upload"></i> Upload E-book
        </button>

    </form>
</div>

@endsection

@push('scripts')
<script>
function toggleKoin(val) {
    document.getElementById('koinField').style.display = val === '1' ? 'none' : 'block';
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

// Restore state saat ada old input
toggleKoin('{{ old('is_vip', '0') }}');
</script>
@endpush