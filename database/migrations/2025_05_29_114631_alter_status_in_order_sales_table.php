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
        Schema::table('order_sales', function (Blueprint $table) {
            DB::table('order_sales')->whereNotIn('status', [
                'pending-approval',
                'approved',
                'sold',
                'rejected'
            ])->update(['status' => 'pending_approval']);
    
            // Step 2: Change the ENUM definition
            Schema::table('order_sales', function (Blueprint $table) {
                $table->enum('status', [
                    OrderRental::statuses()
                ])->nullable()->default('pending_approval')->change();
            });
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('order_sales', function (Blueprint $table) {
            //
        });
    }
};
