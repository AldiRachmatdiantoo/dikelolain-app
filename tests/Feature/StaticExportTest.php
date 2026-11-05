<?php

namespace Tests\Feature;

use App\Models\Service;
use Database\Seeders\DatabaseSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\File;
use Tests\TestCase;

class StaticExportTest extends TestCase
{
    use RefreshDatabase;

    private const OUTPUT = 'storage/framework/testing/dist';

    private const URL = 'https://contoh.vercel.app';

    protected function tearDown(): void
    {
        File::deleteDirectory(base_path(self::OUTPUT));

        parent::tearDown();
    }

    public function test_the_export_holds_the_public_site_without_the_php_parts(): void
    {
        $this->seed(DatabaseSeeder::class);

        $this->export()->assertSuccessful();

        $slug = Service::query()->value('slug');

        foreach ([
            'index.html', 'about.html', 'contact.html', 'portfolio-experiment.html', "pages/service/$slug.html",
            '404.html', 'sitemap.xml', 'robots.txt', 'vercel.json', 'css/site.min.css', 'videos/hero.mp4',
        ] as $file) {
            $this->assertFileExists($this->path($file));
        }

        foreach (['index.php', '.htaccess', 'build'] as $file) {
            $this->assertFileDoesNotExist($this->path($file));
        }
    }

    public function test_pages_link_to_the_final_address(): void
    {
        $this->export()->assertSuccessful();

        $home = File::get($this->path('index.html'));

        $this->assertStringContainsString(self::URL . '/css/site.min.css', $home);
        $this->assertStringNotContainsString('http://localhost', $home);
        $this->assertStringContainsString(self::URL . '/about', File::get($this->path('sitemap.xml')));
    }

    public function test_every_link_between_pages_has_a_file(): void
    {
        $this->seed(DatabaseSeeder::class);

        $this->export()->assertSuccessful();

        foreach (File::allFiles($this->path('')) as $file) {
            if ($file->getExtension() !== 'html') {
                continue;
            }

            preg_match_all('/(?:href|src)="' . preg_quote(self::URL, '/') . '([^"?#]*)/', $file->getContents(), $links);

            // The 404 page names the address that was asked for, which has no file by definition.
            foreach (array_diff(array_unique($links[1]), ['/halaman-tidak-ada']) as $link) {
                $target = $link === '' || $link === '/' ? 'index.html' : ltrim($link, '/');

                $this->assertTrue(
                    File::exists($this->path($target)) || File::exists($this->path("$target.html")),
                    "{$file->getRelativePathname()} menautkan $link, tapi filenya tidak ada."
                );
            }
        }
    }

    public function test_a_new_export_replaces_the_old_one_but_keeps_the_vercel_link(): void
    {
        File::ensureDirectoryExists($this->path('.vercel'));
        File::put($this->path('.vercel/project.json'), '{}');
        File::put($this->path('index.html'), 'lama');
        File::put($this->path('halaman-lama.html'), 'lama');

        $this->export()->assertSuccessful();

        $this->assertFileExists($this->path('.vercel/project.json'));
        $this->assertFileDoesNotExist($this->path('halaman-lama.html'));
        $this->assertNotSame('lama', File::get($this->path('index.html')));
    }

    public function test_it_refuses_a_folder_that_holds_other_files(): void
    {
        $this->artisan('site:export', ['url' => self::URL, '--output' => 'public'])->assertFailed();

        $this->assertFileExists(public_path('index.php'));
    }

    private function export()
    {
        return $this->artisan('site:export', ['url' => self::URL, '--output' => self::OUTPUT]);
    }

    private function path(string $file): string
    {
        return base_path(self::OUTPUT . '/' . $file);
    }
}
