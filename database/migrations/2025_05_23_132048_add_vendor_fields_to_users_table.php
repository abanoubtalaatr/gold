<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('logo')->nullable()->after('commercial_registration_image');
            $table->string('iban')->nullable()->after('logo');
            $table->string('city')->nullable()->after('iban');
            $table->text('working_hours')->nullable()->after('longitude');
            $table->enum('venodr_status', ['pending', 'approved', 'rejected'])->default('pending')->after('is_active');
            $table->text('rejection_reason')->nullable()->after('venodr-status');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('logo');
            $table->dropColumn('iban');
            $table->dropColumn('city');
            $table->dropColumn('working_hours');
            $table->dropColumn('vedor_status');
            $table->dropColumn('rejection_reason');
        });
    }
};
