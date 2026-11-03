<?php

namespace Tests\Feature;

use App\Models\Testimonial;
use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\Concerns\ListApiContract;
use Tests\TestCase;

class TestimonialApiTest extends TestCase
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
        return '/admin/api/testimonials';
    }

    protected function makeItem(int $sortOrder, array $attributes = []): Model
    {
        return Testimonial::create([
            'name' => "Klien {$sortOrder}",
            'role' => 'CEO',
            'content' => 'Sangat puas.',
            'rating' => 5,
            'photo' => 'images/author-1.jpg',
            'sort_order' => $sortOrder,
            ...$attributes,
        ]);
    }

    protected function validPayload(): array
    {
        return ['name' => 'Budi', 'role' => 'Pemilik', 'content' => 'Mantap.', 'rating' => '4', 'is_active' => '1'];
    }

    protected function requiredField(): array
    {
        return ['name', 'Nama Baru'];
    }

    private function storedPath(string $value): string
    {
        return substr($value, strlen('storage/'));
    }

    public function test_rating_must_be_one_to_five(): void
    {
        $this->actingAs(User::factory()->create())
            ->postJson($this->listUri(), [...$this->validPayload(), 'rating' => '6'])
            ->assertStatus(422)
            ->assertJsonValidationErrors('rating');
    }

    public function test_photo_upload_replaces_the_previous_upload(): void
    {
        $this->actingAs(User::factory()->create());

        $first = $this->post($this->listUri(), [...$this->validPayload(), 'photo' => UploadedFile::fake()->image('a.jpg')])
            ->assertCreated()
            ->json('data');
        $this->assertStringStartsWith('storage/uploads/testimonials/', $first['photo']);
        $this->assertSame(asset($first['photo']), $first['photo_url']);
        Storage::disk('public')->assertExists($this->storedPath($first['photo']));

        $second = $this->post("{$this->listUri()}/{$first['id']}", ['_method' => 'PATCH', 'photo' => UploadedFile::fake()->image('b.png')])
            ->assertOk()
            ->json('data.photo');

        Storage::disk('public')->assertExists($this->storedPath($second));
        Storage::disk('public')->assertMissing($this->storedPath($first['photo']));
    }

    public function test_deleting_removes_uploaded_files_only(): void
    {
        $this->actingAs(User::factory()->create());
        $id = $this->post($this->listUri(), [...$this->validPayload(), 'logo' => UploadedFile::fake()->image('logo.png')])
            ->json('data.id');
        $logo = Testimonial::find($id)->logo;
        $template = $this->makeItem(9); // photo points at images/author-1.jpg

        $this->deleteJson("{$this->listUri()}/{$id}")->assertNoContent();
        $this->deleteJson("{$this->listUri()}/{$template->id}")->assertNoContent();

        Storage::disk('public')->assertMissing($this->storedPath($logo));
        $this->assertFileExists(public_path('images/author-1.jpg'));
    }

    public function test_script_disguised_as_photo_is_rejected(): void
    {
        $this->actingAs(User::factory()->create())
            ->post($this->listUri(), [...$this->validPayload(), 'photo' => $this->uploadedFile('foto.jpg', '<?php echo 1;')], ['Accept' => 'application/json'])
            ->assertStatus(422)
            ->assertJsonValidationErrors('photo');

        $this->assertDatabaseCount('testimonials', 0);
    }
}
