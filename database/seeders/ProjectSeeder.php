<?php

namespace Database\Seeders;

use App\Models\Project;
use Illuminate\Database\Seeder;

/**
 * Demo projects: the twelve videos previously in SectionContentSeeder, plus six
 * designs with template images so the "Design" filter has something to show.
 * The old demo link (/portfolio/single) led to a 404, so links are left empty.
 */
class ProjectSeeder extends Seeder
{
    public function run(): void
    {
        $videos = [
            ['EszaEO236lw', 'Viera - Bersamamu', 'Music video project.'],
            ['PG0ZAriaBEY', 'News Coverage', 'Berita terkini.'],
            ['oUFJJNQGwhk', 'Cinematic Village', 'Cinematic pedasaan.'],
            ['dQw4w9WgXcQ', 'Brand Story', 'Corporate video.'],
            ['9bZkp7q19f0', 'Product Launch', 'Launch event video.'],
            ['kJQP7kiw5Fk', 'Music Video Pro', 'Professional MV.'],
            ['RgKAFK5djSk', 'Tribute Video', 'Memorial project.'],
            ['JGwWNGJdvx8', 'Dance Cover', 'Choreography video.'],
            ['fRh_vgS2dFE', 'Live Performance', 'Concert recording.'],
            ['OPf0YbXqDm0', 'Classic Hits', 'Retro compilation.'],
            ['hTWKbfoikeg', 'Viral Content', 'Social media hit.'],
            ['YQHsXMglC9A', 'Pop Music Video', 'Popular song MV.'],
        ];

        $designs = [
            ['Brand Identity Refresh', 'Logo, color palette and brand guidelines.'],
            ['Mobile App Interface', 'UI design for a booking app.'],
            ['Packaging Design', 'Product packaging for a local brand.'],
            ['Social Media Campaign', 'Visual set for a product launch campaign.'],
            ['Editorial Layout', 'Magazine spread and cover design.'],
            ['Website Redesign', 'Landing page and design system.'],
        ];

        $order = 0;
        foreach ($videos as [$id, $title, $description]) {
            Project::create([
                'title' => $title,
                'description' => $description,
                'media_type' => 'video',
                'video_url' => "https://www.youtube.com/watch?v={$id}",
                'sort_order' => ++$order,
            ]);
        }

        foreach ($designs as $index => [$title, $description]) {
            Project::create([
                'title' => $title,
                'description' => $description,
                'media_type' => 'image',
                'image' => 'images/work-image-' . ($index + 1) . '.jpg',
                'sort_order' => ++$order,
            ]);
        }
    }
}
