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
        $table->string('website_url')->nullable();
        $table->integer('pages_to_scrape')->nullable();
        $table->text('details')->nullable();
        $table->integer('duration_seconds')->default(0);
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