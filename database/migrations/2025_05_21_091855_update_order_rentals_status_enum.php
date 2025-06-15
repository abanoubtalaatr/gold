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
        Schema::table('order_rentals', function (Blueprint $table) {
            $table->enum('status', [
                'pending_approval',  // في انتظار المتجر
                'approved',           // تم القبول من المتجر
                'piece_sent',         // تم ارسال القطعة للمتجر
                'rented',             // مؤجرة حاليا
                'available',          // متاحة للإيجار مرة أخرى
                'sold',                // تم بيعها
                'rejected',            // تم رفضها
            ])->default('pending_approval')->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('order_rentals', function (Blueprint $table) {
            $table->enum('status', [
                'pending-approval',
                'approved',
                'sold',
                'rejected',

            ])->change();
        });
    }
};
