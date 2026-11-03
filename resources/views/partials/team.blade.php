{{-- Team section (Home, About): the first four members. Needs $settings, $teamMembers and $teamMemberFields. --}}
<div class="our-team">
    <div class="container">
        <div class="row section-row align-items-center">
            <div class="col-lg-8">
                <x-vcms.settings :keys="['team.sub_title', 'team.title']" label="Judul" class="section-title">
                    <h3 class="wow fadeInUp">{{ $settings->get('team.sub_title') }}</h3>
                    <h2 class="text-anime-style-2" data-cursor="-opaque">{!! $settings->get('team.title') !!}</h2>
                </x-vcms.settings>
            </div>
            <div class="col-lg-4">
                <x-vcms.settings :keys="['team.button_text', 'team.button_link']" label="Tombol" class="section-btn wow fadeInUp" data-wow-delay="0.2s">
                    <a href="{{ $settings->get('team.button_link') }}" class="btn-default">{{ $settings->get('team.button_text') }}</a>
                </x-vcms.settings>
            </div>
        </div>

        <div class="row">
            @forelse ($teamMembers as $member)
                @include('partials.team-card', ['delay' => 0.2 * $loop->index])
            @empty
                <div class="col-12 text-center py-5">
                    <p style="color: #888;">No team members yet.</p>
                </div>
            @endforelse
        </div>
    </div>
</div>
