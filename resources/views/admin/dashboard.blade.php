@extends('admin.layouts.admin')

@section('title', 'Dashboard')

@section('content')
<div class="container-fluid admin-fluid">

    <div class="d-flex flex-wrap justify-content-between align-items-center gap-3 mb-4">
        <div>
            <h2 class="mb-1 fw-bold dash-yellow">Dashboard</h2>
            <p class="mb-0 dash-muted">Konten website diedit langsung di halamannya. Pilih halaman, lalu klik tombol ✎ di bagian yang ingin diubah.</p>
        </div>
        <div class="d-flex flex-wrap gap-2">
            <a href="{{ route('admin.messages.index') }}" class="btn btn-sm fw-bold {{ $unreadMessages ? 'dash-btn' : 'dash-btn-outline' }}">
                <i class="fa-solid fa-envelope me-1"></i> Pesan Masuk
                @if ($unreadMessages)
                    <span class="badge rounded-pill text-bg-dark ms-1">{{ $unreadMessages }} baru</span>
                @endif
            </a>
            <a href="{{ route('admin.pages.analytics') }}" class="btn btn-sm fw-bold dash-btn-outline">
                <i class="fa-solid fa-chart-line me-1"></i> Analytics
            </a>
        </div>
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

@endsection
