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
        Schema::create('categories', function (Blueprint $table) {
            $table->id();
            $table->string('name', 100)->unique()->default('');
            $table->boolean('is_active')->default(true);
            $table->string('type', 150)->nullable()->default('service');
            $table->string('image')->nullable();
            $table->timestamps();
        });

        Schema::create('categories_translations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('categorie_id')
                ->constrained('categories')
                ->cascadeOnDelete();
            $table->string('locale')->index();
            $table->string('name')->default('');
            $table->unique(['categorie_id', 'locale']);
            $table->timestamps();
        });

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('categories');
        Schema::dropIfExists('categories_translations');

    }
};
