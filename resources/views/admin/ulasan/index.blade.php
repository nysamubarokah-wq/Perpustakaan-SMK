@extends('layouts.admin')

@section('header_title', 'Kelola Ulasan')

@section('content')
<div class="card-admin">
    <div class="card-admin-header">
        <h5 style="margin:0"><i class="bi bi-star-fill" style="color:#f0932b"></i> Semua Ulasan & Rating</h5>
    </div>
    <div class="card-admin-body">
        <table class="table table-hover">
            <thead style="background:#f8f9fa">
                <tr>
                    <th>#</th>
                    <th>User</th>
                    <th>Buku</th>
                    <th>Rating</th>
                    <th>Komentar</th>
                    <th>Tanggal</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse($ulasanList as $item)
                <tr>
                    <td>{{ $loop->iteration }}</td>
                    <td>{{ $item->user->name ?? '-' }}</td>
                    <td>{{ $item->buku->judul ?? '-' }}</td>
                    <td style="color:#f0932b;white-space:nowrap">
                        @for($i = 1; $i <= 5; $i++)
                            <i class="bi bi-star{{ $i <= $item->rating ? '-fill' : '' }}"></i>
                        @endfor
                    </td>
                    <td style="max-width:250px">{{ $item->komentar ?? '-' }}</td>
                    <td style="white-space:nowrap">{{ $item->created_at->format('d M Y') }}</td>
                    <td>
                        <form action="{{ route('admin.ulasan.destroy', $item->id) }}" method="POST" onsubmit="return confirm('Hapus ulasan ini?')">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-sm btn-danger">
                                <i class="bi bi-trash"></i>
                            </button>
                        </form>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="7" class="text-center text-muted py-4">Belum ada ulasan</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection