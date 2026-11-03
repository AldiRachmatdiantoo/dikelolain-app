<?php

namespace Tests\Feature;

use App\Models\User;
use Database\Seeders\DatabaseSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class AdminSeederTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_is_created_from_config(): void
    {
        config(['app.admin' => ['name' => 'Pemilik', 'email' => 'pemilik@dikelolain.test', 'password' => 'rahasia-uji']]);

        $this->seed(DatabaseSeeder::class);

        $admin = User::sole();
        $this->assertSame('Pemilik', $admin->name);
        $this->assertSame('pemilik@dikelolain.test', $admin->email);
        $this->assertTrue(Hash::check('rahasia-uji', $admin->password));
    }

    public function test_empty_password_gets_a_random_one(): void
    {
        config(['app.admin.password' => null]);

        $this->seed(DatabaseSeeder::class);

        $admin = User::sole();
        $this->assertFalse(Hash::check('', $admin->password));
        $this->assertFalse(Hash::check('123', $admin->password));
    }
}
