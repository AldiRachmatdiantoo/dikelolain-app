<?php

namespace Tests\Feature;

use App\Models\Service;
use App\Models\TeamMember;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\DataProvider;
use Tests\TestCase;

/**
 * The public site carries Dikelolain's name, not the template's: language, author and
 * page titles ("Page - Dikelolain").
 */
class BrandingTest extends TestCase
{
    use RefreshDatabase;

    public static function publicPages(): array
    {
        return [
            'about' => ['/about', 'About Us - Dikelolain'],
            'services' => ['/services', 'Our Services - Dikelolain'],
            'service detail' => ['/pages/service/branding', 'Branding - Dikelolain'],
            'portfolio' => ['/portfolio', 'Our Portfolio - Dikelolain'],
            'portfolio slider' => ['/portfolio-experiment', 'Portfolio Slider - Dikelolain'],
            'pricing' => ['/pricing', 'Pricing - Dikelolain'],
            'faqs' => ['/faqs', 'FAQs - Dikelolain'],
            'team' => ['/team', 'Our Team - Dikelolain'],
            'team member' => ['/pages/team/sari', 'Sari - Our Team - Dikelolain'],
            'testimonials' => ['/testimonials', 'Testimonials - Dikelolain'],
            'contact' => ['/contact', 'Contact - Dikelolain'],
            'not found' => ['/tidak-ada', 'Page Not Found - Dikelolain'],
        ];
    }

    #[DataProvider('publicPages')]
    public function test_page_titles_put_the_page_first(string $url, string $title): void
    {
        Service::create(['slug' => 'branding', 'name' => 'Branding', 'summary' => 'Ringkasan.', 'image' => 'images/service-image-1.jpg', 'is_active' => true]);
        TeamMember::create(['slug' => 'sari', 'name' => 'Sari', 'role' => 'Designer', 'photo' => 'images/team-1.jpg', 'is_active' => true]);

        $this->get($url)
            ->assertSee("<title>{$title}</title>", false)
            ->assertSee('<meta property="og:title" content="' . $title . '">', false);
    }

    public function test_home_title_leads_with_the_brand(): void
    {
        $this->get('/')->assertSee('<title>Dikelolain - Creative Agency</title>', false);
    }

    public function test_language_follows_the_app_locale(): void
    {
        app()->setLocale('id');
        $this->get('/')->assertSee('<html lang="id">', false);

        app()->setLocale('en_US');
        $this->get('/about')->assertSee('<html lang="en-US">', false);
    }

    public function test_template_branding_is_gone(): void
    {
        $this->get('/')
            ->assertOk()
            ->assertSee('<meta name="author" content="Dikelolain">', false)
            ->assertDontSee('Awaiken')
            ->assertDontSee('Rovex')
            ->assertDontSee('zxx');
    }

    public function test_admin_pages_are_marked_indonesian(): void
    {
        $this->get('/admin/login')
            ->assertSee('<html lang="id">', false)
            ->assertSee('<title>Login - Dikelolain VCMS</title>', false);

        $this->actingAs(User::factory()->create())
            ->get('/admin/dashboard')
            ->assertSee('<html lang="id">', false)
            ->assertSee('<title>Dashboard - Dikelolain VCMS</title>', false);
    }
}
