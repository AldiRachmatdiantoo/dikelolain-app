@extends('layouts.app')

@section('title', 'Contact - Dikelolain')

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

// Header - check both sections for backward compatibility
$headerTitle = getContent($sections, 'contact-header', 'contact-header-1_title', 'Contact <span>us</span>');
$headerBreadcrumb = getContent($sections, 'contact-header', 'contact-header-1_breadcrumb', 'Contact');

if (empty($headerTitle)) {
    $headerTitle = getContent($sections, 'contact', 'contact-header-1_title', 'Contact <span>us</span>');
}

// Section Title
$subTitle = getContent($sections, 'contact', 'contact-section_sub_title', 'contact us');
$mainTitle = getContent($sections, 'contact', 'contact-section_main_title', "Let's start a <span>conversation</span>");

// Contact Info
$phoneIcon = getContent($sections, 'contact', 'contact-section_phone_icon', 'images/icon-phone.svg');
$phoneLabel = getContent($sections, 'contact', 'contact-section_phone_label', 'phone number');
$phoneValue = getContent($sections, 'contact', 'contact-section_phone_value', '+987 654 3210');

$emailIcon = getContent($sections, 'contact', 'contact-section_email_icon', 'images/icon-mail.svg');
$emailLabel = getContent($sections, 'contact', 'contact-section_email_label', 'email us');
$emailValue = getContent($sections, 'contact', 'contact-section_email_value', 'info@domain.com');

$addressIcon = getContent($sections, 'contact', 'contact-section_address_icon', 'images/icon-location.svg');
$addressLabel = getContent($sections, 'contact', 'contact-section_address_label', 'address');
$addressValue = getContent($sections, 'contact', 'contact-section_address_value', '7676 Arden Avenue, Ginashire');

// Form Settings - check contact-form_ first (where data is stored), then fallback
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

// Map - check both key formats
$mapEmbed = getContent($sections, 'contact-map', 'contact-map_embed_url', '');
if (empty($mapEmbed)) {
    $mapEmbed = getContent($sections, 'contact-map', 'contact-map-embed_url', 'https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d96737.10562045308!2d-74.08535042841811!3d40.739265258395164!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x89c24fa5d33f083b%3A0xc80b8f06e177fe62!2sNew%20York%2C%20NY%2C%20USA!5e0!3m2!1sen!2sin!4v1703158537552!5m2!1sen!2sin');
}
@endphp

@section('content')

    <x-page-header title="{!! $headerTitle !!}">
        <li class="breadcrumb-item active" aria-current="page">{{ $headerBreadcrumb }}</li>
    </x-page-header>

    <!-- Page Contact Us Start -->
    <div class="page-contact-us">
        <div class="container">
            <div class="row align-items-center">
                <div class="col-lg-6">
                    <!-- Contact Us Content Start -->
                    <div class="contact-us-content">
                        <!-- Section Title Start -->
                        <div class="section-title">
                            <h3>{{ $subTitle }}</h3>
                            <h2 class="text-anime-style-2" data-cursor="-opaque">{!! $mainTitle !!}</h2>
                        </div>
                        <!-- Section Title End -->

                        <!-- Contact Info List Start -->
                        <div class="contact-info-list">
                            <!-- Contact Info Item Start -->
                            <div class="contact-info-item wow fadeInUp">
                                <div class="icon-box">
                                    <img src="{{ asset($phoneIcon) }}" alt="">
                                </div>
                                <div class="contact-info-content">
                                    <h3>{{ $phoneLabel }}</h3>
                                    <p>{{ $phoneValue }}</p>
                                </div>
                            </div>
                            <!-- Contact Info Item End -->

                            <!-- Contact Info Item Start -->
                            <div class="contact-info-item wow fadeInUp" data-wow-delay="0.25s">
                                <div class="icon-box">
                                    <img src="{{ asset($emailIcon) }}" alt="">
                                </div>
                                <div class="contact-info-content">
                                    <h3>{{ $emailLabel }}</h3>
                                    <p>{{ $emailValue }}</p>
                                </div>
                            </div>
                            <!-- Contact Info Item End -->

                            <!-- Contact Info Item Start -->
                            <div class="contact-info-item wow fadeInUp" data-wow-delay="0.5s">
                                <div class="icon-box">
                                    <img src="{{ asset($addressIcon) }}" alt="">
                                </div>
                                <div class="contact-info-content">
                                    <h3>{{ $addressLabel }}</h3>
                                    <p>{{ $addressValue }}</p>
                                </div>
                            </div>
                            <!-- Contact Info Item End -->
                        </div>
                        <!-- Contact Info List End -->
                    </div>
                    <!-- Contact Us Content End -->
                </div>

                <div class="col-lg-6">
                    <!-- Contact Us Form Start -->
                    <div class="contact-us-form">
                        <!-- Section Title Start -->
                        <div class="section-title">
                            <h2 class="text-anime-style-2" data-cursor="-opaque">{!! $formTitle !!}</h2>
                        </div>
                        <!-- Section Title End -->

                        <!-- Contact Form Start -->
                        <div class="contact-form">
                            <form id="contactForm" action="#" method="POST" class="wow fadeInUp" data-wow-delay="0.25s" data-whatsapp="{{ $whatsappNumber }}">
                                <div class="row">
                                    <div class="form-group col-md-6 mb-4">
                                        <label class="form-label">first name</label>
                                        <input type="text" name="fname" class="form-control" id="fname" placeholder="First Name" required>
                                        <div class="help-block with-errors"></div>
                                    </div>

                                    <div class="form-group col-md-6 mb-4">
                                        <label class="form-label">last name</label>
                                        <input type="text" name="lname" class="form-control" id="lname" placeholder="Last Name" required>
                                        <div class="help-block with-errors"></div>
                                    </div>

                                    <div class="form-group col-md-6 mb-4">
                                        <label class="form-label">email address</label>
                                        <input type="email" name="email" class="form-control" id="email" placeholder="Email Address" required>
                                        <div class="help-block with-errors"></div>
                                    </div>

                                    <div class="form-group col-md-6 mb-4">
                                        <label class="form-label">mobile number</label>
                                        <input type="text" name="phone" class="form-control" id="phone" placeholder="Mobile Number" required>
                                        <div class="help-block with-errors"></div>
                                    </div>

                                    <div class="form-group col-md-12 mb-5">
                                        <label class="form-label">your message</label>
                                        <textarea name="message" class="form-control" id="message" rows="4" placeholder="Your message here..."></textarea>
                                        <div class="help-block with-errors"></div>
                                    </div>

                                    <div class="col-md-12">
                                        <button type="submit" class="btn-default">{{ $formBtnText }}</button>
                                        <div id="msgSubmit" class="h3 hidden"></div>
                                    </div>
                                </div>
                            </form>
                        </div>
                        <!-- Contact Form End -->
                    </div>
                    <!-- Contact Us Form End -->
                </div>
            </div>
        </div>
    </div>
    <!-- Page Contact Us End -->

    <!-- Google Map Section Start -->
    <div class="google-map">
        <div class="container">
            <div class="row">
                <div class="col-lg-12">
                    <!-- Google Map IFrame Start -->
                    <div class="google-map-iframe">
                        <iframe src="{{ $mapEmbed }}" allowfullscreen="" loading="lazy" referrerpolicy="no-referrer-when-downgrade"></iframe>
                    </div>
                    <!-- Google Map IFrame End -->
                </div>
            </div>
        </div>
    </div>
    <!-- Google Map Section End -->

@endsection

@push('scripts')
<script>
(function() {
    const contactForm = document.getElementById('contactForm');

    if (contactForm) {
        contactForm.addEventListener('submit', function(e) {
            e.preventDefault();
            e.stopPropagation();

            // Get WhatsApp number from data attribute
            const whatsappNumber = this.getAttribute('data-whatsapp') || '6281234567890';

            // Get form values
            const fname = this.querySelector('[name="fname"]').value.trim();
            const lname = this.querySelector('[name="lname"]').value.trim();
            const email = this.querySelector('[name="email"]').value.trim();
            const phone = this.querySelector('[name="phone"]').value.trim();
            const message = this.querySelector('[name="message"]').value.trim();

            // Validate required fields
            if (!fname || !lname || !email || !phone) {
                alert('Mohon lengkapi semua field yang wajib diisi.');
                return false;
            }

            // Build WhatsApp message
            let waMessage = `Halo, saya ingin menghubungi Anda.\n\n`;
            waMessage += `*Nama:* ${fname} ${lname}\n`;
            waMessage += `*Email:* ${email}\n`;
            waMessage += `*No. HP:* ${phone}\n`;
            if (message) {
                waMessage += `\n*Pesan:*\n${message}`;
            }

            // Encode message for URL
            const encodedMessage = encodeURIComponent(waMessage);

            // Build WhatsApp URL
            const waUrl = `https://wa.me/${whatsappNumber}?text=${encodedMessage}`;

            // Redirect to WhatsApp
            window.location.href = waUrl;

            return false;
        });
    }
})();
</script>
@endpush
