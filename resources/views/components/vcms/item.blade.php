{{-- One item of a list (FAQ, testimonial, ...): edit, move, delete. Renders the same element for visitors. --}}
@props(['resource', 'item', 'fields', 'label' => 'item', 'tag' => 'div', 'movable' => true])
@php
    $editing = \App\Vcms\EditMode::active();
    $payload = $editing ? [
        'kind' => 'item',
        'title' => 'Edit ' . $label,
        'endpoint' => url('/admin/api/' . $resource . '/' . $item->getKey()),
        'fields' => \App\Vcms\FieldSet::withValues($fields, $item),
    ] : null;
@endphp
<{{ $tag }} {{ $attributes->class(['vcms-item' => $editing]) }} @if ($editing) data-vcms="{{ json_encode($payload) }}" @endif>
    @if ($editing)
        <span class="vcms-toolbar">
            <button type="button" class="vcms-btn" data-vcms-action="edit" title="Edit {{ $label }}">&#9998;</button>
            @if ($movable)
                <button type="button" class="vcms-btn" data-vcms-action="up" title="Naikkan">&#8593;</button>
                <button type="button" class="vcms-btn" data-vcms-action="down" title="Turunkan">&#8595;</button>
            @endif
            <button type="button" class="vcms-btn vcms-btn-danger" data-vcms-action="delete" title="Hapus {{ $label }}">&#10005;</button>
        </span>
    @endif
    {{ $slot }}
</{{ $tag }}>
