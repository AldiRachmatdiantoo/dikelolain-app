<?php

namespace App\Legacy;

use App\Models\Achievement;
use App\Models\ClientLogo;
use App\Models\Counter;
use App\Models\Faq;
use App\Models\PageListItem;
use App\Models\PricingPlan;
use App\Models\ProcessStep;
use App\Models\Project;
use App\Models\Service;
use App\Models\TeamMember;
use App\Models\Testimonial;
use App\Models\Tool;
use App\Repositories\Contracts\PageSettingRepositoryInterface;
use App\Services\ImageUploadService;
use App\Support\RichText;
use Database\Seeders\AchievementSeeder;
use Database\Seeders\ClientLogoSeeder;
use Database\Seeders\FaqSeeder;
use Database\Seeders\ProcessStepSeeder;
use Database\Seeders\ServiceSeeder;
use Database\Seeders\TestimonialSeeder;
use Database\Seeders\ToolSeeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

/**
 * Moves the content of the old site (sections + section_contents) into the tables of this
 * version, so every page shows what the old page showed. It runs once per database, from the
 * migration that drops the old tables; `php artisan legacy:report` shows the plan beforehand.
 *
 * - A value the old site stored wins. A missing value gets the default the old page showed.
 * - A list the old site never stored gets the demo items it showed (the seeders), except the
 *   team and projects, where the old pages said "No ... yet".
 * - Links and pictures on the old domain become relative, so the copy works on a test domain.
 * - Uploaded JPG/PNG pictures become WebP like new uploads (when applied, not in the plan).
 */
final class LegacyImporter
{
    /** Absolute links to these hosts become relative. */
    private const OLD_HOSTS = ['dikelolain.com', 'www.dikelolain.com'];

    /** The demo video the old site streamed from the template's server; this version ships a copy. */
    private const DEMO_VIDEO = 'https://demo.awaikenthemes.com/assets/videos/rovex-video-1.mp4';

    /** Single values: old section => old key => setting. Contact and footer need more care, see settings(). */
    private const SETTINGS = [
        'hero' => [
            'hero-content-1_hero_title' => 'hero.title',
            'hero-content-1_hero_description' => 'hero.description',
            'hero-content-1_btn_text' => 'hero.button_text',
            'hero-content-1_btn_link' => 'hero.button_link',
            'hero-images-1_image_1' => 'hero.image_1',
            'hero-images-1_image_2' => 'hero.image_2',
            'hero-images-1_image_3' => 'hero.image_3',
            'hero-images-1_image_4' => 'hero.image_4',
            'hero-images-1_image_5' => 'hero.image_5',
            'hero-video_video_url' => 'hero.video',
        ],
        'services' => [
            'services_sub_title' => 'services.sub_title',
            'services_main_title' => 'services.title',
            'services_btn_text' => 'services.button_text',
            'services_btn_link' => 'services.button_link',
        ],
        'services-header' => [
            'services-header-1_title' => 'services.header.title',
            'services-header-1_breadcrumb' => 'services.header.breadcrumb',
        ],
        'our-work' => [
            'work_sub_title' => 'work.sub_title',
            'work_main_title' => 'work.title',
            'work_btn_text' => 'work.button_text',
            'work_btn_link' => 'work.button_link',
        ],
        'portfolio-header' => [
            'portfolio-header-1_title' => 'portfolio.header.title',
            'portfolio-header-1_breadcrumb' => 'portfolio.header.breadcrumb',
        ],
        'creative-tools' => [
            'tools_sub_title' => 'tools.sub_title',
            'tools_main_title' => 'tools.title',
            'tools_btn_text' => 'tools.button_text',
            'tools_btn_link' => 'tools.button_link',
        ],
        'how-it-works' => [
            'how_sub_title' => 'process.sub_title',
            'how_main_title' => 'process.title',
            'how-cta-btn_image_file' => 'process.cta_image',
            'how-cta-btn_link_url' => 'process.cta_link',
            'how-images_main_image' => 'process.image',
            'how-images_circle_deco' => 'process.circle_image',
        ],
        'our-team' => [
            'team_sub_title' => 'team.sub_title',
            'team_main_title' => 'team.title',
            'team_btn_text' => 'team.button_text',
            'team_btn_link' => 'team.button_link',
        ],
        'team-header' => [
            'team-header-1_title' => 'team.header.title',
            'team-header-1_breadcrumb' => 'team.header.breadcrumb',
        ],
        'achievements' => [
            'achievements_sub_title' => 'achievements.sub_title',
            'achievements_main_title' => 'achievements.title',
            'achievements_description' => 'achievements.description',
            'achievements_btn_text' => 'achievements.button_text',
            'achievements_btn_link' => 'achievements.button_link',
        ],
        'cta' => [
            'cta_text_content' => 'cta.text',
            'cta-circle-img_image_file' => 'cta.circle_image',
            'cta-circle-img_link_url' => 'cta.circle_link',
        ],
        'testimonials' => [
            'testimonials_sub_title' => 'clients.sub_title',
            'testimonials_main_title' => 'clients.title',
            'trusted_section_title_text' => 'clients.box_title',
        ],
        'testimonials-header' => [
            'testimonials-header-1_title' => 'testimonials.header.title',
            'testimonials-header-1_breadcrumb' => 'testimonials.header.breadcrumb',
        ],
        'about-header' => [
            'about-header-1_title' => 'about.header.title',
            'about-header-1_breadcrumb' => 'about.header.breadcrumb',
        ],
        'about-us' => [
            'about-us-1_image' => 'about.image',
            'about-us-1_sub_title' => 'about.sub_title',
            'about-us-1_main_title' => 'about.title',
            'about-us-1_description' => 'about.description',
        ],
        'vision-mission' => [
            'vision-mission-1_vision_sub_title' => 'vision.sub_title',
            'vision-mission-1_vision_title' => 'vision.title',
            'vision-mission-1_vision_description' => 'vision.description',
            'vision-mission-1_vision_image' => 'vision.image',
            'vision-mission-1_mission_sub_title' => 'mission.sub_title',
            'vision-mission-1_mission_title' => 'mission.title',
            'vision-mission-1_mission_description' => 'mission.description',
            'vision-mission-1_mission_image' => 'mission.image',
        ],
        'pricing-header' => [
            'pricing-header-1_title' => 'pricing.header.title',
            'pricing-header-1_breadcrumb' => 'pricing.header.breadcrumb',
        ],
        'faqs-header' => [
            'faqs-header-1_title' => 'faqs.header.title',
            'faqs-header-1_breadcrumb' => 'faqs.header.breadcrumb',
        ],
        'faqs' => [
            'faq-category_general_title' => 'faqs.category.general',
            'faq-category_technical_title' => 'faqs.category.technical',
            'faq-category_project_title' => 'faqs.category.project',
            'faq-category_support_title' => 'faqs.category.support',
            'faq-sidebar-cta_text' => 'faqs.cta.text',
            'faq-sidebar-cta_email' => 'faqs.cta.email',
            'faq-sidebar-cta_btn_text' => 'faqs.cta.button_text',
        ],
        'contact' => [
            'contact-section_sub_title' => 'contact.sub_title',
            'contact-section_main_title' => 'contact.title',
            'contact-section_phone_icon' => 'contact.phone_icon',
            'contact-section_phone_label' => 'contact.phone_label',
            'contact-section_email_icon' => 'contact.email_icon',
            'contact-section_email_label' => 'contact.email_label',
            'contact-section_address_icon' => 'contact.address_icon',
            'contact-section_address_label' => 'contact.address_label',
        ],
        'contact-header' => [
            'contact-header-1_breadcrumb' => 'contact.header.breadcrumb',
        ],
        'footer' => [
            'footer_footer_title' => 'footer.title',
            'footer_footer_title_link' => 'footer.title_link',
            'footer_footer_logo_file' => 'footer.logo',
            'footer_footer_description' => 'footer.description',
            'footer_footer_social_fb' => 'footer.facebook',
            'footer_footer_social_x' => 'footer.x',
            'footer_footer_social_li' => 'footer.linkedin',
            'footer_footer_social_ig' => 'footer.instagram',
            'footer_footer_quick_1_text' => 'footer.quick_1_text',
            'footer_footer_quick_1_url' => 'footer.quick_1_url',
            'footer_footer_quick_2_text' => 'footer.quick_2_text',
            'footer_footer_quick_2_url' => 'footer.quick_2_url',
            'footer_footer_quick_3_text' => 'footer.quick_3_text',
            'footer_footer_quick_3_url' => 'footer.quick_3_url',
            'footer_footer_quick_4_text' => 'footer.quick_4_text',
            'footer_footer_quick_4_url' => 'footer.quick_4_url',
            'footer_footer_copyright' => 'footer.copyright',
        ],
        'analytics' => [
            'looker_iframe_url' => 'analytics.looker_url',
            'looker_iframe_height' => 'analytics.looker_height',
        ],
    ];

    /** Columns that hold a picture, per list (converted to WebP when applied). */
    private const PICTURES = [
        'testimonials' => ['logo', 'photo'],
        'client_logos' => ['logo'],
        'tools' => ['icon'],
        'team_members' => ['photo'],
        'services' => ['image', 'icon', 'feature_image'],
        'projects' => ['image'],
        'page_list_items' => ['icon'],
    ];

    /** Models per list, in the order they are written. */
    private const MODELS = [
        'faqs' => Faq::class,
        'testimonials' => Testimonial::class,
        'client_logos' => ClientLogo::class,
        'tools' => Tool::class,
        'process_steps' => ProcessStep::class,
        'achievements' => Achievement::class,
        'counters' => Counter::class,
        'team_members' => TeamMember::class,
        'services' => Service::class,
        'projects' => Project::class,
        'pricing_plans' => PricingPlan::class,
        'page_list_items' => PageListItem::class,
    ];

    /** @var list<string> */
    private array $notes = [];

    /** @var array<string, string|null> old value => new value */
    private array $converted = [];

    /** @var array<string, array<string, int>> last sort_order per list and group */
    private array $order = [];

    public function __construct(
        private readonly PageSettingRepositoryInterface $settings,
        private readonly ImageUploadService $uploads,
    ) {
    }

    /**
     * What would be written, without touching the database.
     *
     * @return array{settings: array<string, string>, lists: array<string, array{seed?: class-string, rows?: list<array<string, mixed>>}>, notes: list<string>, skipped: array<string, list<string>>, unknown: array<string, list<string>>}
     */
    public function plan(LegacyContent $old): array
    {
        $this->notes = [];
        $this->skipBookkeeping($old);

        $lists = [
            'faqs' => $this->faqs($old),
            'testimonials' => $this->testimonials($old),
            'client_logos' => $this->clientLogos($old),
            'tools' => $this->tools($old),
            'process_steps' => $this->processSteps($old),
            'achievements' => $this->achievements($old),
            'counters' => $this->counters($old),
            'team_members' => $this->team($old),
            'services' => $this->services($old, $serviceSettings),
            'projects' => $this->projects($old),
            'pricing_plans' => $this->pricingPlans($old),
            'page_list_items' => $this->pageLists($old),
        ];

        return [
            'settings' => $this->settings($old) + $serviceSettings,
            'lists' => $lists,
            'notes' => $this->notes,
            'skipped' => $old->skippedKeys(),
            'unknown' => $old->unknownKeys(),
        ];
    }

    /**
     * Writes the plan in one transaction. The target tables must be empty (a fresh migration).
     *
     * @param  array{settings: array<string, string>, lists: array<string, array<string, mixed>>}  $plan
     * @return array{counts: array<string, int>, webp: int, kept: list<string>}
     */
    public function apply(array $plan): array
    {
        $this->converted = [];
        $this->order = [];

        DB::transaction(function () use ($plan) {
            foreach ($plan['settings'] as $key => $value) {
                $type = $this->settingType($key);
                if (in_array($type, ['image', 'video'], true)) {
                    $value = $this->picture($value);
                } elseif ($type === 'rich_text') {
                    $value = RichText::clean($value);
                }
                // put() keeps an empty string, which shows as empty (the admin cleared the field).
                $this->settings->put($key, $value);
            }

            foreach ($plan['lists'] as $list => $entry) {
                if (isset($entry['seed'])) {
                    app($entry['seed'])->run();
                    continue;
                }
                foreach ($entry['rows'] as $row) {
                    $this->createRow($list, $row);
                }
            }
        });

        $counts = [];
        foreach (self::MODELS as $list => $model) {
            $counts[$list] = $model::count();
        }
        $counts['page_settings'] = count($plan['settings']);

        return [
            'counts' => $counts,
            'webp' => count(array_filter($this->converted, fn ($new, $old) => $new !== $old, ARRAY_FILTER_USE_BOTH)),
            'kept' => array_keys(array_filter($this->converted, fn ($new, $old) => $new === $old, ARRAY_FILTER_USE_BOTH)),
        ];
    }

    /**
     * Plans and applies, and returns a readable report of both.
     */
    public function import(LegacyContent $old): string
    {
        $plan = $this->plan($old);
        $result = $this->apply($plan);

        return LegacyReport::render($plan, $result);
    }

    // ---------------------------------------------------------------- single values

    /**
     * @return array<string, string>
     */
    private function settings(LegacyContent $old): array
    {
        $values = [];
        foreach (self::SETTINGS as $section => $keys) {
            foreach ($keys as $oldKey => $key) {
                $this->setting($values, $key, $old->value($section, $oldKey));
            }
        }

        // The contact page preferred the newer "contact-form" keys when they were filled.
        $this->setting($values, 'contact.form_title', $old->firstFilled('contact', 'contact-form_form_title', 'contact-section_form_title'));
        $this->setting($values, 'contact.form_button', $old->firstFilled('contact', 'contact-form_form_btn_text', 'contact-section_form_btn_text'));
        $this->setting($values, 'contact.whatsapp', $old->firstFilled('contact', 'contact-form_whatsapp_number', 'contact-section_whatsapp_number'));
        $this->setting($values, 'contact.map_url', $old->firstFilled('contact-map', 'contact-map_embed_url', 'contact-map-embed_url'));

        $title = $old->value('contact-header', 'contact-header-1_title');
        if ($title === null || $title === '') {
            $title = $old->value('contact', 'contact-header-1_title') ?? $title;
        }
        $this->setting($values, 'contact.header.title', $title);

        // The footer had its own phone, email and address; this version shows the contact page's.
        foreach (['phone' => 'phone', 'email' => 'email', 'address' => 'address'] as $part => $key) {
            $page = $old->value('contact', "contact-section_{$part}_value");
            $footer = $old->value('footer', "footer_footer_contact_{$part}");
            if ($page !== null) {
                $this->setting($values, "contact.{$key}", $page);
            }
            if ($footer !== null && $footer !== $page) {
                $this->notes[] = $page === null
                    ? "Footer {$part} \"{$footer}\" diganti nilai halaman Contact (footer dan Contact kini satu isian)."
                    : "Footer {$part} \"{$footer}\" berbeda dari halaman Contact \"{$page}\"; yang dipakai nilai halaman Contact.";
            }
        }

        $serviceLinks = $old->numbered('footer', '/^footer_footer_service_(\d+)_(?:text|url)$/');
        if ($serviceLinks !== []) {
            $this->notes[] = 'Kolom "our services" di footer kini diisi otomatis dari empat layanan pertama, jadi teks dan link layanan footer yang lama tidak dipindah.';
        }

        if (($values['hero.video'] ?? null) === self::DEMO_VIDEO) {
            unset($values['hero.video']);
            $this->notes[] = 'Video hero dari server demo template diganti salinannya di proyek (/videos/hero.mp4), videonya sama.';
        }

        return $values;
    }

    /**
     * @param  array<string, string>  $values
     */
    private function setting(array &$values, string $key, ?string $value): void
    {
        if ($value === null) {
            return;
        }

        $type = $this->settingType($key);
        $value = in_array($type, ['image', 'video'], true) ? $this->localPath($value) : $this->localLink($value, $type);

        if ($value !== '' && !$this->validSetting($type, $value)) {
            $this->notes[] = "Nilai \"{$value}\" untuk {$key} tidak dipindah karena formatnya tidak diterima; yang tampil nilai bawaan.";

            return;
        }

        $values[$key] = $value;
    }

    private function settingType(string $key): string
    {
        $settings = config('page_settings');

        return $settings[$key]['type'] ?? 'text';
    }

    private function validSetting(string $type, string $value): bool
    {
        return match ($type) {
            'url' => (bool) preg_match('#^(https?://|/|\#|mailto:|tel:)#i', $value),
            'email' => filter_var($value, FILTER_VALIDATE_EMAIL) !== false,
            'video' => (bool) preg_match('#^(https?://|/|storage/)#i', $value),
            default => true,
        };
    }

    // ---------------------------------------------------------------- lists

    /**
     * Questions per category. Without any question the old page showed the demo FAQs (seeded here);
     * a single category without questions showed its demo questions, which is left empty and noted.
     */
    private function faqs(LegacyContent $old): array
    {
        if (!$old->hasSection('faqs') || $old->ids('faqs', '/^faq-list-1_[a-z]+_faq_(.+)_question$/') === []) {
            return ['seed' => FaqSeeder::class];
        }

        $rows = [];
        foreach (array_keys(Faq::CATEGORIES) as $category) {
            $ids = $old->ids('faqs', '/^faq-list-1_' . $category . '_faq_(.+)_question$/');
            if ($ids === []) {
                $this->notes[] = "FAQ kategori {$category} tidak punya isi di web lama (tampil contoh bawaan); kategori ini dibiarkan kosong, isi lewat mode edit.";
            }
            foreach ($ids as $id) {
                $rows[] = [
                    'category' => $category,
                    'question' => $this->fit($old->value('faqs', "faq-list-1_{$category}_faq_{$id}_question"), 255, 'Pertanyaan FAQ'),
                    'answer' => (string) $old->value('faqs', "faq-list-1_{$category}_faq_{$id}_answer"),
                ];
            }
        }

        return ['rows' => $rows];
    }

    private function testimonials(LegacyContent $old): array
    {
        $rows = [];
        $blank = 0;
        foreach ($old->ids('testimonials', '/^testimonial-list-1_testimonial_(.+)_name$/') as $id) {
            $get = fn (string $field, ?string $default = null) => $old->value('testimonials', "testimonial-list-1_testimonial_{$id}_{$field}") ?? $default;

            if (trim((string) $get('name')) === '') {
                // The old page skipped testimonials without a name.
                $blank++;
                $old->skip('testimonials', '/^testimonial-list-1_testimonial_' . preg_quote($id, '/') . '_/');

                continue;
            }

            $rows[] = [
                'name' => $this->fit($get('name'), 100, 'Nama testimoni'),
                'role' => $this->fit($get('role', 'CEO'), 100, 'Jabatan testimoni'),
                'content' => (string) $get('content', ''),
                'rating' => max(1, min(5, (int) $get('rating', '5'))),
                'logo' => $this->localPath($get('logo', 'images/client-logo-1.svg')),
                'photo' => $this->localPath($get('image', 'images/author-1.jpg')),
            ];
        }

        if ($blank > 0) {
            $this->notes[] = "{$blank} testimoni tanpa nama tidak tampil di web lama, jadi tidak dipindah.";
        }

        return $rows === [] ? ['seed' => TestimonialSeeder::class] : ['rows' => $rows];
    }

    private function clientLogos(LegacyContent $old): array
    {
        $logos = array_filter($old->numbered('testimonials', '/^client-logos-2_client_logo_(\d+)$/'), fn ($logo) => trim($logo) !== '');
        if ($logos === []) {
            return ['seed' => ClientLogoSeeder::class];
        }

        return ['rows' => array_map(fn ($logo) => ['logo' => $this->localPath($logo)], array_values($logos))];
    }

    private function tools(LegacyContent $old): array
    {
        $rows = [];
        foreach ($old->ids('creative-tools', '/^tools-list-1_tool_(.+)_name$/') as $id) {
            $get = fn (string $field, string $default) => $old->value('creative-tools', "tools-list-1_tool_{$id}_{$field}") ?? $default;
            $rows[] = [
                'name' => $this->fit($get('name', 'Tool'), 100, 'Nama tool'),
                'category' => $this->fit($get('cat', 'Design tool'), 100, 'Kategori tool'),
                'icon' => $this->localPath($get('icon', 'images/icon-creative-tools-1.svg')),
            ];
        }

        return $rows === [] ? ['seed' => ToolSeeder::class] : ['rows' => $rows];
    }

    private function processSteps(LegacyContent $old): array
    {
        $rows = [];
        foreach ($old->ids('how-it-works', '/^process-list-1_step_(.+)_title$/') as $id) {
            $title = (string) $old->value('how-it-works', "process-list-1_step_{$id}_title");
            $rows[] = [
                // The numbers ("01. ") are now counted from the order instead of typed in the title.
                'title' => $this->fit(preg_replace('/^\s*\d+\s*[.)]\s*/', '', $title), 150, 'Judul langkah'),
                'description' => $old->value('how-it-works', "process-list-1_step_{$id}_desc")
                    ?? 'We begin by getting to know you and your brand. Through in-depth conversations, research, and analysis.',
            ];
        }

        return $rows === [] ? ['seed' => ProcessStepSeeder::class] : ['rows' => $rows];
    }

    private function achievements(LegacyContent $old): array
    {
        $rows = [];
        foreach ($old->ids('achievements', '/^achievement-list-1_ach_(.+)_name$/') as $id) {
            $get = fn (string $field, string $default) => $old->value('achievements', "achievement-list-1_ach_{$id}_{$field}") ?? $default;
            $rows[] = [
                'period' => $this->fit($get('year', '2024 - 2025'), 50, 'Periode pencapaian'),
                'title' => $this->fit($get('name', 'Achievement'), 150, 'Judul pencapaian'),
                'description' => $get('desc', 'Achievement description.'),
            ];
        }

        return $rows === [] ? ['seed' => AchievementSeeder::class] : ['rows' => $rows];
    }

    /**
     * Always three counters on the old site; each part could be changed.
     */
    private function counters(LegacyContent $old): array
    {
        $defaults = [['45', '+', 'project completed'], ['15', '+', 'years of experience'], ['100', '%', 'client satisfaction']];
        $rows = [];
        foreach ($defaults as $index => [$number, $suffix, $label]) {
            $get = fn (string $field, string $default) => $old->value('cta', "cta-counters_cnt_{$index}_{$field}") ?? $default;
            $rows[] = [
                'number' => $this->fit($get('num', $number), 12, 'Angka counter'),
                'suffix' => $this->fit($get('sym', $suffix), 30, 'Simbol counter'),
                'label' => $this->fit($get('label', $label), 100, 'Label counter'),
            ];
        }

        return ['rows' => $rows];
    }

    /**
     * Members were numbered 0, 1, 2, ...; the old pages showed every number up to the highest.
     */
    private function team(LegacyContent $old): array
    {
        $numbers = array_map('intval', $old->ids('our-team', '/^team-list-1_member_(\d+)_name$/'));
        $prefix = 'team-list-1';
        if ($numbers === []) {
            $numbers = array_map('intval', $old->ids('our-team', '/^default_member_(\d+)_name$/'));
            $prefix = 'default';
        }
        if ($numbers === []) {
            return ['rows' => []];
        }

        $rows = [];
        $slugs = [];
        $blankExpertise = 0;
        for ($index = 0; $index <= max($numbers); $index++) {
            $get = fn (string $field, ?string $default = null) => $old->value('our-team', "{$prefix}_member_{$index}_{$field}") ?? $default;
            $name = $this->fit($get('name', 'Team Member'), 100, 'Nama anggota tim');
            if ($name === '') {
                $this->notes[] = 'Anggota tim urutan ke-' . ($index + 1) . ' (' . $get('role', 'Position') . ') tidak punya nama di web lama; tetap dipindah tanpa nama, isi lewat mode edit.';
            }

            $skills = [];
            for ($i = 1; $i <= (int) $get('skills_count', '1'); $i++) {
                $skill = trim((string) $get("skill_{$i}_title", ''));
                $percent = $get("skill_{$i}_percent", '80');
                if ($skill !== '') {
                    $skills[] = ['name' => $this->fit($skill, 100, 'Nama skill'), 'percent' => max(0, min(100, (int) $percent))];
                }
            }

            $expertises = [];
            for ($i = 1; $i <= (int) $get('expertise_count', '4'); $i++) {
                $expertise = trim((string) $get("expertise_{$i}", ''));
                if ($expertise === '') {
                    $blankExpertise++;
                } else {
                    $expertises[] = ['name' => $this->fit($expertise, 150, 'Keahlian')];
                }
            }

            $rows[] = [
                'slug' => $this->uniqueSlug($name, 'member', $slugs),
                'name' => $name,
                'role' => $this->fit($get('role', 'Position'), 100, 'Jabatan anggota tim'),
                'photo' => $this->localPath($get('img', 'images/team-1.jpg')),
                'experience' => $this->fit($get('experience', '5 years'), 50, 'Pengalaman'),
                'about' => $get('about', "With years of experience in design and creativity, {$name} leads the team with a passion for crafting innovative and impactful visual solutions."),
                'skills_intro' => $get('skills_desc', "{$name} excels in creative problem-solving, user-centered design, branding, and UI/UX development."),
                'expertise_intro' => $get('expertise_desc', "{$name} is a seasoned professional with a keen eye for aesthetics and a deep understanding of creative strategies."),
                'skills' => $skills,
                'expertises' => $expertises,
            ];
        }

        if ($blankExpertise > 0) {
            $this->notes[] = "{$blankExpertise} butir keahlian anggota tim yang kosong (di web lama tampil tulisan \"blank\") tidak dipindah.";
        }

        return ['rows' => $rows];
    }

    /**
     * @param  array<string, string>|null  $shared  settings that were per service on the old site and are shared now
     */
    private function services(LegacyContent $old, ?array &$shared): array
    {
        $shared = [];
        $ids = array_values(array_filter(
            $old->ids('services', '/^services-list-1_service_(.+)_title$/'),
            fn ($id) => !preg_match('/_(benefits|process|faq|cta)/', $id),
        ));
        if ($ids === []) {
            return ['seed' => ServiceSeeder::class];
        }

        $rows = [];
        $slugs = [];
        foreach ($ids as $index => $id) {
            $get = fn (string $field, ?string $default = null) => $old->value('services', "services-list-1_service_{$id}_{$field}") ?? $default;
            $name = $this->fit($get('title', 'Service Title'), 100, 'Nama layanan');

            $benefits = [];
            for ($i = 1; $i <= (int) $get('benefit_count', '6'); $i++) {
                $benefits[] = (string) $get("benefit_{$i}", '');
            }
            $benefits = array_values(array_filter($benefits, fn ($text) => trim($text) !== ''));
            $benefits = $benefits === [] ? ServiceSeeder::BENEFITS : $benefits;

            $faqs = [];
            for ($i = 1; $i <= (int) $get('faq_count', '5'); $i++) {
                $faqs[] = [(string) $get("faq_{$i}_question", ''), (string) $get("faq_{$i}_answer", '')];
            }
            $faqs = ($faqs[0][0] ?? '') === '' ? ServiceSeeder::FAQS : array_values(array_filter($faqs, fn ($faq) => trim($faq[0]) !== ''));

            $rows[] = [
                'slug' => $this->uniqueSlug($name, 'service', $slugs),
                'name' => $name,
                'summary' => $get('desc', 'We provide excellent service for your needs.'),
                'image' => $this->localPath($get('image', 'images/service-image-' . (($index % 6) + 1) . '.jpg')),
                'icon' => ($icon = $get('icon')) !== null && $icon !== '' ? $this->localPath($icon) : null,
                'feature_image' => ($feature = $get('feature_image')) !== null && $feature !== '' ? $this->localPath($feature) : null,
                'description' => trim($get('desc_1', 'Our web development services are designed to create dynamic, responsive, and user-friendly websites that drive engagement and deliver exceptional user experiences. From concept to launch, we work closely with you to design and develop websites tailored to your brand, ensuring they not only look stunning but also perform seamlessly across all devices.')
                    . "\n\n" . $get('desc_2', 'Our expertise spans a wide range of technologies, including custom coding, CMS platforms like WordPress, and e-commerce solutions such as Shopify and Magento. Whether you need a sleek business website, a robust e-commerce platform, or a fully customized web application, our team ensures your online presence is impactful, secure, and optimized for search engines.')),
                'benefits_title' => $this->fit($get('betit', 'Benefits <span>web development</span>'), 255, 'Judul manfaat'),
                'benefits_intro' => $get('bedes', 'Web development enhances your online presence, boosts user engagement, and drives business growth with responsive, secure, and customized websites tailored to meet your goals.'),
                'benefits' => array_map(fn ($text) => ['text' => $this->fit($text, 200, 'Manfaat layanan')], $benefits),
                'faqs' => array_map(fn ($faq) => ['question' => $this->fit($faq[0], 255, 'Pertanyaan layanan'), 'answer' => $faq[1]], $faqs),
            ];

            // "Read more" on the home page now always opens the detail page.
            $get('link');

            // These were per service but showed the same text everywhere; the first service's wins.
            foreach (['faq_subtitle' => 'services.faq.sub_title', 'fatit' => 'services.faq.title', 'cta_text' => 'services.cta.text', 'cta_email' => 'services.cta.email', 'cta_btn_text' => 'services.cta.button_text'] as $field => $key) {
                $value = $get($field);
                if ($value !== null && !isset($shared[$key])) {
                    $this->setting($shared, $key, $value);
                }
            }
        }

        return ['rows' => $rows];
    }

    private function projects(LegacyContent $old): array
    {
        $rows = [];
        foreach ($old->ids('our-work', '/^project-list-1_project_(.+)_title$/') as $id) {
            $get = fn (string $field, ?string $default = null) => $old->value('our-work', "project-list-1_project_{$id}_{$field}") ?? $default;
            $type = $get('media_type', 'video') === 'image' ? 'image' : 'video';
            $media = (string) $get('media_src', 'https://www.youtube.com/embed/EszaEO236lw');
            $link = $this->localLink((string) $get('link', '#'), 'url');
            $title = $this->fit($get('title'), 150, 'Judul project');

            if ($type === 'video' && Project::youtubeId($media) === null) {
                $this->notes[] = "Project \"{$title}\": link video \"{$media}\" bukan link YouTube yang dikenali, perlu diperbaiki lewat mode edit.";
            }
            if (!preg_match('#^https?://#i', $link) && !in_array($link, ['', '#', 'javascript:void(0)'], true)) {
                $this->notes[] = "Project \"{$title}\": link \"{$link}\" tidak dipindah karena link project harus alamat lengkap (http/https).";
            }

            $rows[] = [
                'title' => $title,
                'description' => $get('desc', 'Project description'),
                'media_type' => $type,
                'video_url' => $type === 'video' ? $media : null,
                'image' => $type === 'image' ? $this->localPath($media) : null,
                'link' => preg_match('#^https?://#i', $link) ? $this->fit($link, 255, 'Link project') : null,
            ];
        }

        return ['rows' => $rows];
    }

    /**
     * Three plans were written in the old view; each part could be changed.
     */
    private function pricingPlans(LegacyContent $old): array
    {
        $plans = [
            'basic' => ['basic plan', 'Rp 300 Rb', '/per month', false, ['Desain Grafis (Feed, Reels, Story)', 'Desain Interaktif', 'Strategi Konten & Social Media Management']],
            'standard' => ['standard plan', 'Rp 500 Rb', '/per month', true, ['Konten Video & Foto untuk Instagram & TikTok', 'Desain Grafis (Feed, Reels, Story)', 'Strategi Konten & Social Media Management']],
            'premium' => ['premium plan', 'Rp 1 Jt', '/per month', false, ['Konten Video & Foto untuk Instagram & TikTok', 'Desain Grafis (Feed, Reels, Story)', 'Strategi Konten & Social Media Management', 'Ads kreatif untuk Meta (Instagram & FB Ads)', 'Paket manajemen konten full (1 bulan ke depan ready!)']],
        ];

        $rows = [];
        foreach ($plans as $id => [$name, $price, $period, $featured, $features]) {
            $get = fn (string $field, string $default) => $old->value('pricing', "pricing-plans_plan_{$id}_{$field}") ?? $default;

            $stored = [];
            for ($i = 1; $i <= 10; $i++) {
                $feature = trim((string) $old->value('pricing', "pricing-plans_plan_{$id}_feature_{$i}"));
                if ($feature !== '') {
                    $stored[] = $feature;
                }
            }

            $link = $this->localLink($get('btn_link', '#'), 'url');
            $rows[] = [
                'name' => $this->fit($get('name', $name), 100, 'Nama paket'),
                'price' => $this->fit($get('price', $price), 50, 'Harga paket'),
                'period' => $this->fit($get('period', $period), 50, 'Periode paket'),
                'is_featured' => $get('highlighted', $featured ? '1' : '0') === '1',
                'button_text' => $this->fit($get('btn_text', "let's get started"), 50, 'Teks tombol paket'),
                'button_link' => preg_match('#^(https?://|/|\#|mailto:|tel:)#i', $link) ? $link : '#',
                'features' => array_map(fn ($text) => ['text' => $this->fit($text, 200, 'Fitur paket')], $stored === [] ? $features : $stored),
            ];
        }

        return ['rows' => $rows];
    }

    /**
     * About us points, vision and mission points, and the three Pricing benefits.
     */
    private function pageLists(LegacyContent $old): array
    {
        $defaults = ['Creativity and Innovation', 'Client-Centricity', 'Integrity and Transparency', 'Excellence and Quality'];
        $rows = [];

        $lists = [
            'about' => $old->numbered('about-us', '/^about-us-1_list_item_(\d+)$/'),
            'vision' => $old->numbered('vision-mission', '/^vision-mission-1_vision_list_(\d+)$/'),
            'mission' => $old->numbered('vision-mission', '/^vision-mission-1_mission_list_(\d+)$/'),
        ];
        foreach ($lists as $list => $items) {
            foreach ($items === [] ? $defaults : $items as $text) {
                $rows[] = ['list_key' => $list, 'text' => $this->fit($text, 200, 'Butir daftar'), 'icon' => null];
            }
        }

        $benefits = ['Get 30 day free trial', 'No any hidden fees pay', 'You can cancel anytime'];
        foreach ($benefits as $index => $text) {
            $i = $index + 1;
            $rows[] = [
                'list_key' => 'pricing_benefits',
                'text' => $this->fit($old->value('pricing', "pricing-benefits_benefit_{$i}_text") ?? $text, 200, 'Keuntungan Pricing'),
                'icon' => $this->localPath($old->value('pricing', "pricing-benefits_benefit_{$i}_icon") ?? "images/icon-pricing-benefit-{$i}.svg"),
            ];
        }

        return ['rows' => $rows];
    }

    // ---------------------------------------------------------------- helpers

    /**
     * Keys the old editor saved for its own use; the old pages never showed them.
     */
    private function skipBookkeeping(LegacyContent $old): void
    {
        foreach (['hero', 'how-it-works', 'our-team', 'our-work', 'footer', 'about-us'] as $section) {
            $old->skip($section, '/(_current_|_default_|_image_default$)/');
        }
        $old->skip('trusted-clients');                                      // replaced by client-logos-2 before the old site went live
        $old->skip('services', '/^services-list-1_new_service_.+_new$/');   // "this item is new" markers
        $old->skip('services', '/^service-tab-\d+_title$/');                // tab names of the demo services, never read
        $old->skip('how-it-works', '/^process-list-1_step_.+_active$/');    // which accordion item was open in the editor
        $old->skip('our-team', '/^team-list-1_team_member_count$/');
        $old->skip('our-team', '/_member_\d+_(fb|x|li|ig)$/');              // social links the team pages never showed
        $old->skip('testimonials', '/^client-logos-2_(client_logo_count|logo_[ab]_\d+)$/');
    }

    /**
     * Pictures and videos: own-domain URLs become paths relative to the site root ("images/x.jpg").
     */
    private function localPath(?string $value): string
    {
        $value = trim((string) $value);
        $parts = parse_url($value);
        if (isset($parts['host']) && in_array(strtolower($parts['host']), self::OLD_HOSTS, true)) {
            return ltrim($parts['path'] ?? '', '/');
        }

        return $value;
    }

    /**
     * Links: own-domain URLs become root-relative ("/contact"), so a test domain links to itself.
     */
    private function localLink(string $value, string $type): string
    {
        $value = trim($value);
        if ($type !== 'url') {
            return $value;
        }

        $parts = parse_url($value);
        if (isset($parts['host']) && in_array(strtolower($parts['host']), self::OLD_HOSTS, true)) {
            return '/' . ltrim($parts['path'] ?? '', '/')
                . (isset($parts['query']) ? '?' . $parts['query'] : '')
                . (isset($parts['fragment']) ? '#' . $parts['fragment'] : '');
        }

        return $value;
    }

    private function fit(?string $value, int $max, string $label): string
    {
        $value = trim((string) $value);
        if (mb_strlen($value) > $max) {
            $this->notes[] = "{$label} \"" . mb_substr($value, 0, 40) . "...\" dipotong menjadi {$max} karakter.";

            return mb_substr($value, 0, $max);
        }

        return $value;
    }

    /**
     * @param  array<string, true>  $taken
     */
    private function uniqueSlug(string $name, string $fallback, array &$taken): string
    {
        $base = Str::slug($name) ?: $fallback;
        $slug = $base;
        for ($i = 2; isset($taken[$slug]); $i++) {
            $slug = "{$base}-{$i}";
        }
        $taken[$slug] = true;

        return $slug;
    }

    /**
     * Uploads of the old site become WebP. Template images the old editor stored by path point to
     * the WebP copy when the original was replaced by one (PBI-506); anything else stays as it is.
     */
    private function picture(?string $value): ?string
    {
        if ($value !== null && str_starts_with($value, 'images/') && !is_file(public_path($value))) {
            $webp = preg_replace('/\.(png|jpe?g)$/i', '.webp', $value);
            if (is_file(public_path($webp))) {
                return $webp;
            }
            $this->converted[$value] = $value; // listed in the report as a picture used as it is

            return $value;
        }

        if ($value === null || !str_starts_with($value, 'storage/media/')) {
            return $value;
        }

        if (!array_key_exists($value, $this->converted)) {
            $this->converted[$value] = $this->uploads->convertStored($value, 'legacy') ?? $value;
        }

        return $this->converted[$value];
    }

    /**
     * @param  array<string, mixed>  $row
     */
    private function createRow(string $list, array $row): void
    {
        $children = array_intersect_key($row, array_flip(['skills', 'expertises', 'benefits', 'faqs', 'features']));
        $row = array_diff_key($row, $children);

        foreach (self::PICTURES[$list] ?? [] as $column) {
            $row[$column] = $this->picture($row[$column] ?? null);
        }

        // Numbered per list, per FAQ category and per page list, as the seeders do.
        $group = $row['list_key'] ?? ($list === 'faqs' ? $row['category'] : '');
        $row['sort_order'] = $this->order[$list][$group] = ($this->order[$list][$group] ?? 0) + 1;

        $item = self::MODELS[$list]::create($row);

        foreach ($children as $relation => $items) {
            foreach (array_values($items) as $order => $child) {
                $item->{$relation}()->create($child + ['sort_order' => $order + 1]);
            }
        }
    }
}
