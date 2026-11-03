<?php

namespace App\Rules;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Http\UploadedFile;

/**
 * Accepts only images and videos, judged by the file's content (not its name),
 * each within its own size limit. SVG is refused: it can carry scripts.
 */
class MediaUpload implements ValidationRule
{
    public const IMAGE_MIMES = ['image/jpeg', 'image/png', 'image/gif', 'image/webp'];
    public const VIDEO_MIMES = ['video/mp4', 'video/webm'];

    public const IMAGE_MAX_KB = 10 * 1024;
    public const VIDEO_MAX_KB = 50 * 1024;

    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        if (!$value instanceof UploadedFile || !$value->isValid()) {
            $fail('File gagal diunggah.');
            return;
        }

        $mime = $value->getMimeType();
        $sizeKb = $value->getSize() / 1024;

        if (in_array($mime, self::IMAGE_MIMES, true)) {
            if ($sizeKb > self::IMAGE_MAX_KB) {
                $fail('Gambar maksimal ' . (self::IMAGE_MAX_KB / 1024) . ' MB.');
            }
            return;
        }

        if (in_array($mime, self::VIDEO_MIMES, true)) {
            if ($sizeKb > self::VIDEO_MAX_KB) {
                $fail('Video maksimal ' . (self::VIDEO_MAX_KB / 1024) . ' MB.');
            }
            return;
        }

        $fail('Format file tidak didukung. Gunakan JPG, PNG, GIF, WebP, MP4, atau WebM.');
    }
}
