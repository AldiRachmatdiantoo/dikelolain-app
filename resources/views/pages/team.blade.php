@extends('layouts.app')

@section('title', 'Our Team - Dikelolain')

@php
// Helper function to get content from database
if (!function_exists('getTeamContent')) {
    function getTeamContent($sections, $sectionKey, $contentKey, $default = '') {
        if (!isset($sections[$sectionKey])) {
            return $default;
        }
        $content = $sections[$sectionKey]->contents->firstWhere('key', $contentKey);
        return $content ? $content->value : $default;
    }
}

// Page Header data
$headerTitle = getTeamContent($sections, 'team-header', 'team-header-1_title', 'The minds behind <span>the magic</span>');
$headerBreadcrumb = getTeamContent($sections, 'team-header', 'team-header-1_breadcrumb', 'Our Team');
@endphp

@section('content')

    <!-- Page Header -->
    <x-page-header title="{!! $headerTitle !!}">
        <li class="breadcrumb-item active" aria-current="page">{{ $headerBreadcrumb }}</li>
    </x-page-header>

<div class="page-team">
    <div class="container">
        <div class="row">
            @php
                // Hitung jumlah member dari database - support both prefixes for backward compatibility
                $teamCount = 0;
                $keyPrefix = 'team-list-1'; // default prefix
                if (isset($sections['our-team'])) {
                    foreach ($sections['our-team']->contents as $content) {
                        // Check for new format (team-list-1_member_X_name)
                        if (preg_match('/^team-list-1_member_(\d+)_name$/', $content->key, $matches)) {
                            $teamCount = max($teamCount, (int)$matches[1] + 1);
                            $keyPrefix = 'team-list-1';
                        }
                        // Also check for old format (default_member_X_name) for backward compatibility
                        elseif (preg_match('/^default_member_(\d+)_name$/', $content->key, $matches)) {
                            $teamCount = max($teamCount, (int)$matches[1] + 1);
                            $keyPrefix = 'default';
                        }
                    }
                }
            @endphp
            
            @if ($teamCount === 0)
                <div class="col-12 text-center py-5">
                    <p style="color: #888;">No team members yet. Add members from admin panel.</p>
                </div>
            @endif
            
            @for ($index = 0; $index < $teamCount; $index++)
                @php
                    $memberName = getTeamContent($sections, 'our-team', "{$keyPrefix}_member_{$index}_name", 'Team Member');
                    $memberRole = getTeamContent($sections, 'our-team', "{$keyPrefix}_member_{$index}_role", 'Position');
                    $memberImg = getTeamContent($sections, 'our-team', "{$keyPrefix}_member_{$index}_img", 'images/team-1.jpg');
                    $memberLink = route('pages.team.single', ['index' => $index]);
                @endphp
                <div class="col-lg-3 col-md-6">
                    <div class="team-item wow fadeInUp" data-wow-delay="{{ 0.2 * $index }}s">
                        <div class="team-image">
                            <a href="{{ $memberLink }}" data-cursor-text="View">
                                <figure class="image-anime">
                                    <img src="{{ asset($memberImg) }}" alt="{{ $memberName }}">
                                </figure>
                            </a>
                        </div>
                        <div class="team-body">
                            <div class="team-content">
                                <h3><a href="{{ $memberLink }}">{{ $memberName }}</a></h3>
                            </div>
                            <div class="team-social-list">
                                <span>{{ $memberRole }}</span>
                            </div>
                        </div>
                    </div>
                </div>
            @endfor
        </div>
    </div>
</div>

@endsection

@push('scripts')

@endpush
