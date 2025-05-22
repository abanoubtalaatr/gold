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
        Schema::table('contacts', function (Blueprint $table) {
            $table->unsignedBigInteger('sale_order_id')->nullable();
            $table->unsignedBigInteger('rental_order_id')->nullable();

            // Add foreign key constraints if desired
            $table->foreign('sale_order_id')->references('id')->on('order_sales')->onDelete('set null');
            $table->foreign('rental_order_id')->references('id')->on('order_rentals')->onDelete('set null');
        });
    }

    public function down()
    {
        Schema::table('contacts', function (Blueprint $table) {
            $table->dropForeign(['sale_order_id']);
            $table->dropForeign(['rental_order_id']);
            $table->dropColumn('sale_order_id');
            $table->dropColumn('rental_order_id');
        });
    }
};