{{-- CTA section with counters (Home, About). Needs $settings, $counters and $counterFields. --}}
<div class="cta-section bg-section">
    <div class="container">
        <div class="row">
            <div class="col-lg-12">
                <div class="cta-box">
                    <div class="cta-counter-content-box">
                        <x-vcms.settings :keys="['cta.text']" label="Teks" class="cta-box-content wow fadeInUp">
                            <p>{{ $settings->get('cta.text') }}</p>
                        </x-vcms.settings>
                        <div class="cta-counter-box">
                            @foreach ($counters as $counter)
                                <x-vcms.item resource="counters" :item="$counter" :fields="$counterFields" label="angka"
                                    class="cta-counter-item {{ $counter->is_active ? '' : 'vcms-hidden' }}">
                                    <h3><span class="counter">{{ $counter->number }}</span>{{ $counter->suffix }}</h3>
                                    <p>{{ $counter->label }}</p>
                                </x-vcms.item>
                            @endforeach
                        </div>

                        <x-vcms.add resource="counters" :fields="$counterFields" label="angka" />
                    </div>

                    <x-vcms.settings :keys="['cta.circle_image', 'cta.circle_link']" label="Tombol" class="cta-contact-circle">
                        <a href="{{ $settings->get('cta.circle_link') }}"><img src="{{ $settings->url('cta.circle_image') }}" alt="Contact Circle"></a>
                    </x-vcms.settings>
                </div>
            </div>
        </div>
    </div>
</div>
