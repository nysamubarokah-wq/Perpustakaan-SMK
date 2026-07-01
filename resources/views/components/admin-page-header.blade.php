@props(['title', 'icon' => 'bi-arrow-left-circle', 'backUrl', 'backText' => 'Kembali'])

<div class="d-flex align-items-center justify-content-between flex-wrap gap-2" style="margin-bottom:20px">
    <h5 class="fw-bold m-0 admin-dynamic-text">
        <i class="{{ $icon }}" style="color:#1a6e35"></i> {{ $title }}
    </h5>
    <a href="{{ $backUrl }}"
       class="admin-back-btn"
       style="padding:8px 16px;background:#f0f0f0;color:#555;border:none;border-radius:10px;font-size:13px;font-weight:600;cursor:pointer;text-decoration:none">
        <i class="bi bi-arrow-left"></i> {{ $backText }}
    </a>
</div>
