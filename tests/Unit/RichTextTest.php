<?php

namespace Tests\Unit;

use App\Support\RichText;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

class RichTextTest extends TestCase
{
    public static function cases(): array
    {
        return [
            'plain text' => ['FAQs', 'FAQs'],
            'allowed tags' => ['Kami <span>Ahli</span><br><strong>A</strong><em>B</em>', 'Kami <span>Ahli</span><br><strong>A</strong><em>B</em>'],
            'attributes dropped' => ['<span class="x" onmouseover="steal()">A</span>', '<span>A</span>'],
            // strip_tags does not recognise "<span/..." as a span, so the whole tag goes.
            'slash attribute trick' => ['<span/onmouseover=alert(1)>A</span>', 'A</span>'],
            'self-closing br' => ['A<br />B', 'A<br/>B'],
            'other tags removed' => ['<script>x()</script><img src=x onerror=y()><a href="#">L</a>', 'x()L'],
            'null' => [null, ''],
        ];
    }

    #[DataProvider('cases')]
    public function test_clean(?string $input, string $expected): void
    {
        $this->assertSame($expected, RichText::clean($input));
    }
}
