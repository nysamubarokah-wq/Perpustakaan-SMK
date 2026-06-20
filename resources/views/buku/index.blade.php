@extends('layouts.admin')

@section('header_title', 'Kelola Buku')

@section('content')
<div class="card-admin">
    <div style="padding:15px 25px;border-bottom:1px solid #eee;background:#fafafa">
        <form method="GET" action="{{ route('buku.index') }}">
            <div class="d-flex gap-2 flex-wrap">
                <input type="text" name="search"
                       value="{{ $search ?? '' }}"
                       placeholder="Cari judul, pengarang, ISBN, genre..."
                       class="form-control" style="border-radius:10px;min-width:150px;flex:1">
                <button type="submit" class="btn" style="background:linear-gradient(135deg,#1a6e35,#27ae60);color:white;border-radius:10px;padding:8px 20px;font-weight:600;white-space:nowrap">
                    <i class="bi bi-search"></i> Cari
                </button>
                <a href="{{ route('buku.create') }}" class="btn" style="background:linear-gradient(135deg,#1a6e35,#27ae60);color:white;border-radius:10px;padding:8px 20px;font-weight:600;white-space:nowrap;text-decoration:none">
                    <i class="bi bi-plus"></i> Tambah Buku
                </a>
                @if($search)
                    <a href="{{ route('buku.index') }}" class="btn btn-secondary" style="border-radius:10px;padding:8px 20px">
                        <i class="bi bi-x"></i> Reset
                    </a>
                @endif
            </div>
        </form>
    </div>

    <div class="card-admin-body">
        <table class="table table-hover">
            <thead style="background:#f8f9fa">
                <tr>
                    <th>#</th>
                    <th>Sampul</th>
                    <th>Judul</th>
                    <th>Pengarang</th>
                    <th>Genre</th>
                    <th>Lokasi</th>
                    <th>Stok</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse($buku as $item)
                <tr>
                    <td>{{ $loop->iteration }}</td>
                    <td>
                        @if($item->sampul)
                            <img src="{{ asset($item->sampul) }}" style="width:40px;height:55px;object-fit:cover;border-radius:5px">
                        @else
                            <div style="width:40px;height:55px;background:linear-gradient(135deg,#1a6e35,#27ae60);border-radius:5px;display:flex;align-items:center;justify-content:center;color:white;font-size:16px">
                                <i class="bi bi-book"></i>
                            </div>
                        @endif
                    </td>
                    <td>{{ $item->judul }}</td>
                    <td>{{ $item->pengarang }}</td>
                    <td>
                        @if($item->genre)
                            <span style="padding:3px 10px;background:#e8f5e9;color:#1a6e35;border-radius:20px;font-size:11px;font-weight:600">{{ $item->genre }}</span>
                        @else
                            <span style="color:#aaa">-</span>
                        @endif
                    </td>
                    <td>
                        @if($item->lokasi)
                            <span style="padding:3px 10px;background:#e8f4fd;color:#2c3e50;border-radius:20px;font-size:11px;font-weight:600">
                                <i class="bi bi-geo-alt"></i> {{ $item->lokasi }}
                            </span>
                        @else
                            <span style="color:#aaa">-</span>
                        @endif
                    </td>
                    <td>
                        <span style="padding:3px 10px;border-radius:20px;font-size:11px;font-weight:600;background:{{ $item->stok > 0 ? '#d4edda' : '#f8d7da' }};color:{{ $item->stok > 0 ? '#1a6e35' : '#721c24' }}">
                            {{ $item->stok }}
                        </span>
                    </td>
                    <td>
                        <a href="{{ route('buku.edit', $item->id) }}" class="btn btn-sm btn-warning me-1">
                            <i class="bi bi-pencil"></i>
                        </a>
                        <form action="{{ route('buku.destroy', $item->id) }}" method="POST" class="d-inline">
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
                    <td colspan="8" class="text-center text-muted py-4">Belum ada data buku</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection