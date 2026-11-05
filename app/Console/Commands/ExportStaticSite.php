<?php

namespace App\Console\Commands;

use App\Services\SitemapService;
use Illuminate\Console\Command;
use Illuminate\Contracts\Http\Kernel;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Str;

/**
 * Saves the public site as plain files for a host without PHP, such as Vercel.
 * The admin stays on this computer: edit there, export again, upload again.
 */
class ExportStaticSite extends Command
{
    protected $signature = 'site:export
        {url : Alamat situs setelah online, misalnya https://dikelolain.vercel.app}
        {--output=dist : Folder hasil, relatif terhadap akar proyek}';

    protected $description = 'Menyimpan halaman publik sebagai file statis';

    /**
     * Left out of the sitemap, but the Portfolio page links to it.
     */
    private const EXTRA_PAGES = ['/portfolio-experiment'];

    /**
     * Only useful with PHP behind them: the front controller and the edit mode bundle.
     * Uploads are copied separately, because public/storage is only a link.
     */
    private const SKIPPED = ['index.php', 'hot', 'build', 'storage'];

    public function handle(Kernel $kernel, SitemapService $sitemap): int
    {
        $root = rtrim($this->argument('url'), '/');
        $output = base_path($this->option('output'));

        if (File::isDirectory($output) && File::files($output) && ! File::exists("$output/index.html")) {
            $this->fail("Folder $output berisi file lain, jadi tidak ditimpa.");
        }

        // Pages must link to the final address, and an export should leave no sessions behind.
        config(['app.url' => $root, 'session.driver' => 'array']);
        URL::forceRootUrl($root);
        URL::forceScheme(parse_url($root, PHP_URL_SCHEME));

        $this->emptyFolder($output);
        $this->copyFiles($output);

        $paths = [
            ...array_map(fn (array $url) => Str::after($url['loc'], $root), $sitemap->urls()),
            ...self::EXTRA_PAGES,
        ];

        foreach ($paths as $path) {
            $file = ($path === '' ? 'index' : ltrim($path, '/')) . '.html';
            $this->save("$output/$file", $this->render($kernel, $root . $path));
        }

        $this->save("$output/sitemap.xml", $this->render($kernel, "$root/sitemap.xml"));
        $this->save("$output/robots.txt", $this->render($kernel, "$root/robots.txt"));
        $this->save("$output/404.html", $this->render($kernel, "$root/halaman-tidak-ada", status: 404));
        $this->save("$output/vercel.json", json_encode(['cleanUrls' => true, 'trailingSlash' => false]));

        $this->info(count($paths) . " halaman tersimpan di $output");

        return self::SUCCESS;
    }

    /**
     * Keeps .vercel, the link to the project: the listings below skip hidden entries.
     */
    private function emptyFolder(string $output): void
    {
        File::ensureDirectoryExists($output);
        File::delete(File::files($output));

        foreach (File::directories($output) as $folder) {
            File::deleteDirectory($folder);
        }
    }

    private function copyFiles(string $output): void
    {
        foreach (File::glob(public_path('*')) as $source) {
            $name = basename($source);

            if (in_array($name, self::SKIPPED, true)) {
                continue;
            }

            is_dir($source) ? File::copyDirectory($source, "$output/$name") : File::copy($source, "$output/$name");
        }

        File::copyDirectory(storage_path('app/public'), "$output/storage");
        File::delete("$output/storage/.gitignore");
    }

    private function render(Kernel $kernel, string $url, int $status = 200): string
    {
        $request = Request::create($url);
        $response = $kernel->handle($request);
        $kernel->terminate($request, $response);

        if ($response->getStatusCode() !== $status) {
            $this->fail("$url menjawab {$response->getStatusCode()}, seharusnya $status.");
        }

        return $response->getContent();
    }

    private function save(string $file, string $content): void
    {
        File::ensureDirectoryExists(dirname($file));
        File::put($file, $content);
    }
}
