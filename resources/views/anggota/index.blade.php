@extends('layouts.admin')

@section('title', 'Kelola Anggota')
@section('page-title', 'Kelola Anggota')

@section('content')

{{-- TABEL SISWA --}}
<div class="card-admin mb-4">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <div class="d-flex align-items-center gap-2">
            <i class="bi bi-people fs-4 text-success"></i>
            <h5 class="fw-bold m-0" style="font-size:18px;color:#222;">Daftar Anggota</h5>
        </div>
        <a href="{{ route('anggota.create') }}" class="btn btn-success px-3 rounded-3" style="background-color:#27ae60;border:none;">
            <i class="bi bi-plus-lg me-1"></i> Tambah Anggota
        </a>
    </div>
    <div class="card-admin-body">
        <div style="padding:15px 25px;border-bottom:1px solid #eee;background:#fafafa">
            <form method="GET" action="{{ route('anggota.index') }}">
                <div class="d-flex gap-2">
                    <input type="text" name="search" value="{{ $search ?? '' }}"
                        placeholder="Cari nama, email, no telepon..."
                        class="form-control" style="border-radius:10px">
                    <button type="submit" class="btn" style="background:linear-gradient(135deg,#1a6e35,#27ae60);color:white;border-radius:10px;padding:8px 20px;font-weight:600;white-space:nowrap">
                        <i class="bi bi-search"></i> Cari
                    </button>
                    @if($search ?? '')
                        <a href="{{ route('anggota.index') }}" class="btn btn-secondary" style="border-radius:10px;padding:8px 20px">
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
                    <th>Nama</th>
                    <th>NIS</th>
                    <th>Email</th>
                    <th>No. Telepon</th>
                    <th>Alamat</th>
                    <th>Tgl Daftar</th>
                    <th>Aksi</th>
                </tr>
            </thead>
         <tbody>
    @forelse($anggota->where('role', 'siswa') as $item)
    <tr>
        <td>{{ $loop->iteration }}</td>
        <td>{{ $item->nama }}</td>
        <td>{{ $item->nis ?? \App\Models\User::where('email', $item->email)->first()?->nis ?? '-' }}</td>
        <td>{{ $item->email }}</td>
        <td>{{ $item->no_telepon }}</td>
        <td>{{ $item->alamat }}</td>
        <td>{{ $item->tanggal_daftar }}</td>
        
        <td style="white-space:nowrap">
            {{-- HAPUS BLOK IF($userAdmin...) DARI SINI --}}
            
            {{-- BIARKAN TOMBOL ROLE (Jadikan Admin) DISINI --}}
            <form action="{{ route('admin.anggota.role', [$item->id, 'admin']) }}" method="POST" class="d-inline">
                @csrf @method('PUT')
                <button type="submit" class="btn btn-sm btn-info text-white" title="Jadikan Admin">
                    <i class="bi bi-shield-check"></i>
                </button>
            </form>

            <a href="{{ route('anggota.edit', $item->id) }}" class="btn btn-sm btn-warning">
                <i class="bi bi-pencil"></i>
            </a>

            <form action="{{ route('anggota.destroy', $item->id) }}" method="POST" class="d-inline">
                @csrf @method('DELETE')
                <button type="submit" class="btn btn-sm btn-danger" onclick="return confirm('Yakin hapus?')">
                    <i class="bi bi-trash"></i>
                </button>
            </form>
        </td>
    </tr>
    @empty
        <tr><td colspan="8" class="text-center text-muted py-4">Belum ada data siswa</td></tr>
    @endforelse
</tbody>
        </table>
    </div>
</div>

{{-- TABEL ADMIN --}}
<div class="card-admin">
    <div class="card-admin-header mb-3">
        <h5><i class="bi bi-shield-check" style="color:#1a6e35"></i> Daftar Admin</h5>
    </div>
    <div class="card-admin-body">
        <table class="table table-hover">
            <thead style="background:#f8f9fa">
                <tr>
                    <th>#</th>
                    <th>Nama</th>
                    <th>Email</th>
                    <th>No. Telepon</th>
                    <th>Status Tugas</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse($anggota->where('role', 'admin') as $item)
                @php $userAdmin = \App\Models\User::where('email', $item->email)->first(); @endphp
                <tr>
                    <td>{{ $loop->iteration }}</td>
                   <td>
    <div style="display:flex;align-items:center;gap:5px;flex-wrap:nowrap">
        <span>{{ $item->nama }}</span>
        @if($userAdmin?->id === auth()->id())
            <span style="font-size:10px;background:#e8f5e9;color:#1a6e35;padding:2px 8px;border-radius:10px;white-space:nowrap">Anda</span>
        @endif
        @if($userAdmin?->is_on_duty)
            <span style="font-size:10px;background:#fff3cd;color:#856404;padding:2px 8px;border-radius:10px;white-space:nowrap">⭐ Bertugas</span>
        @endif
    </div>
</td>
                    <td>{{ $item->email }}</td>
                    <td>{{ $item->no_telepon ?? '-' }}</td>
                    <td>
                        @if($userAdmin?->is_on_duty)
                            <span style="font-size:11px;background:#fff3cd;color:#856404;padding:4px 10px;border-radius:8px;font-weight:600">
                                ⭐ Sedang Bertugas
                            </span>
                        @else
                            <span style="font-size:11px;color:#aaa">Tidak Bertugas</span>
                        @endif
                    </td>
                    <td style="white-space:nowrap">
    {{-- Cek apakah userAdmin ada DAN statusnya tidak sedang bertugas --}}
    @if($userAdmin && !$userAdmin->is_on_duty)
        <form action="{{ route('admin.users.duty', $userAdmin->id) }}" method="POST" class="d-inline">
            @csrf
            <button type="submit" class="btn btn-sm" title="Set Bertugas"
                onclick="return confirm('Set {{ $item->nama }} sebagai penjaga yang sedang bertugas?')"
                style="background:#f0932b;border:none;color:white">
                <i class="bi bi-star"></i>
            </button>
        </form>
  @elseif($userAdmin && $userAdmin->is_on_duty)
    <form action="{{ route('admin.users.cabutDuty', $userAdmin->id) }}" method="POST" class="d-inline">
        @csrf
        <button type="submit" class="btn btn-sm"
            onclick="return confirm('Cabut status bertugas {{ $item->nama }}?')"
            style="background:#fff3cd;color:#856404;border:1px solid #f0932b">
            <i class="bi bi-star-fill"></i> Cabut
        </button>
    </form>
@endif
    {{-- Form lainnya taruh di sini --}}


                        <form action="{{ route('admin.anggota.role', [$item->id, 'siswa']) }}" method="POST" class="d-inline">
                            @csrf @method('PUT')
                            <button type="submit" class="btn btn-sm btn-warning" title="Jadikan Siswa">
                                <i class="bi bi-person"></i>
                            </button>
                        </form>

                        <a href="{{ route('anggota.edit', $item->id) }}" class="btn btn-sm btn-warning">
                            <i class="bi bi-pencil"></i>
                        </a>

                        <form action="{{ route('anggota.destroy', $item->id) }}" method="POST" class="d-inline">
                            @csrf @method('DELETE')
                            <button type="submit" class="btn btn-sm btn-danger" onclick="return confirm('Yakin hapus?')">
                                <i class="bi bi-trash"></i>
                            </button>
                        </form>
                    </td>
                </tr>
                @empty
                <tr><td colspan="6" class="text-center text-muted py-4">Belum ada data admin</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

@endsection