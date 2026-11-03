<?php

namespace App\Services;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

/**
 * Stores uploaded images/videos on the public disk. Files must already have passed
 * App\Rules\MediaUpload; the extension comes from the file's content.
 */
class ImageUploadService
{
    private const PREFIX = 'storage/';

    /**
     * @return string value to store, e.g. "storage/uploads/faqs/abc.webp"
     */
    public function store(UploadedFile $file, string $folder): string
    {
        $name = Str::random(32) . '.' . $file->guessExtension();
        $path = $file->storeAs('uploads/' . trim($folder, '/'), $name, 'public');

        return self::PREFIX . $path;
    }

    /**
     * Deletes a previously uploaded file. Template assets (images/...) and URLs are left alone.
     */
    public function delete(?string $value): void
    {
        if (blank($value) || !str_starts_with($value, self::PREFIX . 'uploads/')) {
            return;
        }

        Storage::disk('public')->delete(substr($value, strlen(self::PREFIX)));
    }
}
