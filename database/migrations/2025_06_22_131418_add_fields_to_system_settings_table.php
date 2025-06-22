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
        Schema::table('system_settings', function (Blueprint $table) {
            $table->integer('max_canceled_orders')->default(3)->comment('Maximum canceled orders before account suspension');
            $table->decimal('vendor_debt_limit', 10, 2)->default(0.00)->comment('Maximum debt a vendor can have before order restrictions');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('system_settings', function (Blueprint $table) {
            $table->dropColumn(['max_canceled_orders', 'vendor_debt_limit']);
        });
    }
};
