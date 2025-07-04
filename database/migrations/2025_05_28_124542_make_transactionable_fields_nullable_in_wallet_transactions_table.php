<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::table('wallet_transactions', function (Blueprint $table) {
            // Change existing columns to nullable
            $table->string('transactionable_type')->nullable()->change();
            $table->unsignedBigInteger('transactionable_id')->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down()
    {
        Schema::table('wallet_transactions', function (Blueprint $table) {
            // Revert back to not nullable if needed
            $table->string('transactionable_type')->nullable(false)->change();
            $table->unsignedBigInteger('transactionable_id')->nullable(false)->change();
        });
    }
};
