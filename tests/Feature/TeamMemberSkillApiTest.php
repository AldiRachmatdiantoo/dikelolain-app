<?php

namespace Tests\Feature;

use App\Models\TeamMember;
use App\Models\TeamMemberSkill;
use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\Concerns\ListApiContract;
use Tests\TestCase;

class TeamMemberSkillApiTest extends TestCase
{
    use ListApiContract;
    use RefreshDatabase;

    private ?TeamMember $member = null;

    private function member(): TeamMember
    {
        return $this->member ??= TeamMember::create(['slug' => 'budi', 'name' => 'Budi', 'role' => 'Mekanik', 'photo' => 'images/team-1.jpg']);
    }

    protected function listUri(): string
    {
        return '/admin/api/team-member-skills';
    }

    protected function makeItem(int $sortOrder, array $attributes = []): Model
    {
        return TeamMemberSkill::create(['team_member_id' => $this->member()->id, 'name' => "Skill {$sortOrder}", 'percent' => 80, 'sort_order' => $sortOrder, ...$attributes]);
    }

    protected function validPayload(): array
    {
        return ['team_member_id' => $this->member()->id, 'name' => 'Servis mesin', 'percent' => 90, 'is_active' => true];
    }

    protected function requiredField(): array
    {
        return ['name', 'Kelistrikan'];
    }

    public function test_skills_are_ordered_per_member(): void
    {
        $this->makeItem(3);
        $other = TeamMember::create(['slug' => 'sari', 'name' => 'Sari', 'role' => 'Admin', 'photo' => 'images/team-2.jpg']);

        $this->actingAs(User::factory()->create())
            ->postJson($this->listUri(), [...$this->validPayload(), 'team_member_id' => $other->id])
            ->assertCreated()
            ->assertJsonPath('data.sort_order', 1);
    }

    public function test_percent_and_member_are_validated(): void
    {
        $this->actingAs(User::factory()->create())
            ->postJson($this->listUri(), [...$this->validPayload(), 'percent' => 120, 'team_member_id' => 999])
            ->assertStatus(422)
            ->assertJsonValidationErrors(['percent' => 'Persentase antara 0 dan 100.', 'team_member_id' => 'Anggota tim tidak ditemukan.']);
    }
}
