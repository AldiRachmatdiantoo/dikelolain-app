<?php

namespace Tests\Feature;

use App\Models\PageListItem;
use App\Models\PageSetting;
use App\Models\User;
use App\Vcms\EditMode;
use Database\Seeders\PageListItemSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * The bullet lists and the single values around them on About (about us, vision, mission) and Pricing (benefits).
 */
class PageListPagesTest extends TestCase
{
    use RefreshDatabase;

    private function editor(): static
    {
        return $this->actingAs(User::factory()->create())->withSession([EditMode::SESSION_KEY => true]);
    }

    public function test_about_renders_the_seeded_lists_and_default_texts(): void
    {
        $this->seed(PageListItemSeeder::class);

        $html = $this->get('/about')
            ->assertOk()
            ->assertSee('About our <span>journey</span>', false)
            ->assertSee('A team of <span>creative thinkers</span>', false)
            ->assertSeeInOrder(['our vision', 'Driving the Future of <span>Creativity</span>', 'our mission', 'Bringing ideas to life through <span>creativity</span>'], false)
            ->assertSee(asset('images/our-mission-img.jpg'), false)
            ->assertSee('href="/contact"', false)
            ->getContent();

        $this->assertSame(3, substr_count($html, 'Integrity and Transparency'));
    }

    public function test_each_about_list_shows_its_own_active_items(): void
    {
        PageListItem::create(['list_key' => 'about', 'text' => 'Poin About', 'sort_order' => 1]);
        PageListItem::create(['list_key' => 'vision', 'text' => 'Poin Visi', 'sort_order' => 1]);
        PageListItem::create(['list_key' => 'mission', 'text' => 'Poin Misi', 'sort_order' => 1]);
        PageListItem::create(['list_key' => 'mission', 'text' => 'Poin Rahasia', 'sort_order' => 2, 'is_active' => false]);
        PageSetting::create(['key' => 'vision.title', 'value' => 'Visi <span>kami</span>']);

        $this->get('/about')
            ->assertSeeInOrder(['Poin About', 'Visi <span>kami</span>', 'Poin Visi', 'Poin Misi'], false)
            ->assertDontSee('Poin Rahasia');

        $html = $this->editor()->get('/about')
            ->assertSee('Poin Rahasia')
            ->getContent();
        $this->assertSame(3, substr_count($html, '+ Tambah poin'));
    }

    public function test_pricing_benefits_show_icons_and_hide_inactive_items(): void
    {
        $this->seed(PageListItemSeeder::class);
        PageListItem::create(['list_key' => 'pricing_benefits', 'text' => 'Tanpa ikon', 'sort_order' => 4]);
        PageListItem::create(['list_key' => 'pricing_benefits', 'text' => 'Keuntungan Rahasia', 'sort_order' => 5, 'is_active' => false]);

        $this->get('/pricing')
            ->assertSeeInOrder(['Get 30 day free trial', 'No any hidden fees pay', 'You can cancel anytime', 'Tanpa ikon'])
            ->assertSee('<img src="' . asset('images/icon-pricing-benefit-1.svg') . '" alt="">Get 30 day free trial', false)
            ->assertDontSee('alt="">Tanpa ikon', false)
            ->assertDontSee('Keuntungan Rahasia');

        $this->editor()->get('/pricing')
            ->assertSee('Keuntungan Rahasia')
            ->assertSee('+ Tambah keuntungan');
    }

    public function test_list_text_and_breadcrumb_are_escaped(): void
    {
        PageListItem::create(['list_key' => 'about', 'text' => '<script>alert(1)</script>']);
        PageSetting::create(['key' => 'about.header.breadcrumb', 'value' => '<b>About</b>']);

        $this->get('/about')
            ->assertDontSee('<script>alert(1)</script>', false)
            ->assertDontSee('<b>About</b>', false);
    }
}
