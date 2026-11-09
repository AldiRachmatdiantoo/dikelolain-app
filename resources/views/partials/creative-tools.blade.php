{{-- Creative tools section (Home, Services, Pricing, Testimonials). Needs $settings, $tools and $toolFields; $class adds a wrapper class. --}}
<div class="creative-tools{{ isset($class) ? ' ' . $class : '' }}">
    <div class="container">
        <div class="row section-row align-items-center">
            <div class="col-lg-9">
                <x-vcms.settings :keys="['tools.sub_title', 'tools.title']" label="Judul" class="section-title">
                    <h3>{{ $settings->get('tools.sub_title') }}</h3>
                    <h2 class="text-anime-style-2" data-cursor="-opaque">{!! $settings->get('tools.title') !!}</h2>
                </x-vcms.settings>
            </div>
            <div class="col-lg-3">
                <x-vcms.settings :keys="['tools.button_text', 'tools.button_link']" label="Tombol" class="section-btn">
                    <a href="{{ $settings->get('tools.button_link') }}" class="btn-default">{{ $settings->get('tools.button_text') }}</a>
                </x-vcms.settings>
            </div>
        </div>

        <div class="row">
            <div class="col-lg-12">
                <div class="creative-tools-box">
                    @foreach ($tools as $index => $tool)
                        <x-vcms.item resource="tools" :item="$tool" :fields="$toolFields" label="tool"
                            class="creative-tool-item {{ $tool->is_active ? '' : 'vcms-hidden' }}">
                            <div class="icon-box">
                                <x-img :src="$tool->icon_url" sizes="80px" alt="{{ $tool->name }} Icon" loading="lazy" />
                            </div>
                            <div class="creative-tool-item-content">
                                <h3>{{ $tool->name }}</h3>
                                <p>{{ $tool->category }}</p>
                            </div>
                        </x-vcms.item>
                    @endforeach
                </div>

                <x-vcms.add resource="tools" :fields="$toolFields" label="tool" :defaults="['category' => 'Design tool']" />
            </div>
        </div>
    </div>
</div>
