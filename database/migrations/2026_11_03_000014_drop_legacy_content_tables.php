<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * The old key-value content (sections + section_contents + media), replaced by one table per
 * entity and page_settings in Epic 2–3. down() brings back the empty tables, not their rows.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::dropIfExists('section_contents');
        Schema::dropIfExists('media');
        Schema::dropIfExists('sections');
    }

    public function down(): void
    {
        Schema::create('sections', function (Blueprint $table) {
            $table->id();
            $table->string('key')->unique();
            $table->string('name');
            $table->string('type')->default('default');
            $table->boolean('is_active')->default(true);
            $table->integer('order')->default(0);
            $table->timestamps();
        });

        Schema::create('media', function (Blueprint $table) {
            $table->id();
            $table->string('filename');
            $table->string('original_name');
            $table->string('mime_type');
            $table->unsignedBigInteger('size');
            $table->string('path');
            $table->enum('type', ['image', 'video'])->default('image');
            $table->text('alt_text')->nullable();
            $table->timestamps();
        });

        Schema::create('section_contents', function (Blueprint $table) {
            $table->id();
            $table->foreignId('section_id')->constrained()->onDelete('cascade');
            $table->string('key');
            $table->string('type')->default('text');
            $table->text('value')->nullable();
            $table->integer('order')->default(0);
            $table->foreignId('media_id')->nullable()->constrained()->onDelete('set null');
            $table->timestamps();

            $table->index(['section_id', 'key']);
        });
    }
};
