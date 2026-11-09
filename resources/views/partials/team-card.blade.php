{{-- One team member card (team section, Team page). Needs $member and $teamMemberFields. --}}
<div class="col-lg-3 col-md-6">
    <x-vcms.item resource="team-members" :item="$member" :fields="$teamMemberFields" label="anggota tim"
        class="team-item {{ $member->is_active ? '' : 'vcms-hidden' }}">
        <div class="team-image">
            <a href="{{ route('pages.team.single', $member->slug) }}" data-cursor-text="View">
                <figure class="image-anime">
                    <x-img :src="$member->photo_url" sizes="(min-width: 1200px) 295px, (min-width: 992px) 214px, (min-width: 768px) 346px, calc(100vw - 45px)" alt="{{ $member->name }}" loading="lazy" />
                </figure>
            </a>
        </div>
        <div class="team-body">
            <div class="team-content">
                <h3><a href="{{ route('pages.team.single', $member->slug) }}">{{ $member->name }}</a></h3>
            </div>
            <div class="team-social-list">
                <span>{{ $member->role }}</span>
            </div>
        </div>
    </x-vcms.item>
</div>
