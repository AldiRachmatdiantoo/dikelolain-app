<?php

namespace Tests\Feature;

use App\Models\PageSetting;
use App\Models\TeamMember;
use App\Models\User;
use App\Vcms\EditMode;
use Database\Seeders\TeamMemberSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\DataProvider;
use Tests\TestCase;

/**
 * Team section (Home, About), the Team page and the member detail page.
 */
class TeamPagesTest extends TestCase
{
    use RefreshDatabase;

    private function member(int $sortOrder, array $attributes = []): TeamMember
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

    private function editor(): static
    {
        return $this->actingAs(User::factory()->create())->withSession([EditMode::SESSION_KEY => true]);
    }

    public static function teaserPages(): array
    {
        return ['home' => ['/'], 'about' => ['/about']];
    }

    #[DataProvider('teaserPages')]
    public function test_section_shows_the_first_four_active_members(string $page): void
    {
        $this->seed(TeamMemberSeeder::class);
        TeamMember::where('slug', 'nadia-putri')->update(['is_active' => false]);
        PageSetting::create(['key' => 'team.title', 'value' => 'Tim <span>Bengkel</span>']);

        $html = $this->get($page)
            ->assertOk()
            ->assertSee('Tim <span>Bengkel</span>', false)
            ->assertSeeInOrder(['Rizky Ramadhan', 'Fajar Nugroho', 'Intan Permata', 'Yoga Saputra'])
            ->assertDontSee('Nadia Putri')
            ->assertSee('href="' . route('pages.team.single', 'rizky-ramadhan') . '"', false)
            ->getContent();

        $this->assertSame(4, substr_count($html, 'class="team-item'));
    }

    public function test_team_page_lists_every_active_member_and_edit_mode_shows_hidden_ones(): void
    {
        $this->seed(TeamMemberSeeder::class);
        $this->member(9, ['name' => 'Anggota Rahasia', 'is_active' => false]);

        $html = $this->get('/team')
            ->assertOk()
            ->assertSee('The minds behind <span>the magic</span>', false)
            ->assertDontSee('Anggota Rahasia')
            ->getContent();
        $this->assertSame(8, substr_count($html, 'class="team-item'));

        $this->editor()->get('/team')
            ->assertSee('Anggota Rahasia')
            ->assertSee('+ Tambah anggota tim');
    }

    public function test_empty_team(): void
    {
        $this->get('/team')->assertOk()->assertSee('No team members yet.');
        $this->get('/')->assertOk()->assertSee('No team members yet.');
    }

    public function test_detail_page_shows_the_member_with_active_skills_and_expertises(): void
    {
        $member = $this->member(1, ['name' => 'Budi Santoso Putra', 'experience' => '7 tahun', 'about' => 'Ahli mesin.']);
        $member->skills()->create(['name' => 'Servis mesin', 'percent' => 90, 'sort_order' => 1]);
        $member->skills()->create(['name' => 'Skill Rahasia', 'percent' => 10, 'sort_order' => 2, 'is_active' => false]);
        $member->expertises()->create(['name' => 'Injeksi', 'sort_order' => 1]);
        $member->expertises()->create(['name' => 'Keahlian Rahasia', 'sort_order' => 2, 'is_active' => false]);

        $this->get('/pages/team/anggota-1')
            ->assertOk()
            ->assertSee('Budi <span>Santoso Putra</span>', false)
            ->assertSee('7 tahun')
            ->assertSee('Ahli mesin.')
            ->assertSee('data-percent="90%"', false)
            ->assertSee('Injeksi')
            ->assertDontSee('Skill Rahasia')
            ->assertDontSee('Keahlian Rahasia');

        $this->editor()->get('/pages/team/anggota-1')
            ->assertSee('Skill Rahasia')
            ->assertSee('Keahlian Rahasia')
            ->assertSee('+ Tambah skill')
            ->assertSee('+ Tambah keahlian')
            ->assertDontSee('title="Hapus anggota tim"', false);
    }

    public function test_hidden_or_unknown_member_is_404_for_visitors(): void
    {
        $this->member(1, ['is_active' => false]);

        $this->get('/pages/team/anggota-1')->assertNotFound();
        $this->get('/pages/team/tidak-ada')->assertNotFound();
        $this->editor()->get('/pages/team/anggota-1')->assertOk();
    }

    public function test_member_texts_are_escaped(): void
    {
        $this->member(1, ['name' => '<script>alert(1)</script> X', 'role' => '<b>Bos</b>']);

        $this->get('/pages/team/anggota-1')
            ->assertDontSee('<script>alert(1)</script>', false)
            ->assertDontSee('<b>Bos</b>', false);
        $this->get('/team')->assertDontSee('<script>alert(1)</script>', false);
    }
}
