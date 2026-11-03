<?php

namespace Tests\Feature;

use App\Models\ClientLogo;
use App\Models\PageSetting;
use App\Models\User;
use App\Vcms\EditMode;
use Database\Seeders\ClientLogoSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\DataProvider;
use Tests\TestCase;

/**
 * The client logos section is shared by Home and About.
 */
class ClientLogoSectionTest extends TestCase
{
    use RefreshDatabase;

    public static function pages(): array
    {
        return ['home' => ['/'], 'about' => ['/about']];
    }

    #[DataProvider('pages')]
    public function test_logos_are_shown_two_per_slide(string $page): void
    {
        $this->seed(ClientLogoSeeder::class);

        $html = $this->get($page)->assertOk()->assertSee(asset('images/client-logo-5.svg'), false)->getContent();

        $this->assertSame(10, substr_count($html, 'class="trusted-client-logo'));
        $this->assertSame(5, substr_count($html, 'gap: 40px;'));
    }

    #[DataProvider('pages')]
    public function test_settings_and_hidden_logos(string $page): void
    {
        PageSetting::create(['key' => 'clients.title', 'value' => 'Dipercaya <span>UMKM</span>']);
        ClientLogo::create(['logo' => 'images/client-logo-1.svg', 'name' => 'Toko A', 'sort_order' => 1]);
        ClientLogo::create(['logo' => 'images/client-logo-2.svg', 'name' => 'Toko Rahasia', 'sort_order' => 2, 'is_active' => false]);

        $this->get($page)
            ->assertSee('Dipercaya <span>UMKM</span>', false)
            ->assertSee('alt="Toko A"', false)
            ->assertDontSee('Toko Rahasia');

        $this->actingAs(User::factory()->create())
            ->withSession([EditMode::SESSION_KEY => true])
            ->get($page)
            ->assertSee('Toko Rahasia')
            ->assertSee('+ Tambah logo klien');
    }
}
