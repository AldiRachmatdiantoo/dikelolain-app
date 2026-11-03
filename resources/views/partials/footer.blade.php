@php
    // Helper function to get footer content
    if (!function_exists('getFooterContent')) {
        function getFooterContent($sections, $key, $default = '') {
            if (!isset($sections['footer'])) {
                return $default;
            }
            $content = $sections['footer']->contents->firstWhere('key', $key);
            return $content ? $content->value : $default;
        }
    }

    // Footer Title
    $footerTitle = getFooterContent($sections ?? [], 'footer_footer_title', "Let's Connect");
    $footerTitleLink = getFooterContent($sections ?? [], 'footer_footer_title_link', route('contact'));

    // About Footer
    $footerLogo = getFooterContent($sections ?? [], 'footer_footer_logo_file', 'images/logo-dikelolain-full-hitam.png');
    $footerDesc = getFooterContent($sections ?? [], 'footer_footer_description', 'The creativity and professionalism shown by Rovex exceeded our expectations. Their design transformed our brand, and the results speak for themselves.');

    // Social Links
    $socialFb = getFooterContent($sections ?? [], 'footer_footer_social_fb', '#');
    $socialX = getFooterContent($sections ?? [], 'footer_footer_social_x', '#');
    $socialLi = getFooterContent($sections ?? [], 'footer_footer_social_li', '#');
    $socialIg = getFooterContent($sections ?? [], 'footer_footer_social_ig', '#');

    // Quick Links
    $quickLink1Text = getFooterContent($sections ?? [], 'footer_footer_quick_1_text', 'home');
    $quickLink1Url = getFooterContent($sections ?? [], 'footer_footer_quick_1_url', route('home'));
    $quickLink2Text = getFooterContent($sections ?? [], 'footer_footer_quick_2_text', 'about us');
    $quickLink2Url = getFooterContent($sections ?? [], 'footer_footer_quick_2_url', route('about'));
    $quickLink3Text = getFooterContent($sections ?? [], 'footer_footer_quick_3_text', 'team');
    $quickLink3Url = getFooterContent($sections ?? [], 'footer_footer_quick_3_url', route('team'));
    $quickLink4Text = getFooterContent($sections ?? [], 'footer_footer_quick_4_text', 'our portfolio');
    $quickLink4Url = getFooterContent($sections ?? [], 'footer_footer_quick_4_url', route('portfolio'));

    // Services Links
    $serviceLink1Text = getFooterContent($sections ?? [], 'footer_footer_service_1_text', 'UI/UX Design');
    $serviceLink1Url = getFooterContent($sections ?? [], 'footer_footer_service_1_url', route('services'));
    $serviceLink2Text = getFooterContent($sections ?? [], 'footer_footer_service_2_text', '3D Designs');
    $serviceLink2Url = getFooterContent($sections ?? [], 'footer_footer_service_2_url', route('services'));
    $serviceLink3Text = getFooterContent($sections ?? [], 'footer_footer_service_3_text', 'Web Development');
    $serviceLink3Url = getFooterContent($sections ?? [], 'footer_footer_service_3_url', route('services'));
    $serviceLink4Text = getFooterContent($sections ?? [], 'footer_footer_service_4_text', 'Motion Graphics');
    $serviceLink4Url = getFooterContent($sections ?? [], 'footer_footer_service_4_url', route('services'));

    // Contact Info
    $contactPhone = getFooterContent($sections ?? [], 'footer_footer_contact_phone', '+01 456-852-789');
    $contactEmail = getFooterContent($sections ?? [], 'footer_footer_contact_email', 'info@domainname.com');
    $contactAddress = getFooterContent($sections ?? [], 'footer_footer_contact_address', '843, Apple LanePeoria');

    // Copyright
    $copyrightText = getFooterContent($sections ?? [], 'footer_footer_copyright', 'Copyright © 2025 All Rights Reserved.');
@endphp

    <footer class="footer-section">
        <div class="footer-box bg-section">
            <div class="footer-title">
                <div class="container">
                    <div class="row">
                        <div class="col-lg-12">
                            <!-- Footer Title Content Start -->
                            <div class="footer-title-content">
                                <h2><a href="{{ $footerTitleLink }}">{{ $footerTitle }}</a></h2>
                            </div>
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
                            <div class="about-footer">
                                <!-- Footer Logo Start -->
                                <div class="footer-logo">
                                    <img src="{{ asset($footerLogo) }}" alt="">
                                </div>
                                <!-- Footer Logo End -->

                                <!-- About Footer Content Start -->
                                <div class="about-footer-content">
                                    <p>{{ $footerDesc }}</p>
                                </div>
                                <!-- Footer Social Links Start -->
                                <div class="footer-social-links">
                                    <ul>
                                        <li><a href="{{ $socialFb }}"><i class="fa-brands fa-facebook-f"></i></a></li>
                                        <li><a href="{{ $socialX }}"><i class="fa-brands fa-x-twitter"></i></a></li>
                                        <li><a href="{{ $socialLi }}"><i class="fa-brands fa-linkedin-in"></i></a></li>
                                        <li><a href="{{ $socialIg }}"><i class="fa-brands fa-instagram"></i></a></li>
                                    </ul>
                                </div>
                                <!-- Footer Social Links End -->

                            </div>
                            <!-- About Footer End -->
                        </div>

                        <div class="col-lg-2 col-md-3 col-6">
                            <!-- About Links Start -->
                            <div class="footer-links">
                                <h3>quick links</h3>
                                <ul>
                                    <li><a href="{{ $quickLink1Url }}">{{ $quickLink1Text }}</a></li>
                                    <li><a href="{{ $quickLink2Url }}">{{ $quickLink2Text }}</a></li>
                                    <li><a href="{{ $quickLink3Url }}">{{ $quickLink3Text }}</a></li>
                                    <li><a href="{{ $quickLink4Url }}">{{ $quickLink4Text }}</a></li>
                                </ul>
                            </div>
                            <!-- About Links End -->
                        </div>

                        <div class="col-lg-3 col-md-4 col-6">
                            <!-- About Links Start -->
                            <div class="footer-links">
                                <h3>our services</h3>
                                <ul>
                                    <li><a href="{{ $serviceLink1Url }}">{{ $serviceLink1Text }}</a></li>
                                    <li><a href="{{ $serviceLink2Url }}">{{ $serviceLink2Text }}</a></li>
                                    <li><a href="{{ $serviceLink3Url }}">{{ $serviceLink3Text }}</a></li>
                                    <li><a href="{{ $serviceLink4Url }}">{{ $serviceLink4Text }}</a></li>
                                </ul>
                            </div>
                            <!-- About Links End -->
                        </div>

                        <div class="col-lg-3 col-md-5">
                            <!-- About Links Start -->
                            <div class="footer-contact">
                                <h3>contact info</h3>
                                <!-- Footer Contact Details Start -->
                                <div class="footer-contact-details">
                                    <!-- Footer Info Box Start -->
                                    <div class="footer-info-box">
                                        <div class="icon-box">
                                            <i class="fa-solid fa-phone"></i>
                                        </div>
                                        <div class="footer-info-box-content">
                                            <p><a href="tel:{{ preg_replace('/[^0-9+]/', '', $contactPhone) }}">{{ $contactPhone }}</a></p>
                                        </div>
                                    </div>
                                    <!-- Footer Info Box End -->

                                    <!-- Footer Info Box Start -->
                                    <div class="footer-info-box">
                                        <div class="icon-box">
                                            <i class="fa-solid fa-envelope"></i>
                                        </div>
                                        <div class="footer-info-box-content">
                                            <p><a href="mailto:{{ $contactEmail }}">{{ $contactEmail }}</a></p>
                                        </div>
                                    </div>
                                    <!-- Footer Info Box End -->

                                    <!-- Footer Info Box Start -->
                                    <div class="footer-info-box">
                                        <div class="icon-box">
                                            <i class="fa-solid fa-location-dot"></i>
                                        </div>
                                        <div class="footer-info-box-content">
                                            <p>{{ $contactAddress }}</p>
                                        </div>
                                    </div>
                                    <!-- Footer Info Box End -->
                                </div>
                                <!-- Footer Contact Details End -->
                            </div>
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
                            <div class="footer-copyright-text">
                                <p>{{ $copyrightText }}</p>
                            </div>
                            <!-- Footer Copyright End -->
                        </div>
                    </div>
                </div>
            </div>
            <!-- Footer Copyright Section End -->
        </div>
    </footer>
