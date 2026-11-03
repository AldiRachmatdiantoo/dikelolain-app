<?php

namespace Tests\Feature;

use App\Models\PageListItem;
use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\Concerns\ListApiContract;
use Tests\TestCase;

class PageListItemApiTest extends TestCase
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
        return '/admin/api/page-list-items';
    }

    protected function makeItem(int $sortOrder, array $attributes = []): Model
    {
        return PageListItem::create(['list_key' => 'about', 'text' => "Poin {$sortOrder}", 'sort_order' => $sortOrder, ...$attributes]);
    }

    protected function validPayload(): array
    {
        return ['list_key' => 'about', 'text' => 'Bergaransi', 'is_active' => true];
    }

    protected function requiredField(): array
    {
        return ['text', 'Jujur'];
    }

    public function test_items_are_ordered_per_list_and_the_list_must_be_known(): void
    {
        $this->makeItem(4);
        $this->actingAs(User::factory()->create());

        $this->postJson($this->listUri(), [...$this->validPayload(), 'list_key' => 'mission'])
            ->assertCreated()
            ->assertJsonPath('data.sort_order', 1);

        $this->postJson($this->listUri(), [...$this->validPayload(), 'list_key' => 'footer'])
            ->assertStatus(422)
            ->assertJsonValidationErrors(['list_key' => 'Daftar tidak dikenal.']);
    }

    public function test_icon_is_optional_and_uploaded(): void
    {
        $this->actingAs(User::factory()->create());

        $created = $this->post($this->listUri(), ['list_key' => 'pricing_benefits', 'text' => 'Gratis cek', 'icon' => UploadedFile::fake()->image('ikon.png')], ['Accept' => 'application/json'])
            ->assertCreated()
            ->json('data');
        $this->assertStringStartsWith('storage/uploads/lists/', $created['icon']);

        $this->deleteJson("{$this->listUri()}/{$created['id']}")->assertNoContent();
        Storage::disk('public')->assertMissing(substr($created['icon'], strlen('storage/')));
    }
}
