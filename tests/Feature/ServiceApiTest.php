<?php

namespace Tests\Feature;

use App\Models\Service;
use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\Concerns\ListApiContract;
use Tests\TestCase;

class ServiceApiTest extends TestCase
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
        return '/admin/api/services';
    }

    protected function makeItem(int $sortOrder, array $attributes = []): Model
    {
        return Service::create([
            'slug' => "layanan-{$sortOrder}",
            'name' => "Layanan {$sortOrder}",
            'summary' => 'Ringkasan.',
            'image' => 'images/service-image-1.jpg',
            'sort_order' => $sortOrder,
            ...$attributes,
        ]);
    }

    protected function validPayload(): array
    {
        return ['name' => 'Servis Berkala', 'summary' => 'Ganti oli dan cek rem.', 'image' => UploadedFile::fake()->image('servis.jpg'), 'is_active' => '1'];
    }

    protected function requiredField(): array
    {
        return ['summary', 'Ringkasan baru.'];
    }

    public function test_slug_is_unique_and_kept_on_rename(): void
    {
        $this->actingAs(User::factory()->create());
        $existing = $this->makeItem(1, ['slug' => 'servis-berkala']);

        $this->post($this->listUri(), $this->validPayload(), ['Accept' => 'application/json'])
            ->assertCreated()
            ->assertJsonPath('data.slug', 'servis-berkala-2');

        $this->patchJson("{$this->listUri()}/{$existing->id}", ['name' => 'Nama Lain'])
            ->assertOk()
            ->assertJsonPath('data.slug', 'servis-berkala');
    }

    public function test_images_are_stored_and_deleting_removes_files_benefits_and_faqs(): void
    {
        $this->actingAs(User::factory()->create());

        $created = $this->post($this->listUri(), [...$this->validPayload(), 'feature_image' => UploadedFile::fake()->image('besar.jpg')], ['Accept' => 'application/json'])
            ->assertCreated()
            ->assertJsonPath('data.icon_url', asset(Service::DEFAULT_ICON))
            ->json('data');
        $this->assertStringStartsWith('storage/uploads/services/', $created['image']);
        $this->assertStringStartsWith('storage/uploads/services/', $created['feature_image']);

        $service = Service::find($created['id']);
        $service->benefits()->create(['text' => 'Cepat']);
        $service->faqs()->create(['question' => 'Berapa lama?', 'answer' => 'Satu jam.']);

        $this->deleteJson("{$this->listUri()}/{$service->id}")->assertNoContent();

        Storage::disk('public')->assertMissing(substr($created['image'], strlen('storage/')));
        Storage::disk('public')->assertMissing(substr($created['feature_image'], strlen('storage/')));
        $this->assertDatabaseCount('service_benefits', 0);
        $this->assertDatabaseCount('service_faqs', 0);
    }
}
