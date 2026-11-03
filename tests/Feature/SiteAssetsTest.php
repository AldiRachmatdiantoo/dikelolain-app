<?php

namespace Tests\Feature;

use App\Models\Project;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\File;
use Tests\TestCase;

/**
 * The public site loads one minified stylesheet and one script (npm run build:site),
 * the stylesheet holds no unused CSS, and YouTube players are only loaded on click.
 */
class SiteAssetsTest extends TestCase
{
    use RefreshDatabase;

    /** @return array{css: list<string>, js: list<string>, scan: list<string>} */
    private function manifest(): array
    {
        return json_decode(file_get_contents(resource_path('site-assets.json')), true);
    }

    /**
     * The views and scripts the build searches for CSS classes, in the order it reads them.
     *
     * @return list<string>
     */
    private function scannedFiles(): array
    {
        $paths = [];
        foreach ($this->manifest()['scan'] as $directory) {
            foreach (File::allFiles(base_path($directory)) as $file) {
                if (in_array($file->getExtension(), ['php', 'js'], true)) {
                    $paths[] = $directory . '/' . str_replace('\\', '/', $file->getRelativePathname());
                }
            }
        }
        sort($paths, SORT_STRING);

        return array_map(base_path(...), $paths);
    }

    public function test_bundles_are_rebuilt_after_every_source_change(): void
    {
        $read = fn (string $path) => str_replace("\r\n", "\n", file_get_contents($path));
        $js = array_map(fn (string $path) => $read(public_path($path)), $this->manifest()['js']);
        $css = array_map(fn (string $path) => $read(public_path($path)), $this->manifest()['css']);
        $scanned = array_map($read, $this->scannedFiles());

        // Unused CSS is dropped, so a class first used in a view needs a rebuild too.
        foreach (['css/site.min.css' => [...$css, ...$scanned, ...$js], 'js/site.min.js' => $js] as $bundle => $inputs) {
            $this->assertStringContainsString(
                'sources:' . sha1(implode("\n", $inputs)),
                substr(file_get_contents(public_path($bundle)), 0, 300),
                "{$bundle} is out of date: run npm run build:site",
            );
        }
    }

    public function test_the_stylesheet_only_holds_css_the_site_uses(): void
    {
        $css = file_get_contents(public_path('css/site.min.css'));

        foreach (['.col-lg-4', '.accordion-button', '.fa-star', '.portfolio-modal', '.vcms-bar', '.swiper-pagination-bullet-active', '.slicknav_nav'] as $used) {
            $this->assertStringContainsString($used, $css);
        }
        foreach (['.fa-wheelchair', '.post-entry', '.hero-slider-layout'] as $unused) {
            $this->assertStringNotContainsString($unused, $css);
        }
    }

    public function test_pages_load_only_the_bundles(): void
    {
        $html = $this->get('/')->assertOk()->getContent();

        $this->assertSame(1, preg_match_all('#<link href="[^"]+\.css[^"]*" rel="stylesheet">#', $html), 'one local stylesheet (plus Google Fonts)');
        $this->assertStringContainsString(asset('css/site.min.css') . '?v=', $html);
        $this->assertStringContainsString(asset('js/site.min.js') . '?v=', $html);

        foreach ([...$this->manifest()['css'], ...$this->manifest()['js']] as $source) {
            $this->assertStringNotContainsString(asset($source), $html, "{$source} is loaded separately");
        }
        foreach (['wow.min.js', 'animate.css', 'magnific-popup', 'parallaxie', 'YTPlayer', 'SmoothScroll', 'isotope', 'wow fadeInUp', 'data-wow-'] as $removed) {
            $this->assertStringNotContainsString($removed, $html);
        }
    }

    public function test_the_portfolio_filters_and_pages_without_a_plugin(): void
    {
        $this->get('/portfolio')->assertOk()->assertDontSee('isotope');
        $this->assertFileDoesNotExist(public_path('js/isotope.min.js'));
    }

    public function test_youtube_projects_show_a_thumbnail_until_clicked(): void
    {
        Project::create([
            'title' => 'Video Klien',
            'media_type' => 'video',
            'video_url' => 'https://youtu.be/dQw4w9WgXcQ',
            'is_active' => true,
        ]);

        $this->get('/portfolio')
            ->assertSee('data-embed="https://www.youtube-nocookie.com/embed/dQw4w9WgXcQ?autoplay=1"', false)
            ->assertSee('src="https://i.ytimg.com/vi/dQw4w9WgXcQ/hqdefault.jpg"', false)
            ->assertDontSee('<iframe src="https://www.youtube-nocookie.com', false);
    }
}
