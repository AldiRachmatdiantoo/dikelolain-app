<?php

namespace App\Legacy;

/**
 * Readable summary of an import plan (and of the result, once applied), in Indonesian like the
 * rest of the project documents.
 */
final class LegacyReport
{
    /**
     * @param  array<string, mixed>  $plan  from LegacyImporter::plan()
     * @param  array<string, mixed>|null  $result  from LegacyImporter::apply()
     */
    public static function render(array $plan, ?array $result = null): string
    {
        $lines = [$result === null ? 'Rencana import konten web lama (belum ditulis)' : 'Hasil import konten web lama', ''];

        $lines[] = 'Nilai tunggal (page_settings): ' . count($plan['settings']);
        foreach ($plan['settings'] as $key => $value) {
            $lines[] = '  ' . str_pad($key, 32) . self::short($value);
        }

        $lines[] = '';
        $lines[] = 'Daftar:';
        foreach ($plan['lists'] as $list => $entry) {
            $what = isset($entry['seed'])
                ? 'contoh bawaan, sama seperti yang tampil di web lama (' . class_basename($entry['seed']) . ')'
                : count($entry['rows']) . ' baris';
            if ($result !== null) {
                $what .= ', tersimpan ' . ($result['counts'][$list] ?? 0);
            }
            $lines[] = '  ' . str_pad($list, 18) . $what;
            foreach ($entry['rows'] ?? [] as $row) {
                $label = $row['name'] ?? $row['title'] ?? $row['question'] ?? $row['text'] ?? $row['label'] ?? $row['logo'] ?? '';
                $lines[] = '      - ' . self::short((string) $label, 70);
            }
        }

        if ($result !== null) {
            $lines[] = '';
            $lines[] = "Gambar unggahan diubah ke WebP: {$result['webp']}";
            if ($result['kept'] !== []) {
                $lines[] = 'Gambar yang dipakai apa adanya (file tidak ada, bukan JPG/PNG, atau WebP tidak lebih kecil):';
                foreach ($result['kept'] as $path) {
                    $lines[] = "  - {$path}";
                }
            }
        }

        $lines[] = '';
        $lines[] = 'Catatan:';
        foreach ($plan['notes'] ?: ['tidak ada'] as $note) {
            $lines[] = "  - {$note}";
        }

        $skipped = array_sum(array_map('count', $plan['skipped']));
        $lines[] = '';
        $lines[] = "Kunci yang sengaja dilewati (catatan internal editor lama, tidak pernah tampil): {$skipped}";

        $lines[] = '';
        $lines[] = 'Kunci yang tidak dikenali (perlu dicek manual):';
        if ($plan['unknown'] === []) {
            $lines[] = '  tidak ada';
        }
        foreach ($plan['unknown'] as $section => $keys) {
            foreach ($keys as $key) {
                $lines[] = "  - {$section}: {$key}";
            }
        }

        return implode("\n", $lines) . "\n";
    }

    private static function short(string $value, int $max = 90): string
    {
        $value = trim(preg_replace('/\s+/u', ' ', $value));

        return $value === '' ? '(kosong)' : (mb_strlen($value) > $max ? mb_substr($value, 0, $max - 1) . '…' : $value);
    }
}
