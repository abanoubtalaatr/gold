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
        Schema::create('wallet_transactions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('wallet_id')->constrained()->onDelete('cascade');
            $table->enum('type', ['credit', 'debit']);
            $table->decimal('amount', 15, 2);
            $table->string('description');
            $table->enum('status', ['pending', 'completed', 'failed'])->default('completed');
            // $table->morphs('transactionable'); // Polymorphic relation
            // Replace morphs with explicit columns
            $table->string('transactionable_type');
            $table->unsignedBigInteger('transactionable_id');

            // Add index with a shorter name
            $table->index(['transactionable_type', 'transactionable_id'], 'wallet_tx_transactionable_idx');

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('wallet_transactions');
    }
};