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
        Schema::create('gold_pieces', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->decimal('weight', 8, 2);
            $table->integer('carat')->in([18, 21, 24]);
            $table->decimal('rental_price_per_day', 10, 2)->nullable();
            $table->decimal('sale_price', 10, 2)->nullable();
            $table->decimal('deposit_amount', 10, 2)->nullable();
            $table->foreignId('branch_id')->nullable()->constrained()->onDelete('set null');
            $table->foreignId('user_id')->nullable()->constrained()->onDelete('set null');
            $table->enum('type', ['for_rent', 'for_sale'])->nullable();
            $table->enum('status', ['pending', 'accepted', 'rented', 'sold', 'available'])->default('pending');
            $table->string('qr_code')->nullable();
            $table->text('description')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('gold_pieces');
    }
};
