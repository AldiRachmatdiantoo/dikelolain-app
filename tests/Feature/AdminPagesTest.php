<?php

namespace Tests\Feature;

use App\Models\User;
use App\Vcms\EditablePages;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Route;
use Tests\TestCase;

/**
 * Admin panel pages (dashboard, profile, password) and the rule that routes go through controllers.
 */
class AdminPagesTest extends TestCase
{
    use RefreshDatabase;

    public function test_routes_use_controllers(): void
    {
        $closures = collect(Route::getRoutes()->getRoutes())
            ->filter(fn ($route) => $route->getActionName() === 'Closure')
            ->map(fn ($route) => $route->uri())
            // Laravel's own health check and local storage routes.
            ->reject(fn ($uri) => in_array($uri, ['up', 'storage/{path}'], true))
            // Old editor pages, removed with their views in PBI-403.
            ->reject(fn ($uri) => str_starts_with($uri, 'admin/pages/'))
            ->values()
            ->all();

        $this->assertSame([], $closures);
    }

    public function test_dashboard_links_every_page_to_edit_mode(): void
    {
        $response = $this->actingAs(User::factory()->create())
            ->get('/admin/dashboard')
            ->assertOk()
            ->assertSee(route('admin.pages.analytics'), false);

        foreach (EditablePages::all() as $page) {
            $response->assertSee(e(route('admin.edit-mode.on', ['to' => $page['path']])), false)
                ->assertSee('<h3>' . $page['label'] . '</h3>', false);
        }
    }

    public function test_dashboard_link_opens_the_page_in_edit_mode(): void
    {
        $this->actingAs(User::factory()->create())
            ->get(route('admin.edit-mode.on', ['to' => '/pricing']))
            ->assertRedirect('/pricing');

        $this->get('/pricing')->assertSee('vcms-zone', false);
    }

    public function test_admin_pages_need_a_login(): void
    {
        foreach (['/admin/dashboard', '/admin/pages/analytics', '/admin/profile-detail', '/admin/reset-password'] as $url) {
            $this->get($url)->assertRedirect(route('admin.login'));
        }
    }

    public function test_password_is_changed_at_the_profile_url(): void
    {
        $admin = User::factory()->create(['password' => 'rahasia-lama']);

        $this->assertSame(url('/admin/profile/password'), route('admin.password.update.dashboard'));

        $this->actingAs($admin)
            ->from('/admin/reset-password')
            ->put('/admin/profile/password', [
                'current_password' => 'rahasia-lama',
                'password' => 'rahasia-baru-123',
                'password_confirmation' => 'rahasia-baru-123',
            ])
            ->assertRedirect('/admin/reset-password')
            ->assertSessionHas('status');

        $this->assertTrue(Hash::check('rahasia-baru-123', $admin->fresh()->password));
    }

    public function test_unknown_url_renders_the_404_page(): void
    {
        $this->get('/tidak-ada/sama-sekali')
            ->assertNotFound()
            ->assertSee('contact info');
    }
}
