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
        // Check if the column already exists, if so, skip this migration
        if (Schema::hasColumn('cities', 'state_id')) {
            return;
        }

        Schema::table('cities', function (Blueprint $table) {
            $table->foreignId('state_id')->nullable()->constrained('states')->cascadeOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('cities', function (Blueprint $table) {
            //
        });
    }
};
