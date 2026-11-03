{{-- $settings and $footerServices come from App\View\Composers\FooterComposer, so every page (404 too) has them. --}}
    <footer class="footer-section">
        <div class="footer-box bg-section">
            <div class="footer-title">
                <div class="container">
                    <div class="row">
                        <div class="col-lg-12">
                            <!-- Footer Title Content Start -->
                            <x-vcms.settings :keys="['footer.title', 'footer.title_link']" label="Judul footer" class="footer-title-content">
                                <h2><a href="{{ $settings->get('footer.title_link') }}">{{ $settings->get('footer.title') }}</a></h2>
                            </x-vcms.settings>
                            <!-- Footer Title Content End -->
                        </div>
                    </div>
                </div>
            </div>

            <!-- Main Footer Start -->
            <div class="main-footer">
                <div class="container">
                    <div class="row">
                        <div class="col-lg-4">
                            <!-- About Footer Start -->
                            <x-vcms.settings :keys="['footer.logo', 'footer.description', 'footer.facebook', 'footer.x', 'footer.linkedin', 'footer.instagram']" label="Logo & sosial media" class="about-footer">
                                <!-- Footer Logo Start -->
                                <div class="footer-logo">
                                    <img src="{{ $settings->url('footer.logo') }}" alt="">
                                </div>
                                <!-- Footer Logo End -->

                                <!-- About Footer Content Start -->
                                <div class="about-footer-content">
                                    <p>{{ $settings->get('footer.description') }}</p>
                                </div>
                                <!-- Footer Social Links Start -->
                                <div class="footer-social-links">
                                    <ul>
                                        <li><a href="{{ $settings->get('footer.facebook') }}"><i class="fa-brands fa-facebook-f"></i></a></li>
                                        <li><a href="{{ $settings->get('footer.x') }}"><i class="fa-brands fa-x-twitter"></i></a></li>
                                        <li><a href="{{ $settings->get('footer.linkedin') }}"><i class="fa-brands fa-linkedin-in"></i></a></li>
                                        <li><a href="{{ $settings->get('footer.instagram') }}"><i class="fa-brands fa-instagram"></i></a></li>
                                    </ul>
                                </div>
                                <!-- Footer Social Links End -->
                            </x-vcms.settings>
                            <!-- About Footer End -->
                        </div>

                        <div class="col-lg-2 col-md-3 col-6">
                            <!-- About Links Start -->
                            <x-vcms.settings :keys="collect(range(1, 4))->flatMap(fn ($i) => ['footer.quick_' . $i . '_text', 'footer.quick_' . $i . '_url'])->all()" label="Link cepat" class="footer-links">
                                <h3>quick links</h3>
                                <ul>
                                    @foreach (range(1, 4) as $i)
                                        <li><a href="{{ $settings->get('footer.quick_' . $i . '_url') }}">{{ $settings->get('footer.quick_' . $i . '_text') }}</a></li>
                                    @endforeach
                                </ul>
                            </x-vcms.settings>
                            <!-- About Links End -->
                        </div>

                        <div class="col-lg-3 col-md-4 col-6">
                            <!-- About Links Start -->
                            {{-- The first active services; edited with the services list (Home, Services). --}}
                            <div class="footer-links">
                                <h3>our services</h3>
                                <ul>
                                    @foreach ($footerServices as $service)
                                        <li><a href="{{ route('pages.service.single', $service->slug) }}">{{ $service->name }}</a></li>
                                    @endforeach
                                </ul>
                            </div>
                            <!-- About Links End -->
                        </div>

                        <div class="col-lg-3 col-md-5">
                            <!-- About Links Start -->
                            {{-- Same values as the Contact page. --}}
                            <x-vcms.settings :keys="['contact.phone', 'contact.email', 'contact.address']" label="Kontak" class="footer-contact">
                                <h3>contact info</h3>
                                <!-- Footer Contact Details Start -->
                                <div class="footer-contact-details">
                                    <!-- Footer Info Box Start -->
                                    <div class="footer-info-box">
                                        <div class="icon-box">
                                            <i class="fa-solid fa-phone"></i>
                                        </div>
                                        <div class="footer-info-box-content">
                                            <p><a href="tel:{{ preg_replace('/[^0-9+]/', '', $settings->get('contact.phone')) }}">{{ $settings->get('contact.phone') }}</a></p>
                                        </div>
                                    </div>
                                    <!-- Footer Info Box End -->

                                    <!-- Footer Info Box Start -->
                                    <div class="footer-info-box">
                                        <div class="icon-box">
                                            <i class="fa-solid fa-envelope"></i>
                                        </div>
                                        <div class="footer-info-box-content">
                                            <p><a href="mailto:{{ $settings->get('contact.email') }}">{{ $settings->get('contact.email') }}</a></p>
                                        </div>
                                    </div>
                                    <!-- Footer Info Box End -->

                                    <!-- Footer Info Box Start -->
                                    <div class="footer-info-box">
                                        <div class="icon-box">
                                            <i class="fa-solid fa-location-dot"></i>
                                        </div>
                                        <div class="footer-info-box-content">
                                            <p>{{ $settings->get('contact.address') }}</p>
                                        </div>
                                    </div>
                                    <!-- Footer Info Box End -->
                                </div>
                                <!-- Footer Contact Details End -->
                            </x-vcms.settings>
                            <!-- About Links End -->
                        </div>
                    </div>
                </div>
            </div>
            <!-- Main Footer End -->

            <!-- Footer Copyright Section Start -->
            <div class="footer-copyright">
                <div class="container">
                    <div class="row align-items-center">
                        <div class="col-lg-12">
                            <!-- Footer Copyright Start -->
                            <x-vcms.settings :keys="['footer.copyright']" label="Copyright" class="footer-copyright-text">
                                <p>{{ $settings->get('footer.copyright') }}</p>
                            </x-vcms.settings>
                            <!-- Footer Copyright End -->
                        </div>
                    </div>
                </div>
            </div>
            <!-- Footer Copyright Section End -->
        </div>
    </footer>
