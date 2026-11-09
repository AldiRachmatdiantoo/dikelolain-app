<?php

namespace Tests\Feature;

use Database\Seeders\ProjectSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * What PageSpeed flagged on the live site after release (PBI-521): the page moving after the
 * first paint, the loading screen waiting for every image, and a few accessibility/SEO items.
 */
class PageLoadTest extends TestCase
{
    use RefreshDatabase;

    public function test_montserrat_comes_from_this_site_and_is_preloaded(): void
    {
        $this->get('/')->assertOk()
            ->assertDontSee('fonts.googleapis.com', false)
            ->assertDontSee('fonts.gstatic.com', false)
            ->assertSee('<link rel="preload" href="' . asset('webfonts/montserrat/montserrat-latin.woff2') . '" as="font" type="font/woff2" crossorigin>', false);

        foreach (['montserrat-latin.woff2', 'montserrat-latin-ext.woff2', 'OFL.txt'] as $file) {
            $this->assertFileExists(public_path("webfonts/montserrat/{$file}"));
        }

        $css = file_get_contents(public_path('css/site.min.css'));
        $this->assertStringContainsString('url(../webfonts/montserrat/montserrat-latin.woff2)', $css);
        $this->assertStringContainsString('--default-font: "Montserrat", "Montserrat Fallback", "Montserrat Fallback Roboto", sans-serif', $css);
        $this->assertStringContainsString('font-family:Montserrat Fallback;font-weight:100 450;src:local("Arial")', $css);
    }

    public function test_animated_headings_already_have_their_split_width(): void
    {
        $this->assertStringContainsString('.text-anime-style-2{font-kerning:none;font-variant-ligatures:none}', file_get_contents(public_path('css/site.min.css')));
    }

    public function test_loading_screen_does_not_wait_for_images(): void
    {
        $js = file_get_contents(public_path('js/function.js'));

        $this->assertStringNotContainsString("on('load'", $js);
        $this->assertMatchesRegularExpression('/\$\(function \(\) \{\s*initPreloader\(\);/', $js);
        $this->assertStringContainsString('.preloader{animation:preloader-out .6s ease 3s forwards}', file_get_contents(public_path('css/site.min.css')));
    }

    public function test_zoom_is_allowed_and_icon_links_have_names(): void
    {
        $html = $this->get('/')->assertOk()->getContent();

        $this->assertStringContainsString('<meta name="viewport" content="width=device-width, initial-scale=1.0">', $html);
        foreach (['Facebook', 'X', 'LinkedIn', 'Instagram'] as $name) {
            $this->assertStringContainsString('aria-label="' . $name . '"><i class="fa-brands', $html);
        }
        $this->assertStringContainsString("\$('.slicknav_btn').attr('aria-label', 'Menu');", file_get_contents(public_path('js/function.js')));
    }

    public function test_project_cards_have_no_javascript_links(): void
    {
        $this->seed(ProjectSeeder::class);

        foreach (['/', '/portfolio'] as $page) {
            $this->get($page)->assertOk()
                ->assertDontSee('javascript:void', false)
                ->assertSee('href="#portfolioModal" role="button" class="open-portfolio-modal"', false);
        }
    }
}
