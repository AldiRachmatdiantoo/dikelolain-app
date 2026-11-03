<?php

namespace App\Http\Requests\Admin;

/**
 * Same rules as creating, but every field is optional (PATCH).
 */
class UpdateFaqRequest extends StoreFaqRequest
{
    public function rules(): array
    {
        return array_map(
            fn (array $rules) => ['sometimes', ...array_diff($rules, ['sometimes'])],
            parent::rules(),
        );
    }
}
