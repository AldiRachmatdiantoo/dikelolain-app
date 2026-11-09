<?php

namespace Tests\Feature;

use App\Legacy\LegacyContent;
use App\Legacy\LegacyImporter;
use App\Models\Counter;
use App\Models\Faq;
use App\Models\PageListItem;
use App\Models\PricingPlan;
use App\Models\ProcessStep;
use App\Models\Service;
use App\Models\TeamMember;
use App\Models\Testimonial;
use App\Models\Tool;
use App\Repositories\Contracts\PageSettingRepositoryInterface;
use App\Services\ImageUploadService;
use Database\Seeders\FaqSeeder;
use Database\Seeders\ServiceSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

/**
 * Content of the old site (sections + section_contents) is moved into the new tables by the
 * migration that drops the old tables, with uploads converted to WebP (PBI-520).
 */
class LegacyContentImportTest extends TestCase
{
    use RefreshDatabase;

    private const MIGRATION = 'migrations/2026_11_03_000014_drop_legacy_content_tables.php';

    protected function setUp(): void
    {
        parent::setUp();
        Storage::fake('public');
        Storage::fake('local');
    }

    /**
     * A small but realistic copy of the old site's rows, in creation order.
     *
     * @return array<string, array<string, string>>
     */
    private function oldSite(): array
    {
        return [
            'hero' => [
                'hero-content-1_hero_title' => 'Cuma Bayar <span>Sekali</span>',
                'hero-content-1_btn_link' => 'https://dikelolain.com/contact?from=hero#form',
                'hero-images-1_image_1' => 'https://dikelolain.com/storage/media/images/hero.jpg',
                'hero-video_video_url' => 'https://demo.awaikenthemes.com/assets/videos/rovex-video-1.mp4',
                'hero-images-1_image_current_1' => 'editor bookkeeping',
            ],
            'creative-tools' => [
                'tools_btn_link' => 'javascript:alert(1)',
                'tools-list-1_tool_0_name' => 'Adobe Photoshop',
                'tools-list-1_tool_0_cat' => 'Design Tools',
                'tools-list-1_tool_0_icon' => 'storage/media/images/missing.png',
                // Tool 1 was deleted on the old site; the numbering keeps its gap.
                'tools-list-1_tool_2_name' => 'Adobe Illustrator',
                'tools-list-1_tool_2_cat' => 'Design Tools',
                'tools-list-1_tool_3_name' => 'Capcut PC',
                'tools-list-1_tool_3_cat' => 'Editing Tools',
            ],
            'faqs' => [
                'faq-list-1_general_faq_1_question' => 'Layanan apa saja?',
                'faq-list-1_general_faq_1_answer' => 'Kelola sosmed.',
                'faq-list-1_general_faq_2_question' => 'Berapa lama?',
                'faq-list-1_general_faq_2_answer' => 'Satu bulan.',
                'faq-list-1_support_faq_1_question' => 'Bisa revisi?',
                'faq-list-1_support_faq_1_answer' => 'Bisa.',
            ],
            'testimonials' => [
                'testimonial-list-1_testimonial_a_name' => 'Budi',
                'testimonial-list-1_testimonial_a_content' => 'Mantap.',
                'testimonial-list-1_testimonial_b_name' => '',
                'testimonial-list-1_testimonial_b_content' => 'Tanpa nama, tidak tampil.',
                'client-logos-2_client_logo_count' => '2',
            ],
            'how-it-works' => [
                'process-list-1_step_x_title' => '01. Konsultasi',
                'process-list-1_step_x_desc' => 'Ngobrol dulu.',
                'process-list-1_step_x_active' => '1',
            ],
            'cta' => [
                'cta-counters_cnt_0_num' => '2250',
                'cta-counters_cnt_0_sym' => 'ribu+ views',
            ],
            'our-team' => [
                'team-list-1_member_0_name' => 'HASAN',
                'team-list-1_member_0_role' => 'CEO',
                'team-list-1_member_0_expertise_count' => '2',
                'team-list-1_member_0_expertise_1' => 'Strategi',
                'team-list-1_member_0_expertise_2' => '',
                'team-list-1_member_0_skills_count' => '1',
                'team-list-1_member_0_skill_1_title' => 'Branding',
                'team-list-1_member_0_skill_1_percent' => '90',
                'team-list-1_member_0_ig' => 'https://instagram.com/hasan',
                'team-list-1_member_1_name' => 'HASAN',
                'team-list-1_member_1_role' => 'Editor',
            ],
            'services' => [
                'services-list-1_service_s1_title' => 'Jasa Kelola Sosmed',
                'services-list-1_service_s1_desc_1' => 'Paragraf satu.',
                'services-list-1_service_s1_desc_2' => 'Paragraf dua.',
                'services-list-1_service_s1_fatit' => 'Tanya <span>jawab</span>',
                'services-list-1_service_s1_benefits_title' => 'not a service of its own',
            ],
            'contact' => [
                'contact-section_form_title' => 'Form lama',
                'contact-form_form_title' => 'Form baru',
                'contact-section_phone_value' => '+62 812 0000',
                'contact-section_email_value' => 'halo@dikelolain.com',
            ],
            'footer' => [
                'footer_footer_contact_phone' => '+01 456-852-789',
                'footer_footer_service_1_text' => 'UI/UX Design',
                'footer_footer_service_1_url' => '#',
            ],
            'mystery' => [
                'something_new' => 'never seen before',
            ],
        ];
    }

    /**
     * @param  array<string, array<string, string>>  $sections
     */
    private function content(array $sections): LegacyContent
    {
        $rows = [];
        foreach ($sections as $section => $keys) {
            foreach ($keys as $key => $value) {
                $rows[] = ['section' => $section, 'key' => $key, 'value' => $value];
            }
        }

        return new LegacyContent($rows);
    }

    private function importer(): LegacyImporter
    {
        return app(LegacyImporter::class);
    }

    private function migration(): object
    {
        return require database_path(self::MIGRATION);
    }

    /**
     * Brings back the old tables (empty) and fills them like the old site did.
     *
     * @param  array<string, array<string, string>>  $sections
     * @param  list<string>  $inactive
     */
    private function oldTables(array $sections, array $inactive = []): void
    {
        $this->migration()->down();

        foreach ($sections as $section => $keys) {
            $id = DB::table('sections')->insertGetId([
                'key' => $section,
                'name' => $section,
                'is_active' => !in_array($section, $inactive, true),
                'created_at' => now(),
                'updated_at' => now(),
            ]);
            foreach ($keys as $key => $value) {
                DB::table('section_contents')->insert([
                    'section_id' => $id,
                    'key' => $key,
                    'value' => $value,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }
        }
    }

    private function jpeg(int $width, int $height): string
    {
        $image = imagecreatetruecolor($width, $height);
        for ($x = 0; $x < $width; $x += 10) {
            imagefilledrectangle($image, $x, 0, $x + 9, $height, imagecolorallocate($image, $x % 256, 120, 200));
        }
        ob_start();
        imagejpeg($image, null, 100);

        return ob_get_clean();
    }

    public function test_plan_maps_single_values_and_writes_nothing(): void
    {
        $plan = $this->importer()->plan($this->content($this->oldSite()));

        $this->assertSame('Cuma Bayar <span>Sekali</span>', $plan['settings']['hero.title']);
        // Own-domain links and pictures become relative, so a test domain shows its own copy.
        $this->assertSame('/contact?from=hero#form', $plan['settings']['hero.button_link']);
        $this->assertSame('storage/media/images/hero.jpg', $plan['settings']['hero.image_1']);
        // The demo video is replaced by the copy this version ships.
        $this->assertArrayNotHasKey('hero.video', $plan['settings']);
        // A link the new validation would refuse keeps the default.
        $this->assertArrayNotHasKey('tools.button_link', $plan['settings']);
        // The newer contact-form keys win over the older contact-section ones.
        $this->assertSame('Form baru', $plan['settings']['contact.form_title']);
        $this->assertSame('+62 812 0000', $plan['settings']['contact.phone']);
        // Shared service settings come from the first service.
        $this->assertSame('Tanya <span>jawab</span>', $plan['settings']['services.faq.title']);

        $notes = implode("\n", $plan['notes']);
        $this->assertStringContainsString('Footer phone "+01 456-852-789"', $notes);
        $this->assertStringContainsString('our services', $notes);
        $this->assertStringContainsString('Video hero', $notes);
        $this->assertStringContainsString('javascript:alert(1)', $notes);

        $this->assertSame(0, Tool::count());
        $this->assertSame([], app(PageSettingRepositoryInterface::class)->all());
    }

    public function test_plan_reports_unknown_keys_and_skips_editor_bookkeeping(): void
    {
        $plan = $this->importer()->plan($this->content($this->oldSite()));

        // Keys the importer does not move are listed for a manual check, never silently lost.
        $this->assertSame([
            'services' => ['services-list-1_service_s1_benefits_title'],
            'mystery' => ['something_new'],
        ], $plan['unknown']);
        $this->assertContains('hero-images-1_image_current_1', $plan['skipped']['hero']);
        $this->assertContains('process-list-1_step_x_active', $plan['skipped']['how-it-works']);
        $this->assertContains('team-list-1_member_0_ig', $plan['skipped']['our-team']);
        $this->assertContains('client-logos-2_client_logo_count', $plan['skipped']['testimonials']);
    }

    public function test_only_active_sections_are_read_and_the_first_row_wins(): void
    {
        $this->oldTables([
            'hero' => ['hero-content-1_hero_title' => 'Pertama'],
            'about-us' => ['about-us-1_main_title' => 'Section mati'],
        ], inactive: ['about-us']);
        DB::table('section_contents')->insert([
            'section_id' => DB::table('sections')->where('key', 'hero')->value('id'),
            'key' => 'hero-content-1_hero_title',
            'value' => 'Kedua',
        ]);

        $old = LegacyContent::fromDatabase();

        $this->assertSame('Pertama', $old->value('hero', 'hero-content-1_hero_title'));
        $this->assertFalse($old->hasSection('about-us'));
    }

    public function test_lists_keep_their_order_and_drop_what_the_old_pages_hid(): void
    {
        $importer = $this->importer();
        $result = $importer->apply($importer->plan($this->content($this->oldSite())));

        $this->assertSame(['Adobe Photoshop', 'Adobe Illustrator', 'Capcut PC'], Tool::orderBy('sort_order')->pluck('name')->all());
        $this->assertSame([1, 2, 3], Tool::orderBy('sort_order')->pluck('sort_order')->all());

        // FAQ order is counted per category; a category without questions stays empty.
        $this->assertSame([1, 2], Faq::where('category', 'general')->orderBy('sort_order')->pluck('sort_order')->all());
        $this->assertSame(['Bisa revisi?'], Faq::where('category', 'support')->pluck('question')->all());
        $this->assertSame(0, Faq::where('category', 'technical')->count());

        // A testimonial without a name never showed.
        $this->assertSame(['Budi'], Testimonial::pluck('name')->all());

        $this->assertSame('Konsultasi', ProcessStep::value('title'));

        // Three counters always; the long suffix of the live site fits.
        $this->assertSame(3, Counter::count());
        $this->assertSame(['2250', 'ribu+ views'], [Counter::orderBy('sort_order')->value('number'), Counter::orderBy('sort_order')->value('suffix')]);

        // Team: blank expertise items (shown as "blank") are dropped, duplicate names get unique slugs.
        $hasan = TeamMember::where('slug', 'hasan')->firstOrFail();
        $this->assertSame(['Strategi'], $hasan->expertises()->pluck('name')->all());
        $this->assertSame([['Branding', 90]], $hasan->skills()->get()->map(fn ($skill) => [$skill->name, $skill->percent])->all());
        $this->assertTrue(TeamMember::where('slug', 'hasan-2')->exists());

        // Services: "_benefits" keys are not a service; empty benefits and FAQs get the old defaults.
        $service = Service::sole();
        $this->assertSame('jasa-kelola-sosmed', $service->slug);
        $this->assertSame("Paragraf satu.\n\nParagraf dua.", $service->description);
        $this->assertSame(count(ServiceSeeder::BENEFITS), $service->benefits()->count());
        $this->assertSame(count(ServiceSeeder::FAQS), $service->faqs()->count());

        // Lists the old site never stored show the demo rows it showed.
        $this->assertSame(3, PricingPlan::count());
        $this->assertSame(['about', 'mission', 'pricing_benefits', 'vision'], PageListItem::distinct()->orderBy('list_key')->pluck('list_key')->all());

        $this->assertSame(1, $result['counts']['services']);
    }

    public function test_settings_are_stored_and_rich_text_is_cleaned(): void
    {
        $importer = $this->importer();
        $importer->apply($importer->plan($this->content([
            'hero' => ['hero-content-1_hero_title' => 'Halo <span>dunia</span><script>alert(1)</script>'],
            'about-us' => ['about-us-1_sub_title' => ''],
        ])));

        $settings = app(PageSettingRepositoryInterface::class)->all();
        // Tags outside the allowed list are removed (their text stays, as plain text).
        $this->assertSame('Halo <span>dunia</span>alert(1)', $settings['hero.title']);
        // An empty value means the admin cleared the field on the old site; it stays empty.
        $this->assertSame('', $settings['about.sub_title']);
    }

    public function test_lists_the_old_site_never_stored_get_the_demo_rows(): void
    {
        $importer = $this->importer();
        $plan = $importer->plan($this->content(['hero' => ['hero-content-1_hero_title' => 'Halo']]));

        $this->assertSame(['seed' => FaqSeeder::class], $plan['lists']['faqs']);
        $this->assertSame([], $plan['lists']['team_members']['rows']);
        $this->assertSame([], $plan['lists']['projects']['rows']);

        $importer->apply($plan);

        $this->assertGreaterThan(0, Faq::count());
        $this->assertGreaterThan(0, Tool::count());
        $this->assertSame(0, TeamMember::count());
    }

    public function test_uploaded_pictures_become_webp_and_others_stay_as_they_are(): void
    {
        Storage::disk('public')->put('media/images/hero.jpg', $this->jpeg(2400, 1200));
        Storage::disk('public')->put('media/images/tool.jpg', $this->jpeg(400, 400));

        $importer = $this->importer();
        $result = $importer->apply($importer->plan($this->content([
            'hero' => [
                'hero-images-1_image_1' => 'https://dikelolain.com/storage/media/images/hero.jpg',
                // The same upload used twice is converted once.
                'hero-images-1_image_2' => 'storage/media/images/hero.jpg',
                'hero-images-1_image_3' => 'images/hero-image-3.jpg',
            ],
            'creative-tools' => [
                'tools-list-1_tool_0_name' => 'Photoshop',
                'tools-list-1_tool_0_icon' => 'storage/media/images/tool.jpg',
                'tools-list-1_tool_1_name' => 'Hilang',
                'tools-list-1_tool_1_icon' => 'storage/media/images/missing.png',
            ],
        ])));

        $settings = app(PageSettingRepositoryInterface::class)->all();
        $this->assertSame('storage/uploads/legacy/hero.webp', $settings['hero.image_1']);
        $this->assertSame('storage/uploads/legacy/hero.webp', $settings['hero.image_2']);
        $this->assertSame('images/hero-image-3.jpg', $settings['hero.image_3']);

        [$width, , $type] = getimagesizefromstring(Storage::disk('public')->get('uploads/legacy/hero.webp'));
        $this->assertSame(IMAGETYPE_WEBP, $type);
        $this->assertSame(2000, $width);

        $this->assertSame(['storage/uploads/legacy/tool.webp', 'storage/media/images/missing.png'], Tool::orderBy('sort_order')->pluck('icon')->all());
        $this->assertSame(2, $result['webp']);
        $this->assertSame(['storage/media/images/missing.png'], $result['kept']);
        // The originals stay where they were.
        Storage::disk('public')->assertExists('media/images/hero.jpg');
    }

    public function test_convert_stored_only_handles_files_on_the_public_disk(): void
    {
        $uploads = app(ImageUploadService::class);
        Storage::disk('public')->put('media/images/a.jpg', $this->jpeg(300, 200));
        Storage::disk('public')->put('media/images/notes.txt', 'not a picture');

        $this->assertSame('storage/uploads/legacy/a.webp', $uploads->convertStored('storage/media/images/a.jpg', 'legacy'));
        $this->assertNull($uploads->convertStored('images/a.jpg', 'legacy'));
        $this->assertNull($uploads->convertStored('storage/media/images/none.jpg', 'legacy'));
        $this->assertNull($uploads->convertStored('storage/media/images/notes.txt', 'legacy'));
    }

    public function test_migration_moves_the_old_content_backs_it_up_and_drops_the_old_tables(): void
    {
        $this->oldTables($this->oldSite());
        Storage::disk('public')->put('media/images/hero.jpg', $this->jpeg(800, 400));

        $this->migration()->up();

        foreach (['sections', 'section_contents', 'media'] as $table) {
            $this->assertFalse(Schema::hasTable($table), "{$table} still exists");
        }
        $this->assertSame('Cuma Bayar <span>Sekali</span>', app(PageSettingRepositoryInterface::class)->all()['hero.title']);
        $this->assertSame(3, Tool::count());

        $files = Storage::disk('local')->files('backups');
        $this->assertCount(2, $files);
        $backup = json_decode(Storage::disk('local')->get(collect($files)->first(fn ($file) => str_ends_with($file, '.json'))), true);
        $this->assertCount(count($this->oldSite()), $backup['sections']);
        $this->assertSame('Cuma Bayar <span>Sekali</span>', $backup['section_contents'][0]['value']);

        $report = Storage::disk('local')->get(collect($files)->first(fn ($file) => str_ends_with($file, '.txt')));
        $this->assertStringContainsString('Hasil import konten web lama', $report);
        $this->assertStringContainsString('Gambar unggahan diubah ke WebP: 1', $report);
        $this->assertStringContainsString('mystery: something_new', $report);
    }

    public function test_migration_on_a_database_without_old_content_only_drops_the_tables(): void
    {
        $this->oldTables([]);

        $this->migration()->up();

        $this->assertFalse(Schema::hasTable('section_contents'));
        $this->assertSame([], Storage::disk('local')->allFiles());
        $this->assertSame(0, Tool::count());
    }

    public function test_report_command_shows_the_plan_without_writing(): void
    {
        $this->oldTables($this->oldSite());

        $this->assertSame(0, Artisan::call('legacy:report'));
        $output = Artisan::output();

        $this->assertStringContainsString('Rencana import konten web lama (belum ditulis)', $output);
        $this->assertStringContainsString('hero.title', $output);
        $this->assertStringContainsString('Adobe Illustrator', $output);
        $this->assertStringContainsString('mystery: something_new', $output);
        $this->assertTrue(Schema::hasTable('section_contents'));
        $this->assertSame(0, Tool::count());
        $this->assertSame([], Storage::disk('local')->allFiles());
    }

    public function test_report_command_on_a_migrated_database_says_there_is_nothing(): void
    {
        $this->artisan('legacy:report')
            ->expectsOutputToContain('Tidak ada konten web lama')
            ->assertSuccessful();
    }
}
