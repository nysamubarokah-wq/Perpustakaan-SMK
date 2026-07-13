@extends('layouts.admin')

@section('title', 'Daftar Admin - Perpustakaan Digital')
@section('page-title', 'Daftar Admin')

@section('content')

<style>
    @media (max-width: 768px) {
        .tbl-wrap { overflow-x: auto; -webkit-overflow-scrolling: touch; }
        .tbl-wrap table { min-width: 600px; font-size: 12px; }
        .tbl-wrap td, .tbl-wrap th { padding: 8px 10px !important; }
        .search-row { flex-direction: column !important; }
        .search-row .form-control { width: 100% !important; }
        .search-row .btn { width: 100% !important; }
    }
</style>

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

<div style="background:white;border-radius:16px;padding:20px 24px;border:1px solid #eee;margin-bottom:20px;display:flex;align-items:center;justify-content:space-between;flex-wrap:wrap;gap:16px">

    <div>
        <h4 style="font-weight:800;color:#222;margin:0 0 4px;font-size:18px">
            <i class="bi bi-shield-check" style="color:#1a6e35"></i> Daftar Admin
        </h4>
        <p style="font-size:13px;color:#888;margin:0">
            Total: <strong style="color:#1a6e35">{{ $anggota->count() }}</strong> admin
        </p>
    </div>

    <div style="display:flex;gap:8px;align-items:center">
        <input type="text" id="searchInput" value="{{ request('search') }}"
            placeholder="Cari nama / email..."
            style="padding:8px 14px;border:1.5px solid #eee;border-radius:10px;font-size:13px;outline:none;width:220px"
            onkeydown="if(event.key==='Enter'){window.location.href='?search='+encodeURIComponent(this.value)}">
        <button type="button" onclick="window.location.href='?search='+encodeURIComponent(document.getElementById('searchInput').value)"
                style="padding:8px 16px;background:linear-gradient(135deg,#1a6e35,#27ae60);color:white;border:none;border-radius:10px;font-size:13px;font-weight:600;cursor:pointer">
            <i class="bi bi-search"></i> Cari
        </button>
        @if(request('search'))
            <a href="{{ route('anggota.admin') }}"
               style="padding:8px 14px;background:#f3f4f6;color:#666;border:none;border-radius:10px;font-size:13px;font-weight:600;text-decoration:none">
                <i class="bi bi-x"></i>
            </a>
        @endif

        <a href="{{ route('anggota.index') }}"
           style="padding:8px 16px;background:#f3f4f6;color:#666;border:none;border-radius:10px;font-size:13px;font-weight:600;text-decoration:none;display:inline-flex;align-items:center;gap:6px">
            <i class="bi bi-arrow-left"></i> Siswa
        </a>
    </div>
</div>

<div style="background:white;border-radius:16px;border:1px solid #eee">
    <div class="tbl-wrap">
        <table class="table table-hover mb-0" style="font-size:13px">
            <thead style="background:#f8f9fa">
                <tr>
                    <th style="padding:12px 16px;font-weight:600;color:#555;border:none">#</th>
                    <th style="padding:12px 16px;font-weight:600;color:#555;border:none">Nama</th>
                    <th style="padding:12px 16px;font-weight:600;color:#555;border:none">Email</th>
                    <th style="padding:12px 16px;font-weight:600;color:#555;border:none">No. HP</th>
                    <th style="padding:12px 16px;font-weight:600;color:#555;border:none">Bertugas</th>
                    <th style="padding:12px 16px;font-weight:600;color:#555;border:none;text-align:center">Aksi</th>
                </tr>
            </thead>
            <tbody>
            @forelse($anggota as $item)
                @php $userAdmin = $item->user; @endphp
                <tr>
                    <td style="padding:12px 16px;vertical-align:middle;color:#aaa;font-size:12px">{{ $loop->iteration }}</td>
                    <td style="padding:12px 16px;vertical-align:middle">
                        <div style="display:flex;align-items:center;gap:10px">
                            @if($userAdmin?->foto)
                                <img src="{{ asset($userAdmin->foto) }}" alt="Foto"
                                     style="width:36px;height:36px;border-radius:50%;object-fit:cover;border:2px solid #e8f5e9;flex-shrink:0">
                            @else
                                <div style="width:36px;height:36px;background:#1a6e35;color:white;border-radius:50%;display:flex;align-items:center;justify-content:center;font-size:13px;font-weight:700;flex-shrink:0">
                                    {{ strtoupper(substr($item->nama,0,1)) }}
                                </div>
                            @endif
                            <div>
                                <div style="font-weight:600;color:#222">{{ $item->nama }}</div>
                                @if($userAdmin?->id === auth()->id())
                                    <span style="font-size:10px;background:#e8f5e9;color:#1a6e35;padding:1px 6px;border-radius:10px;font-weight:600">Anda</span>
                                @endif
                            </div>
                        </div>
                    </td>
                    <td style="padding:12px 16px;vertical-align:middle;color:#555;font-size:12px;word-break:break-all">{{ $item->email }}</td>
                    <td style="padding:12px 16px;vertical-align:middle;color:#555">{{ $item->no_telepon ?? '-' }}</td>
                    <td style="padding:12px 16px;vertical-align:middle">
                        @if($userAdmin?->is_on_duty)
                            <span style="background:#fff3cd;color:#d97706;padding:3px 10px;border-radius:20px;font-size:11px;font-weight:600;white-space:nowrap">
                                <i class="bi bi-star-fill"></i> Bertugas
                            </span>
                        @else
                            <span style="color:#ccc;font-size:12px">-</span>
                        @endif
                    </td>
                    <td style="padding:12px 16px;vertical-align:middle;text-align:center">
                        <div style="display:flex;gap:5px;justify-content:center;flex-wrap:nowrap">

                            @if($userAdmin && !$userAdmin->is_on_duty)
                                <form action="{{ route('admin.anggota.duty', $userAdmin->id) }}" method="POST" style="margin:0">
                                    @csrf
                                    <button type="submit" title="Set Bertugas"
                                        style="padding:6px 10px;background:#fef3c7;color:#d97706;border:none;border-radius:7px;font-size:12px;font-weight:600;cursor:pointer"
                                        onclick="return confirm('Set {{ $item->nama }} sebagai penjaga bertugas?')">
                                        <i class="bi bi-star"></i>
                                    </button>
                                </form>
                            @elseif($userAdmin && $userAdmin->is_on_duty)
                                <form action="{{ route('admin.anggota.cabutDuty', $userAdmin->id) }}" method="POST" style="margin:0">
                                    @csrf
                                    <button type="submit" title="Cabut Tugas"
                                        style="padding:6px 10px;background:#fff3cd;color:#d97706;border:none;border-radius:7px;font-size:12px;font-weight:600;cursor:pointer"
                                        onclick="return confirm('Cabut status bertugas?')">
                                        <i class="bi bi-star-fill"></i>
                                    </button>
                                </form>
                            @endif

                            @if($userAdmin?->id !== auth()->id())
                                <form action="{{ route('admin.anggota.role', [$item->id, 'siswa']) }}" method="POST" style="margin:0">
                                    @csrf @method('PUT')
                                    <button type="submit" title="Jadikan Siswa"
                                        style="padding:6px 10px;background:#f3f4f6;color:#666;border:none;border-radius:7px;font-size:12px;font-weight:600;cursor:pointer"
                                        onclick="return confirm('Jadikan siswa?')">
                                        <i class="bi bi-person"></i>
                                    </button>
                                </form>
                            @endif

                            <a href="{{ route('anggota.edit', $item->id) }}?from=admin"
                               title="Edit"
                               style="padding:6px 10px;background:#dbeafe;color:#2563eb;border:none;border-radius:7px;font-size:12px;font-weight:600;text-decoration:none;display:inline-flex;align-items:center">
                                <i class="bi bi-pencil"></i>
                            </a>
                        </div>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="6" class="text-center py-5">
                        <div style="color:#ccc">
                            <i class="bi bi-people" style="font-size:48px"></i>
                            <p class="mt-2 mb-0">Belum ada admin</p>
                        </div>
                    </td>
                </tr>
            @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection
