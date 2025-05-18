<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('mobile_confirm', function (Blueprint $table) {
            $table->id();
            $table->string('mobile')->index();
            $table->string('dialling_code')->default('+966')->nullable();
            $table->unsignedBigInteger('user_id')->nullable();
            $table->string('code');
            $table->timestamps();
        });
        Schema::table('mobile_confirm', function (Blueprint $table) {
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('mobile_confirm');
    }
};
