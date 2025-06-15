<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Step 1: Update any existing invalid values
        DB::table('order_sales')->whereNotIn('status', [
            'pending-approval',
            'approved',
            'sold',
            'rejected',
        ])->update(['status' => 'pending-approval']);

        // Step 2: Change the ENUM definition
        Schema::table('order_sales', function (Blueprint $table) {
            $table->enum('status', [
                'pending-approval',
                'approved',
                'sold',
                'rejected',
            ])->nullable()->default('pending-approval')->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('order_sales', function (Blueprint $table) {
            $table->enum('status', [
                'pending-approval',
                'approved',
                'sold',
                'rejected',
            ])->change();
        });
    }
};
