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
        Schema::create('ratings', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('gold_piece_id')->constrained()->onDelete('cascade');
            $table->decimal('rating', 2, 1)->comment('Rating from 1 to 5');
            $table->text('comment')->nullable();
            $table->timestamps();

            // Ensure a user can only rate a gold piece once
            $table->unique(['user_id', 'gold_piece_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('ratings');
    }
}; 