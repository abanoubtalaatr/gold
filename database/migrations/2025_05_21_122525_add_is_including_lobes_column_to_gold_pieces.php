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
        Schema::table('gold_pieces', function (Blueprint $table) {
            $table->boolean('is_including_lobes')->default(false);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('gold_pieces', function (Blueprint $table) {
            $table->dropColumn('is_including_lobes');
        });
    }
};
