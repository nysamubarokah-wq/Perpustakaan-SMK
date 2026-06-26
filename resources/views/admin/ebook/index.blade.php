@extends('layouts.admin')

@section('header_title', 'Kelola E-book')

@section('content')

@if(session('success'))
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        {{ session('success') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
@endif

<div class="d-flex justify-content-between align-items-center mb-4">
    <span style="color:#888;font-size:14px">{{ $ebooks->total() }} e-book tersedia</span>
    <a href="{{ route('admin.ebook.create') }}" 
       style="background:linear-gradient(135deg,#1a6e35,#27ae60);color:white;padding:10px 20px;border-radius:12px;text-decoration:none;font-size:13px;font-weight:600">
        <i class="bi bi-plus-lg"></i> Tambah E-book
    </a>
</div>

@if($ebooks->count() > 0)
<div style="background:white;border-radius:16px;box-shadow:0 3px 15px rgba(0,0,0,0.08);overflow:hidden">
    <div class="card-admin-body">
        <table class="table table-hover mb-0">
            <thead style="background:#f8f9fa">
                <tr>
                    <th style="padding:15px 20px;font-size:12px;color:#888;font-weight:600">COVER</th>
                    <th style="padding:15px 20px;font-size:12px;color:#888;font-weight:600">JUDUL & PENULIS</th>
                    <th style="padding:15px 20px;font-size:12px;color:#888;font-weight:600">SINOPSIS</th>
                    <th style="padding:15px 20px;font-size:12px;color:#888;font-weight:600">TIPE</th>
                    <th style="padding:15px 20px;font-size:12px;color:#888;font-weight:600">AKSI</th>
                </tr>
            </thead>
            <tbody>
                @foreach($ebooks as $ebook)
                <tr>
                    <td style="padding:12px 20px;vertical-align:middle">
                        @if($ebook->cover)
                            <img src="{{ asset($ebook->cover) }}" 
                                 style="width:45px;height:60px;object-fit:cover;border-radius:8px">
                        @else
                            <div style="width:45px;height:60px;background:linear-gradient(135deg,#1a6e35,#27ae60);border-radius:8px;display:flex;align-items:center;justify-content:center">
                                <i class="bi bi-book" style="color:white"></i>
                            </div>
                        @endif
                    </td>
                    <td style="padding:12px 20px;vertical-align:middle">
                        <div style="font-weight:700;color:#222;font-size:14px">{{ $ebook->judul }}</div>
                        <div style="font-size:12px;color:#888">{{ $ebook->penulis }}</div>
                    </td>
                    <td style="padding:12px 20px;vertical-align:middle;max-width:200px">
                        <div style="font-size:12px;color:#888">
                            {{ $ebook->sinopsis ? Str::limit($ebook->sinopsis, 60) : '-' }}
                        </div>
                    </td>
                    <td style="padding:12px 20px;vertical-align:middle">
                        @if($ebook->is_vip)
                            <span style="background:#f59e0b;color:white;font-size:11px;padding:4px 12px;border-radius:10px;font-weight:600;white-space:nowrap">⭐ VIP</span>
                        @elseif($ebook->harga_koin > 0)
                            <span style="background:#1a6e35;color:white;font-size:11px;padding:3px 10px;border-radius:10px;font-weight:600">🪙 {{ $ebook->harga_koin }} Koin</span>
                        @else
                            <span style="background:#1a6e35;color:white;font-size:11px;padding:3px 10px;border-radius:10px;font-weight:600">✓ Gratis</span>
                        @endif
                    </td>
                   <td style="padding:12px 20px;vertical-align:middle">
    <div style="display:flex;gap:8px">

        <a href="{{ route('admin.ebook.edit', $ebook->id) }}"
           style="background:#dbeafe;color:#2563eb;text-decoration:none;padding:6px 14px;border-radius:8px;font-size:12px;font-weight:600;display:inline-flex;align-items:center;gap:5px">
            <i class="bi bi-pencil-square"></i> Edit
        </a>

        <form action="{{ route('admin.ebook.destroy', $ebook->id) }}" method="POST" class="d-inline">
            @csrf
            @method('DELETE')
            <button type="submit"
                    onclick="return confirm('Hapus e-book \'{{ $ebook->judul }}\'?')"
                    style="background:#fee2e2;color:#dc2626;border:none;padding:6px 14px;border-radius:8px;font-size:12px;font-weight:600;cursor:pointer">
                <i class="bi bi-trash"></i> Hapus
            </button>
        </form>

    </div>
</td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>

<div class="mt-3">{{ $ebooks->links() }}</div>

@else
<div style="text-align:center;padding:80px;color:#aaa;background:white;border-radius:16px;box-shadow:0 3px 15px rgba(0,0,0,0.08)">
    <i class="bi bi-book" style="font-size:50px;display:block;margin-bottom:15px"></i>
    <p style="font-size:15px">Belum ada e-book.</p>
    <a href="{{ route('admin.ebook.create') }}" 
       style="background:linear-gradient(135deg,#1a6e35,#27ae60);color:white;padding:10px 24px;border-radius:12px;text-decoration:none;font-size:13px;font-weight:600;display:inline-block;margin-top:10px">
        + Tambah E-book Pertama
    </a>
</div>
@endif

@endsection