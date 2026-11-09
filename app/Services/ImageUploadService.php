<?php

namespace App\Services;

use GdImage;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

/**
 * Stores uploaded images/videos on the public disk. Files must already have passed
 * App\Rules\MediaUpload; the extension comes from the file's content.
 *
 * JPG, PNG and WebP pictures are scaled down to MAX_SIDE and saved as WebP, so a
 * 5 MB phone photo is not sent to every visitor as is.
 */
class ImageUploadService
{
    private const PREFIX = 'storage/';

    /** Longest side of a stored picture, in pixels. */
    private const MAX_SIDE = 2000;

    private const WEBP_QUALITY = 82;

    /**
     * @param  bool  $optimize  false keeps the file exactly as uploaded (e.g. the social preview image)
     * @return string value to store, e.g. "storage/uploads/faqs/abc.webp"
     */
    public function store(UploadedFile $file, string $folder, bool $optimize = true): string
    {
        $folder = 'uploads/' . trim($folder, '/');
        $webp = $optimize ? $this->toWebp($file) : null;

        if ($webp === null) {
            return self::PREFIX . $file->storeAs($folder, Str::random(32) . '.' . $file->guessExtension(), 'public');
        }

        $path = $folder . '/' . Str::random(32) . '.webp';
        Storage::disk('public')->put($path, $webp);

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

    /**
     * The picture as WebP, or null to keep the upload as it is: GIFs (they may be animated),
     * videos, pictures too large to decode within the memory limit, and pictures that are
     * already small and would not get smaller.
     */
    private function toWebp(UploadedFile $file): ?string
    {
        return $this->webpFromFile($file->getRealPath(), (string) $file->getMimeType(), (int) $file->getSize());
    }

    /**
     * Converts a picture that is already on the public disk (an upload of the old site) exactly
     * like a new upload, and stores it as storage/uploads/{folder}/{same name}.webp.
     *
     * @param  string  $value  stored value, e.g. "storage/media/images/photo.jpg"
     * @return string|null the new value, or null to keep the old one (missing file, GIF, video,
     *                     or a picture that would not get smaller)
     */
    public function convertStored(string $value, string $folder): ?string
    {
        if (!str_starts_with($value, self::PREFIX)) {
            return null;
        }

        $disk = Storage::disk('public');
        $source = substr($value, strlen(self::PREFIX));
        if (!$disk->exists($source)) {
            return null;
        }

        $path = $disk->path($source);
        $webp = $this->webpFromFile($path, (string) mime_content_type($path), (int) filesize($path));
        if ($webp === null) {
            return null;
        }

        $target = 'uploads/' . trim($folder, '/') . '/' . pathinfo($source, PATHINFO_FILENAME) . '.webp';
        $disk->put($target, $webp);

        return self::PREFIX . $target;
    }

    private function webpFromFile(string $path, string $type, int $bytes): ?string
    {
        $size = @getimagesize($path);

        if (!in_array($type, ['image/jpeg', 'image/png', 'image/webp'], true) || !$size || !$this->fitsInMemory($size[0], $size[1])) {
            return null;
        }

        $image = @imagecreatefromstring((string) file_get_contents($path));
        if ($image === false) {
            return null;
        }

        imagepalettetotruecolor($image);
        $scaled = $this->scaleDown($image);
        $rotated = $type === 'image/jpeg' && $this->turnUpright($image, $path);

        imagealphablending($image, false);
        imagesavealpha($image, true);

        ob_start();
        $written = imagewebp($image, null, self::WEBP_QUALITY);
        $webp = (string) ob_get_clean();

        if (!$written || $webp === '' || (!$scaled && !$rotated && strlen($webp) >= $bytes)) {
            return null;
        }

        return $webp;
    }

    private function scaleDown(GdImage &$image): bool
    {
        $width = imagesx($image);
        $height = imagesy($image);
        $ratio = self::MAX_SIDE / max($width, $height);

        if ($ratio >= 1) {
            return false;
        }

        imagealphablending($image, false);
        $image = imagescale($image, max(1, (int) round($width * $ratio)), max(1, (int) round($height * $ratio)), IMG_BICUBIC);

        return true;
    }

    /**
     * Phone photos are often stored sideways with an EXIF "Orientation" tag. GD ignores the
     * tag and WebP output drops it, so the pixels themselves are turned instead.
     */
    private function turnUpright(GdImage &$image, string $path): bool
    {
        $exif = function_exists('exif_read_data') ? @exif_read_data($path) : false;
        $orientation = (int) ($exif['Orientation'] ?? 1);

        if ($orientation < 2 || $orientation > 8) {
            return false;
        }

        $angle = [3 => 180, 4 => 180, 5 => -90, 6 => -90, 7 => 90, 8 => 90][$orientation] ?? 0;
        if ($angle !== 0) {
            $image = imagerotate($image, $angle, 0);
        }
        if (in_array($orientation, [2, 4, 5, 7], true)) {
            imageflip($image, IMG_FLIP_HORIZONTAL);
        }

        return true;
    }

    /**
     * Decoding takes about 4 bytes per pixel, plus the scaled copy. Running out of memory is a
     * fatal error, so pictures that might not fit are stored as uploaded.
     */
    private function fitsInMemory(int $width, int $height): bool
    {
        $limit = (string) ini_get('memory_limit');
        if ($limit === '' || $limit === '-1') {
            return true;
        }

        $bytes = (int) $limit * match (strtolower(substr($limit, -1))) {
            'g' => 1024 ** 3,
            'm' => 1024 ** 2,
            'k' => 1024,
            default => 1,
        };

        return memory_get_usage() + $width * $height * 5 + 32 * 1024 ** 2 < $bytes;
    }
}
