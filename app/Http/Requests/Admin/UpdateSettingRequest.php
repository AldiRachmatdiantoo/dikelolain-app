<?php

namespace App\Http\Requests\Admin;

use App\Rules\MediaUpload;
use App\Services\SettingService;
use Illuminate\Foundation\Http\FormRequest;

class UpdateSettingRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Rules depend on the setting's type in config/page_settings.php.
     */
    public function rules(): array
    {
        $settings = app(SettingService::class);
        abort_unless($settings->exists($this->route('key')), 404);
        $type = $settings->type($this->route('key'));

        return match ($type) {
            'image' => ['file' => ['required', new MediaUpload(images: true, videos: false)]],
            'video' => [
                'file' => ['required_without:value', 'nullable', new MediaUpload(images: false, videos: true)],
                'value' => ['required_without:file', 'nullable', 'string', 'max:2048', 'regex:#^(https?://|/)#i'],
            ],
            'url' => ['value' => ['nullable', 'string', 'max:2048', 'regex:#^(https?://|/|\#|mailto:|tel:)#i']],
            'email' => ['value' => ['nullable', 'email', 'max:255']],
            'textarea' => ['value' => ['nullable', 'string', 'max:5000']],
            default => ['value' => ['nullable', 'string', 'max:500']],
        };
    }

    public function messages(): array
    {
        return [
            'value.regex' => 'Link harus diawali http(s)://, /, #, mailto: atau tel:.',
            'value.email' => 'Format email tidak valid.',
            'value.max' => 'Teks terlalu panjang.',
            'file.required' => 'Pilih file terlebih dahulu.',
        ];
    }
}
