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
            // chec every one of them if exist before not add it 
            if (!Schema::hasColumn('users', 'logo')) {
                $table->string('logo')->nullable()->after('commercial_registration_image');
            }
            if (!Schema::hasColumn('users', 'iban')) {
                $table->string('iban')->nullable()->after('logo');
            }
            if (!Schema::hasColumn('users', 'city')) {
                $table->string('city')->nullable()->after('iban');
            }
            if (!Schema::hasColumn('users', 'working_hours')) {
                $table->text('working_hours')->nullable()->after('longitude');
            }
            if (!Schema::hasColumn('users', 'vendor_status')) {
                $table->enum('vendor_status', ['pending', 'approved', 'rejected'])->default('pending')->after('is_active');
            }
            if (!Schema::hasColumn('users', 'rejection_reason')) {
                $table->text('rejection_reason')->nullable()->after('vendor_status');
            }
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
            $table->dropColumn('vendor_status');
            $table->dropColumn('rejection_reason');
        });
    }
};
