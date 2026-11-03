@extends('admin.layouts.admin')

@section('title', 'Edit Team Page')
@section('page-title', 'Team Page - VCMS Editor')

@php
    $previewUrl = route('team');
@endphp


@section('content')

@php
// Helper function to get content from database
if (!function_exists('getContent')) {
    function getContent($sections, $sectionKey, $contentKey, $default = '') {
        if (!isset($sections[$sectionKey])) {
            return $default;
        }
        $content = $sections[$sectionKey]->contents->firstWhere('key', $contentKey);
        return $content ? $content->value : $default;
    }
}

// Page Header data
$headerTitle = getContent($sections, 'team-header', 'team-header-1_title', 'The minds behind <span>the magic</span>');
$headerBreadcrumb = getContent($sections, 'team-header', 'team-header-1_breadcrumb', 'Our Team');
@endphp

<!-- Page Header Section -->
<div class="page-header bg-section vcms-editable-section" data-section="team-header" data-item-id="team-header-1">
    <div class="vcms-edit-indicator">
        <button class="vcms-edit-btn" data-edit-type="page-header">
            <i class="fa-solid fa-pen"></i> Edit Header
        </button>
    </div>
    <div class="container">
        <div class="row">
            <div class="col-lg-12">
                <div class="page-header-box vcms-editable-item" data-edit-type="page-header" data-item-id="team-header-1"
                    data-title="{{ $headerTitle }}"
                    data-breadcrumb="{{ $headerBreadcrumb }}">
                    <h1 class="text-anime-style-2" data-cursor="-opaque">{!! $headerTitle !!}</h1>
                    <nav class="wow fadeInUp" data-wow-delay="0.25s">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item"><a href="{{ route('home') }}">home</a></li>
                            <li class="breadcrumb-item active" aria-current="page">{{ $headerBreadcrumb }}</li>
                        </ol>
                    </nav>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="page-team vcms-editable-section" data-section="our-team" data-item-id="team-list-1" style="position: relative;">
    <div class="container">
        <div style="position: absolute; top: 10px; right: 15px; z-index: 100;">
            <button type="button" id="btn-manage-team" class="vcms-edit-btn" data-edit-type="team-list" 
                    style="display: inline-flex; align-items: center; gap: 8px; background: linear-gradient(135deg, #ffcc01 0%, #ffdd33 100%); color: #161616; font-weight: 600; padding: 10px 20px; border-radius: 8px; border: none; cursor: pointer; box-shadow: 0 4px 15px rgba(255, 204, 1, 0.3);">
                <i class="fa-solid fa-users"></i> Manage Team Members
            </button>
        </div>
        <div class="row vcms-editable-item" data-edit-type="team-list" data-item-id="team-list-1" style="position: relative; padding-top: 50px;">
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
                    <p style="color: #888;">No team members yet. Click the <i class="fa-solid fa-users"></i> icon above to add members.</p>
                </div>
            @endif

            @for ($index = 0; $index < $teamCount; $index++)
                @php
                    $memberName = getContent($sections, 'our-team', "{$keyPrefix}_member_{$index}_name", 'Team Member');
                    $memberRole = getContent($sections, 'our-team', "{$keyPrefix}_member_{$index}_role", 'Position');
                    $memberImg = getContent($sections, 'our-team', "{$keyPrefix}_member_{$index}_img", 'images/team-1.jpg');
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
<script>
$(document).ready(function() {
    console.log("Team page script loaded");
    
    // Bind click event specifically for team manage button
    $("#btn-manage-team").on("click", function(e) {
        e.preventDefault();
        e.stopPropagation();
        
        console.log("Manage Team button clicked!");
        
        var section = $(this).closest(".vcms-editable-section");
        console.log("Section found:", section.length, section.data("section"));
        
        if (typeof VCMSAdmin !== 'undefined' && VCMSAdmin.openEditModal) {
            console.log("Calling VCMSAdmin.openEditModal...");
            VCMSAdmin.openEditModal("team-list", section);
        } else {
            console.error("VCMSAdmin not defined or openEditModal not available");
            alert("Error: VCMSAdmin not initialized");
        }
        
        return false;
    });
});
</script>
@endpush
