<?php

namespace Tests\Feature;

use App\Models\Achievement;
use App\Models\PageSetting;
use App\Models\User;
use App\Vcms\EditMode;
use Database\Seeders\AchievementSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\DataProvider;
use Tests\TestCase;

/**
 * The achievements section is shared by Home and About.
 */
class AchievementSectionTest extends TestCase
{
    use RefreshDatabase;

    public static function pages(): array
    {
        return ['home' => ['/'], 'about' => ['/about']];
    }

    #[DataProvider('pages')]
    public function test_seeded_achievements_and_default_settings_are_shown(string $page): void
    {
        $this->seed(AchievementSeeder::class);

        $html = $this->get($page)
            ->assertOk()
            ->assertSee('Proud moments & <span>milestones</span>', false)
            ->assertSeeInOrder(['2015 - 2016', 'Best Design Award', 'Creative Excellence'])
            ->getContent();

        $this->assertSame(7, substr_count($html, 'class="achievements-item'));
    }

    #[DataProvider('pages')]
    public function test_settings_and_hidden_achievements(string $page): void
    {
        PageSetting::create(['key' => 'achievements.description', 'value' => 'Dipercaya sejak 2010.']);
        Achievement::create(['period' => '2024', 'title' => 'Bengkel Teladan', 'sort_order' => 1]);
        Achievement::create(['period' => '2025', 'title' => 'Pencapaian Rahasia', 'sort_order' => 2, 'is_active' => false]);

        $this->get($page)
            ->assertSee('Dipercaya sejak 2010.')
            ->assertSee('Bengkel Teladan')
            ->assertDontSee('Pencapaian Rahasia');

        $this->actingAs(User::factory()->create())
            ->withSession([EditMode::SESSION_KEY => true])
            ->get($page)
            ->assertSee('Pencapaian Rahasia')
            ->assertSee('+ Tambah pencapaian');
    }
}
