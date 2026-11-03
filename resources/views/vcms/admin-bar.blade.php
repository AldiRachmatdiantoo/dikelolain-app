{{-- Floating bar for a logged-in admin on the public pages, plus the editor in edit mode. --}}
@php $editing = \App\Vcms\EditMode::active(); @endphp
<style>
    .vcms-bar { position: fixed; left: 50%; bottom: 18px; transform: translateX(-50%); z-index: 10000; display: flex; align-items: center; gap: 6px; padding: 6px; background: #161616; border: 1px solid #333; border-radius: 999px; box-shadow: 0 8px 30px rgba(0,0,0,.45); font: 600 13px/1 "Montserrat", sans-serif; }
    .vcms-bar form { margin: 0; }
    .vcms-bar-label { color: #ffcc01; padding: 0 10px; letter-spacing: .5px; }
    .vcms-bar a, .vcms-bar button { display: inline-block; padding: 9px 14px; border-radius: 999px; border: 0; background: #2a2a2a; color: #eee; text-decoration: none; cursor: pointer; font: inherit; }
    .vcms-bar a:hover, .vcms-bar button:hover { background: #3a3a3a; color: #fff; }
    .vcms-bar .vcms-bar-primary { background: #ffcc01; color: #161616; }
    .vcms-bar .vcms-bar-primary:hover { background: #ffdd33; color: #161616; }
</style>
<div class="vcms-bar" role="toolbar" aria-label="Admin">
    <span class="vcms-bar-label">VCMS{{ $editing ? ' · Mode edit' : '' }}</span>
    @if ($editing)
        <form method="POST" action="{{ route('admin.edit-mode.off') }}">
            @csrf
            <input type="hidden" name="to" value="{{ '/' . ltrim(request()->path(), '/') }}">
            <button type="submit" class="vcms-bar-primary">Selesai edit</button>
        </form>
    @else
        <a href="{{ route('admin.edit-mode.on', ['to' => '/' . ltrim(request()->path(), '/')]) }}" class="vcms-bar-primary">Edit halaman ini</a>
    @endif
    <a href="{{ route('admin.dashboard') }}">Dashboard</a>
    <form method="POST" action="{{ route('admin.logout') }}">
        @csrf
        <button type="submit">Keluar</button>
    </form>
</div>

@if ($editing)
    @vite(['resources/css/vcms.css', 'resources/js/vcms/editor.js'])

    <div id="vcms-editor" data-csrf="{{ csrf_token() }}" x-data="vcmsEditor" x-cloak>
        <div class="vcms-modal" x-show="open" x-transition.opacity x-on:keydown.escape.window="close()">
            <div class="vcms-modal-overlay" x-on:click="close()"></div>
            <form class="vcms-modal-content" x-on:submit.prevent="save()">
                <div class="vcms-modal-header">
                    <h3 x-text="title"></h3>
                    <button type="button" class="vcms-modal-close" x-on:click="close()" aria-label="Tutup">&times;</button>
                </div>

                <div class="vcms-modal-body">
                    <p class="vcms-form-error" x-show="message" x-text="message"></p>

                    <template x-for="field in fields" :key="field.name">
                        <div class="vcms-field">
                            <label :for="'vcms-' + field.name" x-text="field.label"></label>

                            <template x-if="['text', 'url', 'email', 'video'].includes(field.type)">
                                <input :id="'vcms-' + field.name" :type="field.type === 'email' ? 'email' : 'text'" x-model="values[field.name]">
                            </template>
                            <template x-if="field.type === 'number'">
                                <input :id="'vcms-' + field.name" type="number" step="any" x-model="values[field.name]">
                            </template>
                            <template x-if="field.type === 'textarea' || field.type === 'rich_text'">
                                <textarea :id="'vcms-' + field.name" rows="4" x-model="values[field.name]"></textarea>
                            </template>
                            <template x-if="field.type === 'select'">
                                <select :id="'vcms-' + field.name" x-model="values[field.name]">
                                    <template x-for="(optionLabel, optionValue) in field.options" :key="optionValue">
                                        <option :value="optionValue" x-text="optionLabel" :selected="optionValue == values[field.name]"></option>
                                    </template>
                                </select>
                            </template>
                            <template x-if="field.type === 'toggle'">
                                <input :id="'vcms-' + field.name" type="checkbox" x-model="values[field.name]">
                            </template>
                            <template x-if="field.type === 'image' || field.type === 'video'">
                                <div class="vcms-file">
                                    <template x-if="field.type === 'image' && (previews[field.name] || field.url)">
                                        <img :src="previews[field.name] || field.url" alt="">
                                    </template>
                                    <input :id="'vcms-file-' + field.name" type="file" :accept="field.type === 'image' ? 'image/jpeg,image/png,image/gif,image/webp' : 'video/mp4,video/webm'" x-on:change="pick(field, $event)">
                                </div>
                            </template>

                            <small x-show="field.type === 'rich_text'">Boleh memakai &lt;span&gt;, &lt;strong&gt;, &lt;em&gt;, &lt;br&gt;.</small>
                            <small x-show="field.type === 'video'">Isi link video, atau pilih file MP4/WebM.</small>
                            <small x-show="field.help" x-text="field.help"></small>
                            <p class="vcms-field-error" x-show="errors[field.name]" x-text="errors[field.name]"></p>
                        </div>
                    </template>
                </div>

                <div class="vcms-modal-footer">
                    <button type="button" class="vcms-btn-cancel" x-on:click="close()">Batal</button>
                    <button type="submit" class="vcms-btn-save" :disabled="saving" x-text="saving ? 'Menyimpan...' : 'Simpan'"></button>
                </div>
            </form>
        </div>
    </div>
@endif
