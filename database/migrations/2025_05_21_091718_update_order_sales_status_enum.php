<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('order_sales', function (Blueprint $table) {
            $table->enum('status', [
                'pending-approval',
                'approved',
                'sold',
                'rejected'
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
                'rejected'
            ])->change();
        });
    }
};