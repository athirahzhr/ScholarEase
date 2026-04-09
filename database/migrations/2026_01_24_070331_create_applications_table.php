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
    Schema::create('applications', function (Blueprint $table) {
        $table->id();
        $table->foreignId('user_id')->constrained()->onDelete('cascade');
        $table->foreignId('scholarship_id')->constrained()->onDelete('cascade');
        $table->string('status')->default('pending'); // pending, approved, rejected
        $table->text('notes')->nullable();
        $table->timestamps();
        
        $table->unique(['user_id', 'scholarship_id']); // One application per user per scholarship
    });
}


    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('applications');
    }
};
