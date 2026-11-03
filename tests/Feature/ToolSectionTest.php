<?php

namespace Tests\Feature;

use App\Models\PageSetting;
use App\Models\Tool;
use App\Models\User;
use App\Vcms\EditMode;
use Database\Seeders\ToolSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\DataProvider;
use Tests\TestCase;

/**
 * The creative tools section is shared by Home, Services, Pricing and Testimonials.
 */
class ToolSectionTest extends TestCase
{
    use RefreshDatabase;

    public static function pages(): array
    {
        return [
            'home' => ['/', 'creative-tools'],
            'services' => ['/services', 'creative-tools'],
            'pricing' => ['/pricing', 'creative-tools service-creative-tools'],
            'testimonials' => ['/testimonials', 'creative-tools service-creative-tools'],
        ];
    }

    #[DataProvider('pages')]
    public function test_seeded_tools_and_default_settings_are_shown(string $page, string $wrapper): void
    {
        $this->seed(ToolSeeder::class);

        $html = $this->get($page)
            ->assertOk()
            ->assertSee('<div class="' . $wrapper . '">', false)
            ->assertSee('Our toolbox for <span>innovation</span>', false)
            ->assertSee('href="/contact"', false)
            ->assertSee(asset('images/icon-creative-tools-8.svg'), false)
            ->assertSeeInOrder(['figma', 'framer', 'incopy'])
            ->getContent();

        $this->assertSame(8, substr_count($html, 'class="creative-tool-item"'));
    }

    #[DataProvider('pages')]
    public function test_settings_and_hidden_tools(string $page): void
    {
        PageSetting::create(['key' => 'tools.button_text', 'value' => 'Konsultasi gratis']);
        PageSetting::create(['key' => 'tools.button_link', 'value' => 'https://wa.me/628123']);
        Tool::create(['name' => 'Canva', 'category' => 'Desain', 'icon' => 'images/icon-creative-tools-1.svg', 'sort_order' => 1]);
        Tool::create(['name' => 'Alat Rahasia', 'icon' => 'images/icon-creative-tools-2.svg', 'sort_order' => 2, 'is_active' => false]);

        $this->get($page)
            ->assertSee('href="https://wa.me/628123"', false)
            ->assertSee('Konsultasi gratis')
            ->assertSee('Canva')
            ->assertDontSee('Alat Rahasia');

        $this->actingAs(User::factory()->create())
            ->withSession([EditMode::SESSION_KEY => true])
            ->get($page)
            ->assertSee('Alat Rahasia')
            ->assertSee('vcms-hidden', false)
            ->assertSee('+ Tambah tool');
    }

    public function test_tool_names_are_escaped(): void
    {
        Tool::create(['name' => '<script>alert(1)</script>', 'icon' => 'images/icon-creative-tools-1.svg', 'sort_order' => 1]);

        $this->get('/')->assertDontSee('<script>alert(1)</script>', false);
    }
}
