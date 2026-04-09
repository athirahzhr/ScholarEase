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
       Schema::create('scholarships', function (Blueprint $table) {
    $table->id();

    $table->string('name');
    $table->text('description')->nullable();
    $table->string('provider')->nullable();

    // 🔥 raw scraped data (VERY IMPORTANT)
    $table->longText('raw_eligibility')->nullable();

    $table->decimal('amount', 10, 2)->nullable();

    // rule-based categories
    $table->enum('academic_category', ['A1', 'A2', 'A3', 'A4'])->nullable();
    $table->enum('income_category', ['B1', 'B3', 'B4'])->nullable();
    $table->enum('study_path', ['C1', 'C2', 'C3', 'C4'])->nullable();

    $table->date('deadline')->nullable();
    $table->string('application_link');

    $table->enum('source', ['manual', 'scraped']);

    $table->timestamps();
});
    }


    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('scholarships');
    }
};
