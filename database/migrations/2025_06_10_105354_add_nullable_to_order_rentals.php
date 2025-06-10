<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('order_rentals', function (Blueprint $table) {
            // Drop existing foreign key constraint
            $table->dropForeign(['branch_id']);

            // Modify the column to be nullable
            $table->unsignedBigInteger('branch_id')->nullable()->change();

            // Re-add the foreign key constraint
            $table->foreign('branch_id')->references('id')->on('branches')->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::table('order_rentals', function (Blueprint $table) {
            // Drop foreign key constraint
            $table->dropForeign(['branch_id']);

            // Revert the column to be not nullable
            $table->unsignedBigInteger('branch_id')->nullable(false)->change();

            // Re-add the foreign key constraint
            $table->foreign('branch_id')->references('id')->on('branches')->onDelete('cascade');
        });
    }
};