<?php

namespace Tests\Feature;

use App\Models\Counter;
use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\DataProvider;
use Tests\Concerns\ListApiContract;
use Tests\TestCase;

class CounterApiTest extends TestCase
{
    use ListApiContract;
    use RefreshDatabase;

    protected function listUri(): string
    {
        return '/admin/api/counters';
    }

    protected function makeItem(int $sortOrder, array $attributes = []): Model
    {
        return Counter::create(['number' => '10', 'suffix' => '+', 'label' => "Angka {$sortOrder}", 'sort_order' => $sortOrder, ...$attributes]);
    }

    protected function validPayload(): array
    {
        return ['number' => '1.200', 'suffix' => '+', 'label' => 'motor diservis', 'is_active' => true];
    }

    protected function requiredField(): array
    {
        return ['label', 'pelanggan puas'];
    }

    public static function numbers(): array
    {
        return [
            'plain' => ['45', true],
            'thousands' => ['1,000', true],
            'decimal' => ['4.9', true],
            'text' => ['banyak', false],
            'with symbol' => ['45+', false],
            'negative' => ['-5', false],
        ];
    }

    #[DataProvider('numbers')]
    public function test_number_must_be_digits(string $number, bool $valid): void
    {
        $response = $this->actingAs(User::factory()->create())
            ->postJson($this->listUri(), ['number' => $number, 'label' => 'uji']);

        $valid ? $response->assertCreated() : $response->assertStatus(422)->assertJsonValidationErrors('number');
    }
}
