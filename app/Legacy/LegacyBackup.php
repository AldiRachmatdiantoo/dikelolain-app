<?php

namespace App\Legacy;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Storage;

/**
 * Copies the old content tables to a JSON file before they are dropped, so nothing is lost even if
 * the import missed something. Kept in storage/app/private/backups (never published, not in git).
 */
final class LegacyBackup
{
    private const TABLES = ['sections', 'section_contents', 'media'];

    /**
     * @return string path of the file, relative to storage/app/private
     */
    public static function store(string $stamp): string
    {
        $data = ['exported_at' => now()->toIso8601String()];
        foreach (self::TABLES as $table) {
            $data[$table] = Schema::hasTable($table) ? DB::table($table)->orderBy('id')->get()->all() : [];
        }

        $path = "backups/legacy-content-{$stamp}.json";
        Storage::disk('local')->put($path, json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES));

        return $path;
    }
}
