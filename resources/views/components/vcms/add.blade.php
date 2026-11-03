{{-- "Add" button for a list. Renders nothing for visitors. --}}
@props(['resource', 'fields', 'label' => 'item', 'defaults' => []])
@if (\App\Vcms\EditMode::active())
    <div class="vcms-add" data-vcms="{{ json_encode([
        'kind' => 'create',
        'title' => 'Tambah ' . $label,
        'endpoint' => url('/admin/api/' . $resource),
        'fields' => \App\Vcms\FieldSet::withValues($fields, $defaults),
    ]) }}">
        <button type="button" class="vcms-btn vcms-add-btn" data-vcms-action="create">+ Tambah {{ $label }}</button>
    </div>
@endif
