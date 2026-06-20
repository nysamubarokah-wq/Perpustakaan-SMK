@extends('layouts.admin')

@section('title', 'Kelola Peminjaman')
@section('page-title', 'Kelola Peminjaman')

@section('content')
<div class="card-admin">
  <div class="d-flex justify-content-between align-items-center mb-3">
    <div class="d-flex align-items-center gap-2">
        <i class="bi bi-journal-check fs-4 text-success"></i>
        <h5 class="fw-bold m-0" style="font-size: 18px; color: #222;">Daftar Peminjaman</h5>
    </div>
    <a href="{{ route('peminjaman.create') }}" class="btn btn-success px-3 rounded-3" style="background-color: #27ae60; border: none;">
        <i class="bi bi-plus-lg me-1"></i> Tambah Peminjaman
    </a>
</div>
    <div class="card-admin-body">
        <div style="padding:15px 25px;border-bottom:1px solid #eee;background:#fafafa">
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
        <table class="table table-hover">
            <thead style="background:#f8f9fa">
                <tr>
                    <th>#</th>
                    <th>Anggota</th>
                    <th>Buku</th>
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
                    <td>{{ $item->tanggal_pinjam }}</td>
                    <td>{{ $item->tanggal_kembali }}</td>
                    <td>
                        @if($item->status == 'dipinjam' && $item->tanggal_kembali < now()->toDateString())
                            <span style="padding:4px 12px;border-radius:20px;font-size:11px;font-weight:600;background:#f8d7da;color:#721c24">Terlambat</span>
                        @elseif($item->status == 'dipinjam')
                            <span style="padding:4px 12px;border-radius:20px;font-size:11px;font-weight:600;background:#fff3cd;color:#856404">Dipinjam</span>
                        @else
                            <span style="padding:4px 12px;border-radius:20px;font-size:11px;font-weight:600;background:#d4edda;color:#1a6e35">Dikembalikan</span>
                        @endif
                    </td>
                    <td>
                        <a href="{{ route('peminjaman.edit', $item->id) }}" class="btn btn-sm btn-warning me-1">
                            <i class="bi bi-pencil"></i>
                        </a>
                        <form action="{{ route('peminjaman.destroy', $item->id) }}" method="POST" class="d-inline">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-sm btn-danger" onclick="return confirm('Yakin hapus?')">
                                <i class="bi bi-trash"></i>
                            </button>
                        </form>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="7" class="text-center text-muted py-4">Belum ada data peminjaman</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection