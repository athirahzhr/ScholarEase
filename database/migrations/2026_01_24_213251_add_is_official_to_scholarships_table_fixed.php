<?php
// database/migrations/2026_01_25_add_is_official_to_scholarships_table_fixed.php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('scholarships', function (Blueprint $table) {
            // Add after 'updated_at' (which is the last column)
            $table->boolean('is_official')->default(false)->after('updated_at');
            $table->string('source_website')->nullable()->after('is_official');
        });
    }

    public function down()
    {
        Schema::table('scholarships', function (Blueprint $table) {
            $table->dropColumn(['is_official', 'source_website']);
        });
    }
};