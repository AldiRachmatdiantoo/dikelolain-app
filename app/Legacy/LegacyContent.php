<?php

namespace App\Legacy;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

/**
 * The content of the old site: rows of section_contents, grouped by the key of their section.
 *
 * Reads the way the old views did: only active sections, the first row wins when a key appears
 * twice, and list items keep the order their rows were created in. Every key the importer reads
 * or deliberately skips is marked, so whatever is left over can be reported.
 */
final class LegacyContent
{
    /** @var array<string, array<string, string>> section => key => value (first row wins) */
    private array $values = [];

    /** @var array<string, array<string, true>> */
    private array $read = [];

    /** @var array<string, array<string, true>> */
    private array $skipped = [];

    /**
     * @param  iterable<array{section: string, key: string, value: ?string}>  $rows  in creation order
     */
    public function __construct(iterable $rows)
    {
        foreach ($rows as $row) {
            $this->values[$row['section']][$row['key']] ??= (string) ($row['value'] ?? '');
        }
    }

    public static function fromDatabase(): self
    {
        if (!Schema::hasTable('sections') || !Schema::hasTable('section_contents')) {
            return new self([]);
        }

        $rows = DB::table('section_contents')
            ->join('sections', 'sections.id', '=', 'section_contents.section_id')
            ->where('sections.is_active', true)
            ->orderBy('section_contents.id')
            ->get(['sections.key as section', 'section_contents.key as key', 'section_contents.value as value'])
            ->map(fn ($row) => (array) $row);

        return new self($rows);
    }

    public function isEmpty(): bool
    {
        return $this->values === [];
    }

    public function hasSection(string $section): bool
    {
        return isset($this->values[$section]);
    }

    /**
     * The stored value, or null when the old site had no row for it (and showed its default).
     * An empty string means the admin cleared the field.
     */
    public function value(string $section, string $key): ?string
    {
        if (!isset($this->values[$section][$key])) {
            return null;
        }

        $this->read[$section][$key] = true;

        return $this->values[$section][$key];
    }

    /**
     * The first of several keys that has a non-empty value (the old views fell back the same way).
     * Every key of the chain counts as read, also the ones that lost.
     */
    public function firstFilled(string $section, string ...$keys): ?string
    {
        $filled = null;
        $found = null;
        foreach ($keys as $key) {
            $value = $this->value($section, $key);
            if ($filled === null && $value !== null && trim($value) !== '') {
                $filled = $value;
            }
            $found ??= $value;
        }

        return $filled ?? $found;
    }

    /**
     * Item ids captured by the first group of $pattern, in the order they first appear.
     *
     * @return list<string>
     */
    public function ids(string $section, string $pattern): array
    {
        $ids = [];
        foreach (array_keys($this->values[$section] ?? []) as $key) {
            if (preg_match($pattern, $key, $match)) {
                $ids[$match[1]] = true;
            }
        }

        return array_map('strval', array_keys($ids));
    }

    /**
     * Values of the keys matching $pattern, keyed by the number in its first group, in number order.
     *
     * @return array<int, string>
     */
    public function numbered(string $section, string $pattern): array
    {
        $items = [];
        foreach ($this->values[$section] ?? [] as $key => $value) {
            if (preg_match($pattern, $key, $match)) {
                $items[(int) $match[1]] ??= $value;
                $this->read[$section][$key] = true;
            }
        }
        ksort($items);

        return $items;
    }

    /**
     * Marks keys the old views never showed (editor bookkeeping), so they are not reported as unknown.
     */
    public function skip(string $section, string $pattern = '/.*/'): void
    {
        foreach (array_keys($this->values[$section] ?? []) as $key) {
            if (preg_match($pattern, $key)) {
                $this->skipped[$section][$key] = true;
            }
        }
    }

    /**
     * @return array<string, list<string>> keys that were deliberately skipped
     */
    public function skippedKeys(): array
    {
        return array_map('array_keys', $this->skipped);
    }

    /**
     * @return array<string, list<string>> keys that were neither read nor skipped
     */
    public function unknownKeys(): array
    {
        $unknown = [];
        foreach ($this->values as $section => $keys) {
            foreach (array_keys($keys) as $key) {
                if (!isset($this->read[$section][$key]) && !isset($this->skipped[$section][$key])) {
                    $unknown[$section][] = $key;
                }
            }
        }

        return $unknown;
    }
}
