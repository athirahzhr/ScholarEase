// database/migrations/[timestamp]_add_missing_columns_to_scraping_logs_table.php
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('scraping_logs', function (Blueprint $table) {
            // Add missing columns
            $table->string('website_url')->nullable()->after('website_name');
            $table->integer('pages_to_scrape')->nullable()->after('website_url');
            $table->text('details')->nullable()->after('scholarships_added');
            $table->integer('duration_seconds')->default(0)->after('details');
            
            // Optional: Add more columns you might need
            // $table->string('mode')->nullable()->comment('test, real, predefined');
            // $table->integer('scholarships_updated')->default(0);
        });
    }

    public function down()
    {
        Schema::table('scraping_logs', function (Blueprint $table) {
            $table->dropColumn([
                'website_url',
                'pages_to_scrape',
                'details',
                'duration_seconds',
            ]);
        });
    }
};