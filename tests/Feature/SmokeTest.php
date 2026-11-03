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

    protected function setUp(): void
    {
        parent::setUp();
        // Seed per test (rolled back afterwards). `$seed = true` only seeds when the
        // in-memory database is first migrated, so it would depend on test order.
        $this->seed();
    }

    public static function publicPages(): array
    {
        return [
            'home' => ['/'],
            'about' => ['/about'],
            'services' => ['/services'],
            'service detail' => ['/pages/service/web-development'],
            'portfolio' => ['/portfolio'],
            'portfolio experiment' => ['/portfolio-experiment'],
            'pricing' => ['/pricing'],
            'faqs' => ['/faqs'],
            'team' => ['/team'],
            'team detail' => ['/pages/team/rizky-ramadhan'],
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
            'messages' => ['/admin/messages'],
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
        $this->get('/halaman-tidak-ada')
            ->assertNotFound()
            ->assertViewIs('pages.404');
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
