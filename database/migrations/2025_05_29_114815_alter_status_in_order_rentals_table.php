<?php

use App\Models\OrderRental;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Step 1: Update any existing invalid status values
        DB::table('order_rentals')->whereNotIn('status', [
            'pending-approval',
            'approved',
            'piece_sent',
            'rented',
            'available',
            'sold'
        ])->update(['status' => 'pending_approval']);

        // Step 2: Change the ENUM definition
        Schema::table('order_rentals', function (Blueprint $table) {
            $table->enum('status', [
                'pending_approval',
                'approved',
                'piece_sent',
                'rented',
                'available',
                'sold'
            ])->nullable()->default('pending_approval')->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('order_rentals', function (Blueprint $table) {
            $table->enum('status', [
                'pending',
                'accepted', 
                'active',
                'completed',
                'cancelled'
            ])->nullable()->default('pending')->change();
        });
    }
};
