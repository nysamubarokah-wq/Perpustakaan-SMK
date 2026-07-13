<?php $__env->startSection('title', 'Kelola Anggota'); ?>
<?php $__env->startSection('page-title', 'Kelola Anggota'); ?>

<?php
$sortUrl = function($col) use ($sortBy, $sortDir, $search, $perPage) {
    $params = array_filter([
        'search' => $search,
        'sort' => $col,
        'direction' => ($sortBy === $col && $sortDir === 'asc') ? 'desc' : 'asc',
        'per_page' => $perPage,
    ]);
    return route('anggota.index', $params);
};

$sortIcon = function($col) use ($sortBy, $sortDir) {
    if ($sortBy !== $col) return '<i class="bi bi-chevron-expand" style="opacity:0.3"></i>';
    return $sortDir === 'asc'
        ? '<i class="bi bi-chevron-up" style="color:#1a6e35"></i>'
        : '<i class="bi bi-chevron-down" style="color:#1a6e35"></i>';
};
?>

<?php $__env->startSection('content'); ?>

<style>
    @media (max-width: 768px) {
        .tbl-wrap { overflow-x: auto; -webkit-overflow-scrolling: touch; }
        .tbl-wrap table { min-width: 700px; font-size: 12px; }
        .tbl-wrap td, .tbl-wrap th { padding: 8px 10px !important; }
        .search-row { flex-direction: column !important; }
        .search-row .form-control { width: 100% !important; }
        .search-row .btn { width: 100% !important; }
        .pagination-wrap { flex-direction: column; align-items: flex-start !important; gap: 10px; }
        .pagination { flex-wrap: wrap; }
        .pagination .page-link { padding: 4px 8px; font-size: 12px; }
    }
    .sortable-th { cursor: pointer; user-select: none; white-space: nowrap; }
    .sortable-th:hover { background: #e9ecef !important; }
    .sort-icon { font-size: 10px; margin-left: 3px; vertical-align: middle; }
    .pagination { gap: 4px; }
    .pagination .page-link {
        border-radius: 8px !important;
        font-size: 13px;
        color: #1a6e35;
        border: 1.5px solid #eee;
        padding: 6px 12px;
    }
    .pagination .page-item.active .page-link {
        background: linear-gradient(135deg, #1a6e35, #27ae60);
        border-color: #1a6e35;
        color: white;
    }
    .pagination .page-link:hover { background: #e8f5e9; color: #1a6e35; }
    .pagination .page-item.disabled .page-link { border-radius: 8px; }
    .per-page-select {
        border: 1.5px solid #eee;
        border-radius: 8px;
        padding: 6px 10px;
        font-size: 13px;
        color: #555;
        outline: none;
        cursor: pointer;
    }
    .per-page-select:focus { border-color: #1a6e35; }
</style>

<?php if(session('success')): ?>
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        <i class="bi bi-check-circle"></i> <?php echo e(session('success')); ?>

        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
<?php endif; ?>

<?php if(session('error')): ?>
    <div class="alert alert-danger alert-dismissible fade show" role="alert">
        <i class="bi bi-exclamation-circle"></i> <?php echo e(session('error')); ?>

        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
<?php endif; ?>


<div style="background:white;border-radius:16px;padding:20px 24px;border:1px solid #eee;margin-bottom:20px;display:flex;align-items:center;justify-content:space-between;flex-wrap:wrap;gap:16px">

    <div>
        <h4 style="font-weight:800;color:#222;margin:0 0 4px;font-size:18px">
            <i class="bi bi-people" style="color:#1a6e35"></i> Data Anggota
        </h4>
        <p style="font-size:13px;color:#888;margin:0">
            Total: <strong style="color:#1a6e35"><?php echo e($anggota->total()); ?></strong> anggota
        </p>
    </div>

    <div style="display:flex;gap:10px;align-items:center;flex-wrap:wrap">
        
        <div style="display:flex;gap:8px;align-items:center">
            <input type="text" id="searchInput" value="<?php echo e($search ?? ''); ?>"
                placeholder="Cari nama / NIS / email..."
                style="padding:8px 14px;border:1.5px solid #eee;border-radius:10px;font-size:13px;outline:none;width:220px"
                onkeydown="if(event.key==='Enter'){cariAnggota()}">
            <input type="hidden" id="sortInput" value="<?php echo e($sortBy); ?>">
            <input type="hidden" id="dirInput" value="<?php echo e($sortDir); ?>">
            <input type="hidden" id="perPageInput" value="<?php echo e($perPage); ?>">
            <button type="button" onclick="cariAnggota()"
                    style="padding:8px 16px;background:linear-gradient(135deg,#1a6e35,#27ae60);color:white;border:none;border-radius:10px;font-size:13px;font-weight:600;cursor:pointer">
                <i class="bi bi-search"></i> Cari
            </button>
            <?php if($search): ?>
                <a href="<?php echo e(route('anggota.index', array_filter(['sort' => $sortBy, 'direction' => $sortDir, 'per_page' => $perPage]))); ?>"
                   style="padding:8px 14px;background:#f3f4f6;color:#666;border:none;border-radius:10px;font-size:13px;font-weight:600;text-decoration:none">
                    <i class="bi bi-x"></i>
                </a>
            <?php endif; ?>
        </div>

        
        <button onclick="document.getElementById('importModal').style.display='flex'"
                style="padding:8px 16px;background:#2c3e50;color:white;border:none;border-radius:10px;font-size:13px;font-weight:600;cursor:pointer">
            <i class="bi bi-upload"></i> Import
        </button>

        
        <a href="<?php echo e(route('anggota.create')); ?>"
           style="padding:8px 20px;background:linear-gradient(135deg,#1a6e35,#27ae60);color:white;border:none;border-radius:10px;font-size:13px;font-weight:600;text-decoration:none;display:inline-flex;align-items:center;gap:6px">
            <i class="bi bi-plus-lg"></i> Tambah
        </a>
    </div>
</div>


<div id="toolbarHapus" style="display:none;padding:14px 24px;background:#fff3cd;border:1.5px solid #ffe69c;border-radius:14px;margin-bottom:16px;align-items:center;justify-content:space-between;flex-wrap:wrap;gap:12px">
    <span style="font-size:14px;font-weight:700;color:#856404">
        <i class="bi bi-check-square"></i> <span id="jumlahDipilih">0</span> anggota dipilih
    </span>
    <div style="display:flex;gap:8px">
        <button type="button" onclick="pilihSemua()"
                style="padding:7px 16px;background:#856404;color:white;border:none;border-radius:8px;font-size:12px;font-weight:600;cursor:pointer">
            <i class="bi bi-check-all"></i> Pilih Semua
        </button>
        <button type="button" onclick="submitHapusBanyak()"
                style="padding:7px 16px;background:#dc2626;color:white;border:none;border-radius:8px;font-size:12px;font-weight:600;cursor:pointer">
            <i class="bi bi-trash"></i> Hapus Terpilih
        </button>
        <button type="button" onclick="batalPilih()"
                style="padding:7px 16px;background:#f3f4f6;color:#666;border:none;border-radius:8px;font-size:12px;font-weight:600;cursor:pointer">
            Batal
        </button>
    </div>
</div>


<div class="card-admin" style="margin-bottom:20px">
    <div style="padding:16px 24px;border-bottom:1px solid #eee;display:flex;align-items:center;justify-content:space-between;flex-wrap:wrap;gap:10px;background:#f8faff">
        <h5 style="font-weight:700;color:#2563eb;margin:0;font-size:15px">
            <i class="bi bi-mortarboard"></i> Daftar Siswa
            <span style="font-size:12px;font-weight:400;color:#888">(<?php echo e($anggota->total()); ?> siswa)</span>
        </h5>
    </div>
    <div class="card-admin-body tbl-wrap">
        <table class="table table-hover mb-0" style="font-size:13px">
            <thead style="background:#f8f9fa">
                <tr>
                    <th style="padding:12px 16px;font-weight:600;color:#555;border:none;width:40px">
                        <input type="checkbox" id="checkAllSiswa" onchange="toggleCheckAll('siswa')"
                            style="width:16px;height:16px;cursor:pointer">
                    </th>
                    <th style="padding:12px 16px;font-weight:600;color:#555;border:none;width:50px">#</th>
                    <th class="sortable-th" style="padding:12px 16px;font-weight:600;color:#555;border:none" onclick="sortBy('nama')">Nama <span class="sort-icon"><?php echo $sortIcon('nama'); ?></span></th>
                    <th class="sortable-th" style="padding:12px 16px;font-weight:600;color:#555;border:none" onclick="sortBy('nis')">NIS <span class="sort-icon"><?php echo $sortIcon('nis'); ?></span></th>
                    <th class="sortable-th" style="padding:12px 16px;font-weight:600;color:#555;border:none" onclick="sortBy('kelas')">Kelas <span class="sort-icon"><?php echo $sortIcon('kelas'); ?></span></th>
                    <th class="sortable-th" style="padding:12px 16px;font-weight:600;color:#555;border:none" onclick="sortBy('jurusan')">Jurusan <span class="sort-icon"><?php echo $sortIcon('jurusan'); ?></span></th>
                    <th class="sortable-th" style="padding:12px 16px;font-weight:600;color:#555;border:none" onclick="sortBy('email')">Email <span class="sort-icon"><?php echo $sortIcon('email'); ?></span></th>
                    <th class="sortable-th" style="padding:12px 16px;font-weight:600;color:#555;border:none" onclick="sortBy('no_telepon')">No. HP <span class="sort-icon"><?php echo $sortIcon('no_telepon'); ?></span></th>
                    <th style="padding:12px 16px;font-weight:600;color:#555;border:none;text-align:center">Aksi</th>
                </tr>
            </thead>
            <tbody>
            <?php $__empty_1 = true; $__currentLoopData = $anggota; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                <tr id="row-<?php echo e($item->id); ?>" class="row-item" data-role="siswa">
                    <td style="padding:12px 16px;vertical-align:middle">
                        <input type="checkbox" name="ids[]" value="<?php echo e($item->id); ?>"
                            class="cb-siswa"
                            onchange="updateToolbar('siswa')"
                            style="width:16px;height:16px;cursor:pointer">
                    </td>
                    <td style="padding:12px 16px;vertical-align:middle;color:#aaa;font-size:12px"><?php echo e($anggota->firstItem() + $loop->index); ?></td>
                    <td style="padding:12px 16px;vertical-align:middle">
                        <div style="font-weight:600;color:#222"><?php echo e($item->nama); ?></div>
                        <?php if($item->alamat): ?>
                            <div style="font-size:11px;color:#aaa"><?php echo e(Str::limit($item->alamat, 30)); ?></div>
                        <?php endif; ?>
                    </td>
                    <td style="padding:12px 16px;vertical-align:middle">
                        <?php if($item->nis): ?>
                            <span style="background:#e8f5e9;color:#1a6e35;padding:3px 10px;border-radius:20px;font-size:11px;font-weight:600;white-space:nowrap"><?php echo e($item->nis); ?></span>
                        <?php else: ?>
                            <span style="color:#ccc">-</span>
                        <?php endif; ?>
                    </td>
                    <td style="padding:12px 16px;vertical-align:middle;color:#555"><?php echo e($item->kelas ?? '-'); ?></td>
                    <td style="padding:12px 16px;vertical-align:middle;color:#555"><?php echo e($item->jurusan ?? '-'); ?></td>
                    <td style="padding:12px 16px;vertical-align:middle">
                        <span style="color:#555;font-size:12px;word-break:break-all"><?php echo e($item->email); ?></span>
                    </td>
                    <td style="padding:12px 16px;vertical-align:middle;color:#555"><?php echo e($item->no_telepon ?? '-'); ?></td>
                    <td style="padding:12px 16px;vertical-align:middle;text-align:center">
                        <div style="display:flex;gap:5px;justify-content:center;flex-wrap:nowrap">

                            
                            <form action="<?php echo e(route('admin.anggota.role', [$item->id, 'admin'])); ?>" method="POST" style="margin:0">
                                <?php echo csrf_field(); ?> <?php echo method_field('PUT'); ?>
                                <button type="submit" title="Jadikan Admin"
                                    style="padding:6px 10px;background:#ede9fe;color:#7c3aed;border:none;border-radius:7px;font-size:12px;font-weight:600;cursor:pointer"
                                    onclick="return confirm('Jadikan admin?')">
                                    <i class="bi bi-shield-check"></i>
                                </button>
                            </form>

                            
                            <a href="<?php echo e(route('anggota.edit', $item->id)); ?>"
                               title="Edit"
                               style="padding:6px 10px;background:#dbeafe;color:#2563eb;border:none;border-radius:7px;font-size:12px;font-weight:600;text-decoration:none;display:inline-flex;align-items:center">
                                <i class="bi bi-pencil"></i>
                            </a>

                            
                            <form action="<?php echo e(route('anggota.destroy', $item->id)); ?>" method="POST" style="margin:0">
                                <?php echo csrf_field(); ?> <?php echo method_field('DELETE'); ?>
                                <button type="submit" title="Hapus"
                                    style="padding:6px 10px;background:#fee2e2;color:#dc2626;border:none;border-radius:7px;font-size:12px;font-weight:600;cursor:pointer"
                                    onclick="return confirm('Hapus <?php echo e($item->nama); ?>?')">
                                    <i class="bi bi-trash"></i>
                                </button>
                            </form>
                        </div>
                    </td>
                </tr>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                <tr><td colspan="10" style="text-align:center;padding:40px;color:#aaa">
                    <i class="bi bi-people" style="font-size:40px;display:block;margin-bottom:8px"></i>
                    <p style="font-size:13px">Belum ada data siswa</p>
                </td></tr>
            <?php endif; ?>
            </tbody>
        </table>
        <div style="padding:12px 16px;border-top:1px solid #eee;display:flex;align-items:center;justify-content:space-between;flex-wrap:wrap;gap:10px" class="pagination-wrap">
            <div style="font-size:13px;color:#666">
                Menampilkan <strong><?php echo e($anggota->firstItem() ?? 0); ?></strong>–<strong><?php echo e($anggota->lastItem() ?? 0); ?></strong> dari <strong><?php echo e($anggota->total()); ?></strong> data
            </div>
            <div style="display:flex;align-items:center;gap:8px;flex-wrap:wrap">
                <label style="font-size:12px;color:#888;margin:0">Tampilkan:</label>
                <select class="per-page-select" id="perPageSelect" onchange="changePerPage(this.value)" style="width:auto">
                    <option value="10" <?php echo e($perPage == 10 ? 'selected' : ''); ?>>10</option>
                    <option value="25" <?php echo e($perPage == 25 ? 'selected' : ''); ?>>25</option>
                    <option value="50" <?php echo e($perPage == 50 ? 'selected' : ''); ?>>50</option>
                    <option value="100" <?php echo e($perPage == 100 ? 'selected' : ''); ?>>100</option>
                </select>
                <label style="font-size:12px;color:#888;margin:0">data</label>
            </div>
            <?php if($anggota->hasPages()): ?>
            <nav aria-label="Pagination">
                <ul class="pagination mb-0">
                    <?php if($anggota->onFirstPage()): ?>
                        <li class="page-item disabled"><span class="page-link" style="opacity:0.4"><i class="bi bi-chevron-double-left" title="First"></i></span></li>
                        <li class="page-item disabled"><span class="page-link" style="opacity:0.4"><i class="bi bi-chevron-left" title="Previous"></i></span></li>
                    <?php else: ?>
                        <li class="page-item"><a class="page-link" href="<?php echo e($anggota->url(1)); ?>" title="First"><i class="bi bi-chevron-double-left"></i></a></li>
                        <li class="page-item"><a class="page-link" href="<?php echo e($anggota->previousPageUrl()); ?>" title="Previous"><i class="bi bi-chevron-left"></i></a></li>
                    <?php endif; ?>

                    <?php $window = 2; ?>
                    <?php $__currentLoopData = $anggota->getUrlRange(max(1, $anggota->currentPage() - $window), min($anggota->lastPage(), $anggota->currentPage() + $window)); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $page => $url): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <li class="page-item <?php echo e($page == $anggota->currentPage() ? 'active' : ''); ?>">
                            <a class="page-link" href="<?php echo e($url); ?>"><?php echo e($page); ?></a>
                        </li>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>

                    <?php if($anggota->hasMorePages()): ?>
                        <li class="page-item"><a class="page-link" href="<?php echo e($anggota->nextPageUrl()); ?>" title="Next"><i class="bi bi-chevron-right"></i></a></li>
                        <li class="page-item"><a class="page-link" href="<?php echo e($anggota->url($anggota->lastPage())); ?>" title="Last"><i class="bi bi-chevron-double-right"></i></a></li>
                    <?php else: ?>
                        <li class="page-item disabled"><span class="page-link" style="opacity:0.4"><i class="bi bi-chevron-right" title="Next"></i></span></li>
                        <li class="page-item disabled"><span class="page-link" style="opacity:0.4"><i class="bi bi-chevron-double-right" title="Last"></i></span></li>
                    <?php endif; ?>
                </ul>
            </nav>
            <?php endif; ?>
        </div>
    </div>
</div>


<div id="importModal" style="display:none;position:fixed;inset:0;background:rgba(0,0,0,0.5);z-index:9999;align-items:center;justify-content:center;padding:15px">
    <div style="background:white;border-radius:20px;padding:25px;width:100%;max-width:480px;box-shadow:0 20px 60px rgba(0,0,0,0.3);max-height:90vh;overflow-y:auto">
        <h5 style="font-weight:700;color:#222;margin-bottom:5px"><i class="bi bi-upload" style="color:#1a6e35"></i> Import CSV</h5>

        <div style="margin-bottom:16px">
            <p style="font-size:11px;color:#888;margin:0 0 6px">Download template terlebih dahulu agar format CSV sesuai dengan sistem.</p>
            <a href="<?php echo e(route('import.template', 'anggota')); ?>"
               style="display:inline-flex;align-items:center;gap:5px;padding:8px 14px;background:linear-gradient(135deg,#1a6e35,#27ae60);color:white;border:none;border-radius:8px;font-size:12px;font-weight:500;text-decoration:none">
                <i class="bi bi-download"></i> Download Template
            </a>
        </div>

        <form method="POST" action="<?php echo e(route('admin.anggota.import')); ?>" enctype="multipart/form-data" id="formImportAnggota" onsubmit="return validateImportForm()">
            <?php echo csrf_field(); ?>
            <div id="importErrorMsg" style="display:none;margin-bottom:12px;padding:10px 14px;background:#fee2e2;border:1px solid #f5c6cb;border-radius:8px;color:#dc2626;font-size:13px;font-weight:500">
                <i class="bi bi-exclamation-triangle-fill"></i> <span id="importErrorText">Pilih file CSV terlebih dahulu!</span>
            </div>
            <div style="margin-bottom:15px">
                <input type="file" name="file" id="fileImport" accept=".csv,.txt"
                    class="form-control <?php $__errorArgs = ['file'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>"
                    style="border-radius:10px" onchange="hideImportError()">
                <?php $__errorArgs = ['file'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                    <div class="invalid-feedback"><?php echo e($message); ?></div>
                <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
            </div>

            <div style="background:#f8f9fa;border-radius:10px;padding:12px;font-size:12px;color:#666;margin-bottom:15px">
                <strong>Contoh:</strong><br>
                <code style="font-size:10px">2024001,Anisa Rahmawati,X RPL 1,RPL,L,081234567890,anisa@siswa.sch.id</code>
            </div>

            <div style="display:flex;gap:10px;flex-wrap:wrap">
                <button type="submit"
                        style="flex:1;min-width:140px;padding:12px;background:linear-gradient(135deg,#1a6e35,#27ae60);color:white;border:none;border-radius:10px;font-size:14px;font-weight:600;cursor:pointer">
                    <i class="bi bi-upload"></i> Import Sekarang
                </button>
                <button type="button" onclick="document.getElementById('importModal').style.display='none'"
                        style="flex:1;min-width:140px;padding:12px;background:#f0f0f0;color:#555;border:none;border-radius:10px;font-size:14px;font-weight:600;cursor:pointer">
                    Batal
                </button>
            </div>
        </form>
    </div>
</div>

<?php $__env->stopSection(); ?>

<?php $__env->startPush('scripts'); ?>
<script>
function cariAnggota() {
    const keyword = document.getElementById('searchInput').value;
    const sort = document.getElementById('sortInput') ? document.getElementById('sortInput').value : 'nama';
    const dir = document.getElementById('dirInput') ? document.getElementById('dirInput').value : 'asc';
    const perPage = document.getElementById('perPageInput') ? document.getElementById('perPageInput').value : '10';
    const params = new URLSearchParams();
    if (keyword) params.set('search', keyword);
    params.set('sort', sort);
    params.set('direction', dir);
    params.set('per_page', perPage);
    window.location.href = '<?php echo e(route('anggota.index')); ?>?' + params.toString();
}

function sortBy(column) {
    const sort = document.getElementById('sortInput') ? document.getElementById('sortInput').value : 'nama';
    const dir = document.getElementById('dirInput') ? document.getElementById('dirInput').value : 'asc';
    const search = document.getElementById('searchInput') ? document.getElementById('searchInput').value : '';
    const perPage = document.getElementById('perPageInput') ? document.getElementById('perPageInput').value : '10';
    const params = new URLSearchParams();
    if (search) params.set('search', search);
    params.set('sort', column);
    params.set('direction', (sort === column && dir === 'asc') ? 'desc' : 'asc');
    params.set('per_page', perPage);
    window.location.href = '<?php echo e(route('anggota.index')); ?>?' + params.toString();
}

function changePerPage(value) {
    const sort = document.getElementById('sortInput') ? document.getElementById('sortInput').value : 'nama';
    const dir = document.getElementById('dirInput') ? document.getElementById('dirInput').value : 'asc';
    const search = document.getElementById('searchInput') ? document.getElementById('searchInput').value : '';
    const params = new URLSearchParams();
    if (search) params.set('search', search);
    params.set('sort', sort);
    params.set('direction', dir);
    params.set('per_page', value);
    window.location.href = '<?php echo e(route('anggota.index')); ?>?' + params.toString();
}

function toggleCheckAll(role) {
    const checkboxes = document.querySelectorAll('.cb-' + role);
    const master = document.getElementById('checkAll' + (role === 'siswa' ? 'Siswa' : 'Admin'));
    checkboxes.forEach(cb => cb.checked = master.checked);
    updateToolbar(role);
}

function updateToolbar(role) {
    const checkboxes = document.querySelectorAll('.cb-' + role + ':checked');
    const total = document.querySelectorAll('.cb-' + role);
    const master = document.getElementById('checkAll' + (role === 'siswa' ? 'Siswa' : 'Admin'));

    master.checked = checkboxes.length === total.length && total.length > 0;

    const allChecked = document.querySelectorAll('.cb-siswa:checked, .cb-admin:checked');
    const toolbar = document.getElementById('toolbarHapus');
    const jumlah = document.getElementById('jumlahDipilih');

    if (allChecked.length > 0) {
        toolbar.style.display = 'flex';
        jumlah.textContent = allChecked.length;
    } else {
        toolbar.style.display = 'none';
    }
}

function pilihSemua() {
    document.querySelectorAll('.cb-siswa, .cb-admin').forEach(cb => cb.checked = true);
    updateToolbar('siswa');
    updateToolbar('admin');
}

function batalPilih() {
    document.querySelectorAll('.cb-siswa, .cb-admin').forEach(cb => cb.checked = false);
    document.getElementById('checkAllSiswa').checked = false;
    document.getElementById('checkAllAdmin').checked = false;
    updateToolbar('siswa');
    updateToolbar('admin');
}

function submitHapusBanyak() {
    const checked = document.querySelectorAll('.cb-siswa:checked, .cb-admin:checked');
    if (checked.length === 0) {
        alert('Tidak ada data yang dipilih.');
        return;
    }
    if (!confirm('Hapus ' + checked.length + ' anggota yang dipilih?')) return;

    const form = document.createElement('form');
    form.method = 'POST';
    form.action = '<?php echo e(route("admin.anggota.hapusBanyak")); ?>';

    const csrf = document.createElement('input');
    csrf.type = 'hidden';
    csrf.name = '_token';
    csrf.value = '<?php echo e(csrf_token()); ?>';
    form.appendChild(csrf);

    const sort = document.getElementById('sortInput') ? document.getElementById('sortInput').value : 'nama';
    const dir = document.getElementById('dirInput') ? document.getElementById('dirInput').value : 'asc';
    const perPage = document.getElementById('perPageInput') ? document.getElementById('perPageInput').value : '10';
    const search = document.getElementById('searchInput') ? document.getElementById('searchInput').value : '';

    ['search', 'sort', 'direction', 'per_page', 'page'].forEach(name => {
        const input = document.createElement('input');
        input.type = 'hidden';
        input.name = name;
        input.value = name === 'search' ? search : (name === 'sort' ? sort : (name === 'direction' ? dir : (name === 'per_page' ? perPage : '<?php echo e($anggota->currentPage()); ?>')));
        form.appendChild(input);
    });

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

function validateImportForm() {
    var fileInput = document.getElementById('fileImport');
    var errorMsg = document.getElementById('importErrorMsg');
    var errorText = document.getElementById('importErrorText');
    if (!fileInput.files || fileInput.files.length === 0) {
        errorText.textContent = 'Pilih file CSV terlebih dahulu!';
        errorMsg.style.display = 'block';
        return false;
    }
    var fileName = fileInput.files[0].name.toLowerCase();
    if (!fileName.endsWith('.csv') && !fileName.endsWith('.txt')) {
        errorText.textContent = 'File harus berformat CSV atau TXT!';
        errorMsg.style.display = 'block';
        return false;
    }
    return true;
}

function hideImportError() {
    document.getElementById('importErrorMsg').style.display = 'none';
}
</script>
<?php $__env->stopPush(); ?>

<?php echo $__env->make('layouts.admin', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\laragon\www\PerpustakaanDigital\resources\views/anggota/index.blade.php ENDPATH**/ ?>