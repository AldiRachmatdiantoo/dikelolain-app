<?php

namespace App\Console\Commands;

use App\Services\ImageUploadService;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Storage;
use Throwable;

/**
 * Makes the narrower copies (photo-480w.webp, ...) of pictures uploaded before uploads made them
 * themselves, e.g. the old site's photos. Safe to run again: pictures that have every copy are skipped.
 * On hosting without SSH, run it through the one-off script described in DEPLOY.md.
 */
class MediaVariantsCommand extends Command
{
    protected $signature = 'media:variants {--seconds=0 : berhenti setelah sekian detik (0 = tanpa batas); jalankan lagi untuk melanjutkan}';

    protected $description = 'Buat salinan kecil (untuk srcset) bagi gambar unggahan yang belum punya';

    /**
     * Where uploads live on the public disk. The old site's media/ folder is left out: its photos
     * were converted into uploads/legacy (PBI-520), and the few still used as they are only
     * miss srcset, not their width/height.
     */
    private const FOLDERS = ['uploads'];

    public function handle(ImageUploadService $images): int
    {
        $disk = Storage::disk('public');
        $seconds = (int) $this->option('seconds');
        $deadline = $seconds > 0 ? microtime(true) + $seconds : null;

        $pending = collect(self::FOLDERS)
            ->flatMap(fn (string $folder) => $disk->allFiles($folder))
            ->filter(fn (string $path) => $images->missingVariants($path) !== [])
            ->values();

        $done = 0;
        $written = 0;
        $failed = [];
        foreach ($pending as $path) {
            if ($deadline !== null && microtime(true) > $deadline) {
                break;
            }
            // One picture that cannot be processed must not stop the others.
            try {
                $written += $images->createVariants($path);
            } catch (Throwable $e) {
                report($e);
                $failed[$path] = $e->getMessage();
            }
            $done++;
        }

        $left = $pending->count() - $done;
        $this->line("Gambar diproses: {$done}, salinan dibuat: {$written}, sisa: {$left}.");

        if ($failed !== []) {
            $this->line('GAGAL: ' . count($failed) . ' gambar tidak bisa diproses, misalnya ' . array_key_first($failed) . ': ' . reset($failed));
            $this->line('BELUM SELESAI: kirim pesan ini ke pengembang.');

            return self::FAILURE;
        }

        $this->line($left === 0 ? 'SELESAI: semua gambar sudah punya salinan kecil.' : 'BELUM SELESAI: jalankan lagi untuk melanjutkan.');

        return self::SUCCESS;
    }
}
