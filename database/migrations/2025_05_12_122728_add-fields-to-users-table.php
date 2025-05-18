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
            $table->dropUnique('users_email_unique');
            $table->string('password')->nullable()->change();
            $table->string('email')->nullable()->change();
            $table->string('dialling_code')->default('+966')->nullable();
            $table->string('mobile')->nullable();
            $table->timestamp('mobile_verified_at')->nullable();
            $table->boolean('accept_terms')->default(0);
            $table->timestamp('last_login_at')->nullable();
            $table->string('social_provider')->nullable();

        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['dialling_code', 'social_provider']);
            $table->dropColumn('mobile');
            $table->dropColumn('mobile_verified_at');
            $table->dropColumn('accept_terms');
            $table->dropColumn('last_login_at');
        });
    }
};
