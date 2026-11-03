<?php

namespace Tests\Feature;

use App\Models\Counter;
use App\Models\PageSetting;
use App\Models\User;
use App\Vcms\EditMode;
use Database\Seeders\CounterSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\DataProvider;
use Tests\TestCase;

/**
 * The CTA section with counters is shared by Home and About.
 */
class CounterSectionTest extends TestCase
{
    use RefreshDatabase;

    public static function pages(): array
    {
        return ['home' => ['/'], 'about' => ['/about']];
    }

    #[DataProvider('pages')]
    public function test_seeded_counters_and_default_settings_are_shown(string $page): void
    {
        $this->seed(CounterSeeder::class);

        $html = $this->get($page)
            ->assertOk()
            ->assertSee('<span class="counter">45</span>+', false)
            ->assertSee('<span class="counter">100</span>%', false)
            ->assertSee('client satisfaction')
            ->assertSee(asset('images/cta-contact-circle.webp'), false)
            ->getContent();

        $this->assertSame(3, substr_count($html, 'class="cta-counter-item'));
    }

    #[DataProvider('pages')]
    public function test_settings_and_hidden_counters(string $page): void
    {
        PageSetting::create(['key' => 'cta.text', 'value' => 'Servis cepat dan bergaransi.']);
        Counter::create(['number' => '500', 'suffix' => '+', 'label' => 'motor diservis', 'sort_order' => 1]);
        Counter::create(['number' => '9', 'label' => 'Angka Rahasia', 'sort_order' => 2, 'is_active' => false]);

        $this->get($page)
            ->assertSee('Servis cepat dan bergaransi.')
            ->assertSee('motor diservis')
            ->assertDontSee('Angka Rahasia');

        $this->actingAs(User::factory()->create())
            ->withSession([EditMode::SESSION_KEY => true])
            ->get($page)
            ->assertSee('Angka Rahasia')
            ->assertSee('+ Tambah angka');
    }
}
