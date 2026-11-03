<?php

namespace Tests\Feature;

use App\Models\Faq;
use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\Concerns\ListApiContract;
use Tests\TestCase;

class FaqApiTest extends TestCase
{
    use ListApiContract;
    use RefreshDatabase;

    protected function listUri(): string
    {
        return '/admin/api/faqs';
    }

    protected function makeItem(int $sortOrder, array $attributes = []): Model
    {
        return $this->faq('general', $sortOrder, $attributes);
    }

    protected function validPayload(): array
    {
        return ['category' => 'general', 'question' => 'Baru?', 'answer' => 'Ya.', 'is_active' => '1'];
    }

    protected function requiredField(): array
    {
        return ['question', 'Pertanyaan yang diubah?'];
    }

    private function faq(string $category, int $sortOrder, array $attributes = []): Faq
    {
        return Faq::create([
            'category' => $category,
            'question' => "Pertanyaan {$category} {$sortOrder}",
            'answer' => 'Jawaban',
            'sort_order' => $sortOrder,
            ...$attributes,
        ]);
    }

    /**
     * @return list<int>
     */
    private function idsIn(string $category): array
    {
        return Faq::where('category', $category)->ordered()->pluck('id')->all();
    }

    public function test_new_faq_goes_last_in_its_own_category(): void
    {
        $this->faq('general', 1);
        $this->faq('general', 2);
        $this->faq('support', 7);

        $this->actingAs(User::factory()->create())
            ->postJson('/admin/api/faqs', $this->validPayload())
            ->assertCreated()
            ->assertJsonPath('data.sort_order', 3)
            ->assertJsonPath('data.is_active', true);
    }

    public function test_unknown_category_and_long_answer_are_422(): void
    {
        $this->actingAs(User::factory()->create())
            ->postJson('/admin/api/faqs', ['category' => 'lainnya', 'question' => 'Q', 'answer' => str_repeat('a', 5001)])
            ->assertStatus(422)
            ->assertJsonValidationErrors(['category', 'answer']);

        $this->assertDatabaseCount('faqs', 0);
    }

    public function test_changing_category_puts_the_faq_last_there(): void
    {
        $faq = $this->faq('general', 1);
        $this->faq('support', 1);
        $this->faq('support', 2);

        $this->actingAs(User::factory()->create())
            ->patchJson("/admin/api/faqs/{$faq->id}", ['category' => 'support'])
            ->assertOk()
            ->assertJsonPath('data.sort_order', 3);

        $this->assertSame($faq->id, last($this->idsIn('support')));
    }

    public function test_move_stays_within_the_category(): void
    {
        $a = $this->faq('general', 1);
        $b = $this->faq('general', 2);
        $other = $this->faq('support', 1);

        $this->actingAs(User::factory()->create())
            ->postJson("/admin/api/faqs/{$b->id}/move", ['direction' => 'up'])
            ->assertNoContent();

        $this->assertSame([$b->id, $a->id], $this->idsIn('general'));
        $this->assertSame(1, $other->fresh()->sort_order);
    }

    public function test_move_works_when_positions_are_equal(): void
    {
        $a = $this->faq('general', 0);
        $b = $this->faq('general', 0);

        $this->actingAs(User::factory()->create())
            ->postJson("/admin/api/faqs/{$b->id}/move", ['direction' => 'up'])
            ->assertNoContent();

        $this->assertSame([$b->id, $a->id], $this->idsIn('general'));
    }
}
