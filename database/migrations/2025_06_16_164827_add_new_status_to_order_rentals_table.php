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
                'pending_approval',
                'approved',
                'piece_sent',
                'vendor_already_take_the_piece',
                'rented',
                'available',
                'sold',
                'rejected',
            ])->default('pending_approval')->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('order_rentals', function (Blueprint $table) {
            //
        });
    }
};
