<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('team_members', function (Blueprint $table) {
            $table->id();
            $table->string('slug', 120)->unique();
            $table->string('name', 100);
            $table->string('role', 100);
            $table->string('photo');
            $table->string('experience', 50)->nullable();
            $table->text('about')->nullable();
            $table->text('expertise_intro')->nullable();
            $table->text('skills_intro')->nullable();
            $table->unsignedInteger('sort_order')->default(0);
            $table->boolean('is_active')->default(true);
            $table->timestamps();

            $table->index(['is_active', 'sort_order']);
        });

        Schema::create('team_member_skills', function (Blueprint $table) {
            $table->id();
            $table->foreignId('team_member_id')->constrained()->cascadeOnDelete();
            $table->string('name', 100);
            $table->unsignedTinyInteger('percent');
            $table->unsignedInteger('sort_order')->default(0);
            $table->boolean('is_active')->default(true);
            $table->timestamps();

            $table->index(['team_member_id', 'sort_order']);
        });

        Schema::create('team_member_expertises', function (Blueprint $table) {
            $table->id();
            $table->foreignId('team_member_id')->constrained()->cascadeOnDelete();
            $table->string('name', 150);
            $table->unsignedInteger('sort_order')->default(0);
            $table->boolean('is_active')->default(true);
            $table->timestamps();

            $table->index(['team_member_id', 'sort_order']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('team_member_expertises');
        Schema::dropIfExists('team_member_skills');
        Schema::dropIfExists('team_members');
    }
};
