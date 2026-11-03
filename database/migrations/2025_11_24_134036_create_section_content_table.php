<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('section_contents', function (Blueprint $table) {
            $table->id();
            $table->foreignId('section_id')->constrained()->onDelete('cascade');
            $table->string('key'); // 'title', 'description', 'button_text', 'hero-content-1', etc
            $table->string('type')->default('text'); // text, textarea, image, video, url, rich-text, json
            $table->text('value')->nullable(); // Content value (can be JSON for complex data)
            $table->integer('order')->default(0); // For ordering list items
            $table->foreignId('media_id')->nullable()->constrained()->onDelete('set null'); // Link to media table
            $table->timestamps();

            // Index for faster queries
            $table->index(['section_id', 'key']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('section_contents');
    }
};