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
        Schema::create('lessons', function (Blueprint $table) {
            $table->id();

            $table->foreignId('course_id')
                  ->constrained()
                  ->cascadeOnDelete();

            $table->string('title');
            $table->string('slug')->unique();
            $table->longText('content')->nullable();
            $table->string('video_url')->nullable();

            $table->integer('duration_minutes')->default(0);
            $table->integer('order')->default(0);

            $table->boolean('is_free')->default(false);
            $table->boolean('is_published')->default(false);

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('lessons');
    }
};
