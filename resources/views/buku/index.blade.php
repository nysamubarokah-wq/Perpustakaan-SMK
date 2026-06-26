@extends('layouts.admin')

@section('header_title', 'Kelola Buku')

@section('content')

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

<div class="card-admin">
    {{-- TOOLBAR --}}
    <div style="padding:15px 25px;border-bottom:1px solid #eee;background:#fafafa">
        <form method="GET" action="{{ route('buku.index') }}">
            <div class="d-flex gap-2 flex-wrap">
    {{-- Baris 1: Search + Cari + Reset --}}
    <div class="d-flex gap-2 w-100">
        <input type="text" name="search"
               value="{{ $search ?? '' }}"
               placeholder="Cari judul, pengarang, ISBN, genre..."
               class="form-control" style="border-radius:10px;flex:1">
        <button type="submit" class="btn" style="background:linear-gradient(135deg,#1a6e35,#27ae60);color:white;border-radius:10px;padding:8px 20px;font-weight:600;white-space:nowrap">
            <i class="bi bi-search"></i> Cari
        </button>
        @if($search)
        <a href="{{ route('buku.index') }}" class="btn btn-secondary" style="border-radius:10px;padding:8px 16px;white-space:nowrap">
            <i class="bi bi-x"></i> Reset
        </a>
        @endif
    </div>
    {{-- Baris 2: Tambah + Import --}}
    <a href="{{ route('buku.create') }}" class="btn" style="background:linear-gradient(135deg,#1a6e35,#27ae60);color:white;border-radius:10px;padding:8px 20px;font-weight:600;white-space:nowrap;text-decoration:none">
        <i class="bi bi-plus"></i> Tambah Buku
    </a>
    <button type="button" onclick="document.getElementById('modalImport').style.display='flex'"
        class="btn" style="background:#2c3e50;color:white;border-radius:10px;padding:8px 20px;font-weight:600;white-space:nowrap">
        <i class="bi bi-upload"></i> Import CSV
    </button>
</div>
        </form>
    </div>

    {{-- TOOLBAR HAPUS BANYAK --}}
    <div id="toolbarHapus" style="display:none;padding:10px 20px;background:#fff3cd;border-bottom:1px solid #eee;align-items:center;justify-content:space-between;gap:10px;flex-wrap:wrap">
        <span id="jumlahDipilih" style="font-size:13px;font-weight:600;color:#856404">0 buku dipilih</span>
        <div style="display:flex;gap:8px">
            <button type="button" onclick="pilihSemua()" style="padding:6px 14px;background:#856404;color:white;border:none;border-radius:8px;font-size:12px;font-weight:600;cursor:pointer">
                <i class="bi bi-check-all"></i> Pilih Semua
            </button>
            <button type="button" onclick="submitHapusBanyak()" style="padding:6px 14px;background:#e74c3c;color:white;border:none;border-radius:8px;font-size:12px;font-weight:600;cursor:pointer">
                <i class="bi bi-trash"></i> Hapus yang Dipilih
            </button>
        </div>
    </div>

    <div class="card-admin-body">
        <table class="table table-hover">
            <thead style="background:#f8f9fa">
                <tr>
                    <th style="width:40px">
                        <input type="checkbox" id="checkAll" onchange="toggleCheckAll(this)"
                            style="width:15px;height:15px;cursor:pointer">
                    </th>
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
                    <td>
                        <input type="checkbox" name="ids[]" value="{{ $item->id }}"
                            class="checkbox-buku" onchange="updateToolbar()"
                            style="width:15px;height:15px;cursor:pointer">
                    </td>
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
                    <td style="white-space:nowrap">
                        <a href="{{ route('buku.edit', $item->id) }}" class="btn btn-sm btn-warning me-1">
                            <i class="bi bi-pencil"></i>
                        </a>
                        <form action="{{ route('buku.destroy', $item->id) }}" method="POST" class="d-inline">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-sm btn-danger" onclick="return confirm('Yakin hapus buku ini?')">
                                <i class="bi bi-trash"></i>
                            </button>
                        </form>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="9" class="text-center text-muted py-4">Belum ada data buku</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

{{-- MODAL IMPORT --}}
<div id="modalImport" style="display:none;position:fixed;inset:0;background:rgba(0,0,0,0.5);z-index:2000;align-items:center;justify-content:center">
    <div style="background:white;border-radius:20px;padding:30px;width:90%;max-width:480px;box-shadow:0 20px 60px rgba(0,0,0,0.3)">
        <h5 style="font-weight:700;color:#222;margin-bottom:5px"><i class="bi bi-upload" style="color:#1a6e35"></i> Import Buku dari CSV</h5>
        <p style="font-size:12px;color:#888;margin-bottom:20px">Format kolom: <code>judul, pengarang, penerbit, tahun_terbit, isbn, stok, genre, lokasi, deskripsi, url_sampul</code><br>
        <span style="font-size:11px;color:#aaa">* url_sampul opsional</span></p>

        <form method="POST" action="{{ route('buku.import') }}" enctype="multipart/form-data">
            @csrf
            <div style="margin-bottom:15px">
                <input type="file" name="file" accept=".csv,.txt"
                    class="form-control" style="border-radius:10px">
            </div>
            <div style="background:#f8f9fa;border-radius:10px;padding:12px;font-size:12px;color:#666;margin-bottom:15px">
                <strong>Contoh:</strong><br>
                <code style="font-size:10px">Matematika,Pak Budi,Erlangga,2023,9786021000001,10,Sains & Teknologi,A1,Deskripsi,https://url-gambar.jpg</code>
            </div>
            <div style="display:flex;gap:10px">
                <button type="submit" style="flex:1;padding:11px;background:linear-gradient(135deg,#1a6e35,#27ae60);color:white;border:none;border-radius:10px;font-size:14px;font-weight:600;cursor:pointer">
                    <i class="bi bi-upload"></i> Import Sekarang
                </button>
                <button type="button" onclick="document.getElementById('modalImport').style.display='none'"
                    style="flex:1;padding:11px;background:#f8f9fa;color:#666;border:none;border-radius:10px;font-size:14px;font-weight:600;cursor:pointer">
                    Batal
                </button>
            </div>
        </form>
    </div>
</div>

@push('scripts')
<script>
function updateToolbar() {
    const checked = document.querySelectorAll('.checkbox-buku:checked');
    const toolbar = document.getElementById('toolbarHapus');
    const jumlah = document.getElementById('jumlahDipilih');

    if (checked.length > 0) {
        toolbar.style.display = 'flex';
        jumlah.textContent = checked.length + ' buku dipilih';
    } else {
        toolbar.style.display = 'none';
    }

    const semua = document.querySelectorAll('.checkbox-buku');
    document.getElementById('checkAll').checked = checked.length === semua.length && semua.length > 0;
}

function toggleCheckAll(el) {
    document.querySelectorAll('.checkbox-buku').forEach(cb => cb.checked = el.checked);
    updateToolbar();
}

function pilihSemua() {
    document.querySelectorAll('.checkbox-buku').forEach(cb => cb.checked = true);
    updateToolbar();
}

function submitHapusBanyak() {
    const checked = document.querySelectorAll('.checkbox-buku:checked');
    if (checked.length === 0) {
        alert('Tidak ada buku yang dipilih.');
        return;
    }
    if (!confirm('Hapus ' + checked.length + ' buku yang dipilih? Tindakan ini tidak bisa dibatalkan.')) return;

    const form = document.createElement('form');
    form.method = 'POST';
    form.action = '{{ route("buku.hapusBanyak") }}';

    const csrf = document.createElement('input');
    csrf.type = 'hidden';
    csrf.name = '_token';
    csrf.value = '{{ csrf_token() }}';
    form.appendChild(csrf);

    checked.forEach(cb => {
        const input = document.createElement('input');
        input.type = 'hidden';
        input.name = 'ids[]';
        input.value = cb.value;
        form.appendChild(input);
    });

    document.body.appendChild(form);
    form.submit();
}
</script>
@endpush

@endsection