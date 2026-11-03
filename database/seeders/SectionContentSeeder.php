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
        
        // Data Default untuk Creative Tools (where $defaultTools comes from)
        $defaultTools = [
            ['name' => 'figma', 'cat' => 'Design tool', 'icon' => 'images/icon-creative-tools-1.svg'],
            ['name' => 'framer', 'cat' => 'Design tool', 'icon' => 'images/icon-creative-tools-2.svg'],
            ['name' => 'illustrator', 'cat' => 'Design tool', 'icon' => 'images/icon-creative-tools-3.svg'],
            ['name' => 'adobe xd', 'cat' => 'Design tool', 'icon' => 'images/icon-creative-tools-4.svg'],
            ['name' => 'indesign', 'cat' => 'Design tool', 'icon' => 'images/icon-creative-tools-5.svg'],
            ['name' => 'photoshop', 'cat' => 'Design tool', 'icon' => 'images/icon-creative-tools-6.svg'],
            ['name' => 'lightroom', 'cat' => 'Design tool', 'icon' => 'images/icon-creative-tools-7.svg'],
            ['name' => 'incopy', 'cat' => 'Design tool', 'icon' => 'images/icon-creative-tools-8.svg'],
        ];

        // Data Default untuk Section How It Works
        $defaultProcesses = [
            ['title' => 'Discovery & Strategy', 'desc' => 'We begin by getting to know you and your brand. Through in-depth conversations, research, and analysis.', 'active' => 'show'],
            ['title' => 'Concept Development', 'desc' => 'We begin by getting to know you and your brand. Through in-depth conversations, research, and analysis.', 'active' => ''],
            ['title' => 'Design & Iteration', 'desc' => 'We begin by getting to know you and your brand. Through in-depth conversations, research, and analysis.', 'active' => ''],
            ['title' => 'Development & Execution', 'desc' => 'We begin by getting to know you and your brand. Through in-depth conversations, research, and analysis.', 'active' => ''],
        ];

        // Data Default untuk Section Achievements
        $defaultAchievements = [
            ['year' => '2015 - 2016', 'name' => 'Best Design Award', 'desc' => 'We begin by getting to know you and your brand. Through in-depth.'],
            ['year' => '2016 - 2017', 'name' => 'Dribble Winner', 'desc' => 'We begin by getting to know you and your brand. Through in-depth.'],
            ['year' => '2017 - 2018', 'name' => 'Design Of The Year', 'desc' => 'We begin by getting to know you and your brand. Through in-depth.'],
            ['year' => '2017 - 2018', 'name' => 'Graphic Design Winner', 'desc' => 'We begin by getting to know you and your brand. Through in-depth.'],
            ['year' => '2018 - 2019', 'name' => 'Awward Winner', 'desc' => 'We begin by getting to know you and your brand. Through in-depth.'],
            ['year' => '2019 - 2020', 'name' => 'Best Jury Award', 'desc' => 'We begin by getting to know you and your brand. Through in-depth.'],
            ['year' => '2019 - 2020', 'name' => 'Creative Excellence', 'desc' => 'We begin by getting to know you and your brand. Through in-depth.'], // Tambahkan 1 lagi agar ada 7 achievement
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
            // 5. CREATIVE TOOLS SECTION (Order 5)
            [
                'key' => 'creative-tools', 'name' => 'Creative Tools', 'order' => 5,
                'contents' => [
                    ['key' => 'tools_sub_title', 'type' => 'text', 'value' => 'creative toolkit', 'order' => 1], // KEY CHANGE: tools-title_sub_title -> tools_sub_title
                    ['key' => 'tools_main_title', 'type' => 'text', 'value' => 'Our toolbox for <span>innovation</span>', 'order' => 2], // KEY CHANGE: tools-title_main_title -> tools_main_title
                    ['key' => 'tools_btn_text', 'type' => 'text', 'value' => 'let\'s get started !', 'order' => 3], // KEY CHANGE: tools-btn_button_text -> tools_btn_text
                    ['key' => 'tools_btn_link', 'type' => 'url', 'value' => '/contact', 'order' => 4], // KEY CHANGE: tools-btn_button_link -> tools_btn_link
                ]
            ],
            // 6. HOW IT WORKS SECTION (Order 6)
            [
                'key' => 'how-it-works', 'name' => 'How It Works', 'order' => 6,
                'contents' => [
                    ['key' => 'how_sub_title', 'type' => 'text', 'value' => 'how we work', 'order' => 1], // KEY CHANGE: how-title_sub_title -> how_sub_title
                    ['key' => 'how_main_title', 'type' => 'text', 'value' => 'Our methodology for <span>success</span>', 'order' => 2], // KEY CHANGE: how-title_main_title -> how_main_title
                    
                    ['key' => 'how-cta-btn_image_file', 'type' => 'image', 'value' => 'images/contact-circle-yellow.png', 'order' => 3],
                    ['key' => 'how-cta-btn_link_url', 'type' => 'url', 'value' => '/contact', 'order' => 4],

                    ['key' => 'how-images_circle_deco', 'type' => 'image', 'value' => 'images/how-work-image-circle.png', 'order' => 5],
                    ['key' => 'how-images_main_image', 'type' => 'image', 'value' => 'images/how-it-work-image.jpg', 'order' => 6],
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
            // 8. ACHIEVEMENTS SECTION (Order 8)
            [
                'key' => 'achievements', 'name' => 'Achievements', 'order' => 8,
                'contents' => [
                    ['key' => 'achievements_sub_title', 'type' => 'text', 'value' => 'our achievements', 'order' => 1], // KEY CHANGE: achievements-title_sub_title -> achievements_sub_title
                    ['key' => 'achievements_main_title', 'type' => 'text', 'value' => 'Proud moments & <span>milestones</span>', 'order' => 2], // KEY CHANGE: achievements-title_main_title -> achievements_main_title
                    ['key' => 'achievements_description', 'type' => 'textarea', 'value' => 'We\'re a full-service design agency specializing in branding, web design, and creative strategies that elevate businesses.', 'order' => 3], // KEY CHANGE: achievements-title_description -> achievements_description
                    ['key' => 'achievements_btn_text', 'type' => 'text', 'value' => 'let\'s get started !', 'order' => 4], // KEY CHANGE: achievements-btn_button_text -> achievements_btn_text
                    ['key' => 'achievements_btn_link', 'type' => 'url', 'value' => '/contact', 'order' => 5], // KEY CHANGE: achievements-btn_button_link -> achievements_btn_link
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
        // 6. SEED LOOPED CONTENT (Tools List)
        // ========================================
        $toolsSection = Section::where('key', 'creative-tools')->first();
        if ($toolsSection) {
            $order = 5;
            foreach ($defaultTools as $index => $tool) {
                $baseKey = "tools-list-1_tool_{$index}";

                $contents = [
                    ['key' => $baseKey . '_name', 'type' => 'text', 'value' => $tool['name'], 'order' => $order++],
                    ['key' => $baseKey . '_cat', 'type' => 'text', 'value' => $tool['cat'], 'order' => $order++],
                    ['key' => $baseKey . '_icon', 'type' => 'image', 'value' => $tool['icon'], 'order' => $order++],
                ];

                foreach ($contents as $content) {
                    $toolsSection->contents()->updateOrCreate(['key' => $content['key']], $content);
                }
            }
        }

        // ========================================
        // 7. SEED LOOPED CONTENT (How It Works Process List)
        // ========================================
        $howSection = Section::where('key', 'how-it-works')->first();
        if ($howSection) {
            $order = 7;
            foreach ($defaultProcesses as $index => $process) {
                $baseKey = "process-list-1_step_{$index}";
                $processTitle = ($index < 9 ? '0' . ($index + 1) : $index + 1) . '. ' . $process['title']; // Format title: 01. Discovery & Strategy

                $contents = [
                    ['key' => $baseKey . '_title', 'type' => 'text', 'value' => $processTitle, 'order' => $order++], // Diberi prefix nomor di seeder
                    ['key' => $baseKey . '_desc', 'type' => 'textarea', 'value' => $process['desc'], 'order' => $order++],
                    ['key' => $baseKey . '_active', 'type' => 'text', 'value' => $process['active'], 'order' => $order++],
                ];

                foreach ($contents as $content) {
                    $howSection->contents()->updateOrCreate(['key' => $content['key']], $content);
                }
            }
        }
        
        // ========================================
        // 8. SEED LOOPED CONTENT (Achievement List)
        // ========================================
        $achievementsSection = Section::where('key', 'achievements')->first();
        if ($achievementsSection) {
            $order = 6;
            // Gunakan $defaultAchievements yang sudah diubah di bagian atas jika Anda ingin 7 item.
            // Jika Anda ingin tetap 6 item seperti aslinya, gunakan $defaultAchievements yang lama.
            // Saya asumsikan Anda ingin menggunakan array $defaultAchievements yang telah didefinisikan.
            foreach ($defaultAchievements as $index => $ach) {
                $baseKey = "achievement-list-1_ach_{$index}";

                $contents = [
                    ['key' => $baseKey . '_year', 'type' => 'text', 'value' => $ach['year'], 'order' => $order++],
                    ['key' => $baseKey . '_name', 'type' => 'text', 'value' => $ach['name'], 'order' => $order++],
                    ['key' => $baseKey . '_desc', 'type' => 'textarea', 'value' => $ach['desc'], 'order' => $order++],
                ];

                foreach ($contents as $content) {
                    $achievementsSection->contents()->updateOrCreate(['key' => $content['key']], $content);
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