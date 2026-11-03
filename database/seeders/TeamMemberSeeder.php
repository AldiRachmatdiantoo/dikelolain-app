<?php

namespace Database\Seeders;

use App\Models\TeamMember;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

/**
 * Demo team with the template photos, to be replaced by the real team (KD-2).
 * The old seeder had no members, so the team sections showed "No team members yet."
 */
class TeamMemberSeeder extends Seeder
{
    public function run(): void
    {
        $members = [
            ['Rizky Ramadhan', 'Creative Director'],
            ['Nadia Putri', 'UI/UX Designer'],
            ['Fajar Nugroho', 'Web Developer'],
            ['Intan Permata', 'Brand Strategist'],
            ['Yoga Saputra', 'Motion Designer'],
            ['Citra Anggraini', 'Content Writer'],
            ['Bayu Firmansyah', 'Photographer'],
            ['Laras Kusuma', 'Project Manager'],
        ];

        foreach ($members as $index => [$name, $role]) {
            $member = TeamMember::create([
                'slug' => Str::slug($name),
                'name' => $name,
                'role' => $role,
                'photo' => 'images/team-' . ($index + 1) . '.jpg',
                'experience' => (3 + $index) . ' years',
                'about' => "With years of experience in design and creativity, {$name} leads the team with a passion for crafting innovative and impactful visual solutions.",
                'expertise_intro' => "{$name} is a seasoned professional with a keen eye for aesthetics and a deep understanding of creative strategies.",
                'skills_intro' => "{$name} excels in creative problem-solving, user-centered design, branding, and UI/UX development.",
                'sort_order' => $index + 1,
            ]);

            foreach (['Creative Direction' => 90, 'Branding' => 85, 'UI/UX Design' => 80] as $skill => $percent) {
                $member->skills()->create(['name' => $skill, 'percent' => $percent, 'sort_order' => $member->skills()->count() + 1]);
            }

            foreach (['Brand Identity', 'Web Design', 'Art Direction', 'Visual Storytelling'] as $expertise) {
                $member->expertises()->create(['name' => $expertise, 'sort_order' => $member->expertises()->count() + 1]);
            }
        }
    }
}
