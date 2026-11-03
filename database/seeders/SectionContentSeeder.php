<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Section;
use App\Models\SectionContent;

class SectionContentSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // ========================================
        // 2. CORE SECTION DEFINITION (Keys di sini disesuaikan)
        // ========================================

        $sectionsData = [
            // 1. HERO SECTION (Order 1)
            [
                'key' => 'hero', 'name' => 'Hero Section', 'order' => 1,
                'contents' => [
                    ['key' => 'hero-video_video_url', 'type' => 'video', 'value' => 'https://demo.awaikenthemes.com/assets/videos/rovex-video-1.mp4', 'order' => 1],
                    ['key' => 'hero-content-1_hero_title', 'type' => 'text', 'value' => 'Where <span>creativity</span> meets strategy', 'order' => 2],
                    ['key' => 'hero-content-1_hero_description', 'type' => 'textarea', 'value' => 'We\'re a full-service design agency specializing in branding, web design, and creative strategies that elevate businesses.', 'order' => 3],
                    ['key' => 'hero-content-1_btn_text', 'type' => 'text', 'value' => 'view our portfolio', 'order' => 4], // KEY CHANGE: _button_text -> _btn_text
                    ['key' => 'hero-content-1_btn_link', 'type' => 'url', 'value' => '/portfolio', 'order' => 5],    // KEY CHANGE: _button_link -> _btn_link
                    // Hero Images 
                    ['key' => 'hero-images-1_image_1', 'type' => 'image', 'value' => 'images/hero-img-1.jpg', 'order' => 6],
                    ['key' => 'hero-images-1_image_2', 'type' => 'image', 'value' => 'images/hero-img-2.jpg', 'order' => 7],
                    ['key' => 'hero-images-1_image_3', 'type' => 'image', 'value' => 'images/hero-img-3.jpg', 'order' => 8],
                    ['key' => 'hero-images-1_image_4', 'type' => 'image', 'value' => 'images/hero-img-4.jpg', 'order' => 9],
                    ['key' => 'hero-images-1_image_5', 'type' => 'image', 'value' => 'images/hero-img-5.jpg', 'order' => 10],
                ]
            ],
        ];

        // 3. Iterate and seed core sections
        foreach ($sectionsData as $sectionData) {
            $section = Section::firstOrCreate(
                ['key' => $sectionData['key']],
                [
                    'name' => $sectionData['name'],
                    'type' => 'default',
                    'is_active' => true,
                    'order' => $sectionData['order']
                ]
            );

            foreach ($sectionData['contents'] as $content) {
                $section->contents()->updateOrCreate(
                    ['key' => $content['key']],
                    $content
                );
            }
        }

        $this->command->info('✅ All VCMS section content seeded successfully!');
    }
}