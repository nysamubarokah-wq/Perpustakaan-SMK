@extends('layouts.admin')

@section('header_title', 'Konfirmasi Peminjaman')

@section('content')

@if(session('success'))
    <div class="alert alert-success alert-dismissible fade show mb-3" role="alert" style="border-radius:10px">
        <i class="bi bi-check-circle-fill me-2"></i> {{ session('success') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
@endif

@if(session('error'))
    <div class="alert alert-danger alert-dismissible fade show mb-3" role="alert" style="border-radius:10px">
        <i class="bi bi-exclamation-circle-fill me-2"></i> {{ session('error') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
@endif

<div class="d-flex justify-content-between align-items-center mb-3 flex-wrap gap-2">
    <div class="d-flex align-items-center gap-2 flex-wrap">
        <i class="bi bi-bookmark-check fs-4 text-success"></i>
        <h5 class="fw-bold m-0" style="font-size:16px;color:#222">Permintaan Peminjaman Buku</h5>
        @if($permintaan->count() > 0)
            <span style="background:#e74c3c;color:white;border-radius:20px;padding:2px 10px;font-size:12px;font-weight:600">
                {{ $permintaan->count() }} menunggu
            </span>
        @endif
    </div>
    @if($permintaan->count() > 0)
    <form action="{{ route('admin.pinjam.konfirmasiSemua') }}" method="POST" onsubmit="return confirm('Konfirmasi semua?')">
        @csrf
        <button type="submit" class="btn btn-success btn-sm" style="border-radius:8px;font-weight:600;font-size:13px">
            <i class="bi bi-check2-all"></i> Konfirmasi Semua
        </button>
    </form>
    @endif
</div>

    <div class="table-responsive">
        <table class="table table-hover align-middle mb-0">
            <thead style="background:#f8f9fa">
                <tr>
                    <th>#</th>
                    <th>Siswa</th>
                    <th>Buku</th>
                    <th>Tgl Pinjam</th>
                    <th>Tgl Kembali</th>
                    <th>Durasi</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse($permintaan as $index => $item)
                <tr>
                    <td>{{ $index + 1 }}</td>
                    <td>
                        <div style="font-weight:600;color:#222;font-size:13px">{{ $item->anggota->nama ?? '-' }}</div>
                        <div style="font-size:11px;color:#888">{{ $item->anggota->email ?? '-' }}</div>
                    </td>
                   <td>
    <div style="display:flex; align-items:center; gap:10px">
        @if($item->buku->sampul)
            <img src="{{ asset($item->buku->sampul) }}" style="width:35px;height:48px;object-fit:cover;border-radius:5px;flex-shrink:0">
        @else
            <div style="width:35px;height:48px;background:linear-gradient(135deg,#1a6e35,#27ae60);border-radius:5px;display:flex;align-items:center;justify-content:center;color:white;font-size:14px;flex-shrink:0">
                <i class="bi bi-book"></i>
            </div>
        @endif
        
        <div>
            <div style="font-weight:600;color:#222;font-size:13px">{{ $item->buku->judul ?? '-' }}</div>
            
            <div style="display: flex; align-items: center; gap: 8px;">
                <span style="font-size:11px;color:#888">{{ $item->buku->pengarang ?? '-' }}</span>
                <span style="font-size:10px;background:#e8f5e9;color:#1a6e35;padding:2px 8px;border-radius:10px;font-weight:600">
                    Stok: {{ $item->buku->stok }}
                </span>
            </div>
        </div>
    </div>
</td>
                    <td style="font-size:13px">{{ $item->tanggal_pinjam }}</td>
                    <td style="font-size:13px">{{ $item->tanggal_kembali }}</td>
                    <td>
                        @php
                            $durasi = \Carbon\Carbon::parse($item->tanggal_pinjam)->diffInDays(\Carbon\Carbon::parse($item->tanggal_kembali));
                        @endphp
                        <span style="background:#e8f4fd;color:#2c3e50;padding:3px 10px;border-radius:20px;font-size:11px;font-weight:600">
                            {{ $durasi }} hari
                        </span>
                    </td>
                    <td style="white-space:nowrap">
                        {{-- Tombol Konfirmasi --}}
                        <form action="{{ route('admin.pinjam.konfirmasi', $item->id) }}" method="POST" class="d-inline"
                            onsubmit="return confirm('Konfirmasi peminjaman buku {{ addslashes($item->buku->judul ?? '') }} oleh {{ addslashes($item->anggota->nama ?? '') }}?')">
                            @csrf
                            <button type="submit" class="btn btn-sm btn-success" style="border-radius:8px;font-weight:600">
                                <i class="bi bi-check-lg"></i> Konfirmasi
                            </button>
                        </form>

                        {{-- Tombol Tolak --}}
                        <form action="{{ route('admin.pinjam.tolak', $item->id) }}" method="POST" class="d-inline"
                            onsubmit="return confirm('Tolak permintaan peminjaman ini?')">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-sm btn-danger" style="border-radius:8px;font-weight:600">
                                <i class="bi bi-x-lg"></i> Tolak
                            </button>
                        </form>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="7" class="text-center text-muted py-5">
                        <i class="bi bi-bookmark-check" style="font-size:40px;display:block;margin-bottom:10px;color:#ddd"></i>
                        Tidak ada permintaan peminjaman yang menunggu konfirmasi.
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

@endsection