<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

/**
 * Uploaded pictures are scaled down and saved as WebP (ImageUploadService).
 */
class ImageUploadTest extends TestCase
{
    use RefreshDatabase;

    private const URL = '/admin/api/settings/about.image';

    protected function setUp(): void
    {
        parent::setUp();
        Storage::fake('public');
        $this->actingAs(User::factory()->create());
    }

    /**
     * Uploads the file to an image setting and returns the stored file's path on the public disk.
     */
    private function upload(UploadedFile $file, string $url = self::URL): string
    {
        $value = $this->patch($url, ['file' => $file])->assertOk()->json('data.value');
        $path = substr($value, strlen('storage/'));
        Storage::disk('public')->assertExists($path);

        return $path;
    }

    private function file(string $name, string $content): UploadedFile
    {
        $path = tempnam(sys_get_temp_dir(), 'img');
        file_put_contents($path, $content);

        return new UploadedFile($path, $name, null, null, true);
    }

    private function encode(\GdImage $image, string $format): string
    {
        ob_start();
        $format === 'png' ? imagepng($image) : imagejpeg($image, null, 95);

        return ob_get_clean();
    }

    public function test_large_photo_is_scaled_down_and_saved_as_webp(): void
    {
        $path = $this->upload(UploadedFile::fake()->image('foto.jpg', 3000, 1500));

        $this->assertStringEndsWith('.webp', $path);
        [$width, $height, $type] = getimagesizefromstring(Storage::disk('public')->get($path));
        $this->assertSame([2000, 1000, IMAGETYPE_WEBP], [$width, $height, $type]);
    }

    public function test_transparent_png_keeps_its_transparency(): void
    {
        $image = imagecreatetruecolor(2400, 600);
        imagealphablending($image, false);
        imagesavealpha($image, true);
        imagefill($image, 0, 0, imagecolorallocatealpha($image, 0, 0, 0, 127));
        imagefilledrectangle($image, 1000, 200, 1400, 400, imagecolorallocate($image, 255, 200, 0));

        $path = $this->upload($this->file('logo.png', $this->encode($image, 'png')));

        $this->assertStringEndsWith('.webp', $path);
        $stored = imagecreatefromstring(Storage::disk('public')->get($path));
        $this->assertSame(2000, imagesx($stored));
        $this->assertSame(127, (imagecolorat($stored, 5, 5) >> 24) & 0x7F, 'corner stays transparent');
        $this->assertSame(0, (imagecolorat($stored, 1000, 250) >> 24) & 0x7F, 'logo stays opaque');
    }

    public function test_phone_photo_is_turned_upright(): void
    {
        // A landscape JPEG whose EXIF tag says "rotate 90° clockwise to view" (Orientation 6).
        $jpeg = $this->encode(imagecreatetruecolor(300, 100), 'jpg');
        $tiff = "MM\x00\x2A\x00\x00\x00\x08" . "\x00\x01" . "\x01\x12\x00\x03\x00\x00\x00\x01\x00\x06\x00\x00" . "\x00\x00\x00\x00";
        $app1 = "Exif\x00\x00" . $tiff;
        $jpeg = substr($jpeg, 0, 2) . "\xFF\xE1" . pack('n', strlen($app1) + 2) . $app1 . substr($jpeg, 2);

        $path = $this->upload($this->file('hp.jpg', $jpeg));

        $this->assertStringEndsWith('.webp', $path);
        [$width, $height] = getimagesizefromstring(Storage::disk('public')->get($path));
        $this->assertSame([100, 300], [$width, $height]);
    }

    public function test_gif_is_kept_as_uploaded(): void
    {
        $gif = UploadedFile::fake()->image('animasi.gif', 2400, 1200);

        $path = $this->upload($gif);

        $this->assertStringEndsWith('.gif', $path);
        $this->assertSame(file_get_contents($gif->getPathname()), Storage::disk('public')->get($path));
    }

    public function test_social_preview_image_keeps_its_format(): void
    {
        $png = UploadedFile::fake()->image('pratinjau.png', 2400, 1260);

        $path = $this->upload($png, '/admin/api/settings/seo.image');

        $this->assertStringEndsWith('.png', $path);
        $this->assertSame(file_get_contents($png->getPathname()), Storage::disk('public')->get($path));
    }

    public function test_list_uploads_are_optimized_too(): void
    {
        $value = $this->post('/admin/api/client-logos', [
            'logo' => UploadedFile::fake()->image('klien.png', 2600, 800),
        ], ['Accept' => 'application/json'])->assertCreated()->json('data.logo');

        $this->assertStringEndsWith('.webp', $value);
    }
}
