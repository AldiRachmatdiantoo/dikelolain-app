<?php

namespace Tests\Feature;

use App\Models\User;
use App\Services\ImageUploadService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Blade;
use Illuminate\Support\Facades\Exceptions;
use Illuminate\Support\Facades\Storage;
use Mockery\MockInterface;
use Tests\TestCase;
use TypeError;

/**
 * Narrower copies of uploaded pictures (ImageUploadService) and <x-img>, which offers them in
 * srcset and gives every picture its width/height (PBI-521).
 */
class ResponsiveImageTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        Storage::fake('public');
    }

    /** Uploads a picture to an image setting and returns its path on the public disk. */
    private function upload(UploadedFile $file, string $key = 'about.image'): string
    {
        $this->actingAs(User::factory()->create());
        $value = $this->patch("/admin/api/settings/{$key}", ['file' => $file])->assertOk()->json('data.value');

        return substr($value, strlen('storage/'));
    }

    private function variant(string $path, int $width): string
    {
        return preg_replace('/\.webp$/', "-{$width}w.webp", $path);
    }

    /** Puts a WebP picture straight on the public disk, like a file uploaded before copies existed. */
    private function putWebp(string $path, int $width, int $height): void
    {
        ob_start();
        imagewebp(imagecreatetruecolor($width, $height));
        Storage::disk('public')->put($path, (string) ob_get_clean());
    }

    public function test_upload_gets_copies_narrower_than_itself(): void
    {
        $path = $this->upload(UploadedFile::fake()->image('foto.jpg', 1000, 600));
        $disk = Storage::disk('public');

        foreach ([480 => 288, 800 => 480] as $width => $height) {
            $disk->assertExists($this->variant($path, $width));
            [$w, $h, $type] = getimagesizefromstring($disk->get($this->variant($path, $width)));
            $this->assertSame([$width, $height, IMAGETYPE_WEBP], [$w, $h, $type]);
        }
        $disk->assertMissing($this->variant($path, 1200));
        $disk->assertMissing($this->variant($path, 1600));
    }

    public function test_narrow_pictures_gifs_and_the_social_preview_get_no_copies(): void
    {
        $this->upload(UploadedFile::fake()->image('ikon.png', 400, 300), 'contact.phone_icon');
        $this->upload(UploadedFile::fake()->image('animasi.gif', 2400, 1200), 'hero.image_1');
        $this->upload(UploadedFile::fake()->image('pratinjau.png', 2400, 1260), 'seo.image');

        $this->assertSame([], preg_grep('/-\d+w\.webp$/', Storage::disk('public')->allFiles()));
    }

    public function test_replacing_a_picture_deletes_its_copies(): void
    {
        $old = $this->upload(UploadedFile::fake()->image('lama.jpg', 1000, 600));
        $this->upload(UploadedFile::fake()->image('baru.jpg', 1000, 600));

        Storage::disk('public')->assertMissing([$old, $this->variant($old, 480), $this->variant($old, 800)]);
    }

    public function test_img_lists_the_copies_with_sizes_and_the_size_of_the_file(): void
    {
        $path = $this->upload(UploadedFile::fake()->image('foto.jpg', 1000, 600));
        $url = asset('storage/' . $path);

        $html = Blade::render('<x-img :src="$src" sizes="50vw" alt="Foto" loading="lazy" />', ['src' => $url]);

        $srcset = asset('storage/' . $this->variant($path, 480)) . ' 480w, ' . asset('storage/' . $this->variant($path, 800)) . " 800w, {$url} 1000w";
        $this->assertSame('<img src="' . $url . '" srcset="' . $srcset . '" sizes="50vw" width="1000" height="600" alt="Foto" loading="lazy">', $html);
    }

    public function test_img_gives_template_pictures_their_size_and_leaves_other_urls_alone(): void
    {
        $this->assertSame(
            '<img src="' . asset('images/logo-dikelolain.webp') . '" width="720" height="180" alt="Logo">',
            Blade::render('<x-img :src="asset(\'images/logo-dikelolain.webp\')" alt="Logo" />'),
        );

        foreach (['https://i.ytimg.com/vi/abc/hqdefault.jpg', asset('storage/uploads/hilang.webp'), asset('images/arrrow-light.svg'), ''] as $url) {
            $this->assertSame('<img src="' . $url . '" alt="">', Blade::render('<x-img :src="$src" alt="" />', ['src' => $url]));
        }
    }

    public function test_uploads_without_copies_get_their_size_only(): void
    {
        $this->putWebp('uploads/legacy/lama.webp', 1300, 650);

        $this->assertSame(
            '<img src="' . asset('storage/uploads/legacy/lama.webp') . '" width="1300" height="650" alt="">',
            Blade::render('<x-img :src="$src" alt="" />', ['src' => asset('storage/uploads/legacy/lama.webp')]),
        );
    }

    public function test_home_hero_offers_the_copies_at_the_size_it_shows_them(): void
    {
        $path = $this->upload(UploadedFile::fake()->image('hero.jpg', 1000, 700), 'hero.image_1');

        $this->get('/')->assertOk()
            ->assertSee(asset('storage/' . $this->variant($path, 480)) . ' 480w', false)
            ->assertSee('sizes="(min-width: 1200px) 246px, (min-width: 992px) 178px, (min-width: 768px) 243px, calc(50vw - 33px)" width="1000" height="700" alt="Hero Image 1"', false);
    }

    public function test_command_makes_the_missing_copies_once(): void
    {
        $this->putWebp('uploads/legacy/besar.webp', 1300, 650);
        $this->putWebp('uploads/legacy/kecil.webp', 300, 150);
        $this->putWebp('media/images/lama.webp', 900, 900);
        $disk = Storage::disk('public');

        Artisan::call('media:variants');
        $output = Artisan::output();
        $this->assertStringContainsString('Gambar diproses: 1, salinan dibuat: 3, sisa: 0.', $output);
        $this->assertStringContainsString('SELESAI', $output);
        $disk->assertExists(['uploads/legacy/besar-480w.webp', 'uploads/legacy/besar-800w.webp', 'uploads/legacy/besar-1200w.webp']);
        $disk->assertMissing(['uploads/legacy/besar-1600w.webp', 'uploads/legacy/kecil-480w.webp', 'media/images/lama-480w.webp']);

        Artisan::call('media:variants');
        $this->assertStringContainsString('Gambar diproses: 0, salinan dibuat: 0, sisa: 0.', Artisan::output());
    }

    public function test_resizing_does_not_use_imagescale(): void
    {
        // imagescale() with IMG_BICUBIC returned false on the live server (9 Nov 2026), which broke every upload.
        $calls = array_filter(
            token_get_all(file_get_contents(app_path('Services/ImageUploadService.php'))),
            fn ($token) => is_array($token) && $token[0] === T_STRING && strtolower($token[1]) === 'imagescale',
        );
        $this->assertSame([], $calls);
    }

    public function test_copies_of_a_transparent_png_stay_transparent(): void
    {
        $png = imagecreatetruecolor(1000, 600);
        imagealphablending($png, false);
        imagesavealpha($png, true);
        imagefill($png, 0, 0, imagecolorallocatealpha($png, 0, 0, 0, 127));
        $file = tempnam(sys_get_temp_dir(), 'png');
        imagepng($png, $file);

        $path = $this->upload(new UploadedFile($file, 'transparan.png', 'image/png', null, true));
        $copy = imagecreatefromstring(Storage::disk('public')->get($this->variant($path, 480)));

        $this->assertSame(127, (imagecolorat($copy, 10, 10) >> 24) & 0x7F);
    }

    public function test_an_upload_still_counts_when_its_copies_fail(): void
    {
        Exceptions::fake();
        $this->partialMock(ImageUploadService::class, function (MockInterface $mock) {
            $mock->shouldReceive('createVariants')->andThrow(new TypeError('imagealphablending(): Argument #1 ($image) must be of type GdImage, bool given'));
        });

        $path = $this->upload(UploadedFile::fake()->image('foto.jpg', 1000, 600));

        Storage::disk('public')->assertExists($path);
        Exceptions::assertReported(TypeError::class);
    }

    public function test_command_reports_a_picture_it_cannot_process_and_goes_on(): void
    {
        Exceptions::fake();
        $this->putWebp('uploads/legacy/besar.webp', 1300, 650);
        $this->putWebp('uploads/legacy/rusak.webp', 1300, 650);
        $this->partialMock(ImageUploadService::class, function (MockInterface $mock) {
            $mock->shouldReceive('createVariants')->with('uploads/legacy/rusak.webp')->andThrow(new TypeError('gambar rusak'));
            $mock->shouldReceive('createVariants')->with('uploads/legacy/besar.webp')->passthru();
        });

        $this->assertSame(1, Artisan::call('media:variants'));
        $output = Artisan::output();

        $this->assertStringContainsString('Gambar diproses: 2, salinan dibuat: 3, sisa: 0.', $output);
        $this->assertStringContainsString('GAGAL: 1 gambar tidak bisa diproses, misalnya uploads/legacy/rusak.webp: gambar rusak', $output);
        $this->assertDoesNotMatchRegularExpression('/^SELESAI:/m', $output);
        Storage::disk('public')->assertExists('uploads/legacy/besar-1200w.webp');
    }
}
