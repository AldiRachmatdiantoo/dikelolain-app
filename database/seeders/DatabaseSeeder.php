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

        User::factory()->create([
            'name' => config('app.admin.name'),
            'email' => config('app.admin.email'),
            'password' => $password,
        ]);

        // Seed VCMS sections and content
        $this->call([
            SectionContentSeeder::class,
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
