<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Schema;
use Tests\TestCase;

/**
 * The old key-value content tables are dropped (PBI-405); every page reads its own tables.
 */
class LegacyContentRemovedTest extends TestCase
{
    use RefreshDatabase;

    public function test_old_content_tables_are_gone(): void
    {
        foreach (['sections', 'section_contents', 'media'] as $table) {
            $this->assertFalse(Schema::hasTable($table), "{$table} still exists");
        }
    }

    public function test_old_content_models_are_gone(): void
    {
        foreach (['Section', 'SectionContent', 'Media'] as $model) {
            $this->assertFalse(class_exists("App\\Models\\{$model}"), "App\\Models\\{$model} still exists");
        }
    }
}
