@extends('layouts.admin')

@section('header_title', 'Persetujuan Pengembalian')

@section('content')
    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show mb-3" role="alert" style="border-radius: 10px;">
            <i class="bi bi-check-circle-fill me-2"></i> {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <div class="card p-4 border-0 shadow-sm" style="border-radius: 15px;">
        <div class="d-flex align-items-center gap-2 mb-3">
            <i class="bi bi-arrow-counterclockwise fs-4 text-success"></i>
            <h5 class="fw-bold m-0" style="font-size: 18px; color: #222;">Daftar Persetujuan Pengembalian</h5>
        </div>
        
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
               <thead>
    <tr>
        <th style="width: 5%">#</th>
        <th style="width: 20%">Anggota</th>
        <th style="width: 25%">Buku</th>
        <th style="width: 12%">Tgl Pinjam</th>
        <th style="width: 12%">Tgl Kembali</th>
        <th style="width: 16%"> Denda</th>
        <th style="width: 10%">Aksi</th>
    </tr>
</thead>
<tbody>
    @forelse($persetujuan as $index => $item)
    <tr>
        <td>{{ $index + 1 }}</td>
        <td class="text-capitalize">{{ $item->anggota->nama ?? '-' }}</td>
        <td>{{ $item->buku->judul ?? '-' }}</td>
        <td>{{ $item->tanggal_pinjam }}</td>
        <td>{{ $item->tanggal_kembali }}</td>
        <td>
            @if($item->taksiran_denda > 0)
                <span class="badge bg-danger rounded-pill px-2 py-1 mb-1">Terlambat {{ (int)$item->terlambat_hari }} Hari</span>
                <div class="fw-bold text-danger" style="font-size: 13px;">Rp {{ number_format($item->taksiran_denda, 0, ',', '.') }}</div>
            @else
                <span class="badge bg-success rounded-pill px-2 py-1">Tepat Waktu</span>
                <div class="text-muted" style="font-size: 12px;">Tidak ada denda</div>
            @endif
        </td>
        <td>
            <form action="{{ route('admin.pengembalian.setujui', $item->id) }}" method="POST" onsubmit="return confirm('Yakin ingin menyetujui pengembalian buku ini?')">
                @csrf
                @method('PUT')
                <button type="submit" class="btn btn-success btn-sm rounded-3" style="background-color: #27ae60; border: none;">
                    Setujui
                </button>
            </form>
        </td>
    </tr>
    @empty
        <tr>
            <td colspan="7" class="text-center text-muted">Tidak ada data persetujuan pengembalian.</td>
        </tr>
    @endforelse
</tbody>
            </table>
        </div>
    </div>
@endsection