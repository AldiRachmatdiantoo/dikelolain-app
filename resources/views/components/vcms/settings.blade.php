{{-- A group of page settings with one edit button. Renders the same element for visitors, without the editor bits. --}}
@props(['keys', 'label' => 'Edit', 'tag' => 'div'])
@php
    $editing = \App\Vcms\EditMode::active();
    $payload = $editing ? [
        'kind' => 'settings',
        'title' => $label,
        'fields' => app(\App\Services\SettingService::class)->fields($keys),
    ] : null;
@endphp
<{{ $tag }} {{ $attributes->class(['vcms-zone' => $editing]) }} @if ($editing) data-vcms="{{ json_encode($payload) }}" @endif>
    @if ($editing)
        <button type="button" class="vcms-btn vcms-zone-btn" data-vcms-action="edit">&#9998; {{ $label }}</button>
    @endif
    {{ $slot }}
</{{ $tag }}>
