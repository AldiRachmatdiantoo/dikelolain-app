@extends('layouts.app')

@section('title', 'Contact - Dikelolain')

@section('content')

    <x-vcms.settings :keys="['contact.header.title', 'contact.header.breadcrumb']" label="Header">
        <x-page-header :title="$settings->get('contact.header.title')">
            <li class="breadcrumb-item active" aria-current="page">{{ $settings->get('contact.header.breadcrumb') }}</li>
        </x-page-header>
    </x-vcms.settings>

    <!-- Page Contact Us Start -->
    <div class="page-contact-us">
        <div class="container">
            <div class="row align-items-center">
                <div class="col-lg-6">
                    <!-- Contact Us Content Start -->
                    <div class="contact-us-content">
                        <x-vcms.settings :keys="['contact.sub_title', 'contact.title']" label="Judul" class="section-title">
                            <h3>{{ $settings->get('contact.sub_title') }}</h3>
                            <h2 class="text-anime-style-2" data-cursor="-opaque">{!! $settings->get('contact.title') !!}</h2>
                        </x-vcms.settings>

                        <!-- Contact Info List Start -->
                        <div class="contact-info-list">
                            @foreach (['phone' => 'Telepon', 'email' => 'Email', 'address' => 'Alamat'] as $item => $label)
                                <x-vcms.settings :keys="['contact.' . $item . '_icon', 'contact.' . $item . '_label', 'contact.' . $item]" :label="$label"
                                    class="contact-info-item wow fadeInUp" :data-wow-delay="$loop->first ? null : (0.25 * $loop->index) . 's'">
                                    <div class="icon-box">
                                        <img src="{{ $settings->url('contact.' . $item . '_icon') }}" alt="">
                                    </div>
                                    <div class="contact-info-content">
                                        <h3>{{ $settings->get('contact.' . $item . '_label') }}</h3>
                                        <p>{{ $settings->get('contact.' . $item) }}</p>
                                    </div>
                                </x-vcms.settings>
                            @endforeach
                        </div>
                        <!-- Contact Info List End -->
                    </div>
                    <!-- Contact Us Content End -->
                </div>

                <div class="col-lg-6">
                    <!-- Contact Us Form Start -->
                    <x-vcms.settings :keys="['contact.form_title', 'contact.form_button', 'contact.whatsapp']" label="Form" class="contact-us-form">
                        <div class="section-title">
                            <h2 class="text-anime-style-2" data-cursor="-opaque">{!! $settings->get('contact.form_title') !!}</h2>
                        </div>

                        <!-- Contact Form Start -->
                        <div class="contact-form">
                            {{-- The form opens WhatsApp with the message; the number keeps digits only. --}}
                            <form id="contactForm" action="#" method="POST" class="wow fadeInUp" data-wow-delay="0.25s" data-whatsapp="{{ preg_replace('/\D+/', '', $settings->get('contact.whatsapp')) }}">
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
                                        <button type="submit" class="btn-default">{{ $settings->get('contact.form_button') }}</button>
                                        <div id="msgSubmit" class="h3 hidden"></div>
                                    </div>
                                </div>
                            </form>
                        </div>
                        <!-- Contact Form End -->
                    </x-vcms.settings>
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
                    <x-vcms.settings :keys="['contact.map_url']" label="Peta" class="google-map-iframe">
                        <iframe src="{{ $settings->get('contact.map_url') }}" allowfullscreen="" loading="lazy" referrerpolicy="no-referrer-when-downgrade"></iframe>
                    </x-vcms.settings>
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
