<?php

namespace App\Http\Requests\Admin;

use App\Models\Project;
use App\Rules\MediaUpload;
use Closure;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Validator;

class ProjectRequest extends ListItemRequest
{
    protected function fields(): array
    {
        return [
            'title' => ['required', 'string', 'max:150'],
            'description' => ['nullable', 'string', 'max:2000'],
            'media_type' => ['required', Rule::in(array_keys(Project::MEDIA_TYPES))],
            'video_url' => ['nullable', 'required_if:media_type,video', 'string', 'max:255', $this->youtubeLink(...)],
            'image' => ['nullable', 'required_if:media_type,image', new MediaUpload(images: true, videos: false)],
            'link' => ['nullable', 'url:http,https', 'max:255'],
            'is_active' => ['sometimes', 'boolean'],
        ];
    }

    /**
     * On PATCH the editor sends an image only when a new one was picked, so the
     * stored values decide whether a video link or an image is still missing.
     */
    public function after(): array
    {
        if (!$this->isMethod('PATCH')) {
            return [];
        }

        return [function (Validator $validator) {
            $project = Project::find($this->route('id'));
            if (!$project) {
                return;
            }

            $type = $this->input('media_type', $project->media_type);
            $videoUrl = $this->exists('video_url') ? $this->input('video_url') : $project->video_url;

            if ($type === 'video' && blank($videoUrl) && !$validator->errors()->has('video_url')) {
                $validator->errors()->add('video_url', $this->messages()['video_url.required_if']);
            }
            if ($type === 'image' && !$this->hasFile('image') && blank($project->image) && !$validator->errors()->has('image')) {
                $validator->errors()->add('image', $this->messages()['image.required_if']);
            }
        }];
    }

    public function messages(): array
    {
        return [
            'title.required' => 'Judul project wajib diisi.',
            'title.max' => 'Judul maksimal 150 karakter.',
            'description.max' => 'Deskripsi terlalu panjang.',
            'media_type.required' => 'Pilih jenis project.',
            'media_type.in' => 'Jenis project tidak dikenal.',
            'video_url.required_if' => 'Isi link video YouTube untuk project video.',
            'video_url.max' => 'Link maksimal 255 karakter.',
            'image.required_if' => 'Pilih gambar untuk project desain.',
            'link.url' => 'Link harus alamat lengkap yang diawali http:// atau https://.',
            'link.max' => 'Link maksimal 255 karakter.',
        ];
    }

    private function youtubeLink(string $attribute, mixed $value, Closure $fail): void
    {
        if (Project::youtubeId($value) === null) {
            $fail('Link video harus link YouTube, misalnya https://www.youtube.com/watch?v=...');
        }
    }
}
