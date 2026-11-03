<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('pricing_plans', function (Blueprint $table) {
            $table->id();
            $table->string('name', 100);
            $table->string('price', 50);
            $table->string('period', 50)->nullable();
            $table->boolean('is_featured')->default(false);
            $table->string('button_text', 50)->nullable();
            $table->string('button_link', 2048)->nullable();
            $table->unsignedInteger('sort_order')->default(0);
            $table->boolean('is_active')->default(true);
            $table->timestamps();

            $table->index(['is_active', 'sort_order']);
        });

        Schema::create('pricing_features', function (Blueprint $table) {
            $table->id();
            $table->foreignId('pricing_plan_id')->constrained()->cascadeOnDelete();
            $table->string('text', 200);
            $table->unsignedInteger('sort_order')->default(0);
            $table->boolean('is_active')->default(true);
            $table->timestamps();

            $table->index(['pricing_plan_id', 'sort_order']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pricing_features');
        Schema::dropIfExists('pricing_plans');
    }
};
