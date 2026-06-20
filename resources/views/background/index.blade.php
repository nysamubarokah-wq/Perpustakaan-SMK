@extends('layouts.admin')

@section('header_title', 'Kelola Background')

@section('content')
<div class="card-admin">
    <div class="card-admin-header" style="display:flex;align-items:center;justify-content:space-between">
    <h5 style="margin:0"><i class="bi bi-palette" style="color:#1a6e35"></i> Daftar Background</h5>
    <a href="{{ route('background.create') }}" class="btn btn-sm" style="background:linear-gradient(135deg,#1a6e35,#27ae60);color:white;border-radius:10px;padding:8px 18px;font-weight:600">
        <i class="bi bi-plus-circle"></i> Tambah Background
    </a>
</div>
    </div>
    <div class="card-admin-body">
        <table class="table table-hover">
            <thead style="background:#f8f9fa">
                <tr>
                    <th>#</th>
                    <th>Preview</th>
                    <th>Nama</th>
                    <th>Tipe</th>
                    <th>Harga</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse($backgrounds as $item)
                <tr>
                    <td>{{ $loop->iteration }}</td>
                    <td>
                        <div style="width:70px;height:45px;border-radius:6px;overflow:hidden;position:relative;{{ $item->type === 'color' ? $item->value : 'background:#000' }}">
                            @if($item->type === 'image')
                                <img src="{{ asset($item->value) }}" style="width:100%;height:100%;object-fit:cover">
                            @elseif($item->type === 'video')
                                <video muted style="width:100%;height:100%;object-fit:cover">
                                    <source src="{{ asset($item->value) }}">
                                </video>
                            @endif
                        </div>
                    </td>
                    <td>{{ $item->nama }}</td>
                    <td>
                        <span style="padding:3px 10px;background:#e8f5e9;color:#1a6e35;border-radius:20px;font-size:11px;font-weight:600;text-transform:capitalize">{{ $item->type }}</span>
                    </td>
                    <td>🪙 {{ $item->harga }}</td>
                    <td>
                        <a href="{{ route('background.edit', $item->id) }}" class="btn btn-sm btn-warning me-1">
                            <i class="bi bi-pencil"></i>
                        </a>
                        @if($item->slug !== 'default')
                        <form action="{{ route('background.destroy', $item->id) }}" method="POST" class="d-inline">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-sm btn-danger" onclick="return confirm('Yakin hapus background ini?')">
                                <i class="bi bi-trash"></i>
                            </button>
                        </form>
                        @endif
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="6" class="text-center text-muted py-4">Belum ada data background</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection