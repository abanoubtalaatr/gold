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
        Schema::table('users', function (Blueprint $table) {
            $table->string('store_name_en')->nullable();
            $table->string('store_name_ar')->nullable();
            $table->string('commercial_registration_number')->nullable();
            $table->string('commercial_registration_image')->nullable();

            
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('store_name_en');
            $table->dropColumn('store_name_ar');
            $table->dropColumn('commercial_registration_number');
            $table->dropColumn('commercial_registration_image');
        });
    }
};