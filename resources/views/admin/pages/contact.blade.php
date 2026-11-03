@extends('admin.layouts.admin')

@section('title', 'Edit Contact Page')
@section('page-title', 'Contact Page - VCMS Editor')

@php
    $previewUrl = route('contact');
@endphp

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
$headerTitle = getContent($sections, 'contact-header', 'contact-header-1_title', '');
if (empty($headerTitle)) {
    $headerTitle = getContent($sections, 'contact', 'contact-header-1_title', 'Contact <span>us</span>');
}
$headerBreadcrumb = getContent($sections, 'contact-header', 'contact-header-1_breadcrumb', '');
if (empty($headerBreadcrumb)) {
    $headerBreadcrumb = getContent($sections, 'contact', 'contact-header-1_breadcrumb', 'Contact');
}
@endphp

<!-- Page Header Section -->
<div class="page-header bg-section vcms-editable-section" data-section="contact-header" data-item-id="contact-header-1">
    <div class="vcms-edit-indicator">
        <button class="vcms-edit-btn" data-edit-type="page-header">
            <i class="fa-solid fa-pen"></i> Edit Header
        </button>
    </div>
    <div class="container">
        <div class="row">
            <div class="col-lg-12">
                <div class="page-header-box vcms-editable-item" data-edit-type="page-header" data-item-id="contact-header-1"
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

<!-- Contact Section -->
<div class="page-contact-us vcms-editable-section" data-section="contact" data-item-id="contact-section">
    <div class="vcms-edit-indicator">
        <button class="vcms-edit-btn" data-edit-type="contact-section">
            <i class="fa-solid fa-pen"></i> Edit Contact Section
        </button>
    </div>

    <div class="container">
        <div class="row align-items-center">
            <!-- Contact Info -->
            <div class="col-lg-6">
                <div class="contact-us-content">
                    <div class="section-title">
                        @php
                            $contactSubTitle = getContent($sections, 'contact', 'contact-section_sub_title', 'contact us');
                            $contactMainTitle = getContent($sections, 'contact', 'contact-section_main_title', "Let's start a <span>conversation</span>");
                        @endphp
                        <h3>{{ $contactSubTitle }}</h3>
                        <h2>{!! $contactMainTitle !!}</h2>
                    </div>

                    <div class="contact-info-list">
                        <!-- Phone -->
                        <div class="contact-info-item" data-item-id="contact-phone">
                            @php
                                $phoneIcon = getContent($sections, 'contact', 'contact-section_phone_icon', 'images/icon-phone.svg');
                                $phoneLabel = getContent($sections, 'contact', 'contact-section_phone_label', 'phone number');
                                $phoneValue = getContent($sections, 'contact', 'contact-section_phone_value', '+987 654 3210');
                            @endphp
                            <div class="icon-box">
                                <img src="{{ asset($phoneIcon) }}" alt="">
                            </div>
                            <div class="contact-info-content">
                                <h3>{{ $phoneLabel }}</h3>
                                <p>{{ $phoneValue }}</p>
                            </div>
                        </div>

                        <!-- Email -->
                        <div class="contact-info-item" data-item-id="contact-email">
                            @php
                                $emailIcon = getContent($sections, 'contact', 'contact-section_email_icon', 'images/icon-mail.svg');
                                $emailLabel = getContent($sections, 'contact', 'contact-section_email_label', 'email us');
                                $emailValue = getContent($sections, 'contact', 'contact-section_email_value', 'info@domain.com');
                            @endphp
                            <div class="icon-box">
                                <img src="{{ asset($emailIcon) }}" alt="">
                            </div>
                            <div class="contact-info-content">
                                <h3>{{ $emailLabel }}</h3>
                                <p>{{ $emailValue }}</p>
                            </div>
                        </div>

                        <!-- Address -->
                        <div class="contact-info-item" data-item-id="contact-address">
                            @php
                                $addressIcon = getContent($sections, 'contact', 'contact-section_address_icon', 'images/icon-location.svg');
                                $addressLabel = getContent($sections, 'contact', 'contact-section_address_label', 'address');
                                $addressValue = getContent($sections, 'contact', 'contact-section_address_value', '7676 Arden Avenue, Ginashire');
                            @endphp
                            <div class="icon-box">
                                <img src="{{ asset($addressIcon) }}" alt="">
                            </div>
                            <div class="contact-info-content">
                                <h3>{{ $addressLabel }}</h3>
                                <p>{{ $addressValue }}</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Contact Form -->
            <div class="col-lg-6">
                <div class="contact-us-form">
                    @php
                        // Check contact-form_ first (where data is stored), then fallback to contact-section_
                        $formTitle = getContent($sections, 'contact', 'contact-form_form_title', '');
                        if (empty($formTitle)) {
                            $formTitle = getContent($sections, 'contact', 'contact-section_form_title', 'Get a <span>free advise</span>');
                        }
                        
                        $formBtnText = getContent($sections, 'contact', 'contact-form_form_btn_text', '');
                        if (empty($formBtnText)) {
                            $formBtnText = getContent($sections, 'contact', 'contact-section_form_btn_text', 'send message');
                        }
                        
                        $whatsappNumber = getContent($sections, 'contact', 'contact-form_whatsapp_number', '');
                        if (empty($whatsappNumber)) {
                            $whatsappNumber = getContent($sections, 'contact', 'contact-section_whatsapp_number', '6281234567890');
                        }
                    @endphp
                    <div class="section-title">
                        <h2>{!! $formTitle !!}</h2>
                    </div>
                    <div class="contact-form">
                        <form id="contactForm" action="#" method="POST">
                            <div class="row">
                                <div class="form-group col-md-6 mb-4">
                                    <label class="form-label">first name</label>
                                    <input type="text" name="fname" class="form-control" placeholder="First Name" required>
                                </div>
                                <div class="form-group col-md-6 mb-4">
                                    <label class="form-label">last name</label>
                                    <input type="text" name="lname" class="form-control" placeholder="Last Name" required>
                                </div>
                                <div class="form-group col-md-6 mb-4">
                                    <label class="form-label">email address</label>
                                    <input type="email" name="email" class="form-control" placeholder="Email Address" required>
                                </div>
                                <div class="form-group col-md-6 mb-4">
                                    <label class="form-label">mobile number</label>
                                    <input type="text" name="phone" class="form-control" placeholder="Mobile Number" required>
                                </div>
                                <div class="form-group col-md-12 mb-5">
                                    <label class="form-label">your message</label>
                                    <textarea name="message" class="form-control" rows="4" placeholder="Subject"></textarea>
                                </div>
                                <div class="col-md-12">
                                    <button type="submit" class="btn-default">{{ $formBtnText }}</button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Google Map Section -->
<div class="google-map vcms-editable-section" data-section="contact-map" data-item-id="contact-map">
    <div class="vcms-edit-indicator">
        <button class="vcms-edit-btn" data-edit-type="map-section">
            <i class="fa-solid fa-pen"></i> Edit Map
        </button>
    </div>
    <div class="container">
        <div class="row">
            <div class="col-lg-12">
                <div class="google-map-iframe">
                    @php
                        $mapEmbed = getContent($sections, 'contact-map', 'contact-map_embed_url', '');
                        if (empty($mapEmbed)) {
                            $mapEmbed = getContent($sections, 'contact-map', 'contact-map-embed_url', 'https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d96737.10562045308!2d-74.08535042841811!3d40.739265258395164!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x89c24fa5d33f083b%3A0xc80b8f06e177fe62!2sNew%20York%2C%20NY%2C%20USA!5e0!3m2!1sen!2sin!4v1703158537552!5m2!1sen!2sin');
                        }
                    @endphp
                    <iframe src="{{ $mapEmbed }}" allowfullscreen="" loading="lazy" referrerpolicy="no-referrer-when-downgrade"></iframe>
                </div>
            </div>
        </div>
    </div>
</div>

@endsection
