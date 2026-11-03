<?php

namespace App\Vcms;

use App\Support\Media;
use Illuminate\Database\Eloquent\Model;

/**
 * Fills an entity's field definitions (app/Vcms/Fields) with values for the editor.
 */
class FieldSet
{
    /**
     * @param  list<array<string, mixed>>  $definition
     * @param  Model|array<string, mixed>  $values  a model, or defaults for a new item
     * @return list<array<string, mixed>>
     */
    public static function withValues(array $definition, Model|array $values = []): array
    {
        return array_map(function (array $field) use ($values) {
            $value = $values instanceof Model ? $values->getAttribute($field['name']) : ($values[$field['name']] ?? null);

            if ($field['type'] === 'toggle') {
                $value = (bool) ($value ?? true);
            }

            $field['value'] = $value ?? ($field['default'] ?? '');

            if ($field['type'] === 'image') {
                $field['url'] = Media::url($field['value']);
            }

            return $field;
        }, $definition);
    }
}
