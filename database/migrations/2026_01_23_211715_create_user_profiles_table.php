<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
   public function up()
    {
        Schema::create('user_profiles', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->enum('academic_category', ['A1', 'A2', 'A3', 'A4'])->nullable();
            $table->enum('income_category', ['B1', 'B3', 'B4'])->nullable();
            $table->enum('study_path', ['C1', 'C2', 'C3', 'C4'])->nullable();
            $table->json('spm_results')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('user_profiles');
    }
};
