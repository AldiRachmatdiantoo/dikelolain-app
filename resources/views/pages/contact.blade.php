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
                            {{-- The form saves the message (admin inbox), then opens WhatsApp with it; the number keeps digits only. --}}
                            <form id="contactForm" action="{{ route('contact.send') }}" method="POST" class="wow fadeInUp" data-wow-delay="0.25s" data-whatsapp="{{ preg_replace('/\D+/', '', $settings->get('contact.whatsapp')) }}">
                                @csrf
                                {{-- Honeypot: hidden from people, bots fill it in. --}}
                                <div class="d-none" aria-hidden="true"><input type="text" name="website" tabindex="-1" autocomplete="off"></div>
                                <div class="row">
                                    <div class="form-group col-md-6 mb-4">
                                        <label class="form-label" for="fname">first name</label>
                                        <input type="text" name="first_name" class="form-control" id="fname" placeholder="First Name" maxlength="100" required>
                                        <div class="help-block with-errors"></div>
                                    </div>

                                    <div class="form-group col-md-6 mb-4">
                                        <label class="form-label" for="lname">last name</label>
                                        <input type="text" name="last_name" class="form-control" id="lname" placeholder="Last Name" maxlength="100" required>
                                        <div class="help-block with-errors"></div>
                                    </div>

                                    <div class="form-group col-md-6 mb-4">
                                        <label class="form-label" for="email">email address</label>
                                        <input type="email" name="email" class="form-control" id="email" placeholder="Email Address" maxlength="255" required>
                                        <div class="help-block with-errors"></div>
                                    </div>

                                    <div class="form-group col-md-6 mb-4">
                                        <label class="form-label" for="phone">mobile number</label>
                                        <input type="tel" name="phone" class="form-control" id="phone" placeholder="Mobile Number" maxlength="30" required>
                                        <div class="help-block with-errors"></div>
                                    </div>

                                    <div class="form-group col-md-12 mb-5">
                                        <label class="form-label" for="message">your message</label>
                                        <textarea name="message" class="form-control" id="message" rows="4" placeholder="Your message here..." maxlength="5000"></textarea>
                                        <div class="help-block with-errors"></div>
                                    </div>

                                    <div class="col-md-12">
                                        <button type="submit" class="btn-default">{{ $settings->get('contact.form_button') }}</button>
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

    if (!contactForm) {
        return;
    }

    function clearErrors() {
        contactForm.querySelectorAll('.help-block.with-errors').forEach(function(help) {
            help.textContent = '';
        });
    }

    // Server validation messages go under their field, in the template's error style.
    function showErrors(errors) {
        Object.keys(errors).forEach(function(name) {
            const field = contactForm.querySelector('[name="' + name + '"]');
            const help = field && field.parentElement.querySelector('.help-block.with-errors');
            if (!help) {
                return;
            }
            const list = document.createElement('ul');
            list.className = 'list-unstyled';
            const item = document.createElement('li');
            item.textContent = errors[name][0];
            list.appendChild(item);
            help.appendChild(list);
        });

        const first = contactForm.querySelector('.help-block.with-errors li');
        if (first) {
            first.closest('.form-group').querySelector('.form-control').focus();
        }
    }

    function whatsappUrl(form) {
        const value = function(name) {
            return form.querySelector('[name="' + name + '"]').value.trim();
        };
        const number = form.getAttribute('data-whatsapp') || '6281234567890';

        let text = 'Halo, saya ingin menghubungi Anda.\n\n';
        text += '*Nama:* ' + value('first_name') + ' ' + value('last_name') + '\n';
        text += '*Email:* ' + value('email') + '\n';
        text += '*No. HP:* ' + value('phone') + '\n';
        if (value('message')) {
            text += '\n*Pesan:*\n' + value('message');
        }

        return 'https://wa.me/' + number + '?text=' + encodeURIComponent(text);
    }

    // The browser checks required fields and the email format before this runs.
    contactForm.addEventListener('submit', async function(e) {
        e.preventDefault();

        const button = this.querySelector('[type="submit"]');
        const url = whatsappUrl(this);
        button.disabled = true;
        clearErrors();

        // Save first (admin inbox), then continue to WhatsApp. Only invalid input stops
        // the visitor; if saving fails for another reason, WhatsApp still opens.
        try {
            const response = await fetch(this.action, {
                method: 'POST',
                body: new FormData(this),
                headers: { 'Accept': 'application/json' },
            });

            if (response.status === 422) {
                showErrors((await response.json()).errors || {});
                button.disabled = false;
                return;
            }
        } catch (error) {
            // Network error: the message is not saved, but WhatsApp still has it.
        }

        this.reset();
        window.location.href = url;
    });

    // Coming back from WhatsApp with the Back button restores the page as it was left.
    window.addEventListener('pageshow', function() {
        contactForm.querySelector('[type="submit"]').disabled = false;
    });
})();
</script>
@endpush
