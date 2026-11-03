<?php

namespace Tests\Feature;

use App\Models\PageSetting;
use App\Models\Service;
use App\Models\User;
use App\Vcms\EditMode;
use Database\Seeders\ServiceSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * Services section on Home, the Services page and the service detail page.
 */
class ServicePagesTest extends TestCase
{
    use RefreshDatabase;

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

    private function editor(): static
    {
        return $this->actingAs(User::factory()->create())->withSession([EditMode::SESSION_KEY => true]);
    }

    public function test_home_tabs_are_numbered_and_link_to_the_detail_pages(): void
    {
        $this->seed(ServiceSeeder::class);
        Service::where('slug', '3d-designs')->update(['is_active' => false]);
        PageSetting::create(['key' => 'services.title', 'value' => 'Layanan <span>kami</span>']);

        $html = $this->get('/')
            ->assertOk()
            ->assertSee('Layanan <span>kami</span>', false)
            ->assertSeeInOrder(['<span>01</span> UI/UX Design', '<span>02</span> Web Development', '<span>03</span> Digital Marketing Design'], false)
            ->assertDontSee('</span> 3D Designs', false)
            ->assertSee('href="' . route('pages.service.single', 'web-development') . '" class="readmore-btn"', false)
            ->assertSee(asset(Service::DEFAULT_ICON), false)
            ->getContent();

        $this->assertSame(4, substr_count($html, 'class="our-service-box tab-pane'));
        $this->assertSame(1, substr_count($html, 'tab-pane fade show active'));
    }

    public function test_services_page_lists_active_services_and_edit_mode_shows_hidden_ones(): void
    {
        $this->seed(ServiceSeeder::class);
        $this->service(9, ['name' => 'Layanan Rahasia', 'is_active' => false]);

        $html = $this->get('/services')
            ->assertOk()
            ->assertSee('Explore our <span>services</span>', false)
            ->assertDontSee('Layanan Rahasia')
            ->getContent();
        $this->assertSame(5, substr_count($html, 'class="service-item'));

        $this->editor()->get('/services')
            ->assertSee('Layanan Rahasia')
            ->assertSee('+ Tambah layanan');
    }

    public function test_services_page_shows_twelve_services_per_page(): void
    {
        foreach (range(1, 13) as $sortOrder) {
            $this->service($sortOrder);
        }

        $html = $this->get('/services')->assertOk()->getContent();

        $this->assertSame(2, substr_count($html, 'class="btn service-page-btn'));
        $this->assertSame(12, substr_count($html, 'service-item-col" data-page="1" >'));
        $this->assertSame(1, substr_count($html, 'service-item-col" data-page="2" hidden>'));
    }

    public function test_detail_page_shows_paragraphs_benefits_and_faqs(): void
    {
        $service = $this->service(1, [
            'description' => "Paragraf satu.\n\nParagraf dua.",
            'benefits_title' => 'Manfaat <span>servis</span><script>x</script>',
            'feature_image' => 'images/service-benefit-img-1.jpg',
        ]);
        $service->benefits()->create(['text' => 'Bergaransi', 'sort_order' => 1]);
        $service->benefits()->create(['text' => 'Manfaat Rahasia', 'sort_order' => 2, 'is_active' => false]);
        $service->faqs()->create(['question' => 'Berapa lama?', 'answer' => 'Satu jam.', 'sort_order' => 1]);
        $service->faqs()->create(['question' => 'Tanya Rahasia?', 'answer' => 'x', 'sort_order' => 2, 'is_active' => false]);
        $this->service(2, ['name' => 'Cuci Motor']);

        $this->get('/pages/service/layanan-1')
            ->assertOk()
            ->assertSeeInOrder(['<p>Paragraf satu.</p>', 'Paragraf dua.</p>'], false)
            ->assertSee('Manfaat <span>servis</span>', false)
            ->assertDontSee('<script>x</script>', false)
            ->assertSee(asset('images/service-benefit-img-1.jpg'), false)
            ->assertSee('Bergaransi')
            ->assertSee('Berapa lama?')
            ->assertSee('Cuci Motor')
            ->assertSee("Find your <span>answers here</span>", false)
            ->assertDontSee('Manfaat Rahasia')
            ->assertDontSee('Tanya Rahasia?');

        $this->editor()->get('/pages/service/layanan-1')
            ->assertSee('Manfaat Rahasia')
            ->assertSee('Tanya Rahasia?')
            ->assertSee('+ Tambah manfaat')
            ->assertSee('+ Tambah FAQ')
            ->assertDontSee('title="Hapus detail layanan"', false);
    }

    public function test_feature_image_falls_back_to_the_card_image(): void
    {
        $this->service(1, ['image' => 'images/service-image-4.jpg']);

        $this->get('/pages/service/layanan-1')->assertSee(asset('images/service-image-4.jpg'), false);
    }

    public function test_hidden_or_unknown_service_is_404_for_visitors(): void
    {
        $this->service(1, ['is_active' => false]);

        $this->get('/pages/service/layanan-1')->assertNotFound();
        $this->get('/pages/service/01')->assertNotFound();
        $this->editor()->get('/pages/service/layanan-1')->assertOk()->assertSee('Layanan 1');
    }

    public function test_service_name_is_escaped(): void
    {
        $this->service(1, ['name' => '<script>alert(1)</script>']);

        $this->get('/pages/service/layanan-1')->assertDontSee('<script>alert(1)</script>', false);
        $this->get('/services')->assertDontSee('<script>alert(1)</script>', false);
        $this->get('/')->assertDontSee('<script>alert(1)</script>', false);
    }
}
