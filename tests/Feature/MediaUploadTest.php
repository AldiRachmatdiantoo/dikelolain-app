<?php

namespace Tests\Feature;

use App\Models\PageSetting;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

/**
 * The MediaUpload rule on a real upload endpoint (an image setting; the list APIs use the same rule).
 */
class MediaUploadTest extends TestCase
{
    use RefreshDatabase;

    private const URL = '/admin/api/settings/footer.logo';

    protected function setUp(): void
    {
        parent::setUp();
        Storage::fake('public');
        $this->actingAs(User::factory()->create());
    }

    public function test_image_is_stored_with_the_extension_of_its_content(): void
    {
        // A GIF is stored as uploaded (JPG/PNG become WebP, see ImageUploadTest).
        $image = UploadedFile::fake()->image('foto.gif');
        // Same picture, misleading name: the stored file must still end in .gif.
        $renamed = new UploadedFile($image->getPathname(), 'foto.php', null, null, true);

        $value = $this->patch(self::URL, ['file' => $renamed])
            ->assertOk()
            ->json('data.value');

        $this->assertStringEndsWith('.gif', $value);
        Storage::disk('public')->assertExists(substr($value, strlen('storage/')));
    }

    public function test_php_file_disguised_as_an_image_is_rejected_with_a_readable_message(): void
    {
        $script = $this->uploadedFile('logo.jpg', '<?php echo "pwned";');

        $this->patchJson(self::URL, ['file' => $script])
            ->assertStatus(422)
            ->assertJsonPath('errors.file.0', 'Format gambar tidak didukung. Gunakan JPG, PNG, GIF, atau WebP.');

        $this->assertDatabaseCount('page_settings', 0);
    }

    public function test_svg_is_rejected(): void
    {
        $svg = $this->uploadedFile(
            'logo.svg',
            '<svg xmlns="http://www.w3.org/2000/svg"><script>alert(1)</script></svg>'
        );

        $this->patchJson(self::URL, ['file' => $svg])->assertStatus(422);

        $this->assertDatabaseCount('page_settings', 0);
    }

    public function test_oversized_image_is_rejected(): void
    {
        $big = UploadedFile::fake()->image('besar.jpg')->size(11 * 1024);

        $this->patchJson(self::URL, ['file' => $big])
            ->assertStatus(422)
            ->assertJsonPath('errors.file.0', 'Gambar maksimal 10 MB.');
    }

    public function test_video_setting_refuses_an_image(): void
    {
        $this->patchJson('/admin/api/settings/hero.video', ['file' => UploadedFile::fake()->image('bukan-video.jpg')])
            ->assertStatus(422)
            ->assertJsonPath('errors.file.0', 'Format video tidak didukung. Gunakan MP4 atau WebM.');

        $this->assertNull(PageSetting::where('key', 'hero.video')->value('value'));
    }
}
