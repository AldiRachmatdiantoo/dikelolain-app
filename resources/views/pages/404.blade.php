@extends('layouts.app')

@section('title', 'Page Not Found - Dikelolain')

@push('meta')
    <meta name="robots" content="noindex">
@endpush

@section('content')


	<x-page-header title="Page <span>not found</span>">
        <li class="breadcrumb-item active" aria-current="page">404 Error page</li>
    </x-page-header>
    <!-- error section start -->
    <div class="error-page">
        <div class="container">
            <div class="row">
                <div class="col-lg-12">
                    <div class="error-page-image wow fadeInUp">
                        <img src="{{ asset('images/404-error-img.webp') }}" alt="Halaman tidak ditemukan">
                    </div>
                    <div class="error-page-content">
                        <div class="section-title">
                            <h2 class="text-anime-style-2" data-cursor="-opaque">Oops! <span>page not found</span></h2>
                        </div>
                        <div class="error-page-content-body">
                            <p class="wow fadeInUp" data-wow-delay="0.25s">The page you are looking for does not exist</p>
                            <a class="btn-default wow fadeInUp" data-wow-delay="0.5s" href="./"><span>back to home</span></a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

@endsection

@push('scripts')
@endpush
