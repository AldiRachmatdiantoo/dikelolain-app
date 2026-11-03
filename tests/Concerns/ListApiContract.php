<?php

namespace Tests\Concerns;

use App\Models\User;
use Illuminate\Database\Eloquent\Model;

/**
 * Behaviour every ordered-list API shares (Admin\Api\ListController).
 * The test class provides the URI, an item factory and a valid payload.
 */
trait ListApiContract
{
    /** e.g. "/admin/api/faqs" */
    abstract protected function listUri(): string;

    /** Creates an item in the default group at the given position. */
    abstract protected function makeItem(int $sortOrder, array $attributes = []): Model;

    /** A valid body for creating an item in the default group. */
    abstract protected function validPayload(): array;

    /** A field that must not be empty, and a valid new value for it. */
    abstract protected function requiredField(): array;

    private function admin(): static
    {
        return $this->actingAs(User::factory()->create());
    }

    /**
     * Ids of all items of $item's table, in list order.
     *
     * @return list<int>
     */
    private function orderOf(Model $item): array
    {
        return $item->newQuery()->orderBy('sort_order')->orderBy($item->getKeyName())->pluck($item->getKeyName())->all();
    }

    public function test_list_api_needs_login(): void
    {
        $item = $this->makeItem(1);

        $this->getJson($this->listUri())->assertUnauthorized();
        $this->postJson($this->listUri(), $this->validPayload())->assertUnauthorized();
        $this->patchJson("{$this->listUri()}/{$item->getKey()}", $this->validPayload())->assertUnauthorized();
        $this->deleteJson("{$this->listUri()}/{$item->getKey()}")->assertUnauthorized();
        $this->postJson("{$this->listUri()}/{$item->getKey()}/move", ['direction' => 'up'])->assertUnauthorized();

        $this->assertModelExists($item);
        $this->assertSame(1, $item->newQuery()->count());
    }

    public function test_list_api_index_includes_hidden_items(): void
    {
        $this->makeItem(1);
        $this->makeItem(2, ['is_active' => false]);

        $this->admin()->getJson($this->listUri())->assertOk()->assertJsonCount(2, 'data');
    }

    public function test_list_api_store_puts_the_item_last(): void
    {
        $this->makeItem(1);
        $this->makeItem(5);

        $this->admin()
            ->postJson($this->listUri(), $this->validPayload())
            ->assertCreated()
            ->assertJsonPath('data.sort_order', 6);
    }

    public function test_list_api_update_changes_only_given_fields(): void
    {
        $item = $this->makeItem(1);
        [$field, $value] = $this->requiredField();
        $before = $item->toArray();

        $this->admin()
            ->patchJson("{$this->listUri()}/{$item->getKey()}", [$field => $value, 'is_active' => '0'])
            ->assertOk()
            ->assertJsonPath("data.{$field}", $value)
            ->assertJsonPath('data.is_active', false)
            ->assertJsonPath('data.sort_order', $before['sort_order']);
    }

    public function test_list_api_rejects_an_empty_required_field(): void
    {
        $item = $this->makeItem(1);
        [$field] = $this->requiredField();

        $this->admin()->patchJson("{$this->listUri()}/{$item->getKey()}", [$field => ''])
            ->assertStatus(422)
            ->assertJsonValidationErrors($field);

        $this->postJson($this->listUri(), [...$this->validPayload(), $field => ''])
            ->assertStatus(422)
            ->assertJsonValidationErrors($field);

        $this->assertSame(1, $item->newQuery()->count());
    }

    public function test_list_api_delete(): void
    {
        $item = $this->makeItem(1);

        $this->admin()->deleteJson("{$this->listUri()}/{$item->getKey()}")->assertNoContent();

        $this->assertModelMissing($item);
    }

    public function test_list_api_unknown_item_is_404(): void
    {
        $this->admin();

        $this->patchJson("{$this->listUri()}/999999", $this->validPayload())->assertNotFound();
        $this->deleteJson("{$this->listUri()}/999999")->assertNotFound();
        $this->postJson("{$this->listUri()}/999999/move", ['direction' => 'up'])->assertNotFound();
    }

    public function test_list_api_move_swaps_neighbours(): void
    {
        $a = $this->makeItem(1);
        $b = $this->makeItem(2);
        $c = $this->makeItem(3);
        $this->admin();

        $this->postJson("{$this->listUri()}/{$c->getKey()}/move", ['direction' => 'up'])->assertNoContent();
        $this->assertSame([$a->getKey(), $c->getKey(), $b->getKey()], $this->orderOf($a));

        $this->postJson("{$this->listUri()}/{$a->getKey()}/move", ['direction' => 'down'])->assertNoContent();
        $this->assertSame([$c->getKey(), $a->getKey(), $b->getKey()], $this->orderOf($a));
    }

    public function test_list_api_move_at_the_edge_changes_nothing(): void
    {
        $a = $this->makeItem(1);
        $b = $this->makeItem(2);
        $this->admin();

        $this->postJson("{$this->listUri()}/{$a->getKey()}/move", ['direction' => 'up'])->assertNoContent();
        $this->postJson("{$this->listUri()}/{$b->getKey()}/move", ['direction' => 'down'])->assertNoContent();

        $this->assertSame([$a->getKey(), $b->getKey()], $this->orderOf($a));
    }

    public function test_list_api_move_needs_a_direction(): void
    {
        $item = $this->makeItem(1);

        $this->admin()
            ->postJson("{$this->listUri()}/{$item->getKey()}/move", ['direction' => 'left'])
            ->assertStatus(422)
            ->assertJsonValidationErrors('direction');
    }
}
