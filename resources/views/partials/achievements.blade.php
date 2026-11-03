{{-- Achievements section (Home, About). Needs $settings, $achievements and $achievementFields. --}}
<div class="our-achievements">
    <div class="container">
        <div class="row">
            <div class="col-lg-5">
                <div class="our-achievements-content">
                    <x-vcms.settings :keys="['achievements.sub_title', 'achievements.title', 'achievements.description']" label="Judul" class="section-title">
                        <h3>{{ $settings->get('achievements.sub_title') }}</h3>
                        <h2 class="text-anime-style-2" data-cursor="-opaque">{!! $settings->get('achievements.title') !!}</h2>
                        <p>{{ $settings->get('achievements.description') }}</p>
                    </x-vcms.settings>
                    <x-vcms.settings :keys="['achievements.button_text', 'achievements.button_link']" label="Tombol" class="achievements-content-btn">
                        <a href="{{ $settings->get('achievements.button_link') }}" class="btn-default">{{ $settings->get('achievements.button_text') }}</a>
                    </x-vcms.settings>
                </div>
            </div>

            <div class="col-lg-7">
                <div class="our-achievements-box">
                    @foreach ($achievements as $achievement)
                        <x-vcms.item resource="achievements" :item="$achievement" :fields="$achievementFields" label="pencapaian"
                            class="achievements-item {{ $achievement->is_active ? '' : 'vcms-hidden' }}">
                            <h3>{{ $achievement->period }}</h3>
                            <h2>{{ $achievement->title }}</h2>
                            <p>{{ $achievement->description }}</p>
                        </x-vcms.item>
                    @endforeach
                </div>

                <x-vcms.add resource="achievements" :fields="$achievementFields" label="pencapaian" />
            </div>
        </div>
    </div>
</div>
