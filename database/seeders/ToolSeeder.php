<?php

namespace Database\Seeders;

use App\Models\Tool;
use Illuminate\Database\Seeder;

/**
 * Demo creative tools (previously in SectionContentSeeder).
 */
class ToolSeeder extends Seeder
{
    public function run(): void
    {
        $tools = ['figma', 'framer', 'illustrator', 'adobe xd', 'indesign', 'photoshop', 'lightroom', 'incopy'];

        foreach ($tools as $index => $name) {
            Tool::create([
                'name' => $name,
                'category' => 'Design tool',
                'icon' => 'images/icon-creative-tools-' . ($index + 1) . '.svg',
                'sort_order' => $index + 1,
            ]);
        }
    }
}
