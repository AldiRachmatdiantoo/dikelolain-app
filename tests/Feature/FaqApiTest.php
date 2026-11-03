<?php

namespace Tests\Feature;

use App\Models\Faq;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class FaqApiTest extends TestCase
{
    use RefreshDatabase;

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

    public function test_guest_gets_401_everywhere(): void
    {
        $faq = $this->faq('general', 1);

        $this->getJson('/admin/api/faqs')->assertUnauthorized();
        $this->postJson('/admin/api/faqs', ['category' => 'general', 'question' => 'Q', 'answer' => 'A'])->assertUnauthorized();
        $this->patchJson("/admin/api/faqs/{$faq->id}", ['question' => 'X'])->assertUnauthorized();
        $this->deleteJson("/admin/api/faqs/{$faq->id}")->assertUnauthorized();
        $this->postJson("/admin/api/faqs/{$faq->id}/move", ['direction' => 'up'])->assertUnauthorized();

        $this->assertSame('Pertanyaan general 1', $faq->fresh()->question);
    }

    public function test_index_lists_all_faqs(): void
    {
        $this->faq('general', 1);
        $this->faq('support', 1, ['is_active' => false]);

        $this->actingAs(User::factory()->create())
            ->getJson('/admin/api/faqs')
            ->assertOk()
            ->assertJsonCount(2, 'data')
            ->assertJsonStructure(['data' => [['id', 'category', 'question', 'answer', 'sort_order', 'is_active']]]);
    }

    public function test_new_faq_goes_last_in_its_category(): void
    {
        $this->faq('general', 1);
        $this->faq('general', 2);
        $this->faq('support', 7);

        $this->actingAs(User::factory()->create())
            ->postJson('/admin/api/faqs', ['category' => 'general', 'question' => 'Baru?', 'answer' => 'Ya.', 'is_active' => '1'])
            ->assertCreated()
            ->assertJsonPath('data.sort_order', 3)
            ->assertJsonPath('data.is_active', true);
    }

    public function test_invalid_faq_is_422(): void
    {
        $this->actingAs(User::factory()->create())
            ->postJson('/admin/api/faqs', ['category' => 'lainnya', 'question' => '', 'answer' => str_repeat('a', 5001)])
            ->assertStatus(422)
            ->assertJsonValidationErrors(['category', 'question', 'answer']);

        $this->assertDatabaseCount('faqs', 0);
    }

    public function test_update_changes_only_the_given_fields(): void
    {
        $faq = $this->faq('general', 1);

        $this->actingAs(User::factory()->create())
            ->patchJson("/admin/api/faqs/{$faq->id}", ['answer' => 'Jawaban baru', 'is_active' => '0'])
            ->assertOk()
            ->assertJsonPath('data.question', 'Pertanyaan general 1')
            ->assertJsonPath('data.answer', 'Jawaban baru')
            ->assertJsonPath('data.is_active', false);
    }

    public function test_update_rejects_an_empty_question(): void
    {
        $faq = $this->faq('general', 1);

        $this->actingAs(User::factory()->create())
            ->patchJson("/admin/api/faqs/{$faq->id}", ['question' => ''])
            ->assertStatus(422)
            ->assertJsonValidationErrors('question');
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

    public function test_delete(): void
    {
        $faq = $this->faq('general', 1);

        $this->actingAs(User::factory()->create())
            ->deleteJson("/admin/api/faqs/{$faq->id}")
            ->assertNoContent();

        $this->assertModelMissing($faq);
    }

    public function test_unknown_faq_is_404(): void
    {
        $this->actingAs(User::factory()->create());

        $this->patchJson('/admin/api/faqs/999', ['question' => 'X'])->assertNotFound();
        $this->deleteJson('/admin/api/faqs/999')->assertNotFound();
        $this->postJson('/admin/api/faqs/999/move', ['direction' => 'up'])->assertNotFound();
    }

    public function test_move_swaps_with_the_neighbour_in_the_same_category(): void
    {
        $a = $this->faq('general', 1);
        $b = $this->faq('general', 2);
        $c = $this->faq('general', 3);
        $other = $this->faq('support', 1);
        $this->actingAs(User::factory()->create());

        $this->postJson("/admin/api/faqs/{$c->id}/move", ['direction' => 'up'])->assertNoContent();
        $this->assertSame([$a->id, $c->id, $b->id], $this->idsIn('general'));

        $this->postJson("/admin/api/faqs/{$a->id}/move", ['direction' => 'down'])->assertNoContent();
        $this->assertSame([$c->id, $a->id, $b->id], $this->idsIn('general'));

        $this->assertSame(1, $other->fresh()->sort_order);
    }

    public function test_move_at_the_edge_changes_nothing(): void
    {
        $a = $this->faq('general', 1);
        $b = $this->faq('general', 2);
        $this->actingAs(User::factory()->create());

        $this->postJson("/admin/api/faqs/{$a->id}/move", ['direction' => 'up'])->assertNoContent();
        $this->postJson("/admin/api/faqs/{$b->id}/move", ['direction' => 'down'])->assertNoContent();

        $this->assertSame([$a->id, $b->id], $this->idsIn('general'));
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

    public function test_move_needs_a_direction(): void
    {
        $faq = $this->faq('general', 1);

        $this->actingAs(User::factory()->create())
            ->postJson("/admin/api/faqs/{$faq->id}/move", ['direction' => 'left'])
            ->assertStatus(422)
            ->assertJsonValidationErrors('direction');
    }
}
