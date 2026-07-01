@extends('layouts.admin')

@section('title', 'Kelola Anggota')
@section('page-title', 'Kelola Anggota')

@section('content')

<style>
    .anggota-search-bar .d-flex { gap: 8px; }
    @media (max-width: 768px) {
        .anggota-search-bar {
            padding: 12px 16px !important;
        }
        .anggota-search-bar .d-flex {
            flex-direction: column;
        }
        .anggota-search-bar .form-control { font-size: 14px; }
        .anggota-search-bar button,
        .anggota-search-bar a {
            width: 100%;
            text-align: center;
            justify-content: center;
        }
        .anggota-table-wrap { overflow-x: auto; -webkit-overflow-scrolling: touch; }
        .anggota-table-wrap table { min-width: 750px; }
        .anggota-table-wrap td, .anggota-table-wrap th {
            padding: 8px 10px !important;
            font-size: 12px;
        }
        .anggota-table-wrap .btn-sm {
            padding: 4px 6px !important;
            font-size: 10px !important;
        }
    }
    @media (max-width: 480px) {
        .anggota-table-wrap table { min-width: 650px; }
        .anggota-table-wrap td, .anggota-table-wrap th {
            padding: 6px 8px !important;
            font-size: 11px;
        }
    }
</style>

{{-- TABEL SISWA --}}
<div class="card-admin mb-4">
    <x-admin-card-header title="Daftar Anggota" icon="bi bi-people">
        <x-slot:action>
            <a href="{{ route('anggota.create') }}" style="padding:8px 18px;background:linear-gradient(135deg,#1a6e35,#27ae60);color:white;border:none;border-radius:10px;font-size:13px;font-weight:600;cursor:pointer;text-decoration:none">
                <i class="bi bi-plus-lg"></i> Tambah Anggota
            </a>
        </x-slot:action>
    </x-admin-card-header>
    <div class="card-admin-body">
        <div style="padding:15px 25px;border-bottom:1px solid #eee;background:#fafafa" class="anggota-search-bar">
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
        <div class="anggota-table-wrap">
        <table class="table table-hover">
            <thead style="background:#f8f9fa">
                <tr>
                    <th style="width:35px">#</th>
                    <th>Nama</th>
                    <th>NIS</th>
                    <th>Email</th>
                    <th>No. Telepon</th>
                    <th>Alamat</th>
                    <th>Tgl Daftar</th>
                    <th style="min-width:120px">Aksi</th>
                </tr>
            </thead>
         <tbody>
    @forelse($anggota->where('role', 'siswa') as $item)
    <tr>
        <td>{{ $loop->iteration }}</td>
        <td>{{ $item->nama }}</td>
        <td style="white-space:nowrap">{{ $item->nis ?? \App\Models\User::where('email', $item->email)->first()?->nis ?? '-' }}</td>
        <td style="white-space:nowrap">{{ $item->email }}</td>
        <td style="white-space:nowrap">{{ $item->no_telepon }}</td>
        <td>{{ $item->alamat }}</td>
        <td style="white-space:nowrap">{{ $item->tanggal_daftar }}</td>
        
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
</div>

{{-- TABEL ADMIN --}}
<div class="card-admin">
    <div class="card-admin-header mb-3">
        <h5><i class="bi bi-shield-check" style="color:#1a6e35"></i> Daftar Admin</h5>
    </div>
    <div class="card-admin-body">
        <div class="anggota-table-wrap">
        <table class="table table-hover">
            <thead style="background:#f8f9fa">
                <tr>
                    <th style="width:35px">#</th>
                    <th>Nama</th>
                    <th>Email</th>
                    <th>No. Telepon</th>
                    <th>Status Tugas</th>
                    <th style="min-width:150px">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse($anggota->where('role', 'admin') as $item)
                @php $userAdmin = \App\Models\User::where('email', $item->email)->first(); @endphp
                <tr>
                    <td>{{ $loop->iteration }}</td>
                   <td>
    <div style="display:flex;align-items:center;gap:4px;flex-wrap:nowrap">
        <span style="white-space:nowrap">{{ $item->nama }}</span>
        @if($userAdmin?->id === auth()->id())
            <span style="font-size:9px;background:#e8f5e9;color:#1a6e35;padding:1px 6px;border-radius:10px;white-space:nowrap;line-height:1.4">Anda</span>
        @endif
        @if($userAdmin?->is_on_duty)
            <span style="font-size:9px;background:#fff3cd;color:#856404;padding:1px 6px;border-radius:10px;white-space:nowrap;line-height:1.4">⭐</span>
        @endif
    </div>
</td>
                    <td style="white-space:nowrap">{{ $item->email }}</td>
                    <td style="white-space:nowrap">{{ $item->no_telepon ?? '-' }}</td>
                    <td style="white-space:nowrap">
                        @if($userAdmin?->is_on_duty)
                            <span style="font-size:11px;background:#fff3cd;color:#856404;padding:4px 10px;border-radius:8px;font-weight:600;white-space:nowrap">
                                ⭐ Bertugas
                            </span>
                        @else
                            <span style="font-size:11px;color:#aaa">-</span>
                        @endif
                    </td>
                    <td style="white-space:nowrap">
    {{-- Cek apakah userAdmin ada DAN statusnya tidak sedang bertugas --}}
    @if($userAdmin && !$userAdmin->is_on_duty)
        <form action="{{ route('admin.anggota.duty', $userAdmin->id) }}" method="POST" class="d-inline">
            @csrf
            <button type="submit" class="btn btn-sm" title="Set Bertugas"
                onclick="return confirm('Set {{ $item->nama }} sebagai penjaga yang sedang bertugas?')"
                style="background:#f0932b;border:none;color:white">
                <i class="bi bi-star"></i>
            </button>
        </form>
  @elseif($userAdmin && $userAdmin->is_on_duty)
    <form action="{{ route('admin.anggota.cabutDuty', $userAdmin->id) }}" method="POST" class="d-inline">
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
</div>

@endsection