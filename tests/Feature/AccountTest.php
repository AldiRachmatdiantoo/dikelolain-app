<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

/**
 * The admin's own account: login, logout, profile and password
 * (FormRequest → AccountService → UserRepository).
 */
class AccountTest extends TestCase
{
    use RefreshDatabase;

    private const EMAIL = 'admin@contoh.test';

    private const PASSWORD = 'rahasia-lama';

    private function admin(): User
    {
        return User::factory()->create(['email' => self::EMAIL, 'password' => self::PASSWORD]);
    }

    public function test_admin_logs_in_and_lands_on_the_dashboard(): void
    {
        $admin = $this->admin();

        $this->post('/admin/login', ['email' => self::EMAIL, 'password' => self::PASSWORD])
            ->assertRedirect(route('admin.dashboard'));

        $this->assertAuthenticatedAs($admin);
    }

    public function test_remember_me_keeps_the_admin_logged_in(): void
    {
        $this->admin();

        $this->post('/admin/login', ['email' => self::EMAIL, 'password' => self::PASSWORD, 'remember' => 'on'])
            ->assertCookie(auth()->guard()->getRecallerName());
    }

    public function test_wrong_password_returns_to_the_form_with_only_the_email(): void
    {
        $this->admin();

        $this->post('/admin/login', ['email' => self::EMAIL, 'password' => 'salah'])
            ->assertRedirect(route('admin.login'))
            ->assertSessionHas('error', 'Email atau password salah.')
            ->assertSessionHasInput('email', self::EMAIL)
            ->assertSessionMissing('_old_input.password');

        $this->assertGuest();
    }

    public function test_login_needs_an_email_and_a_password(): void
    {
        $this->post('/admin/login', ['email' => 'bukan-email', 'password' => ''])
            ->assertSessionHasErrors(['email', 'password']);

        $this->assertGuest();
    }

    public function test_login_allows_five_tries_a_minute(): void
    {
        $this->admin();

        foreach (range(1, 5) as $try) {
            $this->post('/admin/login', ['email' => self::EMAIL, 'password' => 'salah'])->assertRedirect();
        }

        $this->post('/admin/login', ['email' => self::EMAIL, 'password' => self::PASSWORD])->assertStatus(429);
        $this->assertGuest();
    }

    public function test_logout_ends_the_session(): void
    {
        $this->actingAs($this->admin())
            ->post('/admin/logout')
            ->assertRedirect(route('home'));

        $this->assertGuest();
    }

    public function test_new_email_is_saved_and_has_to_be_verified_again(): void
    {
        $admin = $this->admin();

        $this->actingAs($admin)
            ->from('/admin/profile-detail')
            ->patch('/admin/profile', ['name' => 'Admin Baru', 'email' => 'baru@contoh.test'])
            ->assertRedirect('/admin/profile-detail')
            ->assertSessionHas('status', 'Detail profil berhasil diperbarui.');

        $admin->refresh();
        $this->assertSame(['Admin Baru', 'baru@contoh.test'], [$admin->name, $admin->email]);
        $this->assertNull($admin->email_verified_at);
    }

    public function test_keeping_the_email_keeps_it_verified(): void
    {
        $admin = $this->admin();

        $this->actingAs($admin)
            ->patch('/admin/profile', ['name' => 'Nama Lain', 'email' => self::EMAIL])
            ->assertSessionHasNoErrors();

        $this->assertSame('Nama Lain', $admin->fresh()->name);
        $this->assertNotNull($admin->fresh()->email_verified_at);
    }

    public function test_email_of_another_user_is_rejected(): void
    {
        User::factory()->create(['email' => 'lain@contoh.test']);
        $admin = $this->admin();

        $this->actingAs($admin)
            ->patch('/admin/profile', ['name' => 'Admin', 'email' => 'lain@contoh.test'])
            ->assertSessionHasErrors('email');

        $this->assertSame(self::EMAIL, $admin->fresh()->email);
    }

    public function test_profile_form_cannot_change_other_columns(): void
    {
        $admin = $this->admin();

        $this->actingAs($admin)->patch('/admin/profile', [
            'name' => 'Admin',
            'email' => self::EMAIL,
            'password' => 'diam-diam-123',
            'email_verified_at' => null,
        ]);

        $admin->refresh();
        $this->assertTrue(Hash::check(self::PASSWORD, $admin->password));
        $this->assertNotNull($admin->email_verified_at);
    }

    public function test_wrong_current_password_is_rejected(): void
    {
        $admin = $this->admin();

        $this->actingAs($admin)
            ->from('/admin/reset-password')
            ->put('/admin/profile/password', [
                'current_password' => 'salah',
                'password' => 'rahasia-baru-123',
                'password_confirmation' => 'rahasia-baru-123',
            ])
            ->assertRedirect('/admin/reset-password')
            ->assertSessionHasErrors(['current_password' => 'Password saat ini salah. Mohon periksa kembali.']);

        $this->assertTrue(Hash::check(self::PASSWORD, $admin->fresh()->password));
    }

    public function test_new_password_must_be_long_enough_and_confirmed(): void
    {
        $admin = $this->admin();

        $this->actingAs($admin)
            ->put('/admin/profile/password', [
                'current_password' => self::PASSWORD,
                'password' => 'pendek',
                'password_confirmation' => 'beda',
            ])
            ->assertSessionHasErrors(['password' => 'Password baru minimal harus 8 karakter.'])
            ->assertSessionHasErrors(['password' => 'Konfirmasi password tidak cocok dengan password baru.']);

        $this->assertTrue(Hash::check(self::PASSWORD, $admin->fresh()->password));
    }
}
