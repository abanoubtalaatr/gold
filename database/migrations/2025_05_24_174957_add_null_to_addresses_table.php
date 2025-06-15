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
        Schema::table('addresses', function (Blueprint $table) {
            $table->string('street_name')->nullable()->change();
            $table->string('neighborhood')->nullable()->change();
            $table->string('address')->nullable()->change();
            $table->foreignId('country_id')->nullable()->change();
            $table->foreignId('state_id')->nullable()->change();
            $table->foreignId('city_id')->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('addresses', function (Blueprint $table) {
            $table->string('street_name')->nullable(false)->change();
            $table->string('neighborhood')->nullable(false)->change();
            $table->string('address')->nullable(false)->change();
            $table->foreignId('country_id')->nullable(false)->change();
            $table->foreignId('state_id')->nullable(false)->change();
            $table->foreignId('city_id')->nullable(false)->change();
        });
    }
};
