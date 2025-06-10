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
        Schema::table('banner_translations', function (Blueprint $table) {
            // check if exist not migrate it 
            if (!Schema::hasColumn('banner_translations', 'description')) {
                $table->text('description')->nullable()->after('title');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('banner_translations', function (Blueprint $table) {
            $table->dropColumn('description');
        });
    }
};
