<?php

namespace Tests\Feature;

use App\Models\User;
use App\Vcms\EditMode;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\DataProvider;
use Tests\TestCase;

class EditModeTest extends TestCase
{
    use RefreshDatabase;

    public function test_guest_sees_no_admin_bar_or_editor(): void
    {
        $this->get('/faqs')
            ->assertOk()
            ->assertDontSee('vcms-bar')
            ->assertDontSee('vcms-editor');
    }

    public function test_guest_cannot_turn_edit_mode_on(): void
    {
        $this->get('/admin/edit-mode?to=/faqs')->assertRedirect('/admin/login');
        $this->assertFalse(session()->has(EditMode::SESSION_KEY));
    }

    public function test_admin_sees_the_bar_but_no_editor_until_edit_mode_is_on(): void
    {
        $this->actingAs(User::factory()->create())
            ->get('/faqs')
            ->assertOk()
            ->assertSee('Edit halaman ini')
            ->assertDontSee('id="vcms-editor"', false);
    }

    public function test_edit_mode_loads_the_editor_on_the_requested_page(): void
    {
        $this->actingAs(User::factory()->create());

        $this->get('/admin/edit-mode?to=/faqs')
            ->assertRedirect('/faqs')
            ->assertSessionHas(EditMode::SESSION_KEY, true);

        $this->get('/faqs')
            ->assertOk()
            ->assertSee('id="vcms-editor"', false)
            ->assertSee('Selesai edit');
    }

    public function test_turning_edit_mode_off_returns_to_the_page(): void
    {
        $this->actingAs(User::factory()->create())
            ->withSession([EditMode::SESSION_KEY => true]);

        $this->post('/admin/edit-mode/off', ['to' => '/faqs'])
            ->assertRedirect('/faqs')
            ->assertSessionMissing(EditMode::SESSION_KEY);
    }

    public function test_edit_mode_flag_is_ignored_without_login(): void
    {
        $this->withSession([EditMode::SESSION_KEY => true])
            ->get('/faqs')
            ->assertDontSee('vcms-editor');
    }

    public static function unsafeTargets(): array
    {
        return [
            'other site' => ['https://evil.example'],
            'protocol relative' => ['//evil.example'],
            'backslash' => ['/\\evil.example'],
            'no slash' => ['faqs'],
        ];
    }

    #[DataProvider('unsafeTargets')]
    public function test_redirect_target_must_be_a_path_on_this_site(string $to): void
    {
        $this->actingAs(User::factory()->create())
            ->get('/admin/edit-mode?to=' . urlencode($to))
            ->assertRedirect('/');
    }

    public function test_settings_zone_carries_the_editor_payload_only_in_edit_mode(): void
    {
        $view = '<x-vcms.settings :keys="[\'faqs.cta.email\']" label="CTA">Isi</x-vcms.settings>';

        $this->blade($view)->assertSee('Isi')->assertDontSee('data-vcms', false);

        $this->actingAs(User::factory()->create());
        session([EditMode::SESSION_KEY => true]);

        $this->blade($view)
            ->assertSee('class="vcms-zone"', false)
            ->assertSee('data-vcms', false)
            ->assertSee('info@domainname.com');
    }
}
