<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('service_images', function (Blueprint $table) {
            $table->id();
            $table->foreignId('service_id')->constrained()->onDelete('cascade');
            $table->string('path');
            $table->integer('order')->default(0);
            $table->timestamps();

            $table->index(['service_id', 'order']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('service_images');
    }
};
