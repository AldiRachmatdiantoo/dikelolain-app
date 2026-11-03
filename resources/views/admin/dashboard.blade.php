@extends('admin.layouts.admin')

@section('title', 'Dashboard')

@section('content')
<div class="container-fluid" style="padding-left: 15px; padding-right: 15px;">

    <div class="d-flex flex-wrap justify-content-between align-items-center gap-3 mb-4">
        <div>
            <h2 class="mb-1 fw-bold dash-yellow">Dashboard</h2>
            <p class="mb-0 dash-muted">Konten website diedit langsung di halamannya. Pilih halaman, lalu klik tombol ✎ di bagian yang ingin diubah.</p>
        </div>
        <a href="{{ route('admin.pages.analytics') }}" class="btn btn-sm fw-bold dash-btn-outline">
            <i class="fa-solid fa-chart-line me-1"></i> Analytics
        </a>
    </div>

    <div class="row g-3">
        @foreach ($pages as $page)
            <div class="col-sm-6 col-xl-4">
                <div class="dash-card">
                    <div class="dash-card-icon"><i class="fa-solid {{ $page['icon'] }}"></i></div>
                    <div class="dash-card-body">
                        <h3>{{ $page['label'] }}</h3>
                        <p>{{ $page['path'] }}</p>
                    </div>
                    <div class="dash-card-actions">
                        <a href="{{ route('admin.edit-mode.on', ['to' => $page['path']]) }}" class="btn btn-sm fw-bold dash-btn">
                            <i class="fa-solid fa-pen-to-square me-1"></i> Edit halaman
                        </a>
                        <a href="{{ $page['path'] }}" target="_blank" rel="noopener" class="btn btn-sm dash-btn-outline" title="Lihat halaman {{ $page['label'] }}">
                            <i class="fa-solid fa-arrow-up-right-from-square"></i>
                        </a>
                    </div>
                </div>
            </div>
        @endforeach
    </div>
</div>

<style>
    .dash-yellow { color: #ffcc01 !important; }
    .dash-muted { color: #c9c9c9; }

    .dash-card {
        display: flex;
        align-items: center;
        gap: 14px;
        height: 100%;
        padding: 18px;
        background-color: #212529;
        border: 1px solid #333;
        border-radius: 16px;
    }

    .dash-card-icon {
        flex: 0 0 44px;
        height: 44px;
        display: flex;
        align-items: center;
        justify-content: center;
        border-radius: 12px;
        background-color: rgba(255, 204, 1, 0.12);
        color: #ffcc01;
        font-size: 18px;
    }

    .dash-card-body { flex: 1; min-width: 0; }
    .dash-card-body h3 { margin: 0 0 2px; font-size: 17px; color: #fff; }
    .dash-card-body p { margin: 0; font-size: 13px; color: #8f8f8f; }

    .dash-card-actions { display: flex; gap: 6px; }

    @media (max-width: 575px) {
        .dash-card { flex-wrap: wrap; }
        .dash-card-actions { width: 100%; }
        .dash-card-actions .dash-btn { flex: 1; }
    }

    .dash-btn {
        background-color: #ffcc01;
        border: 1px solid #ffcc01;
        color: #000;
    }

    .dash-btn:hover { background-color: #ffdd33; border-color: #ffdd33; color: #000; }

    .dash-btn-outline {
        background: transparent;
        border: 1px solid #ffcc01;
        color: #ffcc01;
    }

    .dash-btn-outline:hover { background-color: rgba(255, 204, 1, 0.15); color: #ffcc01; }
</style>
@endsection
