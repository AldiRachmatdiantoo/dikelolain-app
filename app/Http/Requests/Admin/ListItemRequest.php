<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

/**
 * Validation for creating (POST) and updating (PATCH) a list item. On PATCH
 * every field is optional, so the editor may send only what changed.
 */
abstract class ListItemRequest extends FormRequest
{
    /**
     * Rules for creating an item.
     *
     * @return array<string, list<mixed>>
     */
    abstract protected function fields(): array;

    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $rules = $this->fields();

        if ($this->isMethod('PATCH')) {
            foreach ($rules as $field => $fieldRules) {
                $rules[$field] = ['sometimes', ...array_filter($fieldRules, fn ($rule) => $rule !== 'sometimes')];
            }
        }

        return $rules;
    }
}
