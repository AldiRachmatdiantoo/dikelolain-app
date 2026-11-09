@extends('layouts.app')

@section('title', 'Our Services - Dikelolain')

@section('content')

@php
    $servicesPerPage = 12;
    $totalPages = (int) ceil($services->count() / $servicesPerPage);
@endphp

    <x-vcms.settings :keys="['services.header.title', 'services.header.breadcrumb']" label="Header">
        <x-page-header :title="$settings->get('services.header.title')">
            <li class="breadcrumb-item active" aria-current="page">{{ $settings->get('services.header.breadcrumb') }}</li>
        </x-page-header>
    </x-vcms.settings>

    <div class="page-services">
        <div class="container">
            @if ($totalPages > 1)
                <div class="row mb-4">
                    <div class="col-12">
                        <div class="services-pagination d-flex justify-content-center align-items-center gap-2 flex-wrap">
                            @for ($page = 1; $page <= $totalPages; $page++)
                                <button type="button" class="btn service-page-btn {{ $page == 1 ? 'active' : '' }}" data-page="{{ $page }}">{{ $page }}</button>
                            @endfor
                            <span class="services-pagination-count">
                                ({{ $services->count() }} services)
                            </span>
                        </div>
                    </div>
                </div>
            @endif

            <div class="row service-items-row">
                @foreach ($services as $service)
                    @php
                        $serviceLink = route('pages.service.single', $service->slug);
                        $pageNum = intdiv($loop->index, $servicesPerPage) + 1;
                    @endphp
                    <div class="col-lg-4 col-md-6 service-item-col" data-page="{{ $pageNum }}" {{ $pageNum > 1 ? 'hidden' : '' }}>
                        <x-vcms.item resource="services" :item="$service" :fields="$serviceFields" label="layanan"
                            class="service-item {{ $service->is_active ? '' : 'vcms-hidden' }}">
                            <div class="service-image">
                                <a href="{{ $serviceLink }}" data-cursor-text="View">
                                    <figure class="image-anime">
                                        <x-img :src="$service->image_url" sizes="(min-width: 1200px) 355px, (min-width: 992px) 248px, (min-width: 768px) 318px, calc(100vw - 73px)" alt="{{ $service->name }}" loading="lazy" />
                                    </figure>
                                </a>
                            </div>
                            <div class="service-body">
                                <div class="service-content">
                                    <h3><a href="{{ $serviceLink }}">{{ $service->name }}</a></h3>
                                    <p>{{ $service->summary }}</p>
                                </div>
                                <div class="service-btn">
                                    <a href="{{ $serviceLink }}"><img src="{{ asset('images/arrrow-light.svg') }}" alt=""></a>
                                </div>
                            </div>
                        </x-vcms.item>
                    </div>
                @endforeach
            </div>

            <x-vcms.add resource="services" :fields="$serviceFields" label="layanan" />
        </div>
    </div>

    <!-- Creative Tools Section Start -->
    @include('partials.creative-tools')
    <!-- Creative Tools Section End -->

@endsection

@push('scripts')
<script>
// Pagination of the services grid: 12 services per page.
(function ($) {
    'use strict';

    var $buttons = $('.service-page-btn');
    var $items = $('.service-item-col');

    $buttons.on('click', function () {
        var page = $(this).attr('data-page');

        $buttons.removeClass('active');
        $(this).addClass('active');
        $items.prop('hidden', true).filter('[data-page="' + page + '"]').prop('hidden', false);
        $('html, body').animate({ scrollTop: $('.page-services').offset().top - 100 }, 300);
    });
})(jQuery);
</script>
@endpush
