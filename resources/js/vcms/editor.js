// VCMS editor: generic form for any element carrying a `data-vcms` payload
// (rendered by the <x-vcms.*> Blade components). Knows nothing about FAQs,
// teams or prices: fields come from the payload, saves go to its endpoint,
// and the page reloads after every change.
import Alpine from 'alpinejs';

const SETTINGS_ENDPOINT = '/admin/api/settings/';

function csrfToken() {
    return document.getElementById('vcms-editor').dataset.csrf;
}

async function send(url, method, data = {}) {
    const body = new FormData();
    body.append('_method', method);
    for (const [name, value] of Object.entries(data)) {
        if (value === null || value === undefined) continue;
        body.append(name, typeof value === 'boolean' ? (value ? '1' : '0') : value);
    }

    const response = await fetch(url, {
        method: 'POST',
        headers: { 'X-CSRF-TOKEN': csrfToken(), Accept: 'application/json' },
        body,
    });

    let json = {};
    try {
        json = await response.json();
    } catch {
        // 204 No Content, or an unexpected non-JSON answer.
    }

    return { ok: response.ok, status: response.status, json };
}

function firstErrors(errors = {}) {
    return Object.fromEntries(Object.entries(errors).map(([name, messages]) => [name, messages[0]]));
}

document.addEventListener('alpine:init', () => {
    Alpine.data('vcmsEditor', () => ({
        open: false,
        kind: null,
        title: '',
        endpoint: '',
        fields: [],
        values: {},
        initial: {},
        files: {},
        previews: {},
        errors: {},
        message: '',
        saving: false,

        init() {
            // Capture phase, so template handlers (accordions, sliders, links) never see these clicks.
            document.addEventListener('click', (event) => {
                const button = event.target.closest('[data-vcms-action]');
                if (!button) return;
                event.preventDefault();
                event.stopPropagation();

                const host = button.closest('[data-vcms]');
                this.handle(button.dataset.vcmsAction, JSON.parse(host.dataset.vcms));
            }, true);
        },

        handle(action, payload) {
            if (action === 'edit' || action === 'create') return this.edit(payload);
            if (action === 'delete') return this.remove(payload);
            if (action === 'up' || action === 'down') return this.move(payload, action);
        },

        edit(payload) {
            this.kind = payload.kind;
            this.title = payload.title;
            this.endpoint = payload.endpoint || '';
            this.fields = payload.fields;
            this.values = Object.fromEntries(payload.fields.map((field) => [field.name, field.value ?? '']));
            this.initial = { ...this.values };
            this.files = {};
            this.previews = {};
            this.errors = {};
            this.message = '';
            this.open = true;
        },

        close() {
            if (this.saving) return;
            this.open = false;
        },

        pick(field, event) {
            const file = event.target.files[0];
            if (!file) return;
            this.files[field.name] = file;
            if (field.type === 'image') this.previews[field.name] = URL.createObjectURL(file);
        },

        async save() {
            this.saving = true;
            this.errors = {};
            this.message = '';

            try {
                const saved = this.kind === 'settings' ? await this.saveSettings() : await this.saveItem();
                if (saved) window.location.reload();
            } catch (error) {
                console.error(error);
                this.message = 'Gagal menyimpan, periksa koneksi lalu coba lagi.';
            } finally {
                this.saving = false;
            }
        },

        // One request per changed setting (PATCH /admin/api/settings/{key}).
        async saveSettings() {
            for (const field of this.fields) {
                const file = this.files[field.name];
                const changed = file || this.values[field.name] !== this.initial[field.name];
                if (!changed) continue;

                const data = file ? { file } : { value: this.values[field.name] };
                const result = await send(SETTINGS_ENDPOINT + encodeURIComponent(field.name), 'PATCH', data);
                if (!result.ok) {
                    const errors = result.json.errors || {};
                    this.errors[field.name] = (errors.value || errors.file || [result.json.message || 'Gagal menyimpan.'])[0];
                    return false;
                }
            }
            return true;
        },

        // Whole item: PATCH for an existing one, POST for a new one.
        async saveItem() {
            // Images are only sent as a newly picked file; a video is either a picked file or its URL text.
            const data = { ...this.values };
            for (const field of this.fields) {
                if (field.type === 'image' || this.files[field.name]) delete data[field.name];
            }
            Object.assign(data, this.files);

            const result = await send(this.endpoint, this.kind === 'create' ? 'POST' : 'PATCH', data);
            if (!result.ok) {
                this.errors = firstErrors(result.json.errors);
                this.message = result.status === 422 ? 'Periksa isian yang ditandai.' : (result.json.message || 'Gagal menyimpan.');
                return false;
            }
            return true;
        },

        async remove(payload) {
            if (!window.confirm('Hapus item ini? Tindakan ini tidak bisa dibatalkan.')) return;
            const result = await send(payload.endpoint, 'DELETE');
            if (result.ok) window.location.reload();
            else window.alert(result.json.message || 'Gagal menghapus.');
        },

        async move(payload, direction) {
            const result = await send(payload.endpoint + '/move', 'POST', { direction });
            if (result.ok) window.location.reload();
            else window.alert(result.json.message || 'Gagal memindahkan.');
        },
    }));
});

window.Alpine = Alpine;
Alpine.start();
