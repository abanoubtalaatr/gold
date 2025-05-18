<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        //i want if exist branch id not make it again
        if (!Schema::hasColumn('order_rentals', 'branch_id')) {                             
            Schema::table('order_rentals', function (Blueprint $table) {
                $table->foreignId('branch_id')->after('gold_piece_id')->constrained('branches')->onDelete('cascade');
            });
        }

        if (!Schema::hasColumn('order_sales', 'branch_id')) {
            Schema::table('order_sales', function (Blueprint $table) {
                $table->foreignId('branch_id')->after('gold_piece_id')->constrained('branches')->onDelete('cascade');
            });
        }
    }

    public function down(): void
    {
        Schema::table('order_rentals', function (Blueprint $table) {
            $table->dropForeign(['branch_id']);
            $table->dropColumn('branch_id');
        });

        Schema::table('order_sales', function (Blueprint $table) {
            $table->dropForeign(['branch_id']);
            $table->dropColumn('branch_id');
        });
    }
};
