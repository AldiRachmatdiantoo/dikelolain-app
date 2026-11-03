<?php

namespace Tests\Feature;

use App\Models\PageSetting;
use App\Models\PricingPlan;
use App\Models\User;
use App\Vcms\EditMode;
use Database\Seeders\PricingPlanSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PricingPageTest extends TestCase
{
    use RefreshDatabase;

    private function editor(): static
    {
        return $this->actingAs(User::factory()->create())->withSession([EditMode::SESSION_KEY => true]);
    }

    public function test_seeded_plans_render_like_the_old_page(): void
    {
        $this->seed(PricingPlanSeeder::class);

        $html = $this->get('/pricing')
            ->assertOk()
            ->assertSee('Pricing <span>plan</span>', false)
            ->assertSeeInOrder(['basic plan', 'Rp 300 Rb <sub>/per month</sub>', 'standard plan', 'premium plan', 'Paket manajemen konten full'], false)
            ->assertSee('Strategi Konten &amp; Social Media Management', false)
            ->getContent();

        $this->assertSame(1, substr_count($html, 'pricing-box highlighted-box'));
        $this->assertSame(3, substr_count($html, 'What included?'));
        $this->assertSame(11, substr_count($html, '<li class="" >'));
    }

    public function test_hidden_plans_and_features_show_only_in_edit_mode(): void
    {
        $plan = PricingPlan::create(['name' => 'Paket Servis', 'price' => 'Rp 250 Rb', 'button_text' => 'Pesan', 'button_link' => '/contact', 'sort_order' => 1]);
        $plan->features()->create(['text' => 'Ganti oli', 'sort_order' => 1]);
        $plan->features()->create(['text' => 'Fitur Rahasia', 'sort_order' => 2, 'is_active' => false]);
        PricingPlan::create(['name' => 'Paket Rahasia', 'price' => 'Rp 1', 'sort_order' => 2, 'is_active' => false]);
        PageSetting::create(['key' => 'pricing.list_title', 'value' => 'Termasuk:']);

        $this->get('/pricing')
            ->assertSee('Ganti oli')
            ->assertSee('Termasuk:')
            ->assertSee('<a href="/contact" class="btn-default btn-highlighted">Pesan</a>', false)
            ->assertDontSee('Fitur Rahasia')
            ->assertDontSee('Paket Rahasia');

        $this->editor()->get('/pricing')
            ->assertSee('Fitur Rahasia')
            ->assertSee('Paket Rahasia')
            ->assertSee('+ Tambah paket')
            ->assertSee('+ Tambah fitur');
    }

    public function test_empty_period_and_button_text_render_nothing(): void
    {
        PricingPlan::create(['name' => 'Gratis', 'price' => 'Rp 0']);

        $this->get('/pricing')
            ->assertSee('Rp 0', false)
            ->assertDontSee('<sub>', false)
            ->assertDontSee('class="pricing-btn"', false);
    }

    public function test_plan_text_is_escaped(): void
    {
        $plan = PricingPlan::create(['name' => '<script>alert(1)</script>', 'price' => '<b>Rp</b>']);
        $plan->features()->create(['text' => '<img src=x onerror=alert(2)>']);

        $this->get('/pricing')
            ->assertDontSee('<script>alert(1)</script>', false)
            ->assertDontSee('<b>Rp</b>', false)
            ->assertDontSee('<img src=x onerror=alert(2)>', false);
    }
}
