@extends('layouts.app')

@section('title', 'Our Team - Dikelolain')

@section('content')

    <!-- Page Header -->
    <x-vcms.settings :keys="['team.header.title', 'team.header.breadcrumb']" label="Header">
        <x-page-header :title="$settings->get('team.header.title')">
            <li class="breadcrumb-item active" aria-current="page">{{ $settings->get('team.header.breadcrumb') }}</li>
        </x-page-header>
    </x-vcms.settings>

<div class="page-team">
    <div class="container">
        <div class="row">
            @forelse ($teamMembers as $member)
                @include('partials.team-card')
            @empty
                <div class="col-12 text-center py-5">
                    <p class="text-dim">No team members yet.</p>
                </div>
            @endforelse
        </div>

        <x-vcms.add resource="team-members" :fields="$teamMemberFields" label="anggota tim" />
    </div>
</div>

@endsection
