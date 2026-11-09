<?php

namespace App\Console\Commands;

use App\Legacy\LegacyContent;
use App\Legacy\LegacyImporter;
use App\Legacy\LegacyReport;
use Illuminate\Console\Command;

/**
 * Shows what the migration would move from the old site's tables, without writing anything.
 * Run it on a copy of the old database before `php artisan migrate` (see DEPLOY.md).
 */
class LegacyReportCommand extends Command
{
    protected $signature = 'legacy:report';

    protected $description = 'Tampilkan rencana pemindahan konten dari tabel web lama (tanpa menulis apa pun)';

    public function handle(LegacyImporter $importer): int
    {
        $legacy = LegacyContent::fromDatabase();

        if ($legacy->isEmpty()) {
            $this->info('Tidak ada konten web lama di database ini (tabel sections/section_contents kosong atau sudah dihapus).');

            return self::SUCCESS;
        }

        $this->line(LegacyReport::render($importer->plan($legacy)));

        return self::SUCCESS;
    }
}
