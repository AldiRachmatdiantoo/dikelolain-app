<?php

namespace Database\Seeders;

use App\Models\ProcessStep;
use Illuminate\Database\Seeder;

/**
 * Demo "how we work" steps (previously in SectionContentSeeder).
 */
class ProcessStepSeeder extends Seeder
{
    public function run(): void
    {
        $titles = ['Discovery & Strategy', 'Concept Development', 'Design & Iteration', 'Development & Execution'];

        foreach ($titles as $index => $title) {
            ProcessStep::create([
                'title' => $title,
                'description' => 'We begin by getting to know you and your brand. Through in-depth conversations, research, and analysis.',
                'sort_order' => $index + 1,
            ]);
        }
    }
}
