@props(['title', 'icon' => 'bi-list'])

<div class="card-header-wrap mb-3">
    <div class="d-flex align-items-center gap-2">
        <i class="{{ $icon }} fs-4 text-success"></i>
        <h5 class="fw-bold m-0 admin-dynamic-text" style="font-size:18px">{{ $title }}</h5>
    </div>
    @if(isset($action))
        <div class="card-header-actions">
            {{ $action }}
        </div>
    @endif
</div>

<style>
    .card-header-wrap {
        display: flex;
        justify-content: space-between;
        align-items: center;
        flex-wrap: wrap;
        gap: 12px;
    }
    .card-header-actions {
        display: flex;
        gap: 8px;
        flex-wrap: wrap;
    }
    @media (max-width: 768px) {
        .card-header-wrap {
            flex-direction: column;
            align-items: flex-start;
        }
        .card-header-actions {
            width: 100%;
        }
        .card-header-actions > * {
            flex: 1 1 auto;
            min-width: 0;
            text-align: center;
            justify-content: center;
            display: inline-flex;
            align-items: center;
        }
    }
</style>
