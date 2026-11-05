<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Create admin user from ADMIN_* in .env
        $password = config('app.admin.password');
        if (blank($password)) {
            $password = Str::password(16, symbols: false);
            $this->command?->warn('ADMIN_PASSWORD kosong, password admin dibuat acak: ' . $password);
        }

        // Not a factory: those need Faker, which a release install leaves out.
        User::forceCreate([
            'name' => config('app.admin.name'),
            'email' => config('app.admin.email'),
            'password' => $password,
            'email_verified_at' => now(),
        ]);

        // Demo content for the lists (single values fall back to config/page_settings.php)
        $this->call([
            FaqSeeder::class,
            TestimonialSeeder::class,
            ClientLogoSeeder::class,
            ToolSeeder::class,
            ProcessStepSeeder::class,
            AchievementSeeder::class,
            CounterSeeder::class,
            TeamMemberSeeder::class,
            ServiceSeeder::class,
            ProjectSeeder::class,
            PricingPlanSeeder::class,
            PageListItemSeeder::class,
        ]);
    }
}
