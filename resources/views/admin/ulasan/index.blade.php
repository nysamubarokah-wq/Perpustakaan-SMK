@extends('layouts.admin')

@section('header_title', 'Kelola Ulasan')

@section('content')

<style>
    @media (max-width: 768px) {
        .ulasan-filter > div { min-width: 100% !important; }
        .ulasan-filter { gap: 8px !important; }
        .ulasan-tbl-wrap { overflow-x: auto; -webkit-overflow-scrolling: touch; }
        .ulasan-tbl-wrap table { min-width: 850px; }
        .ulasan-tbl-wrap td, .ulasan-tbl-wrap th { padding: 8px 10px !important; font-size: 12px; }
        .ulasan-tbl-wrap .btn, .ulasan-tbl-wrap button { padding: 4px 8px !important; font-size: 10px !important; }
    }
</style>
@if(session('success'))
    <div style="background:#dcfce7;color:#166534;padding:12px 16px;border-radius:10px;margin-bottom:16px;font-size:13px;font-weight:500">
        <i class="bi bi-check-circle-fill"></i> {{ session('success') }}
    </div>
@endif
@if(session('error'))
    <div style="background:#fee2e2;color:#991b1b;padding:12px 16px;border-radius:10px;margin-bottom:16px;font-size:13px;font-weight:500">
        <i class="bi bi-exclamation-circle-fill"></i> {{ session('error') }}
    </div>
@endif
@if($errors->any())
    <div style="background:#fee2e2;color:#991b1b;padding:12px 16px;border-radius:10px;margin-bottom:16px;font-size:13px;font-weight:500">
        <i class="bi bi-exclamation-circle-fill"></i> {{ $errors->first() }}
    </div>
@endif

<!-- HEADER ACTIONS -->
<div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:16px;flex-wrap:wrap;gap:10px">
    <h5 style="margin:0"><i class="bi bi-star-fill" style="color:#f0932b"></i> Semua Ulasan & Rating</h5>
    <div style="display:flex;gap:8px;flex-wrap:wrap">
    </div>
</div>

<!-- FILTER -->
<div class="card-admin" style="margin-bottom:16px">
    <div class="card-admin-body">
        <form method="GET" action="{{ route('admin.ulasan.index') }}">
            <div style="display:flex;gap:12px;flex-wrap:wrap;align-items:end" class="ulasan-filter">
                <div style="flex:1;min-width:180px">
                    <label style="font-size:12px;font-weight:600;color:#666;margin-bottom:4px;display:block">Cari</label>
                    <input type="text" name="search" value="{{ request('search') }}" placeholder="Nama user, buku, komentar..."
                           style="width:100%;padding:8px 12px;border:1px solid #ddd;border-radius:8px;font-size:13px">
                </div>
                <div style="min-width:120px">
                    <label style="font-size:12px;font-weight:600;color:#666;margin-bottom:4px;display:block">Rating</label>
                    <select name="rating" style="width:100%;padding:8px 12px;border:1px solid #ddd;border-radius:8px;font-size:13px">
                        <option value="">Semua</option>
                        @for($i = 5; $i >= 1; $i--)
                            <option value="{{ $i }}" {{ request('rating') == $i ? 'selected' : '' }}>{{ $i }} ⭐</option>
                        @endfor
                    </select>
                </div>
                <div style="min-width:150px">
                    <label style="font-size:12px;font-weight:600;color:#666;margin-bottom:4px;display:block">Buku</label>
                    <select name="buku_id" style="width:100%;padding:8px 12px;border:1px solid #ddd;border-radius:8px;font-size:13px">
                        <option value="">Semua Buku</option>
                        @foreach($bukuList as $buku)
                            <option value="{{ $buku->id }}" {{ request('buku_id') == $buku->id ? 'selected' : '' }}>{{ Str::limit($buku->judul, 30) }}</option>
                        @endforeach
                    </select>
                </div>
                <div style="min-width:130px">
                    <label style="font-size:12px;font-weight:600;color:#666;margin-bottom:4px;display:block">Status Balasan</label>
                    <select name="status_balasan" style="width:100%;padding:8px 12px;border:1px solid #ddd;border-radius:8px;font-size:13px">
                        <option value="">Semua</option>
                        <option value="sudah" {{ request('status_balasan') === 'sudah' ? 'selected' : '' }}>Sudah Dibalas</option>
                        <option value="belum" {{ request('status_balasan') === 'belum' ? 'selected' : '' }}>Belum Dibalas</option>
                    </select>
                </div>
                <div style="display:flex;gap:6px">
                    <button type="submit"
                            style="padding:8px 16px;background:#1a6e35;color:white;border:none;border-radius:8px;font-size:13px;font-weight:600;cursor:pointer">
                        <i class="bi bi-search"></i> Filter
                    </button>
                    <a href="{{ route('admin.ulasan.index') }}"
                       style="padding:8px 16px;background:#f3f4f6;color:#6b7280;border:none;border-radius:8px;font-size:13px;font-weight:600;text-decoration:none">
                        <i class="bi bi-x-circle"></i> Reset
                    </a>
                </div>
            </div>
        </form>
    </div>
</div>

<!-- BULK DELETE -->
<div style="margin-bottom:12px;display:flex;gap:8px;align-items:center">
    <form id="bulkForm" action="{{ route('admin.ulasan.bulkDelete') }}" method="POST" onsubmit="return confirm('Hapus semua ulasan yang dipilih?')" style="display:contents">
        @csrf
        <button type="submit" id="bulkDeleteBtn" style="display:none;padding:6px 14px;background:#fee2e2;color:#dc2626;border:none;border-radius:8px;font-size:12px;font-weight:600;cursor:pointer">
            <i class="bi bi-trash"></i> Hapus Terpilih (<span id="selectedCount">0</span>)
        </button>
    </form>
    <span id="selectAllWrap" style="display:none;font-size:12px;color:#888">
        <label style="cursor:pointer;display:flex;align-items:center;gap:4px">
            <input type="checkbox" id="selectAll" style="cursor:pointer"> Pilih Semua
        </label>
    </span>
</div>

<!-- TABLE -->
<div class="card-admin">
    <div class="card-admin-body">
        <div class="ulasan-tbl-wrap">
        <table class="table table-hover">
            <thead style="background:#f8f9fa">
                <tr>
                    <th style="width:40px"><input type="checkbox" id="checkAll" style="cursor:pointer"></th>
                    <th>#</th>
                    <th>User</th>
                    <th>Buku</th>
                    <th>Rating</th>
                    <th>Komentar</th>
                    <th>Balasan Admin</th>
                    <th>Tanggal</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse($ulasanList as $item)
                <tr>
                    <td><input type="checkbox" value="{{ $item->id }}" class="row-check" style="cursor:pointer"></td>
                    <td>{{ ($ulasanList->currentPage() - 1) * $ulasanList->perPage() + $loop->iteration }}</td>
                    <td>{{ $item->user->name ?? '-' }}</td>
                    <td>{{ Str::limit($item->buku->judul ?? '-', 25) }}</td>
                    <td style="color:#f0932b;white-space:nowrap">
                        @for($i = 1; $i <= 5; $i++)
                            <i class="bi bi-star{{ $i <= $item->rating ? '-fill' : '' }}"></i>
                        @endfor
                    </td>
                    <td style="max-width:180px">{{ $item->komentar ?? '-' }}</td>
                    <td style="max-width:250px">
                        @if($item->balasan_admin)
                            <div id="balasan-view-{{ $item->id }}" style="background:#f0f9ff;border-left:3px solid #3b82f6;padding:8px 10px;border-radius:6px;font-size:13px;margin-bottom:6px">
                                <i class="bi bi-reply-fill" style="color:#3b82f6"></i> {{ $item->balasan_admin }}
                            </div>
                            <div id="balasan-edit-{{ $item->id }}" style="display:none;margin-bottom:6px">
                                <form action="{{ route('admin.ulasan.editBalasan', $item->id) }}" method="POST">
                                    @csrf
                                    @method('PUT')
                                    <textarea name="balasan_admin" rows="2" required
                                              style="width:100%;padding:8px;border:1px solid #d1d5db;border-radius:8px;font-size:13px;resize:vertical">{{ $item->balasan_admin }}</textarea>
                                    <div style="margin-top:6px;display:flex;gap:6px">
                                        <button type="submit"
                                                style="padding:4px 12px;background:#3b82f6;color:#fff;border:none;border-radius:6px;font-size:11px;font-weight:600;cursor:pointer">
                                            <i class="bi bi-check"></i> Simpan
                                        </button>
                                        <button type="button" onclick="toggleEdit({{ $item->id }})"
                                                style="padding:4px 12px;background:#f3f4f6;color:#6b7280;border:none;border-radius:6px;font-size:11px;font-weight:600;cursor:pointer">
                                            Batal
                                        </button>
                                    </div>
                                </form>
                            </div>
                            <div id="balasan-actions-{{ $item->id }}" style="display:flex;gap:4px">
                                <button type="button" onclick="toggleEdit({{ $item->id }})"
                                        style="padding:4px 10px;background:#dbeafe;color:#2563eb;border:none;border-radius:6px;font-size:11px;font-weight:600;cursor:pointer">
                                    <i class="bi bi-pencil"></i> Edit
                                </button>
                                <form action="{{ route('admin.ulasan.hapusBalasan', $item->id) }}" method="POST" style="display:inline" onsubmit="return confirm('Hapus balasan ini?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit"
                                            style="padding:4px 10px;background:#fee2e2;color:#dc2626;border:none;border-radius:6px;font-size:11px;font-weight:600;cursor:pointer">
                                        <i class="bi bi-x-circle"></i> Hapus
                                    </button>
                                </form>
                            </div>
                        @else
                            <button type="button"
                                    onclick="toggleForm({{ $item->id }})"
                                    style="padding:6px 14px;background:#dbeafe;color:#2563eb;border:none;border-radius:8px;font-size:12px;font-weight:600;cursor:pointer">
                                <i class="bi bi-reply"></i> Balas
                            </button>
                            <form id="form-balas-{{ $item->id }}" action="{{ route('admin.ulasan.balas', $item->id) }}" method="POST" style="display:none;margin-top:8px">
                                @csrf
                                <textarea name="balasan_admin" rows="2" placeholder="Tulis balasan..." required
                                          style="width:100%;padding:8px;border:1px solid #d1d5db;border-radius:8px;font-size:13px;resize:vertical"></textarea>
                                <div style="margin-top:6px;display:flex;gap:6px">
                                    <button type="submit"
                                            style="padding:6px 14px;background:#3b82f6;color:#fff;border:none;border-radius:8px;font-size:12px;font-weight:600;cursor:pointer">
                                        <i class="bi bi-send"></i> Kirim
                                    </button>
                                    <button type="button" onclick="toggleForm({{ $item->id }})"
                                            style="padding:6px 14px;background:#f3f4f6;color:#6b7280;border:none;border-radius:8px;font-size:12px;font-weight:600;cursor:pointer">
                                        Batal
                                    </button>
                                </div>
                            </form>
                        @endif
                    </td>
                    <td style="white-space:nowrap">{{ $item->created_at->format('d M Y') }}</td>
                    <td>
                        <form action="{{ route('admin.ulasan.destroy', $item->id) }}" method="POST" onsubmit="return confirm('Hapus ulasan ini?')">
                            @csrf
                            @method('DELETE')
                            <button type="submit"
                                    style="padding:6px 14px;background:#fee2e2;color:#dc2626;border:none;border-radius:8px;font-size:12px;font-weight:600;cursor:pointer">
                                <i class="bi bi-trash"></i> Hapus
                            </button>
                        </form>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="9" class="text-center text-muted py-4">Belum ada ulasan</td>
                </tr>
                @endforelse
            </tbody>
        </table>
        </div>

        <!-- PAGINATION -->
        <div style="margin-top:16px">
            {{ $ulasanList->links() }}
        </div>
    </div>
</div>

@endsection

@section('script')
<script>
function toggleForm(id) {
    const form = document.getElementById('form-balas-' + id);
    if (form) {
        form.style.display = form.style.display === 'none' ? 'block' : 'none';
    }
}

function toggleEdit(id) {
    const view = document.getElementById('balasan-view-' + id);
    const edit = document.getElementById('balasan-edit-' + id);
    const actions = document.getElementById('balasan-actions-' + id);
    if (view && edit) {
        const isHidden = edit.style.display === 'none';
        view.style.display = isHidden ? 'none' : 'block';
        edit.style.display = isHidden ? 'block' : 'none';
        if (actions) {
            actions.style.display = isHidden ? 'none' : 'flex';
        }
    }
}

// Bulk delete
const checkAll = document.getElementById('checkAll');
const rowChecks = document.querySelectorAll('.row-check');
const bulkDeleteBtn = document.getElementById('bulkDeleteBtn');
const selectedCount = document.getElementById('selectedCount');
const selectAllWrap = document.getElementById('selectAllWrap');
const selectAll = document.getElementById('selectAll');

function updateBulkUI() {
    const checked = document.querySelectorAll('.row-check:checked');
    const count = checked.length;
    bulkDeleteBtn.style.display = count > 0 ? 'inline-flex' : 'none';
    selectAllWrap.style.display = count > 0 ? 'inline' : 'none';
    selectedCount.textContent = count;
    checkAll.checked = count === rowChecks.length && rowChecks.length > 0;
    if (selectAll) selectAll.checked = checkAll.checked;
}

checkAll.addEventListener('change', function() {
    rowChecks.forEach(cb => cb.checked = this.checked);
    updateBulkUI();
});

if (selectAll) {
    selectAll.addEventListener('change', function() {
        rowChecks.forEach(cb => cb.checked = this.checked);
        updateBulkUI();
    });
}

rowChecks.forEach(cb => {
    cb.addEventListener('change', updateBulkUI);
});

// Fix bulk form submit - parse JSON array to form data
document.getElementById('bulkForm').addEventListener('submit', function(e) {
    const checked = document.querySelectorAll('.row-check:checked');
    const ids = Array.from(checked).map(cb => cb.value);

    if (ids.length === 0) {
        e.preventDefault();
        alert('Pilih ulasan yang ingin dihapus terlebih dahulu!');
        return false;
    }

    // Remove old hidden input
    const oldInput = document.getElementById('bulkIds');
    if (oldInput) oldInput.remove();

    // Add new hidden inputs for each ID
    ids.forEach(id => {
        const input = document.createElement('input');
        input.type = 'hidden';
        input.name = 'ids[]';
        input.value = id;
        this.appendChild(input);
    });
});
</script>
@endsection
