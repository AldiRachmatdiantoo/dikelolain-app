<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\DataProvider;
use Tests\TestCase;

/**
 * Every page renders without errors, on the seeded demo content.
 * Safety net for the rebuild: run before and after every change.
 */
class SmokeTest extends TestCase
{
    use RefreshDatabase;

    protected bool $seed = true;

    public static function publicPages(): array
    {
        return [
            'home' => ['/'],
            'about' => ['/about'],
            'services' => ['/services'],
            'service detail' => ['/pages/service/01'],
            'portfolio' => ['/portfolio'],
            'portfolio experiment' => ['/portfolio-experiment'],
            'pricing' => ['/pricing'],
            'faqs' => ['/faqs'],
            'team' => ['/team'],
            'team detail' => ['/pages/team/0'],
            'testimonials' => ['/testimonials'],
            'contact' => ['/contact'],
            'admin login' => ['/admin/login'],
        ];
    }

    public static function adminPages(): array
    {
        return [
            'dashboard' => ['/admin/dashboard'],
            'analytics' => ['/admin/pages/analytics'],
            'home' => ['/admin/pages/home'],
            'about' => ['/admin/pages/about'],
            'services' => ['/admin/pages/services'],
            'service detail' => ['/admin/pages/service/01'],
            'portfolio' => ['/admin/pages/portfolio'],
            'team' => ['/admin/pages/team'],
            'pricing' => ['/admin/pages/pricing'],
            'contact' => ['/admin/pages/contact'],
            'faqs' => ['/admin/pages/faqs'],
            'testimonials' => ['/admin/pages/testimonials'],
            'profile' => ['/admin/profile-detail'],
            'reset password' => ['/admin/reset-password'],
        ];
    }

    #[DataProvider('publicPages')]
    public function test_public_page_renders(string $url): void
    {
        $this->get($url)->assertOk();
    }

    public function test_unknown_url_shows_the_404_page(): void
    {
        // The status should be 404 as well; see PBI-109.
        $this->get('/halaman-tidak-ada')->assertViewIs('pages.404');
    }

    #[DataProvider('adminPages')]
    public function test_admin_page_redirects_guests_to_login(string $url): void
    {
        $this->get($url)->assertRedirect(route('admin.login'));
    }

    #[DataProvider('adminPages')]
    public function test_admin_page_renders_for_admin(string $url): void
    {
        $this->actingAs(User::first())->get($url)->assertOk();
    }

    public function test_content_api_returns_a_section(): void
    {
        $this->actingAs(User::first())
            ->getJson('/admin/api/content/hero')
            ->assertOk()
            ->assertJsonPath('success', true)
            ->assertJsonPath('data.key', 'hero');
    }

    public function test_content_api_lists_media(): void
    {
        $this->actingAs(User::first())
            ->getJson('/admin/api/content/media-list')
            ->assertOk();
    }

    public function test_admin_can_log_in_and_out(): void
    {
        $admin = User::factory()->create(['password' => 'rahasia-uji']);

        $this->post('/admin/login', ['email' => $admin->email, 'password' => 'rahasia-uji'])
            ->assertRedirect(route('admin.dashboard'));
        $this->assertAuthenticatedAs($admin);

        $this->post('/admin/logout')->assertRedirect(route('home'));
        $this->assertGuest();
    }

    public function test_login_with_a_wrong_password_is_rejected(): void
    {
        $admin = User::factory()->create(['password' => 'rahasia-uji']);

        $this->post('/admin/login', ['email' => $admin->email, 'password' => 'salah'])
            ->assertRedirect(route('admin.login'))
            ->assertSessionHas('error');
        $this->assertGuest();
    }
}
