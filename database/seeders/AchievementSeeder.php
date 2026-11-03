<?php

namespace Database\Seeders;

use App\Models\Achievement;
use Illuminate\Database\Seeder;

/**
 * Demo achievements (previously in SectionContentSeeder).
 */
class AchievementSeeder extends Seeder
{
    public function run(): void
    {
        $achievements = [
            ['2015 - 2016', 'Best Design Award'],
            ['2016 - 2017', 'Dribble Winner'],
            ['2017 - 2018', 'Design Of The Year'],
            ['2017 - 2018', 'Graphic Design Winner'],
            ['2018 - 2019', 'Awward Winner'],
            ['2019 - 2020', 'Best Jury Award'],
            ['2019 - 2020', 'Creative Excellence'],
        ];

        foreach ($achievements as $index => [$period, $title]) {
            Achievement::create([
                'period' => $period,
                'title' => $title,
                'description' => 'We begin by getting to know you and your brand. Through in-depth.',
                'sort_order' => $index + 1,
            ]);
        }
    }
}
