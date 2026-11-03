{{-- A bullet list from page_list_items (About lists). Needs $lists, $listItemFields and $list (the list key). --}}
<ul>
    @foreach ($lists[$list] as $item)
        <x-vcms.item resource="page-list-items" :item="$item" :fields="$listItemFields" label="poin" tag="li"
            class="{{ $item->is_active ? '' : 'vcms-hidden' }}">{{ $item->text }}</x-vcms.item>
    @endforeach
</ul>
<x-vcms.add resource="page-list-items" :fields="$listItemFields" label="poin" :defaults="['list_key' => $list]" />
