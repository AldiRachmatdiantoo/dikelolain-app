<?php

namespace App\Services;

use App\Repositories\Contracts\PageSettingRepositoryInterface;
use App\Support\Media;
use App\Support\RichText;
use Illuminate\Http\UploadedFile;
use InvalidArgumentException;

/**
 * Single editable values (config/page_settings.php). Stored values override the
 * registry defaults; all stored values are loaded once per request.
 */
class SettingService
{
    /** @var array<string, string|null>|null */
    private ?array $stored = null;

    public function __construct(
        private PageSettingRepositoryInterface $settings,
        private ImageUploadService $uploads,
    ) {
    }

    public function exists(string $key): bool
    {
        // Keys contain dots, so look them up in the array rather than with config dot notation.
        return array_key_exists($key, config('page_settings', []));
    }

    /**
     * @return array{type: string, label: string, default: string, original?: bool}
     */
    public function definition(string $key): array
    {
        if (!$this->exists($key)) {
            throw new InvalidArgumentException("Unknown page setting [{$key}].");
        }

        return config('page_settings')[$key];
    }

    public function type(string $key): string
    {
        return $this->definition($key)['type'];
    }

    /**
     * The value to display. rich_text comes back cleaned, ready for {!! !!}.
     */
    public function get(string $key): string
    {
        $definition = $this->definition($key);
        $value = $this->stored()[$key] ?? null;
        $value = $value ?? $definition['default'];

        return $definition['type'] === 'rich_text' ? RichText::clean($value) : (string) $value;
    }

    /**
     * True while the key still holds its registry default (nothing stored, or the same value).
     */
    public function isDefault(string $key): bool
    {
        $default = $this->definition($key)['default'];

        return ($this->stored()[$key] ?? $default) === $default;
    }

    /**
     * URL for image/video settings.
     */
    public function url(string $key): string
    {
        return Media::url($this->get($key));
    }

    public function set(string $key, string|UploadedFile|null $value): void
    {
        $type = $this->type($key);

        if ($value instanceof UploadedFile) {
            $this->uploads->delete($this->stored()[$key] ?? null);
            $value = $this->uploads->store($value, 'settings', optimize: !($this->definition($key)['original'] ?? false));
        } elseif ($type === 'rich_text') {
            $value = RichText::clean($value);
        }

        // An empty value means "use the default again".
        $this->settings->put($key, blank($value) ? null : $value);
        $this->stored = null;
    }

    /**
     * Field definitions with current values, for the VCMS editor.
     *
     * @param  list<string>  $keys
     * @return list<array<string, mixed>>
     */
    public function fields(array $keys): array
    {
        return array_map(function (string $key) {
            $definition = $this->definition($key);
            $field = [
                'name' => $key,
                'label' => $definition['label'],
                'type' => $definition['type'],
                'value' => $this->get($key),
            ];

            if (in_array($definition['type'], ['image', 'video'], true)) {
                $field['url'] = $this->url($key);
            }

            return $field;
        }, $keys);
    }

    /**
     * @return array<string, string|null>
     */
    private function stored(): array
    {
        return $this->stored ??= $this->settings->all();
    }
}
