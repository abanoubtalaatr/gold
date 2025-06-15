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
        Schema::table('branches', function (Blueprint $table) {
            // for each one if the column is not exist add it
            if (! Schema::hasColumn('branches', 'number_of_available_items')) {
                $table->integer('number_of_available_items')->default(0);
            }
            if (! Schema::hasColumn('branches', 'user_id')) {
                $table->foreignId('user_id')->nullable()->constrained('users')->cascadeOnDelete();
            }
            if (! Schema::hasColumn('branches', 'contact_number')) {
                $table->string('contact_number')->nullable();
            }
            if (! Schema::hasColumn('branches', 'contact_email')) {
                $table->string('contact_email')->nullable();
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('branches', function (Blueprint $table) {
            //
        });
    }
};
