<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('scholarships', function (Blueprint $table) {

            if (!Schema::hasColumn('scholarships', 'is_official')) {
                $table->boolean('is_official')->default(false)->after('updated_at');
            }

            if (!Schema::hasColumn('scholarships', 'source_website')) {
                $table->string('source_website')->nullable()->after('is_official');
            }

        });
    }

    public function down()
    {
        Schema::table('scholarships', function (Blueprint $table) {
            if (Schema::hasColumn('scholarships', 'is_official')) {
                $table->dropColumn('is_official');
            }

            if (Schema::hasColumn('scholarships', 'source_website')) {
                $table->dropColumn('source_website');
            }
        });
    }
};