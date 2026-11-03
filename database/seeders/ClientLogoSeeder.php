<?php

namespace Database\Seeders;

use App\Models\ClientLogo;
use Illuminate\Database\Seeder;

/**
 * Demo client logos (template images, twice, so the two-row slider is full).
 */
class ClientLogoSeeder extends Seeder
{
    public function run(): void
    {
        foreach (range(0, 9) as $index) {
            ClientLogo::create([
                'logo' => 'images/client-logo-' . ($index % 5 + 1) . '.svg',
                'sort_order' => $index + 1,
            ]);
        }
    }
}
