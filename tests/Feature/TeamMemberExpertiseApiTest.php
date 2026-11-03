<?php

namespace Tests\Feature;

use App\Models\TeamMember;
use App\Models\TeamMemberExpertise;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\Concerns\ListApiContract;
use Tests\TestCase;

class TeamMemberExpertiseApiTest extends TestCase
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
        return '/admin/api/team-member-expertises';
    }

    protected function makeItem(int $sortOrder, array $attributes = []): Model
    {
        return TeamMemberExpertise::create(['team_member_id' => $this->member()->id, 'name' => "Keahlian {$sortOrder}", 'sort_order' => $sortOrder, ...$attributes]);
    }

    protected function validPayload(): array
    {
        return ['team_member_id' => $this->member()->id, 'name' => 'Injeksi', 'is_active' => true];
    }

    protected function requiredField(): array
    {
        return ['name', 'Karburator'];
    }
}
