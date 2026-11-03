<?php

namespace Tests\Feature;

use App\Models\Tool;
use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\Concerns\ListApiContract;
use Tests\TestCase;

class ToolApiTest extends TestCase
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
        return '/admin/api/tools';
    }

    protected function makeItem(int $sortOrder, array $attributes = []): Model
    {
        return Tool::create([
            'name' => "Tool {$sortOrder}",
            'category' => 'Design tool',
            'icon' => 'images/icon-creative-tools-1.svg',
            'sort_order' => $sortOrder,
            ...$attributes,
        ]);
    }

    protected function validPayload(): array
    {
        return [
            'name' => 'Canva',
            'category' => 'Design tool',
            'icon' => UploadedFile::fake()->image('canva.png'),
            'is_active' => '1',
        ];
    }

    protected function requiredField(): array
    {
        return ['name', 'Notion'];
    }

    public function test_new_icon_is_stored_and_replacing_it_deletes_the_old_upload(): void
    {
        $this->actingAs(User::factory()->create());

        $created = $this->post($this->listUri(), $this->validPayload(), ['Accept' => 'application/json'])
            ->assertCreated()
            ->assertJsonPath('data.name', 'Canva')
            ->json('data');
        $this->assertStringStartsWith('storage/uploads/tools/', $created['icon']);

        $this->post("{$this->listUri()}/{$created['id']}", ['_method' => 'PATCH', 'icon' => UploadedFile::fake()->image('b.png')], ['Accept' => 'application/json'])
            ->assertOk();

        Storage::disk('public')->assertMissing(substr($created['icon'], strlen('storage/')));
    }

    public function test_icon_is_required_and_svg_is_rejected(): void
    {
        $this->actingAs(User::factory()->create());

        $this->postJson($this->listUri(), ['name' => 'Canva'])
            ->assertStatus(422)
            ->assertJsonValidationErrors(['icon' => 'Pilih file ikon.']);

        $svg = $this->uploadedFile('icon.svg', '<svg xmlns="http://www.w3.org/2000/svg"><script>alert(1)</script></svg>');
        $this->post($this->listUri(), ['name' => 'Canva', 'icon' => $svg], ['Accept' => 'application/json'])
            ->assertStatus(422)
            ->assertJsonValidationErrors('icon');
    }
}
