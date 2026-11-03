<?php

namespace Tests\Feature;

use App\Models\TeamMember;
use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\Concerns\ListApiContract;
use Tests\TestCase;

class TeamMemberApiTest extends TestCase
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
        return '/admin/api/team-members';
    }

    protected function makeItem(int $sortOrder, array $attributes = []): Model
    {
        return TeamMember::create([
            'slug' => "anggota-{$sortOrder}",
            'name' => "Anggota {$sortOrder}",
            'role' => 'Mekanik',
            'photo' => 'images/team-1.jpg',
            'sort_order' => $sortOrder,
            ...$attributes,
        ]);
    }

    protected function validPayload(): array
    {
        return ['name' => 'Budi Mekanik', 'role' => 'Kepala Bengkel', 'photo' => UploadedFile::fake()->image('budi.jpg'), 'is_active' => '1'];
    }

    protected function requiredField(): array
    {
        return ['role', 'Teknisi'];
    }

    public function test_slug_is_made_from_the_name_and_kept_unique(): void
    {
        $this->actingAs(User::factory()->create());
        $this->makeItem(1, ['slug' => 'budi-mekanik']);

        $this->post($this->listUri(), $this->validPayload(), ['Accept' => 'application/json'])
            ->assertCreated()
            ->assertJsonPath('data.slug', 'budi-mekanik-2');

        $this->post($this->listUri(), [...$this->validPayload(), 'name' => '!!!'], ['Accept' => 'application/json'])
            ->assertCreated()
            ->assertJsonPath('data.slug', 'anggota');
    }

    public function test_renaming_keeps_the_slug(): void
    {
        $member = $this->makeItem(1);

        $this->actingAs(User::factory()->create())
            ->patchJson("{$this->listUri()}/{$member->id}", ['name' => 'Nama Baru', 'slug' => 'diabaikan'])
            ->assertOk()
            ->assertJsonPath('data.name', 'Nama Baru')
            ->assertJsonPath('data.slug', 'anggota-1');
    }

    public function test_photo_is_stored_and_deleting_the_member_removes_photo_skills_and_expertises(): void
    {
        $this->actingAs(User::factory()->create());

        $created = $this->post($this->listUri(), $this->validPayload(), ['Accept' => 'application/json'])
            ->assertCreated()
            ->json('data');
        $this->assertStringStartsWith('storage/uploads/team/', $created['photo']);

        $member = TeamMember::find($created['id']);
        $member->skills()->create(['name' => 'Servis mesin', 'percent' => 90]);
        $member->expertises()->create(['name' => 'Injeksi']);

        $this->deleteJson("{$this->listUri()}/{$member->id}")->assertNoContent();

        Storage::disk('public')->assertMissing(substr($created['photo'], strlen('storage/')));
        $this->assertDatabaseCount('team_member_skills', 0);
        $this->assertDatabaseCount('team_member_expertises', 0);
    }
}
