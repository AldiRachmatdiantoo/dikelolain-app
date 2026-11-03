@extends('layouts.app')

@section('title', 'Testimonials - Dikelolain')

@section('content')

    <!-- Page Header -->
    <x-vcms.settings :keys="['testimonials.header.title', 'testimonials.header.breadcrumb']" label="Header">
        <x-page-header :title="$settings->get('testimonials.header.title')">
            <li class="breadcrumb-item active" aria-current="page">{{ $settings->get('testimonials.header.breadcrumb') }}</li>
        </x-page-header>
    </x-vcms.settings>

    <!-- Page Testimonials Start -->
    <div class="page-testimonial">
        <div class="container">
            <div class="row">
                @foreach ($testimonials as $testimonial)
                    <div class="col-lg-4 col-md-6">
                        <!-- Testimonial Item Start -->
                        <x-vcms.item resource="testimonials" :item="$testimonial" :fields="$testimonialFields" label="testimoni"
                            class="testimonial-item wow fadeInUp {{ $testimonial->is_active ? '' : 'vcms-hidden' }}"
                            :data-wow-delay="$loop->index ? ($loop->index * 0.2) . 's' : null">
                            <div class="testimonial-header">
                                <div class="testimonial-logo-img">
                                    @if ($testimonial->logo)
                                        <img src="{{ $testimonial->logo_url }}" alt="">
                                    @endif
                                </div>
                                <div class="testimonial-rating">
                                    @for ($i = 1; $i <= 5; $i++)
                                        <i class="fa-solid fa-star" @if ($i > $testimonial->rating) style="opacity: 0.3;" @endif></i>
                                    @endfor
                                </div>
                            </div>
                            <div class="testimonial-content">
                                <p>{{ $testimonial->content }}</p>
                            </div>
                            <div class="testimonial-author">
                                @if ($testimonial->photo)
                                    <div class="author-image">
                                        <figure class="image-anime">
                                            <img src="{{ $testimonial->photo_url }}" alt="">
                                        </figure>
                                    </div>
                                @endif
                                <div class="author-content">
                                    <h3>{{ $testimonial->name }}</h3>
                                    <p>{{ $testimonial->role }}</p>
                                </div>
                            </div>
                        </x-vcms.item>
                        <!-- Testimonial Item End -->
                    </div>
                @endforeach
            </div>

            <x-vcms.add resource="testimonials" :fields="$testimonialFields" label="testimoni" :defaults="['rating' => 5]" />
        </div>
    </div>
    <!-- Page Testimonials End -->

    <!-- Creative Tools Section Start -->
    @include('partials.creative-tools', ['class' => 'service-creative-tools'])
    <!-- Creative Tools Section End -->

@endsection

@push('scripts')
@endpush
