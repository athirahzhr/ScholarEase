<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('scraping_logs', function (Blueprint $table) {
            $table->id();
            $table->string('website_name');
            $table->enum('status', ['in_progress', 'success', 'failed', 'test_completed']);
            $table->integer('pages_to_scrape')->nullable();
            $table->integer('scholarships_added')->default(0);
            $table->text('details')->nullable();
            $table->text('error_message')->nullable();
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('scraping_logs');
    }
};