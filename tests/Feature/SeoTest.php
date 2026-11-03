<?php

namespace Tests\Feature;

use App\Models\PageSetting;
use App\Models\Service;
use App\Models\TeamMember;
use App\Models\User;
use App\Vcms\EditMode;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\DataProvider;
use Tests\TestCase;

/**
 * Meta description and social preview tags, sitemap.xml and robots.txt.
 */
class SeoTest extends TestCase
{
    use RefreshDatabase;

    private function service(string $slug, bool $active = true): Service
    {
        return Service::create([
            'slug' => $slug,
            'name' => ucfirst($slug),
            'summary' => "Ringkasan {$slug}.",
            'image' => 'images/service-image-1.jpg',
            'is_active' => $active,
        ]);
    }

    private function member(string $slug, bool $active = true): TeamMember
    {
        return TeamMember::create([
            'slug' => $slug,
            'name' => ucfirst($slug),
            'role' => 'Designer',
            'photo' => 'images/team-1.jpg',
            'about' => '<p>Suka <b>tipografi</b>.</p>',
            'is_active' => $active,
        ]);
    }

    public static function mainPages(): array
    {
        return [
            'home' => ['/', 'home'],
            'about' => ['/about', 'about'],
            'services' => ['/services', 'services'],
            'portfolio' => ['/portfolio', 'portfolio'],
            'pricing' => ['/pricing', 'pricing'],
            'faqs' => ['/faqs', 'faqs'],
            'team' => ['/team', 'team'],
            'testimonials' => ['/testimonials', 'testimonials'],
            'contact' => ['/contact', 'contact'],
        ];
    }

    #[DataProvider('mainPages')]
    public function test_each_main_page_has_its_own_description(string $url, string $page): void
    {
        PageSetting::create(['key' => "seo.{$page}.description", 'value' => "Deskripsi {$page} & \"kutip\""]);

        $this->get($url)
            ->assertOk()
            ->assertSee('<meta name="description" content="Deskripsi ' . $page . ' &amp; &quot;kutip&quot;">', false)
            ->assertSee('<meta property="og:description" content="Deskripsi ' . $page . ' &amp; &quot;kutip&quot;">', false)
            ->assertSee('<link rel="canonical" href="' . url($url) . '">', false)
            ->assertSee('<meta property="og:image" content="' . asset('images/logo-dikelolain-full-hitam.png') . '">', false);
    }

    public function test_detail_pages_describe_themselves(): void
    {
        $this->service('branding');
        $this->member('sari');

        $this->get('/pages/service/branding')
            ->assertSee('<meta name="description" content="Ringkasan branding.">', false);

        $this->get('/pages/team/sari')
            ->assertSee('<meta name="description" content="Suka tipografi.">', false);
    }

    public function test_other_pages_fall_back_to_the_home_description(): void
    {
        $this->get('/portfolio-experiment')
            ->assertSee('<meta name="description" content="We&#039;re a full-service design agency', false);
    }

    public function test_not_found_page_is_not_indexed(): void
    {
        $this->get('/tidak-ada')
            ->assertNotFound()
            ->assertSee('<meta name="robots" content="noindex">', false);

        $this->get('/')->assertDontSee('noindex');
    }

    public function test_admin_bar_edits_the_page_seo_in_edit_mode(): void
    {
        $editor = User::factory()->create();
        $this->service('branding');

        $this->actingAs($editor)->withSession([EditMode::SESSION_KEY => true])
            ->get('/pricing')
            ->assertSee('class="vcms-zone vcms-bar-seo"', false)
            ->assertSee('seo.pricing.description', false);

        // Detail pages take their description from the service or member itself.
        $this->actingAs($editor)->withSession([EditMode::SESSION_KEY => true])
            ->get('/pages/service/branding')
            ->assertDontSee('class="vcms-zone vcms-bar-seo"', false);

        $this->actingAs($editor)->withSession([EditMode::SESSION_KEY => false])
            ->get('/pricing')
            ->assertDontSee('class="vcms-zone vcms-bar-seo"', false);
    }

    public function test_sitemap_lists_main_pages_and_active_detail_pages(): void
    {
        $this->service('branding');
        $this->service('rahasia', active: false);
        $this->member('sari');
        $this->member('tersembunyi', active: false);

        $response = $this->get('/sitemap.xml')
            ->assertOk()
            ->assertHeader('Content-Type', 'application/xml; charset=UTF-8');

        $xml = simplexml_load_string($response->getContent());
        $locs = [];
        foreach ($xml->url as $url) {
            $locs[] = (string) $url->loc;
        }

        foreach (self::mainPages() as [$url]) {
            $this->assertContains(url($url), $locs);
        }
        $this->assertContains(route('pages.service.single', 'branding'), $locs);
        $this->assertContains(route('pages.team.single', 'sari'), $locs);
        $this->assertNotContains(route('pages.service.single', 'rahasia'), $locs);
        $this->assertNotContains(route('pages.team.single', 'tersembunyi'), $locs);
        $this->assertNotContains(url('/portfolio-experiment'), $locs);
        $this->assertStringContainsString('<lastmod>', $response->getContent());
    }

    public function test_robots_points_to_the_sitemap_and_keeps_admin_out(): void
    {
        $this->get('/robots.txt')
            ->assertOk()
            ->assertHeader('Content-Type', 'text/plain; charset=UTF-8')
            ->assertSee('Disallow: /admin')
            ->assertSee('Sitemap: ' . url('/sitemap.xml'));
    }
}
