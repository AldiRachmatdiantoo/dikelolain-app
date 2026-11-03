<?php

namespace Tests\Feature;

use App\Models\ClientLogo;
use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\Concerns\ListApiContract;
use Tests\TestCase;

class ClientLogoApiTest extends TestCase
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
        return '/admin/api/client-logos';
    }

    protected function makeItem(int $sortOrder, array $attributes = []): Model
    {
        return ClientLogo::create(['logo' => 'images/client-logo-1.svg', 'sort_order' => $sortOrder, ...$attributes]);
    }

    protected function validPayload(): array
    {
        return ['logo' => UploadedFile::fake()->image('logo.png'), 'name' => 'Toko Maju', 'is_active' => '1'];
    }

    protected function requiredField(): array
    {
        return ['logo', UploadedFile::fake()->image('baru.png')];
    }

    public function test_list_api_update_changes_only_given_fields(): void
    {
        // The contract version patches JSON; a logo is a file, so check the name instead.
        $logo = $this->makeItem(1);

        $this->actingAs(User::factory()->create())
            ->patchJson("{$this->listUri()}/{$logo->id}", ['name' => 'Klien Baru', 'is_active' => '0'])
            ->assertOk()
            ->assertJsonPath('data.name', 'Klien Baru')
            ->assertJsonPath('data.logo', 'images/client-logo-1.svg')
            ->assertJsonPath('data.is_active', false);
    }

    public function test_new_logo_is_stored_and_replacing_it_deletes_the_old_upload(): void
    {
        $this->actingAs(User::factory()->create());

        $created = $this->post($this->listUri(), $this->validPayload(), ['Accept' => 'application/json'])
            ->assertCreated()
            ->json('data');
        $this->assertStringStartsWith('storage/uploads/client-logos/', $created['logo']);

        $this->post("{$this->listUri()}/{$created['id']}", ['_method' => 'PATCH', 'logo' => UploadedFile::fake()->image('b.png')], ['Accept' => 'application/json'])
            ->assertOk();

        Storage::disk('public')->assertMissing(substr($created['logo'], strlen('storage/')));
    }

    public function test_svg_logo_is_rejected(): void
    {
        $svg = $this->uploadedFile('logo.svg', '<svg xmlns="http://www.w3.org/2000/svg"><script>alert(1)</script></svg>');

        $this->actingAs(User::factory()->create())
            ->post($this->listUri(), ['logo' => $svg], ['Accept' => 'application/json'])
            ->assertStatus(422)
            ->assertJsonValidationErrors('logo');
    }
}
