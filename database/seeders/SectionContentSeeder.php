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
        // 1. DEFAULT DATA DEFINITIONS (FIXED SCOPE)
        // ========================================
        
        // Data Default untuk Section Services
        $defaultServices = [
            ['id' => '01', 'name' => 'UI/UX Design', 'img_index' => 1],
            ['id' => '02', 'name' => 'Web Development', 'img_index' => 2],
            ['id' => '03', 'name' => '3D Designs', 'img_index' => 3],
            ['id' => '04', 'name' => 'Digital Marketing Design', 'img_index' => 4],
            ['id' => '05', 'name' => 'Motion Graphics', 'img_index' => 5],
        ];
        $serviceDefaultDesc = 'We\'re a full-service design agency specializing in branding, web design, and creative strategies that elevate businesses.';

        // Data Default untuk Section Work Projects (12 projects)
        $defaultProjects = [
            ['type' => 'video', 'src' => 'https://www.youtube.com/embed/EszaEO236lw', 'title' => 'Viera - Bersamamu', 'desc' => 'Music video project.', 'link' => '/portfolio/single'],
            ['type' => 'video', 'src' => 'https://www.youtube.com/embed/PG0ZAriaBEY', 'title' => 'News Coverage', 'desc' => 'Berita terkini.', 'link' => '/portfolio/single'],
            ['type' => 'video', 'src' => 'https://www.youtube.com/embed/oUFJJNQGwhk', 'title' => 'Cinematic Village', 'desc' => 'Cinematic pedasaan.', 'link' => '/portfolio/single'],
            ['type' => 'video', 'src' => 'https://www.youtube.com/embed/dQw4w9WgXcQ', 'title' => 'Brand Story', 'desc' => 'Corporate video.', 'link' => '/portfolio/single'],
            ['type' => 'video', 'src' => 'https://www.youtube.com/embed/9bZkp7q19f0', 'title' => 'Product Launch', 'desc' => 'Launch event video.', 'link' => '/portfolio/single'],
            ['type' => 'video', 'src' => 'https://www.youtube.com/embed/kJQP7kiw5Fk', 'title' => 'Music Video Pro', 'desc' => 'Professional MV.', 'link' => '/portfolio/single'],
            ['type' => 'video', 'src' => 'https://www.youtube.com/embed/RgKAFK5djSk', 'title' => 'Tribute Video', 'desc' => 'Memorial project.', 'link' => '/portfolio/single'],
            ['type' => 'video', 'src' => 'https://www.youtube.com/embed/JGwWNGJdvx8', 'title' => 'Dance Cover', 'desc' => 'Choreography video.', 'link' => '/portfolio/single'],
            ['type' => 'video', 'src' => 'https://www.youtube.com/embed/fRh_vgS2dFE', 'title' => 'Live Performance', 'desc' => 'Concert recording.', 'link' => '/portfolio/single'],
            ['type' => 'video', 'src' => 'https://www.youtube.com/embed/OPf0YbXqDm0', 'title' => 'Classic Hits', 'desc' => 'Retro compilation.', 'link' => '/portfolio/single'],
            ['type' => 'video', 'src' => 'https://www.youtube.com/embed/hTWKbfoikeg', 'title' => 'Viral Content', 'desc' => 'Social media hit.', 'link' => '/portfolio/single'],
            ['type' => 'video', 'src' => 'https://www.youtube.com/embed/YQHsXMglC9A', 'title' => 'Pop Music Video', 'desc' => 'Popular song MV.', 'link' => '/portfolio/single'],
        ];
        
        // Data Default untuk Section CTA (Counters)
        $defaultCounters = [
            ['num' => '45', 'sym' => '+', 'label' => 'project completed'],
            ['num' => '15', 'sym' => '+', 'label' => 'years of experience'],
            ['num' => '100', 'sym' => '%', 'label' => 'client satisfaction'],
        ];

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
            // 3. SERVICES SECTION (Order 3)
            [
                'key' => 'services', 'name' => 'Our Services', 'order' => 3,
                'contents' => [
                    ['key' => 'services_sub_title', 'type' => 'text', 'value' => 'our Services', 'order' => 1],        // KEY CHANGE: services-title_sub_title -> services_sub_title
                    ['key' => 'services_main_title', 'type' => 'text', 'value' => 'Our innovative <span>services</span>', 'order' => 2], // KEY CHANGE: services-title_main_title -> services_main_title
                    ['key' => 'services_btn_text', 'type' => 'text', 'value' => 'view all services', 'order' => 3], // KEY CHANGE: services-btn_button_text -> services_btn_text
                    ['key' => 'services_btn_link', 'type' => 'url', 'value' => '/services', 'order' => 4],    // KEY CHANGE: services-btn_button_link -> services_btn_link
                ]
            ],
            // 4. OUR WORK SECTION (Order 4)
            [
                'key' => 'our-work', 'name' => 'Our Work', 'order' => 4,
                'contents' => [
                    ['key' => 'work_sub_title', 'type' => 'text', 'value' => 'our work', 'order' => 1], // KEY CHANGE: work-title_sub_title -> work_sub_title
                    ['key' => 'work_main_title', 'type' => 'text', 'value' => 'Our latest <span>Projects</span>', 'order' => 2], // KEY CHANGE: work-title_main_title -> work_main_title
                    ['key' => 'work_btn_text', 'type' => 'text', 'value' => 'view all portfolio', 'order' => 3], // KEY CHANGE: work-btn_button_text -> work_btn_text
                    ['key' => 'work_btn_link', 'type' => 'url', 'value' => '/portfolio', 'order' => 4], // KEY CHANGE: work-btn_button_link -> work_btn_link
                ]
            ],
            // 7. OUR TEAM SECTION (Order 7)
            [
                'key' => 'our-team', 'name' => 'Our Team', 'order' => 7,
                'contents' => [
                    ['key' => 'team_sub_title', 'type' => 'text', 'value' => 'our team', 'order' => 1], // KEY CHANGE: team-title_sub_title -> team_sub_title
                    ['key' => 'team_main_title', 'type' => 'text', 'value' => 'The Minds Behind <span>the Magic</span>', 'order' => 2], // KEY CHANGE: team-title_main_title -> team_main_title
                    ['key' => 'team_btn_text', 'type' => 'text', 'value' => 'all team members', 'order' => 3], // KEY CHANGE: team-btn_button_text -> team_btn_text
                    ['key' => 'team_btn_link', 'type' => 'url', 'value' => '/team', 'order' => 4], // KEY CHANGE: team-btn_button_link -> team_btn_link
                ]
            ],
            // 9. CTA SECTION (Order 9)
            [
                'key' => 'cta', 'name' => 'CTA Section', 'order' => 9,
                'contents' => [
                    ['key' => 'cta_text_content', 'type' => 'textarea', 'value' => 'We specialize in delivering innovative and impactful design solutions that elevate brands and drive results. From digital experiences to print media, our team of creative professionals is dedicated to transforming ideas into compelling visual stories that resonate with audiences. With a focus on creativity, strategy, and client collaboration.', 'order' => 1], // KEY CHANGE: cta-desc_text_content -> cta_text_content
                    
                    ['key' => 'cta-circle-img_image_file', 'type' => 'image', 'value' => 'images/cta-contact-circle.png', 'order' => 5],
                    ['key' => 'cta-circle-img_link_url', 'type' => 'url', 'value' => '/contact', 'order' => 6],
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

        // ========================================
        // 4. SEED LOOPED CONTENT (Service List)
        // ========================================
        $servicesSection = Section::where('key', 'services')->first();
        if ($servicesSection) {
            $order = 5;
            foreach ($defaultServices as $index => $service) {
                $serviceIndex = $service['id'];
                $imgIndex = $service['img_index'];
                
                // Tambahkan key untuk tab title
                $contents = [
                    ['key' => 'service-tab-' . $serviceIndex . '_title', 'type' => 'text', 'value' => $service['name'], 'order' => $order++],
                ];

                // Base key untuk konten service list
                $baseKey = "services-list-1_service_{$serviceIndex}";

                $contents = array_merge($contents, [
                    ['key' => $baseKey . '_title', 'type' => 'text', 'value' => $service['name'], 'order' => $order++],
                    ['key' => $baseKey . '_desc', 'type' => 'textarea', 'value' => $serviceDefaultDesc, 'order' => $order++],
                    ['key' => $baseKey . '_image', 'type' => 'image', 'value' => "images/service-image-{$imgIndex}.jpg", 'order' => $order++],
                    // Icon disamakan dengan yang ada di Blade: images/icon-service-1.svg, images/icon-service-2.svg, dst.
                    ['key' => $baseKey . '_icon', 'type' => 'image', 'value' => "images/icon-service-{$imgIndex}.svg", 'order' => $order++], 
                    ['key' => $baseKey . '_link', 'type' => 'url', 'value' => '/services', 'order' => $order++],
                ]);

                // Hapus key _active di seeder, karena di Blade tidak di-getContent() tapi menggunakan index loop
                
                foreach ($contents as $content) {
                    $servicesSection->contents()->updateOrCreate(['key' => $content['key']], $content);
                }
            }
        }

        // ========================================
        // 5. SEED LOOPED CONTENT (Project List)
        // ========================================
        $workSection = Section::where('key', 'our-work')->first();
        if ($workSection) {
            $order = 5;
            foreach ($defaultProjects as $index => $project) {
                $idx = $index + 1;
                $baseKey = "project-list-1_project_{$idx}";

                $contents = [
                    ['key' => $baseKey . '_title', 'type' => 'text', 'value' => $project['title'], 'order' => $order++],
                    ['key' => $baseKey . '_desc', 'type' => 'textarea', 'value' => $project['desc'], 'order' => $order++],
                    ['key' => $baseKey . '_link', 'type' => 'url', 'value' => $project['link'], 'order' => $order++],
                    ['key' => $baseKey . '_media_src', 'type' => $project['type'] == 'video' ? 'video' : 'image', 'value' => $project['src'], 'order' => $order++],
                    ['key' => $baseKey . '_media_type', 'type' => 'text', 'value' => $project['type'], 'order' => $order++],
                ];

                foreach ($contents as $content) {
                    $workSection->contents()->updateOrCreate(['key' => $content['key']], $content);
                }
            }
        }
        
        // ========================================
        // 10. SEED LOOPED CONTENT (CTA Counters)
        // ========================================
        $ctaSection = Section::where('key', 'cta')->first();
        if ($ctaSection) {
            $order = 2; // Starts after 'cta_text_content' (order 1 in core definition)
            foreach ($defaultCounters as $index => $counter) {
                $baseKey = "cta-counters_cnt_{$index}";

                $contents = [
                    ['key' => $baseKey . '_num', 'type' => 'number', 'value' => $counter['num'], 'order' => $order++],
                    ['key' => $baseKey . '_sym', 'type' => 'text', 'value' => $counter['sym'], 'order' => $order++],
                    ['key' => $baseKey . '_label', 'type' => 'text', 'value' => $counter['label'], 'order' => $order++],
                ];

                foreach ($contents as $content) {
                    $ctaSection->contents()->updateOrCreate(['key' => $content['key']], $content);
                }
            }
        }

        $this->command->info('✅ All VCMS section content seeded successfully!');
    }
}