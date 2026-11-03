<?php

namespace Tests\Feature;

use App\Models\Project;
use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use PHPUnit\Framework\Attributes\DataProvider;
use Tests\Concerns\ListApiContract;
use Tests\TestCase;

class ProjectApiTest extends TestCase
{
    use ListApiContract;
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        Storage::fake('public');
    }

    protected function listUri(): string
    {
        return '/admin/api/projects';
    }

    protected function makeItem(int $sortOrder, array $attributes = []): Model
    {
        return Project::create([
            'title' => "Project {$sortOrder}",
            'media_type' => 'video',
            'video_url' => 'https://www.youtube.com/watch?v=dQw4w9WgXcQ',
            'sort_order' => $sortOrder,
            ...$attributes,
        ]);
    }

    protected function validPayload(): array
    {
        return ['title' => 'Video Profil Bengkel', 'media_type' => 'video', 'video_url' => 'https://youtu.be/EszaEO236lw', 'is_active' => true];
    }

    protected function requiredField(): array
    {
        return ['title', 'Judul baru'];
    }

    public static function youtubeLinks(): array
    {
        return [
            'watch' => ['https://www.youtube.com/watch?v=dQw4w9WgXcQ&t=10', 'dQw4w9WgXcQ'],
            'watch, v not first' => ['https://youtube.com/watch?feature=share&v=dQw4w9WgXcQ', 'dQw4w9WgXcQ'],
            'short link' => ['https://youtu.be/dQw4w9WgXcQ?si=abc', 'dQw4w9WgXcQ'],
            'embed' => ['https://www.youtube.com/embed/dQw4w9WgXcQ', 'dQw4w9WgXcQ'],
            'nocookie embed' => ['https://www.youtube-nocookie.com/embed/dQw4w9WgXcQ', 'dQw4w9WgXcQ'],
            'shorts' => ['https://www.youtube.com/shorts/dQw4w9WgXcQ', 'dQw4w9WgXcQ'],
            'mobile' => ['https://m.youtube.com/watch?v=dQw4w9WgXcQ', 'dQw4w9WgXcQ'],
            'vimeo' => ['https://vimeo.com/123456789', null],
            'look-alike host' => ['https://youtube.com.evil.test/watch?v=dQw4w9WgXcQ', null],
            'id too long' => ['https://youtu.be/dQw4w9WgXcQxyz', null],
            'script' => ['javascript:alert(1)//youtu.be/dQw4w9WgXcQ', null],
        ];
    }

    #[DataProvider('youtubeLinks')]
    public function test_youtube_links_are_recognised(string $url, ?string $id): void
    {
        $this->assertSame($id, Project::youtubeId($url));
    }

    public function test_video_link_must_be_youtube_and_is_embedded_without_cookies(): void
    {
        $this->actingAs(User::factory()->create());

        $this->postJson($this->listUri(), [...$this->validPayload(), 'video_url' => 'https://vimeo.com/123456789'])
            ->assertStatus(422)
            ->assertJsonValidationErrors(['video_url' => 'Link video harus link YouTube']);

        $this->postJson($this->listUri(), [...$this->validPayload(), 'video_url' => 'https://www.youtube.com/watch?v=dQw4w9WgXcQ&t=10'])
            ->assertCreated()
            ->assertJsonPath('data.embed_url', 'https://www.youtube-nocookie.com/embed/dQw4w9WgXcQ');
    }

    public function test_each_media_type_needs_its_media(): void
    {
        $this->actingAs(User::factory()->create());

        $this->postJson($this->listUri(), ['title' => 'Video', 'media_type' => 'video'])
            ->assertStatus(422)
            ->assertJsonValidationErrors(['video_url' => 'Isi link video YouTube untuk project video.']);

        $this->postJson($this->listUri(), ['title' => 'Desain', 'media_type' => 'image'])
            ->assertStatus(422)
            ->assertJsonValidationErrors(['image' => 'Pilih gambar untuk project desain.']);

        $this->postJson($this->listUri(), ['title' => 'Lain', 'media_type' => 'audio', 'video_url' => 'https://youtu.be/dQw4w9WgXcQ'])
            ->assertStatus(422)
            ->assertJsonValidationErrors(['media_type' => 'Jenis project tidak dikenal.']);

        $image = $this->post($this->listUri(), ['title' => 'Desain', 'media_type' => 'image', 'image' => UploadedFile::fake()->image('poster.jpg')], ['Accept' => 'application/json'])
            ->assertCreated()
            ->json('data.image');
        $this->assertStringStartsWith('storage/uploads/projects/', $image);
    }

    public function test_patch_checks_the_stored_media(): void
    {
        $this->actingAs(User::factory()->create());
        $project = $this->makeItem(1);

        // Switching to a design needs an image, since the video project has none.
        $this->patchJson("{$this->listUri()}/{$project->id}", ['media_type' => 'image'])
            ->assertStatus(422)
            ->assertJsonValidationErrors(['image' => 'Pilih gambar untuk project desain.']);

        $this->post("{$this->listUri()}/{$project->id}", ['_method' => 'PATCH', 'media_type' => 'image', 'image' => UploadedFile::fake()->image('poster.jpg')], ['Accept' => 'application/json'])
            ->assertOk()
            ->assertJsonPath('data.media_type', 'image');

        // Later edits without a new file keep the stored image; switching back needs a link again.
        $this->patchJson("{$this->listUri()}/{$project->id}", ['title' => 'Poster', 'media_type' => 'image'])->assertOk();
        $this->patchJson("{$this->listUri()}/{$project->id}", ['media_type' => 'video', 'video_url' => null])
            ->assertStatus(422)
            ->assertJsonValidationErrors(['video_url' => 'Isi link video YouTube untuk project video.']);
        $this->patchJson("{$this->listUri()}/{$project->id}", ['media_type' => 'video'])->assertOk();
    }

    public function test_link_is_optional_but_must_be_a_web_address(): void
    {
        $this->actingAs(User::factory()->create());

        foreach (['javascript:alert(1)', 'ftp://example.com/file', '/portfolio/single'] as $link) {
            $this->postJson($this->listUri(), [...$this->validPayload(), 'link' => $link])
                ->assertStatus(422)
                ->assertJsonValidationErrors(['link' => 'Link harus alamat lengkap yang diawali http:// atau https://.']);
        }

        $this->postJson($this->listUri(), [...$this->validPayload(), 'link' => 'https://example.com/karya'])
            ->assertCreated()
            ->assertJsonPath('data.link', 'https://example.com/karya');
    }

    public function test_deleting_a_design_removes_its_image(): void
    {
        $this->actingAs(User::factory()->create());

        $created = $this->post($this->listUri(), ['title' => 'Desain', 'media_type' => 'image', 'image' => UploadedFile::fake()->image('poster.jpg')], ['Accept' => 'application/json'])
            ->json('data');

        $this->deleteJson("{$this->listUri()}/{$created['id']}")->assertNoContent();

        Storage::disk('public')->assertMissing(substr($created['image'], strlen('storage/')));
    }
}
