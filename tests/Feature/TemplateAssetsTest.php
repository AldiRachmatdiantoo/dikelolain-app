<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\File;
use Tests\TestCase;

/**
 * Template pictures referenced from views and CSS exist in public/images (they were
 * converted to WebP in PBI-506), and pictures below the first screen load lazily.
 */
class TemplateAssetsTest extends TestCase
{
    use RefreshDatabase;

    public function test_every_referenced_template_image_exists(): void
    {
        $sources = [...File::allFiles(resource_path('views')), public_path('css/custom.css'), public_path('css/admin.css')];
        $missing = [];

        foreach ($sources as $source) {
            preg_match_all('#images/[\w./-]+\.(?:png|jpe?g|gif|svg|webp|ico)#', File::get($source), $matches);
            foreach (array_unique($matches[0]) as $image) {
                if (!File::exists(public_path($image))) {
                    $missing[] = basename($source) . ': ' . $image;
                }
            }
        }

        $this->assertSame([], $missing);
    }

    public function test_template_pictures_are_not_full_size_pngs(): void
    {
        // The header logo used to be a 3500px PNG shown 45px tall.
        $this->get('/about')
            ->assertSee(asset('images/logo-dikelolain.webp'), false)
            ->assertSee(asset('images/favicon-dikelolain.png'), false)
            ->assertDontSee('logo-dikelolain.png', false);
    }

    public function test_pictures_below_the_first_screen_load_lazily(): void
    {
        $html = $this->get('/')->assertOk()->getContent();

        // Hero pictures are on the first screen; the footer logo is not.
        $this->assertMatchesRegularExpression('#<img src="[^"]*hero-img-1[^"]*" alt="Hero Image 1">#', $html);
        $this->assertMatchesRegularExpression('#<img src="[^"]*logo-dikelolain-full-hitam\.webp" alt="" loading="lazy">#', $html);
    }

    public function test_not_found_image_works_on_nested_urls(): void
    {
        $this->get('/tidak/ada/sama-sekali')
            ->assertNotFound()
            ->assertSee(asset('images/404-error-img.webp'), false);
    }
}
