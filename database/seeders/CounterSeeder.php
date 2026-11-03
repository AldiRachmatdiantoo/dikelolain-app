<?php

namespace Database\Seeders;

use App\Models\Counter;
use Illuminate\Database\Seeder;

/**
 * Demo counters for the CTA section (previously in SectionContentSeeder).
 */
class CounterSeeder extends Seeder
{
    public function run(): void
    {
        $counters = [
            ['45', '+', 'project completed'],
            ['15', '+', 'years of experience'],
            ['100', '%', 'client satisfaction'],
        ];

        foreach ($counters as $index => [$number, $suffix, $label]) {
            Counter::create([
                'number' => $number,
                'suffix' => $suffix,
                'label' => $label,
                'sort_order' => $index + 1,
            ]);
        }
    }
}
