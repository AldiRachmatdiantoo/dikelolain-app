<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('page_list_items', function (Blueprint $table) {
            $table->id();
            $table->string('list_key', 30);
            $table->string('text', 200);
            $table->string('icon')->nullable();
            $table->unsignedInteger('sort_order')->default(0);
            $table->boolean('is_active')->default(true);
            $table->timestamps();

            $table->index(['list_key', 'sort_order']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('page_list_items');
    }
};
