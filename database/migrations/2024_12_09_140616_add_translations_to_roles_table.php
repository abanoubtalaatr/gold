<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddTranslationsToRolesTable extends Migration
{
    public function up()
    {
        Schema::create('role_translations', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('role_id');
            $table->string('locale')->index();
            $table->string('name'); // الحقل المترجم
            $table->timestamps();

            $table->unique(['role_id', 'locale']);
            $table->foreign('role_id')->references('id')->on('roles')->onDelete('cascade');
        });
    }

    public function down()
    {
        Schema::dropIfExists('role_translations');
    }
}
