<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateFaqTranslationsTable extends Migration
{
    public function up()
    {
        Schema::create('faq_translations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('faq_id')->constrained('faqs')->onDelete('cascade'); // ربط بالسؤال الأصلي
            $table->string('locale');
            $table->string('question');
            $table->text('answer');
            $table->unique(['faq_id', 'locale']);
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('faq_translations');
    }
}
