<?php

namespace Tests\Feature;

use App\Models\PageSetting;
use App\Models\Service;
use App\Models\User;
use App\Vcms\EditMode;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\DataProvider;
use Tests\TestCase;

/**
 * The last single values moved to page_settings: Home hero, Contact page, footer,
 * Testimonials header and the admin analytics report.
 */
class SitePagesSettingsTest extends TestCase
{
    use RefreshDatabase;

    private function editor(): static
    {
        return $this->actingAs(User::factory()->create())->withSession([EditMode::SESSION_KEY => true]);
    }

    private function service(int $sortOrder, array $attributes = []): Service
    {
        return Service::create([
            'slug' => "layanan-{$sortOrder}",
            'name' => "Layanan {$sortOrder}",
            'summary' => 'Ringkasan.',
            'image' => 'images/service-image-1.jpg',
            'sort_order' => $sortOrder,
            ...$attributes,
        ]);
    }

    public function test_home_hero_shows_the_defaults(): void
    {
        $this->get('/')
            ->assertOk()
            ->assertSee('Where <span>creativity</span> meets strategy', false)
            ->assertSee('<source src="' . asset('videos/hero.mp4') . '" type="video/mp4"', false)
            ->assertSee('<a href="/portfolio" class="btn-default">view our portfolio</a>', false)
            ->assertSeeInOrder(array_map(fn ($i) => asset("images/hero-img-{$i}.jpg"), range(1, 5)), false);
    }

    public function test_the_default_hero_video_is_a_small_file_of_our_own(): void
    {
        $this->assertFileExists(public_path('videos/hero.mp4'));
        $this->assertLessThan(4 * 1024 * 1024, filesize(public_path('videos/hero.mp4')));
    }

    public function test_home_hero_shows_saved_values(): void
    {
        PageSetting::create(['key' => 'hero.title', 'value' => 'Kelola <span>bisnis</span>']);
        PageSetting::create(['key' => 'hero.button_text', 'value' => 'lihat layanan']);
        PageSetting::create(['key' => 'hero.button_link', 'value' => '/services']);
        PageSetting::create(['key' => 'hero.video', 'value' => 'storage/uploads/settings/hero.mp4']);

        $this->get('/')
            ->assertSee('Kelola <span>bisnis</span>', false)
            ->assertSee('<a href="/services" class="btn-default">lihat layanan</a>', false)
            ->assertSee('<source src="' . asset('storage/uploads/settings/hero.mp4') . '"', false)
            ->assertDontSee('meets strategy');
    }

    public function test_phones_skip_the_hero_video(): void
    {
        $this->get('/')
            ->assertSee('type="video/mp4" media="(min-width: 768px)"', false)
            ->assertDontSee('poster=', false);
    }

    public function test_the_hero_poster_replaces_the_video_on_phones(): void
    {
        PageSetting::create(['key' => 'hero.poster', 'value' => 'storage/uploads/settings/hero.webp']);

        $this->get('/')->assertSee('poster="' . asset('storage/uploads/settings/hero.webp') . '"', false);
    }

    public function test_nothing_is_preloaded_without_a_hero_poster(): void
    {
        $this->get('/')->assertDontSee('as="image"', false);
    }

    public function test_phones_fetch_the_hero_poster_early(): void
    {
        PageSetting::create(['key' => 'hero.poster', 'value' => 'storage/uploads/settings/hero.webp']);

        $this->get('/')->assertSeeInOrder([
            '<link rel="preload" href="' . asset('storage/uploads/settings/hero.webp') . '" as="image" fetchpriority="high" media="(max-width: 767px)">',
            'css/site.min.css',
        ], false);
    }

    public function test_phones_get_a_small_cut_of_the_hero_video(): void
    {
        $this->assertFileExists(public_path('videos/hero-mobile.mp4'));
        $this->assertLessThan(filesize(public_path('videos/hero.mp4')) / 2, filesize(public_path('videos/hero-mobile.mp4')));

        $this->get('/')
            ->assertSee('<video autoplay muted loop playsinline id="myVideo"', false)
            ->assertSee('data-mobile-src="' . asset('videos/hero-mobile.mp4') . '"', false);
    }

    public function test_phones_keep_the_poster_when_only_the_main_video_is_replaced(): void
    {
        PageSetting::create(['key' => 'hero.video', 'value' => 'storage/uploads/settings/hero.mp4']);

        $this->get('/')->assertDontSee('data-mobile-src', false);
    }

    public function test_an_uploaded_phone_video_goes_with_an_uploaded_main_video(): void
    {
        PageSetting::create(['key' => 'hero.video', 'value' => 'storage/uploads/settings/hero.mp4']);
        PageSetting::create(['key' => 'hero.video_mobile', 'value' => 'storage/uploads/settings/hero-hp.mp4']);

        $this->get('/')->assertSee('data-mobile-src="' . asset('storage/uploads/settings/hero-hp.mp4') . '"', false);
    }

    public function test_contact_page_shows_the_defaults(): void
    {
        $this->get('/contact')
            ->assertOk()
            ->assertSee('Contact <span>us</span>', false)
            ->assertSeeInOrder(['phone number', '+987 654 3210', 'email us', 'info@domain.com', 'address', '7676 Arden Avenue, Ginashire'])
            ->assertSee(asset('images/icon-mail.svg'), false)
            ->assertSee('data-whatsapp="6281234567890"', false)
            ->assertSee('<iframe src="https://www.google.com/maps/embed?pb=', false)
            ->assertSee('send message');
    }

    public function test_whatsapp_number_keeps_digits_only(): void
    {
        PageSetting::create(['key' => 'contact.whatsapp', 'value' => '+62 812-3456" onclick="x']);

        $this->get('/contact')
            ->assertSee('data-whatsapp="628123456"', false)
            ->assertDontSee('onclick="x', false);
    }

    #[DataProvider('pagesWithFooter')]
    public function test_footer_shows_contact_details_and_the_first_active_services(string $url, int $status): void
    {
        foreach (range(1, 5) as $i) {
            $this->service($i);
        }
        $this->service(0, ['slug' => 'tersembunyi', 'name' => 'Layanan Tersembunyi', 'is_active' => false]);
        PageSetting::create(['key' => 'contact.phone', 'value' => '+62 812-0000-1111']);
        PageSetting::create(['key' => 'footer.copyright', 'value' => 'Hak cipta Dikelolain']);

        $this->get($url)
            ->assertStatus($status)
            ->assertSeeInOrder(['our services', 'Layanan 1', 'Layanan 2', 'Layanan 3', 'Layanan 4', 'contact info'])
            ->assertSee('href="' . route('pages.service.single', 'layanan-1') . '"', false)
            ->assertDontSee('Layanan 5')
            ->assertDontSee('Layanan Tersembunyi')
            ->assertSee('<a href="tel:+6281200001111">+62 812-0000-1111</a>', false)
            ->assertSee('Hak cipta Dikelolain');
    }

    public static function pagesWithFooter(): array
    {
        return [
            // Home and Services list every service, so they are left out here.
            'about' => ['/about', 200],
            'contact' => ['/contact', 200],
            'testimonials' => ['/testimonials', 200],
            'not found' => ['/halaman-tidak-ada', 404],
        ];
    }

    public function test_footer_links_come_from_settings(): void
    {
        PageSetting::create(['key' => 'footer.quick_2_text', 'value' => 'tentang kami']);
        PageSetting::create(['key' => 'footer.quick_2_url', 'value' => '/about#tim']);
        PageSetting::create(['key' => 'footer.instagram', 'value' => 'https://instagram.com/dikelolain']);

        $this->get('/about')
            ->assertSeeInOrder(['quick links', 'home', 'tentang kami', 'team', 'our portfolio'])
            ->assertSee('<a href="/about#tim">tentang kami</a>', false)
            ->assertSee('<a href="https://instagram.com/dikelolain" aria-label="Instagram"><i class="fa-brands fa-instagram" aria-hidden="true"></i></a>', false)
            ->assertSee('<a href="/contact">Let&#039;s Connect</a>', false);
    }

    public function test_testimonials_header_uses_settings(): void
    {
        PageSetting::create(['key' => 'testimonials.header.title', 'value' => 'Kata <span>klien</span>']);

        $this->get('/testimonials')
            ->assertOk()
            ->assertSee('Kata <span>klien</span>', false);
    }

    public function test_plain_values_are_escaped(): void
    {
        PageSetting::create(['key' => 'hero.description', 'value' => '<script>alert(1)</script>']);
        PageSetting::create(['key' => 'contact.address', 'value' => '<b>Jalan</b>']);
        PageSetting::create(['key' => 'footer.description', 'value' => '<img src=x onerror=alert(1)>']);

        $this->get('/')
            ->assertDontSee('<script>alert(1)</script>', false)
            ->assertSee('&lt;script&gt;alert(1)&lt;/script&gt;', false)
            ->assertSee('&lt;b&gt;Jalan&lt;/b&gt;', false)
            ->assertDontSee('<img src=x', false);
    }

    public function test_edit_mode_shows_the_new_zones(): void
    {
        $this->editor()->get('/')
            ->assertSee('Gambar &amp; video', false)
            ->assertSee('Judul footer')
            ->assertSee('Link cepat');

        $this->editor()->get('/contact')
            ->assertSee('&#9998; Peta', false)
            ->assertSee('&#9998; Form', false)
            ->assertSee('&#9998; Kontak', false);
    }

    public function test_analytics_page_reads_the_settings(): void
    {
        $admin = User::factory()->create();

        $this->actingAs($admin)->get('/admin/pages/analytics')
            ->assertOk()
            ->assertSee('src="https://lookerstudio.google.com/embed/reporting/c13bd4e0-3d10-48f4-ad69-27e600a65c1a/page/GzLhF"', false)
            ->assertSee("let customIframeHeight = \"\";", false)
            ->assertSee(json_encode(route('admin.api.settings.update', ['key' => '__key__'])), false);

        PageSetting::create(['key' => 'analytics.looker_url', 'value' => 'https://lookerstudio.google.com/embed/reporting/baru']);
        PageSetting::create(['key' => 'analytics.looker_height', 'value' => '1500']);
        // SettingService is scoped per request; tests reuse one application for both requests.
        $this->app->forgetScopedInstances();

        $this->actingAs($admin)->get('/admin/pages/analytics')
            ->assertSee('src="https://lookerstudio.google.com/embed/reporting/baru"', false)
            ->assertSee('let customIframeHeight = "1500";', false);
    }

    public function test_analytics_height_that_is_not_a_number_means_auto(): void
    {
        PageSetting::create(['key' => 'analytics.looker_height', 'value' => "'; alert(1); '"]);

        $this->actingAs(User::factory()->create())->get('/admin/pages/analytics')
            ->assertSee('let customIframeHeight = "";', false)
            ->assertDontSee('alert(1)', false);
    }

    public function test_analytics_settings_are_saved_through_the_settings_api(): void
    {
        $this->actingAs(User::factory()->create());

        $this->patchJson('/admin/api/settings/analytics.looker_url', ['value' => 'https://lookerstudio.google.com/embed/reporting/x'])->assertOk();
        $this->patchJson('/admin/api/settings/analytics.looker_height', ['value' => '1200'])->assertOk();
        $this->patchJson('/admin/api/settings/analytics.looker_url', ['value' => 'javascript:alert(1)'])->assertUnprocessable();

        $this->assertSame('https://lookerstudio.google.com/embed/reporting/x', PageSetting::where('key', 'analytics.looker_url')->value('value'));
        $this->assertSame('1200', PageSetting::where('key', 'analytics.looker_height')->value('value'));
    }
}
