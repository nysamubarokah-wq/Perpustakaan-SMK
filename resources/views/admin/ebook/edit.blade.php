@extends('layouts.admin')

@section('header_title', 'Edit E-book')

@section('content')

<style>
    @media (max-width: 768px) {
        .ebook-form-card { padding: 20px 16px !important; max-width: 100% !important; }
    }
</style>

<x-admin-page-header title="Edit E-book" icon="bi bi-pencil-square" :backUrl="route('admin.ebook.index')" backText="Kembali ke Kelola E-book" />

<div class="ebook-form-card" style="background:white;border-radius:16px;box-shadow:0 3px 15px rgba(0,0,0,.08);padding:30px;max-width:650px">

    @if($errors->any())
        <div class="alert alert-danger">
            <ul class="mb-0">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form action="{{ route('admin.ebook.update',$ebook->id) }}" method="POST" enctype="multipart/form-data">
        @csrf
        @method('PUT')

        {{-- Judul --}}
        <div class="mb-3">
            <label style="font-size:13px;font-weight:600;color:#555;display:block;margin-bottom:6px">
                Judul E-book <span style="color:red">*</span>
            </label>

            <input
                type="text"
                name="judul"
                value="{{ old('judul',$ebook->judul) }}"
                required
                style="width:100%;padding:12px 15px;border:1.5px solid #e5e7eb;border-radius:10px;font-size:14px"
            >
        </div>

        {{-- Penulis --}}
        <div class="mb-3">
            <label style="font-size:13px;font-weight:600;color:#555;display:block;margin-bottom:6px">
                Penulis <span style="color:red">*</span>
            </label>

            <input
                type="text"
                name="penulis"
                value="{{ old('penulis',$ebook->penulis) }}"
                required
                style="width:100%;padding:12px 15px;border:1.5px solid #e5e7eb;border-radius:10px;font-size:14px"
            >
        </div>

        {{-- Sinopsis --}}
        <div class="mb-3">
            <label style="font-size:13px;font-weight:600;color:#555;display:block;margin-bottom:6px">
                Sinopsis
            </label>

            <textarea
                name="sinopsis"
                rows="5"
                style="width:100%;padding:12px 15px;border:1.5px solid #e5e7eb;border-radius:10px;font-size:14px;resize:vertical"
            >{{ old('sinopsis',$ebook->sinopsis) }}</textarea>
        </div>

        {{-- PDF --}}
        <div class="mb-3">

            <label style="font-size:13px;font-weight:600;color:#555;display:block;margin-bottom:6px">
                File PDF
                <span style="color:#888;font-weight:400">(Kosongkan jika tidak ingin mengganti)</span>
            </label>

            @if($ebook->file_pdf)
                <div style="margin-bottom:8px;font-size:13px;color:#1a6e35">
                    <i class="bi bi-file-earmark-pdf"></i>
                    File saat ini tersedia
                </div>
            @endif

            <input
                type="file"
                name="file_pdf"
                accept=".pdf"
                style="width:100%;padding:12px 15px;border:1.5px solid #e5e7eb;border-radius:10px;font-size:14px"
            >

        </div>

        {{-- Cover --}}
        <div class="mb-3">

            <label style="font-size:13px;font-weight:600;color:#555;display:block;margin-bottom:6px">
                Cover
                <span style="color:#888;font-weight:400">(Kosongkan jika tidak ingin mengganti)</span>
            </label>

            @if($ebook->cover)
                <img
                    src="{{ asset('storage/'.$ebook->cover) }}"
                    id="coverPreview"
                    style="width:110px;height:150px;object-fit:cover;border-radius:10px;border:2px solid #eee;margin-bottom:10px"
                >
            @else
                <img
                    id="coverPreview"
                    style="display:none;width:110px;height:150px;object-fit:cover;border-radius:10px;border:2px solid #eee;margin-bottom:10px"
                >
            @endif

            <input
                type="file"
                name="cover"
                accept="image/*"
                onchange="previewCover(this)"
                style="width:100%;padding:12px 15px;border:1.5px solid #e5e7eb;border-radius:10px;font-size:14px"
            >

        </div>

        {{-- Tipe --}}
        <div class="mb-3">

            <label style="font-size:13px;font-weight:600;color:#555;display:block;margin-bottom:6px">
                Tipe Akses
            </label>

            <select
                name="is_vip"
                id="tipeAkses"
                onchange="toggleKoin(this.value)"
                style="width:100%;padding:12px 15px;border:1.5px solid #e5e7eb;border-radius:10px;background:white"
            >

                <option value="0" {{ old('is_vip',$ebook->is_vip)==0 ? 'selected':'' }}>
                    Gratis / Bayar Koin
                </option>

                <option value="1" {{ old('is_vip',$ebook->is_vip)==1 ? 'selected':'' }}>
                    ⭐ VIP
                </option>

            </select>

        </div>

        {{-- Harga Koin --}}
        <div class="mb-4" id="koinField">

            <label style="font-size:13px;font-weight:600;color:#555;display:block;margin-bottom:6px">
                Harga Koin
            </label>

            <input
                type="number"
                name="harga_koin"
                min="0"
                value="{{ old('harga_koin',$ebook->harga_koin) }}"
                style="width:100%;padding:12px 15px;border:1.5px solid #e5e7eb;border-radius:10px"
            >

        </div>

        <button
            type="submit"
            style="width:100%;padding:12px;background:linear-gradient(135deg,#1a6e35,#27ae60);color:white;border:none;border-radius:10px;font-size:14px;font-weight:600;cursor:pointer"
        >
            <i class="bi bi-save"></i>
            Simpan Perubahan
        </button>

    </form>

</div>

@endsection

@push('scripts')
<script>

function toggleKoin(val){
    document.getElementById('koinField').style.display =
        val == 1 ? 'none' : 'block';
}

toggleKoin('{{ old('is_vip',$ebook->is_vip) }}');

function previewCover(input){

    const preview=document.getElementById('coverPreview');

    if(input.files && input.files[0]){

        const reader=new FileReader();

        reader.onload=function(e){
            preview.src=e.target.result;
            preview.style.display='block';
        }

        reader.readAsDataURL(input.files[0]);
    }

}

</script>
@endpush