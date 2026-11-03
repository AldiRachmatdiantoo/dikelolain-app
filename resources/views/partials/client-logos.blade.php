{{-- Client logos section (Home, About). Needs $settings, $clientLogos and $clientLogoFields. --}}
<div class="our-testimonial bg-section">
    <div class="container">
        <div class="row section-row align-items-center">
            <div class="col-lg-12">
                <x-vcms.settings :keys="['clients.sub_title', 'clients.title']" label="Judul" class="section-title">
                    <h3>{{ $settings->get('clients.sub_title') }}</h3>
                    <h2 class="text-anime-style-2" data-cursor="-opaque">{!! $settings->get('clients.title') !!}</h2>
                </x-vcms.settings>
            </div>
        </div>

        <div class="row">
            <div class="col-lg-12">
                <div class="trusted-client-box">
                    <x-vcms.settings :keys="['clients.box_title']" label="Judul kotak" class="trusted-client-title">
                        <h3>{{ $settings->get('clients.box_title') }}</h3>
                    </x-vcms.settings>
                    <div class="trusted-clients-slider">
                        <div class="swiper">
                            <div class="swiper-wrapper">
                                @foreach ($clientLogos->chunk(2) as $pair)
                                    <div class="swiper-slide">
                                        <div class="d-flex flex-column justify-content-between align-items-center client-logo-stack">
                                            @foreach ($pair as $logo)
                                                <x-vcms.item resource="client-logos" :item="$logo" :fields="$clientLogoFields" label="logo"
                                                    class="trusted-client-logo {{ $logo->is_active ? '' : 'vcms-hidden' }}">
                                                    <img src="{{ $logo->logo_url }}" alt="{{ $logo->name ?: 'Client Logo' }}">
                                                </x-vcms.item>
                                            @endforeach
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    </div>
                </div>

                <x-vcms.add resource="client-logos" :fields="$clientLogoFields" label="logo klien" />
            </div>
        </div>
    </div>
</div>
