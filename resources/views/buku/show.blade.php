@extends('layouts.admin')

@section('title', 'Detail Buku')
@section('page-title', 'Detail Buku')

@section('content')
<x-admin-page-header title="Detail Buku" icon="bi bi-book" :backUrl="route('buku.index')" />

@if(session('success'))
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        <i class="bi bi-check-circle"></i> {{ session('success') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
@endif

@if(session('error'))
    <div class="alert alert-danger alert-dismissible fade show" role="alert">
        <i class="bi bi-exclamation-circle"></i> {{ session('error') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
@endif

<div class="card-admin" style="margin-bottom:20px">
    <div class="card-admin-body">
        <div style="display:flex;gap:20px;flex-wrap:wrap">
            <div style="flex-shrink:0">
                @if($buku->sampul)
                    <img src="{{ asset($buku->sampul) }}" style="width:120px;height:170px;object-fit:cover;border-radius:10px">
                @else
                    <div style="width:120px;height:170px;background:linear-gradient(135deg,#1a6e35,#27ae60);border-radius:10px;display:flex;align-items:center;justify-content:center;color:white;font-size:40px">
                        <i class="bi bi-book"></i>
                    </div>
                @endif
            </div>
            <div style="flex:1;min-width:200px">
                <h3 style="font-weight:700;color:#222;margin-bottom:5px">{{ $buku->judul }}</h3>
                <p style="color:#666;font-size:14px;margin-bottom:3px"><i class="bi bi-person"></i> {{ $buku->pengarang }}</p>
                <p style="color:#666;font-size:14px;margin-bottom:3px"><i class="bi bi-building"></i> {{ $buku->penerbit }} ({{ $buku->tahun_terbit }})</p>
                <p style="color:#666;font-size:14px;margin-bottom:3px"><i class="bi bi-upc-scan"></i> ISBN: {{ $buku->isbn ?? '-' }}</p>
                <p style="color:#666;font-size:14px;margin-bottom:3px"><i class="bi bi-geo-alt"></i> Rak: {{ $buku->lokasi ?? '-' }}</p>
                @if($buku->genre)
                    <span style="padding:4px 12px;background:#e8f5e9;color:#1a6e35;border-radius:20px;font-size:12px;font-weight:600">{{ $buku->genre?->nama ?? '-' }}</span>
                @endif
                <div style="margin-top:10px;display:flex;gap:15px;flex-wrap:wrap">
                    <div style="background:#d4edda;padding:8px 15px;border-radius:10px">
                        <span style="font-size:11px;color:#666">Tersedia</span>
                        <p style="font-size:18px;font-weight:700;color:#1a6e35;margin:0">{{ $buku->eksemplarTersedia()->count() }}</p>
                    </div>
                    <div style="background:#fff3cd;padding:8px 15px;border-radius:10px">
                        <span style="font-size:11px;color:#666">Dipinjam</span>
                        <p style="font-size:18px;font-weight:700;color:#856404;margin:0">{{ $buku->eksemplar()->where('status','dipinjam')->count() }}</p>
                    </div>
                    <div style="background:#e8f4fd;padding:8px 15px;border-radius:10px">
                        <span style="font-size:11px;color:#666">Total Eksemplar</span>
                        <p style="font-size:18px;font-weight:700;color:#2563eb;margin:0">{{ $buku->eksemplar()->count() }}</p>
                    </div>
                </div>
            </div>
            <div style="display:flex;flex-direction:column;gap:8px">
                <a href="{{ route('buku.edit', $buku->id) }}" style="padding:8px 16px;background:#dbeafe;color:#2563eb;border:none;border-radius:8px;font-size:12px;font-weight:600;text-decoration:none;text-align:center">
                    <i class="bi bi-pencil"></i> Edit Buku
                </a>
            </div>
        </div>
        @if($buku->deskripsi)
            <div style="margin-top:15px;padding-top:15px;border-top:1px solid #eee">
                <p style="font-size:13px;color:#555;line-height:1.6">{{ $buku->deskripsi }}</p>
            </div>
        @endif
    </div>
</div>

<div class="card-admin" style="margin-bottom:20px">
    <x-admin-card-header title="QR Code Buku" icon="bi bi-qr-code">
        <x-slot:action>
            <div style="display:flex;gap:8px">
                <a href="{{ route('buku.qrcodeDownload', $buku->id) }}"
                   style="padding:8px 16px;background:#dbeafe;color:#2563eb;border:none;border-radius:8px;font-size:12px;font-weight:600;text-decoration:none;display:inline-flex;align-items:center;gap:6px">
                    <i class="bi bi-download"></i> Download QR
                </a>
                <a href="{{ route('buku.qrcodePrint', $buku->id) }}" target="_blank"
                   style="padding:8px 16px;background:#fff3cd;color:#92400e;border:none;border-radius:8px;font-size:12px;font-weight:600;text-decoration:none;display:inline-flex;align-items:center;gap:6px">
                    <i class="bi bi-printer"></i> Cetak QR
                </a>
            </div>
        </x-slot:action>
    </x-admin-card-header>
    <div class="card-admin-body" style="text-align:center;padding:30px">
        <div style="display:inline-block;padding:20px;background:white;border:2px dashed #e5e7eb;border-radius:12px">
            @if($buku->qr_exists)
                <img src="{{ asset($buku->qrcode_path) }}" style="width:150px;height:150px" alt="QR Code">
            @else
                <div style="width:150px;height:150px;background:#f3f4f6;display:flex;align-items:center;justify-content:center;border-radius:8px;margin:0 auto 10px">
                    <i class="bi bi-qr-code" style="font-size:60px;color:#ccc"></i>
                </div>
            @endif
            <p style="font-family:monospace;font-size:14px;font-weight:700;color:#333;margin:10px 0 0">{{ $buku->kode_buku }}</p>
            <p style="font-size:12px;color:#666;margin:5px 0 0">{{ $buku->isbn ?? '-' }}</p>
        </div>
        <p style="margin-top:15px;font-size:13px;color:#888">QR Code ini merepresentasikan judul buku "{{ $buku->judul }}"</p>
    </div>
</div>

<div class="card-admin" style="margin-bottom:20px">
    <x-admin-card-header title="Tambah Eksemplar" icon="bi bi-plus-circle">
        <x-slot:action>
            <span style="font-size:12px;color:#666">Total: {{ $buku->eksemplar()->count() }} eksemplar</span>
        </x-slot:action>
    </x-admin-card-header>
    <div class="card-admin-body">
        <form action="{{ route('buku.eksemplar.tambah', $buku->id) }}" method="POST" style="display:flex;gap:10px;align-items:flex-end;flex-wrap:wrap">
            @csrf
            <div style="flex:1;min-width:200px">
                <label class="form-label fw-600" style="font-size:13px">Jumlah Eksemplar Baru</label>
                <input type="number" name="jumlah" class="form-control" value="1" min="1" max="100" style="border-radius:8px">
            </div>
            <button type="submit" style="padding:8px 20px;background:linear-gradient(135deg,#1a6e35,#27ae60);color:white;border:none;border-radius:8px;font-size:13px;font-weight:600;cursor:pointer;white-space:nowrap">
                <i class="bi bi-plus-circle"></i> Tambah
            </button>
        </form>
    </div>
</div>

<div class="card-admin">
    <x-admin-card-header title="Daftar Eksemplar" icon="bi bi-collection">
        <x-slot:action>
            <span style="font-size:12px;color:#666">{{ $buku->eksemplar()->count() }} eksemplar</span>
        </x-slot:action>
    </x-admin-card-header>
    <div class="card-admin-body">
        @if($buku->eksemplar->isEmpty())
            <div style="text-align:center;padding:40px;color:#aaa">
                <i class="bi bi-collection" style="font-size:40px;display:block;margin-bottom:10px"></i>
                <p>Belum ada eksemplar</p>
            </div>
        @else
            <div style="display:grid;grid-template-columns:repeat(auto-fill,minmax(280px,1fr));gap:15px">
                @foreach($buku->eksemplar as $eksemplar)
                <div style="background:white;border:1px solid #e5e7eb;border-radius:12px;padding:15px;display:flex;gap:12px;align-items:flex-start">
                    <div style="flex-shrink:0;width:50px;height:50px;background:#f0fdf4;border-radius:8px;display:flex;align-items:center;justify-content:center">
                        <i class="bi bi-collection" style="font-size:20px;color:#1a6e35"></i>
                    </div>
                    <div style="flex:1;min-width:0">
                        <p style="font-size:14px;font-weight:700;color:#222;margin-bottom:3px;font-family:monospace">{{ $eksemplar->kode_buku }}</p>
                        <span style="display:inline-block;padding:3px 10px;border-radius:20px;font-size:11px;font-weight:600;background:{{ $eksemplar->status_bg }};color:{{ $eksemplar->status_color }}">
                            {{ $eksemplar->status_label }}
                        </span>
                        @if($eksemplar->kondisi)
                            <span style="display:inline-block;padding:3px 10px;border-radius:20px;font-size:11px;font-weight:600;background:#f3f4f6;color:#555;margin-left:3px">
                                {{ ucfirst(str_replace('_',' ',$eksemplar->kondisi)) }}
                            </span>
                        @endif
                        <div style="margin-top:8px;display:flex;gap:5px;flex-wrap:wrap">
                            <form action="{{ route('eksemplar.updateStatus', $eksemplar->id) }}" method="POST" style="display:inline-flex;gap:3px;align-items:center">
                                @csrf
                                @method('PUT')
                                <select name="status" style="padding:3px 6px;border:1px solid #ddd;border-radius:6px;font-size:10px" onchange="this.form.submit()">
                                    <option value="tersedia" {{ $eksemplar->status === 'tersedia' ? 'selected' : '' }}>Tersedia</option>
                                    <option value="dipinjam" {{ $eksemplar->status === 'dipinjam' ? 'selected' : '' }}>Dipinjam</option>
                                    <option value="rusak" {{ $eksemplar->status === 'rusak' ? 'selected' : '' }}>Rusak</option>
                                    <option value="hilang" {{ $eksemplar->status === 'hilang' ? 'selected' : '' }}>Hilang</option>
                                    <option value="maintenance" {{ $eksemplar->status === 'maintenance' ? 'selected' : '' }}>Maintenance</option>
                                </select>
                            </form>
                            <form action="{{ route('eksemplar.hapus', $eksemplar->id) }}" method="POST" style="display:inline">
                                @csrf
                                @method('DELETE')
                                <button type="submit" onclick="return confirm('Hapus eksemplar {{ $eksemplar->kode_buku }}?')"
                                    style="padding:4px 10px;background:#fee2e2;color:#dc2626;border:none;border-radius:6px;font-size:10px;font-weight:600;cursor:pointer">
                                    <i class="bi bi-trash"></i>
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
                @endforeach
            </div>
        @endif
    </div>
</div>
@endsection
