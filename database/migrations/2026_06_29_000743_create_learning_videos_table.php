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
        Schema::create('resource_videos', function (Blueprint $table) {

            $table->id();

            $table->string('title');

            $table->enum('category', [
                'Scholarship Journey',
                'Scholarship Tips',
                'Scholarship Interview',
            ]);

            $table->string('youtube_url');

            $table->text('description')->nullable();

            $table->boolean('is_active')->default(true);

            $table->timestamps();

        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('resource_videos');
    }
};