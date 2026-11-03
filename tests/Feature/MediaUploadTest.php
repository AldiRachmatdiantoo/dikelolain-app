<?php

namespace Tests\Feature;

use App\Models\Media;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class MediaUploadTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        Storage::fake('public');
        $this->actingAs(User::factory()->create());
    }

    public function test_image_is_stored_with_the_extension_of_its_content(): void
    {
        $image = UploadedFile::fake()->image('foto.png');
        // Same picture, misleading name: the stored file must still end in .png.
        $renamed = new UploadedFile($image->getPathname(), 'foto.php', null, null, true);

        $this->post('/admin/api/content/upload', ['file' => $renamed])
            ->assertOk()
            ->assertJsonPath('success', true);

        $media = Media::sole();
        $this->assertSame('image', $media->type);
        $this->assertStringEndsWith('.png', $media->path);
        Storage::disk('public')->assertExists($media->path);
    }

    /**
     * A real upload whose type is detected from its content. Laravel's fake files
     * report a type from their name, which would hide exactly what is tested here.
     */
    private function uploadedFile(string $name, string $content): UploadedFile
    {
        $path = tempnam(sys_get_temp_dir(), 'upl');
        file_put_contents($path, $content);

        return new UploadedFile($path, $name, null, null, true);
    }

    public function test_php_file_disguised_as_an_image_is_rejected(): void
    {
        $script = $this->uploadedFile('logo.jpg', '<?php echo "pwned";');

        $this->post('/admin/api/content/upload', ['file' => $script])
            ->assertStatus(422);

        $this->assertDatabaseCount('media', 0);
    }

    public function test_svg_is_rejected(): void
    {
        $svg = $this->uploadedFile(
            'logo.svg',
            '<svg xmlns="http://www.w3.org/2000/svg"><script>alert(1)</script></svg>'
        );

        $this->post('/admin/api/content/upload', ['file' => $svg])
            ->assertStatus(422);
    }

    public function test_oversized_image_is_rejected(): void
    {
        $big = UploadedFile::fake()->image('besar.jpg')->size(11 * 1024);

        $this->post('/admin/api/content/upload', ['file' => $big])
            ->assertStatus(422);
    }

    public function test_batch_update_rejects_a_bad_file_with_a_readable_message(): void
    {
        $script = $this->uploadedFile('hero.jpg', '<?php echo "pwned";');

        $this->post('/admin/api/content/batch-update', [
            'section_key' => 'hero',
            'contents' => json_encode([['key' => 'hero-images-1_image_1', 'type' => 'image', 'value' => '']]),
            'files' => ['hero-images-1_image_1' => $script],
        ])
            ->assertStatus(422)
            ->assertJsonPath('message', 'Format file tidak didukung. Gunakan JPG, PNG, GIF, WebP, MP4, atau WebM.');

        $this->assertDatabaseCount('media', 0);
    }

    public function test_batch_update_stores_a_valid_image(): void
    {
        $this->post('/admin/api/content/batch-update', [
            'section_key' => 'hero',
            'contents' => json_encode([['key' => 'hero-images-1_image_1', 'type' => 'image', 'value' => '']]),
            'files' => ['hero-images-1_image_1' => UploadedFile::fake()->image('hero.jpg')],
        ])->assertOk();

        $this->assertStringEndsWith('.jpg', Media::sole()->path);
    }
}
