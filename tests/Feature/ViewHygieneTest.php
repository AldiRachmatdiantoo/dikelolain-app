<?php

namespace Tests\Feature;

use Illuminate\Support\Facades\File;
use Tests\TestCase;

/**
 * Views only display what services give them: no helper functions, no queries,
 * no old key-value content (the getContent() helpers removed in Epic 3–4).
 */
class ViewHygieneTest extends TestCase
{
    public function test_views_define_no_functions_and_read_no_old_content(): void
    {
        $patterns = [
            'function_exists' => '/function_exists\s*\(/',
            'old sections data' => '/\$sections\b|->contents\b/',
            'model query' => '/App\\\\Models\\\\\w+::|\bDB::/',
        ];

        $found = [];
        foreach (File::allFiles(resource_path('views')) as $file) {
            $source = $file->getContents();
            foreach ($patterns as $label => $pattern) {
                if (preg_match($pattern, $source)) {
                    $found[] = "{$file->getRelativePathname()}: {$label}";
                }
            }

            // Named functions in PHP blocks (JavaScript functions in <script> are fine).
            preg_match_all('/@php(.*?)@endphp/s', $source, $blocks);
            if (preg_grep('/\bfunction\s+[a-zA-Z_]\w*\s*\(/', $blocks[1])) {
                $found[] = "{$file->getRelativePathname()}: helper function";
            }
        }

        $this->assertSame([], $found);
    }
}
