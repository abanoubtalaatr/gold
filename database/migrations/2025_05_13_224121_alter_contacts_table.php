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
            $table->string('phone')->nullable()->change();
            $table->string('name')->nullable()->change();
            $table->string('email')->nullable()->change();
            // Add 'reply' column only if it doesn't exist
        if (!Schema::hasColumn('contacts', 'reply')) {
            Schema::table('contacts', function (Blueprint $table) {
                $table->text('reply')->nullable();
            });
        }

        // Add 'user_id' column only if it doesn't exist
        if (!Schema::hasColumn('contacts', 'user_id')) {
            Schema::table('contacts', function (Blueprint $table) {
                $table->foreignId('user_id')->nullable()->constrained('users');
            });
        }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('contacts', function (Blueprint $table) {
            //
        });
    }
};
