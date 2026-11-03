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

// Get testimonials from database
$testimonials = [];
$testimonialSection = $sections['testimonials'] ?? null;
if ($testimonialSection && $testimonialSection->contents) {
    $testimonialIds = [];
    foreach ($testimonialSection->contents as $content) {
        if (preg_match('/testimonial-list-1_testimonial_(.+)_name$/', $content->key, $matches)) {
            $testimonialIds[$matches[1]] = true;
        }
    }
    foreach (array_keys($testimonialIds) as $id) {
        $name = getContent($sections, 'testimonials', "testimonial-list-1_testimonial_{$id}_name", '');
        if (!empty($name)) {
            $testimonials[] = [
                'id' => $id,
                'name' => $name,
                'role' => getContent($sections, 'testimonials', "testimonial-list-1_testimonial_{$id}_role", 'CEO'),
                'content' => getContent($sections, 'testimonials', "testimonial-list-1_testimonial_{$id}_content", ''),
                'rating' => getContent($sections, 'testimonials', "testimonial-list-1_testimonial_{$id}_rating", '5'),
                'logo' => getContent($sections, 'testimonials', "testimonial-list-1_testimonial_{$id}_logo", 'images/client-logo-1.svg'),
                'image' => getContent($sections, 'testimonials', "testimonial-list-1_testimonial_{$id}_image", 'images/author-1.jpg'),
            ];
        }
    }
}
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

<!-- Testimonials Section -->
<div class="page-testimonial vcms-editable-section" data-section="testimonials" data-item-id="testimonials-section">
    <div class="vcms-edit-indicator">
        <button class="vcms-edit-btn" data-edit-type="testimonials-section">
            <i class="fa-solid fa-pen"></i> Edit Testimonials
        </button>
    </div>

    <div class="container">
        <div class="row">
            @forelse($testimonials as $index => $testimonial)
                <div class="col-lg-4 col-md-6">
                    <div class="testimonial-item wow fadeInUp" data-testimonial-id="{{ $testimonial['id'] }}">
                        <div class="testimonial-header">
                            <div class="testimonial-logo-img">
                                <img src="{{ asset($testimonial['logo']) }}" alt="">
                            </div>
                            <div class="testimonial-rating">
                                @for($i = 1; $i <= 5; $i++)
                                    <i class="fa-solid fa-star {{ $i <= intval($testimonial['rating']) ? '' : 'text-muted' }}" style="{{ $i <= intval($testimonial['rating']) ? '' : 'opacity: 0.3;' }}"></i>
                                @endfor
                            </div>
                        </div>
                        <div class="testimonial-content">
                            <p>{{ $testimonial['content'] }}</p>
                        </div>
                        <div class="testimonial-author">
                            <div class="author-image">
                                <figure class="image-anime" style="width: 48px; height: 48px; border-radius: 50%; overflow: hidden;">
                                    <img src="{{ asset($testimonial['image']) }}" alt="" style="width: 48px; height: 48px; min-width: 48px; min-height: 48px; object-fit: cover; border-radius: 50%;">
                                </figure>
                            </div>
                            <div class="author-content">
                                <h3>{{ $testimonial['name'] }}</h3>
                                <p>{{ $testimonial['role'] }}</p>
                            </div>
                        </div>
                    </div>
                </div>
            @empty
                <div class="col-12 text-center py-5">
                    <p style="color: #888;">Belum ada testimonial. Klik tombol "Edit Testimonials" untuk menambahkan.</p>
                </div>
            @endforelse
        </div>
    </div>
</div>

@endsection
