@extends('layouts.admin')

@section('title', 'Kelola Peminjaman')
@section('page-title', 'Kelola Peminjaman')

@section('content')
<style>
    @media (max-width: 768px) {
        .peminjaman-table-wrap { display: none; }
        .peminjaman-mobile { display: block; }
        .peminjaman-search { padding: 10px 15px !important; }
        .peminjaman-search .d-flex { flex-direction: column; gap: 8px !important; }
        .peminjaman-search .btn { width: 100%; }
        .peminjaman-header { flex-direction: column; align-items: flex-start !important; }
        .peminjaman-header .btn { width: 100%; text-align: center; }
    }
    @media (min-width: 769px) {
        .peminjaman-mobile { display: none; }
    }
    .pm-card {
        background: white;
        border-radius: 12px;
        padding: 14px;
        margin-bottom: 10px;
        box-shadow: 0 1px 4px rgba(0,0,0,0.06);
        border: 1px solid #eee;
    }
    .pm-card-top {
        display: flex;
        justify-content: space-between;
        align-items: flex-start;
        margin-bottom: 10px;
    }
    .pm-card-name {
        font-weight: 700;
        font-size: 14px;
        color: #222;
    }
    .pm-card-book {
        font-size: 13px;
        color: #555;
        margin-top: 2px;
    }
    .pm-card-meta {
        display: flex;
        gap: 12px;
        font-size: 11px;
        color: #888;
        margin-top: 8px;
        flex-wrap: wrap;
    }
    .pm-card-meta i { font-size: 12px; margin-right: 3px; }
    .pm-card-actions {
        display: flex;
        gap: 6px;
        margin-top: 10px;
        justify-content: flex-end;
    }
    .pm-card-actions .btn {
        padding: 6px 14px;
        font-size: 12px;
        border-radius: 8px;
        font-weight: 600;
    }
    .pm-eksemplar {
        font-family: monospace;
        font-size: 10px;
        background: #e8f5e9;
        color: #1a6e35;
        padding: 2px 8px;
        border-radius: 10px;
        font-weight: 600;
    }
</style>

<div class="card-admin">
    <x-admin-card-header title="Daftar Peminjaman" icon="bi bi-journal-check">
        <x-slot:action>
            <a href="{{ route('peminjaman.create') }}" style="padding:8px 18px;background:linear-gradient(135deg,#1a6e35,#27ae60);color:white;border:none;border-radius:10px;font-size:13px;font-weight:600;cursor:pointer;text-decoration:none">
                <i class="bi bi-plus-lg"></i> Tambah Peminjaman
            </a>
        </x-slot:action>
    </x-admin-card-header>

    <div class="card-admin-body">
        <div class="peminjaman-search" style="padding:15px 25px;border-bottom:1px solid #eee;background:#fafafa">
            <form method="GET" action="{{ route('peminjaman.index') }}">
                <div class="d-flex gap-2">
                    <input type="text" name="search" 
                           value="{{ $search ?? '' }}"
                           placeholder="Cari nama anggota atau judul buku..."
                           class="form-control" style="border-radius:10px">
                    <button type="submit" class="btn" style="background:linear-gradient(135deg,#1a6e35,#27ae60);color:white;border-radius:10px;padding:8px 20px;font-weight:600;white-space:nowrap">
                        <i class="bi bi-search"></i> Cari
                    </button>
                    @if($search ?? '')
                        <a href="{{ route('peminjaman.index') }}" class="btn btn-secondary" style="border-radius:10px;padding:8px 20px">
                            <i class="bi bi-x"></i> Reset
                        </a>
                    @endif
                </div>
            </form>
        </div>

        {{-- Desktop Table --}}
        <div class="peminjaman-table-wrap">
            <table class="table table-hover">
                <thead style="background:#f8f9fa">
                    <tr>
                        <th>#</th>
                        <th>Anggota</th>
                        <th>Buku</th>
                        <th>Eksemplar</th>
                        <th>Tgl Pinjam</th>
                        <th>Tgl Kembali</th>
                        <th>Status</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($peminjaman as $item)
                    <tr>
                        <td>{{ $loop->iteration }}</td>
                        <td>{{ $item->anggota->nama ?? '-' }}</td>
                        <td>{{ $item->buku->judul ?? '-' }}</td>
                        <td style="font-family:monospace;font-size:12px;color:#1a6e35">{{ $item->eksemplar->kode_buku ?? ($item->buku->kode_buku ?? '-') }}</td>
                        <td>{{ $item->tanggal_pinjam }}</td>
                        <td>{{ $item->tanggal_kembali }}</td>
                        <td>
                            @if($item->status == 'dipinjam' && $item->tanggal_kembali < now()->toDateString())
                                <span style="padding:4px 12px;border-radius:20px;font-size:11px;font-weight:600;background:#f8d7da;color:#721c24">Terlambat</span>
                            @elseif($item->status == 'dipinjam')
                                <span style="padding:4px 12px;border-radius:20px;font-size:11px;font-weight:600;background:#fff3cd;color:#856404">Dipinjam</span>
                            @elseif($item->status == 'menunggu_konfirmasi')
                                <span style="padding:4px 12px;border-radius:20px;font-size:11px;font-weight:600;background:#e8f4fd;color:#2c3e50">Menunggu</span>
                            @else
                                <span style="padding:4px 12px;border-radius:20px;font-size:11px;font-weight:600;background:#d4edda;color:#1a6e35">Dikembalikan</span>
                            @endif
                        </td>
                        <td>
                            <a href="{{ route('peminjaman.edit', $item->id) }}"
                                style="padding:6px 14px;background:#dbeafe;color:#2563eb;border:none;border-radius:8px;font-size:12px;font-weight:600;cursor:pointer;text-decoration:none;display:inline-block">
                                <i class="bi bi-pencil"></i>
                            </a>
                            <form action="{{ route('peminjaman.destroy', $item->id) }}" method="POST" class="d-inline">
                                @csrf
                                @method('DELETE')
                                <button type="submit" onclick="return confirm('Yakin hapus?')"
                                    style="padding:6px 14px;background:#fee2e2;color:#dc2626;border:none;border-radius:8px;font-size:12px;font-weight:600;cursor:pointer">
                                    <i class="bi bi-trash"></i>
                                </button>
                            </form>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="8" class="text-center text-muted py-4">Belum ada data peminjaman</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{-- Mobile Cards --}}
        <div class="peminjaman-mobile" style="padding:12px">
            @forelse($peminjaman as $item)
            <div class="pm-card">
                <div class="pm-card-top">
                    <div>
                        <div class="pm-card-name">
                            <i class="bi bi-person-fill" style="color:#1a6e35;font-size:13px"></i>
                            {{ $item->anggota->nama ?? '-' }}
                        </div>
                        <div class="pm-card-book">
                            <i class="bi bi-book" style="font-size:12px"></i>
                            {{ $item->buku->judul ?? '-' }}
                            @if($item->eksemplar)
                                <span class="pm-eksemplar">{{ $item->eksemplar->kode_buku }}</span>
                            @endif
                        </div>
                    </div>
                    <div>
                        @if($item->status == 'dipinjam' && $item->tanggal_kembali < now()->toDateString())
                            <span style="padding:4px 10px;border-radius:20px;font-size:10px;font-weight:600;background:#f8d7da;color:#721c24">Terlambat</span>
                        @elseif($item->status == 'dipinjam')
                            <span style="padding:4px 10px;border-radius:20px;font-size:10px;font-weight:600;background:#fff3cd;color:#856404">Dipinjam</span>
                        @elseif($item->status == 'menunggu_konfirmasi')
                            <span style="padding:4px 10px;border-radius:20px;font-size:10px;font-weight:600;background:#e8f4fd;color:#2c3e50">Menunggu</span>
                        @else
                            <span style="padding:4px 10px;border-radius:20px;font-size:10px;font-weight:600;background:#d4edda;color:#1a6e35">Selesai</span>
                        @endif
                    </div>
                </div>
                <div class="pm-card-meta">
                    <span><i class="bi bi-calendar-event"></i> Pinjam: {{ $item->tanggal_pinjam }}</span>
                    <span><i class="bi bi-calendar-check"></i> Kembali: {{ $item->tanggal_kembali }}</span>
                </div>
                <div class="pm-card-actions">
                    <a href="{{ route('peminjaman.edit', $item->id) }}"
                        style="padding:6px 14px;background:#dbeafe;color:#2563eb;border:none;border-radius:8px;font-size:12px;font-weight:600;cursor:pointer;text-decoration:none">
                        <i class="bi bi-pencil"></i> Edit
                    </a>
                    <form action="{{ route('peminjaman.destroy', $item->id) }}" method="POST" class="d-inline">
                        @csrf
                        @method('DELETE')
                        <button type="submit" onclick="return confirm('Yakin hapus?')"
                            style="padding:6px 14px;background:#fee2e2;color:#dc2626;border:none;border-radius:8px;font-size:12px;font-weight:600;cursor:pointer">
                            <i class="bi bi-trash"></i> Hapus
                        </button>
                    </form>
                </div>
            </div>
            @empty
            <div style="text-align:center;padding:40px 20px;color:#aaa">
                <i class="bi bi-journal-x" style="font-size:48px;display:block;margin-bottom:12px"></i>
                <p style="font-size:14px">Belum ada data peminjaman</p>
            </div>
            @endforelse
        </div>
    </div>
</div>
@endsection
