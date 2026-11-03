{{-- One team member card (team section, Team page). Needs $member, $teamMemberFields and $delay. --}}
<div class="col-lg-3 col-md-6">
    <x-vcms.item resource="team-members" :item="$member" :fields="$teamMemberFields" label="anggota tim"
        class="team-item wow fadeInUp {{ $member->is_active ? '' : 'vcms-hidden' }}" data-wow-delay="{{ $delay }}s">
        <div class="team-image">
            <a href="{{ route('pages.team.single', $member->slug) }}" data-cursor-text="View">
                <figure class="image-anime">
                    <img src="{{ $member->photo_url }}" alt="{{ $member->name }}" loading="lazy">
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
