@extends('admin.layouts.admin')

@section('title', 'Edit Testimonials Page')
@section('page-title', 'Testimonials Page - VCMS Editor')

@section('content')

@php
if (!function_exists('getContent')) {
    function getContent($sections, $sectionKey, $contentKey, $default = '') {
        if (!isset($sections[$sectionKey])) {
            return $default;
        }
        $content = $sections[$sectionKey]->contents->firstWhere('key', $contentKey);
        return $content ? $content->value : $default;
    }
}

// Page Header data
$headerTitle = getContent($sections, 'testimonials-header', 'testimonials-header-1_title', 'Testimonials');
$headerBreadcrumb = getContent($sections, 'testimonials-header', 'testimonials-header-1_breadcrumb', 'Testimonials');

@endphp

<!-- Page Header Section -->
<div class="page-header bg-section vcms-editable-section" data-section="testimonials-header" data-item-id="testimonials-header-1">
    <div class="vcms-edit-indicator">
        <button class="vcms-edit-btn" data-edit-type="page-header">
            <i class="fa-solid fa-pen"></i> Edit Header
        </button>
    </div>
    <div class="container">
        <div class="row">
            <div class="col-lg-12">
                <div class="page-header-box vcms-editable-item" data-edit-type="page-header" data-item-id="testimonials-header-1"
                    data-title="{{ $headerTitle }}"
                    data-breadcrumb="{{ $headerBreadcrumb }}">
                    <h1 class="text-anime-style-2" data-cursor="-opaque">{!! $headerTitle !!}</h1>
                    <nav class="wow fadeInUp" data-wow-delay="0.25s">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item"><a href="{{ route('home') }}">home</a></li>
                            <li class="breadcrumb-item active" aria-current="page">{{ $headerBreadcrumb }}</li>
                        </ol>
                    </nav>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Testimonials: edited in place on the public page (VCMS edit mode) -->
<div class="container" style="padding: 40px 0; text-align: center;">
    <a href="{{ route('admin.edit-mode.on', ['to' => '/testimonials']) }}" class="btn-default">Edit testimoni di halaman Testimonials</a>
</div>

@endsection
