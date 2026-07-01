@extends('layouts.admin')

@section('header_title', 'Konfirmasi Peminjaman')

@section('content')
<style>
    @media (max-width: 768px) {
        .kpinjam-tbl-wrap { display: none; }
        .kpinjam-mobile { display: block; }
        .kpinjam-header { flex-direction: column; align-items: flex-start !important; gap: 10px !important; }
        .kpinjam-header .btn { width: 100%; text-align: center; }
    }
    @media (min-width: 769px) {
        .kpinjam-mobile { display: none; }
    }
    .kpm-card {
        background: white;
        border-radius: 12px;
        padding: 14px;
        margin-bottom: 10px;
        box-shadow: 0 1px 4px rgba(0,0,0,0.06);
        border: 1px solid #eee;
    }
    .kpm-card-top {
        display: flex;
        justify-content: space-between;
        align-items: flex-start;
        margin-bottom: 8px;
    }
    .kpm-card-user {
        display: flex;
        align-items: center;
        gap: 10px;
    }
    .kpm-avatar {
        width: 36px;
        height: 36px;
        background: linear-gradient(135deg,#1a6e35,#27ae60);
        color: white;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        font-weight: 700;
        font-size: 13px;
        flex-shrink: 0;
    }
    .kpm-user-name {
        font-weight: 700;
        font-size: 14px;
        color: #222;
    }
    .kpm-user-email {
        font-size: 11px;
        color: #888;
    }
    .kpm-book-wrap {
        display: flex;
        align-items: center;
        gap: 10px;
        margin-top: 6px;
        padding: 8px;
        background: #fafafa;
        border-radius: 8px;
    }
    .kpm-book-img {
        width: 35px;
        height: 48px;
        object-fit: cover;
        border-radius: 5px;
        flex-shrink: 0;
    }
    .kpm-book-placeholder {
        width: 35px;
        height: 48px;
        background: linear-gradient(135deg,#1a6e35,#27ae60);
        border-radius: 5px;
        display: flex;
        align-items: center;
        justify-content: center;
        color: white;
        font-size: 14px;
        flex-shrink: 0;
    }
    .kpm-book-title {
        font-weight: 600;
        font-size: 13px;
        color: #222;
    }
    .kpm-book-info {
        display: flex;
        align-items: center;
        gap: 6px;
        flex-wrap: wrap;
        margin-top: 2px;
    }
    .kpm-book-author {
        font-size: 11px;
        color: #888;
    }
    .kpm-badge-kode {
        font-size: 10px;
        background: #dbeafe;
        color: #2563eb;
        padding: 2px 8px;
        border-radius: 10px;
        font-weight: 600;
        font-family: monospace;
    }
    .kpm-badge-tersedia {
        font-size: 10px;
        background: #e8f5e9;
        color: #1a6e35;
        padding: 2px 8px;
        border-radius: 10px;
        font-weight: 600;
    }
    .kpm-card-meta {
        display: flex;
        gap: 12px;
        font-size: 11px;
        color: #888;
        margin-top: 8px;
        flex-wrap: wrap;
    }
    .kpm-card-meta i { font-size: 12px; margin-right: 3px; }
    .kpm-card-actions {
        display: flex;
        gap: 8px;
        margin-top: 12px;
    }
    .kpm-card-actions .btn {
        flex: 1;
        padding: 8px;
        font-size: 12px;
        border-radius: 8px;
        font-weight: 600;
        text-align: center;
    }
    .kpm-durasi {
        display: inline-block;
        background: #e8f4fd;
        color: #2c3e50;
        padding: 2px 10px;
        border-radius: 20px;
        font-size: 10px;
        font-weight: 600;
    }
</style>

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

<div class="d-flex justify-content-between align-items-center mb-3 flex-wrap gap-2 kpinjam-header">
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
        <button type="submit"
                style="padding:8px 18px;background:linear-gradient(135deg,#1a6e35,#27ae60);color:white;border:none;border-radius:10px;font-size:13px;font-weight:600;cursor:pointer">
            <i class="bi bi-check2-all"></i> Konfirmasi Semua
        </button>
    </form>
    @endif
</div>

{{-- Desktop Table --}}
<div class="kpinjam-tbl-wrap">
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
                                @if($item->eksemplar)
                                    <span style="font-size:10px;background:#dbeafe;color:#2563eb;padding:2px 8px;border-radius:10px;font-weight:600;font-family:monospace">
                                        {{ $item->eksemplar->kode_buku }}
                                    </span>
                                @endif
                                <span style="font-size:10px;background:#e8f5e9;color:#1a6e35;padding:2px 8px;border-radius:10px;font-weight:600">
                                    Tersedia: {{ $item->buku->eksemplarTersedia()->count() }}
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
                    <form action="{{ route('admin.pinjam.konfirmasi', $item->id) }}" method="POST" class="d-inline"
                        onsubmit="return confirm('Konfirmasi peminjaman buku {{ addslashes($item->buku->judul ?? '') }} oleh {{ addslashes($item->anggota->nama ?? '') }}?')">
                        @csrf
                        <button type="submit"
                                style="padding:6px 14px;background:#d4edda;color:#1a6e35;border:none;border-radius:8px;font-size:12px;font-weight:600;cursor:pointer">
                            <i class="bi bi-check-lg"></i> Konfirmasi
                        </button>
                    </form>
                    <form action="{{ route('admin.pinjam.tolak', $item->id) }}" method="POST" class="d-inline"
                        onsubmit="return confirm('Tolak permintaan peminjaman ini?')">
                        @csrf
                        @method('DELETE')
                        <button type="submit"
                                style="padding:6px 14px;background:#fee2e2;color:#dc2626;border:none;border-radius:8px;font-size:12px;font-weight:600;cursor:pointer">
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

{{-- Mobile Cards --}}
<div class="kpinjam-mobile" style="padding:12px">
    @forelse($permintaan as $index => $item)
    @php
        $durasi = \Carbon\Carbon::parse($item->tanggal_pinjam)->diffInDays(\Carbon\Carbon::parse($item->tanggal_kembali));
    @endphp
    <div class="kpm-card">
        <div class="kpm-card-top">
            <div class="kpm-card-user">
                <div class="kpm-avatar">{{ strtoupper(substr($item->anggota->nama ?? '?', 0, 1)) }}</div>
                <div>
                    <div class="kpm-user-name">{{ $item->anggota->nama ?? '-' }}</div>
                    <div class="kpm-user-email">{{ $item->anggota->email ?? '-' }}</div>
                </div>
            </div>
            <span class="kpm-durasi">{{ $durasi }} hari</span>
        </div>

        <div class="kpm-book-wrap">
            @if($item->buku->sampul)
                <img src="{{ asset($item->buku->sampul) }}" class="kpm-book-img">
            @else
                <div class="kpm-book-placeholder">
                    <i class="bi bi-book"></i>
                </div>
            @endif
            <div>
                <div class="kpm-book-title">{{ $item->buku->judul ?? '-' }}</div>
                <div class="kpm-book-info">
                    <span class="kpm-book-author">{{ $item->buku->pengarang ?? '-' }}</span>
                    @if($item->eksemplar)
                        <span class="kpm-badge-kode">{{ $item->eksemplar->kode_buku }}</span>
                    @endif
                    <span class="kpm-badge-tersedia">Tersedia: {{ $item->buku->eksemplarTersedia()->count() }}</span>
                </div>
            </div>
        </div>

        <div class="kpm-card-meta">
            <span><i class="bi bi-calendar-event"></i> Pinjam: {{ $item->tanggal_pinjam }}</span>
            <span><i class="bi bi-calendar-check"></i> Kembali: {{ $item->tanggal_kembali }}</span>
        </div>

        <div class="kpm-card-actions">
            <form action="{{ route('admin.pinjam.konfirmasi', $item->id) }}" method="POST"
                onsubmit="return confirm('Konfirmasi peminjaman buku {{ addslashes($item->buku->judul ?? '') }} oleh {{ addslashes($item->anggota->nama ?? '') }}?')">
                @csrf
                <button type="submit" class="btn" style="background:#d4edda;color:#1a6e35;border:none">
                    <i class="bi bi-check-lg"></i> Konfirmasi
                </button>
            </form>
            <form action="{{ route('admin.pinjam.tolak', $item->id) }}" method="POST"
                onsubmit="return confirm('Tolak permintaan peminjaman ini?')">
                @csrf
                @method('DELETE')
                <button type="submit" class="btn" style="background:#fee2e2;color:#dc2626;border:none">
                    <i class="bi bi-x-lg"></i> Tolak
                </button>
            </form>
        </div>
    </div>
    @empty
    <div style="text-align:center;padding:40px 20px;color:#aaa">
        <i class="bi bi-bookmark-check" style="font-size:48px;display:block;margin-bottom:12px"></i>
        <p style="font-size:14px">Tidak ada permintaan peminjaman yang menunggu konfirmasi.</p>
    </div>
    @endforelse
</div>
@endsection
