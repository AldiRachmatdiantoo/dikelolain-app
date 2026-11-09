<?php

namespace App\View\Components;

use App\Services\ImageUploadService;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\Component;

/**
 * An <img> for a picture of this site, with what the browser needs to load it well:
 *  - width/height of the file, so its space is kept before it arrives and nothing below jumps;
 *  - for uploads that have narrower copies (ImageUploadService::createVariants), srcset + sizes,
 *    so a phone downloads a picture about as wide as the box it fills instead of the full file.
 * Other URLs (YouTube thumbnails, missing files) are written out as a plain <img>.
 *
 *   <x-img :src="$service->image_url" sizes="(min-width: 992px) 533px, calc(100vw - 45px)" alt="..." loading="lazy" />
 *
 * sizes says how wide the picture is shown at each screen width (measured from the CSS, see
 * PBI-521); without it the browser assumes the full screen width.
 */
class Img extends Component
{
    public ?int $width = null;

    public ?int $height = null;

    public ?string $srcset = null;

    public function __construct(public ?string $src = '', public string $sizes = '100vw')
    {
        $this->src = (string) $src;
        [$file, $upload] = $this->locate($this->src);

        $size = $file !== null ? @getimagesize($file) : false;
        if (!$size) {
            return;
        }
        [$this->width, $this->height] = [$size[0], $size[1]];

        if ($upload === null) {
            return;
        }

        $candidates = [];
        foreach (ImageUploadService::VARIANT_WIDTHS as $width) {
            $variant = ImageUploadService::variantPath($upload, $width);
            if ($width < $this->width && Storage::disk('public')->exists($variant)) {
                $candidates[] = asset('storage/' . $variant) . " {$width}w";
            }
        }

        if ($candidates !== []) {
            $candidates[] = "{$this->src} {$this->width}w";
            $this->srcset = implode(', ', $candidates);
        }
    }

    public function render(): View
    {
        return view('components.img');
    }

    /**
     * The file behind a URL of this site, and its path on the public disk when it is an upload.
     *
     * @return array{0: ?string, 1: ?string}
     */
    private function locate(string $url): array
    {
        $base = rtrim(asset(''), '/') . '/';
        if (!str_starts_with($url, $base)) {
            return [null, null];
        }

        $relative = rawurldecode((string) strtok(substr($url, strlen($base)), '?#'));
        if ($relative === '' || str_contains($relative, '..')) {
            return [null, null];
        }

        if (str_starts_with($relative, 'storage/')) {
            $upload = substr($relative, strlen('storage/'));

            return Storage::disk('public')->exists($upload) ? [Storage::disk('public')->path($upload), $upload] : [null, null];
        }

        return is_file(public_path($relative)) ? [public_path($relative), null] : [null, null];
    }
}
