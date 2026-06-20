@extends('layouts.admin')

@section('title', 'Kelola User')
@section('page-title', 'Kelola User')

@section('content')
<div class="card-admin">
    <div class="card-admin-header">
        <h5><i class="bi bi-people" style="color:#1a6e35"></i> Daftar User</h5>
    </div>
    <div class="card-admin-body">
        <table class="table table-hover">
            <thead style="background:#f8f9fa">
                <tr>
                    <th>#</th>
                    <th>Nama</th>
                    <th>Email</th>
                    <th>NIS</th>
                    <th>Role</th>
                    <th>Terdaftar</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse($users as $index => $user)
                <tr>
                    <td>{{ $index + 1 }}</td>
                    <td>
                        <div class="d-flex align-items-center gap-2">
                            <img src="https://ui-avatars.com/api/?name={{ urlencode($user->name) }}&background=1a6e35&color=fff&size=50"
                                 style="width:35px;height:35px;border-radius:50%">
                            {{ $user->name }}
                            @if($user->id === auth()->id())
                                <span style="font-size:10px;background:#e8f5e9;color:#1a6e35;padding:2px 8px;border-radius:10px">Anda</span>
                            @endif
                        </div>
                    </td>
                    <td>{{ $user->email }}</td>
                    <td>{{ $user->nis ?? '-' }}</td>
                    <td>
                        <span style="padding:4px 12px;border-radius:20px;font-size:11px;font-weight:600;
                            background:{{ $user->role === 'admin' ? '#d4edda' : '#e8f4fd' }};
                            color:{{ $user->role === 'admin' ? '#1a6e35' : '#2c3e50' }}">
                            {{ ucfirst($user->role) }}
                        </span>
                    </td>
                    <td>{{ $user->created_at->format('d M Y') }}</td>
                    <td>
                        @if($user->id !== auth()->id())
                            <form action="{{ route('admin.users.role', $user->id) }}" method="POST" class="d-inline">
                                @csrf
                                @method('PUT')
                                <button type="submit" 
                                    onclick="return confirm('Ubah role {{ $user->name }} menjadi {{ $user->role === 'admin' ? 'siswa' : 'admin' }}?')"
                                    class="btn btn-sm"
                                    style="background:{{ $user->role === 'admin' ? '#fff3cd' : '#d4edda' }};
                                           color:{{ $user->role === 'admin' ? '#856404' : '#1a6e35' }};
                                           border:none;border-radius:8px;font-size:12px;font-weight:600;padding:6px 12px">
                                    @if($user->role === 'admin')
                                        <i class="bi bi-arrow-down-circle"></i> Jadikan Siswa
                                    @else
                                        <i class="bi bi-arrow-up-circle"></i> Jadikan Admin
                                    @endif
                                </button>
                            </form>
                        @else
                            <span style="color:#aaa;font-size:12px">-</span>
                        @endif
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="7" class="text-center text-muted py-4">Belum ada user</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection