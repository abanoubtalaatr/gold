<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddStatusToCitiesTable extends Migration
{
    public function up()
    {
        Schema::table('cities', function (Blueprint $table) {
            $table->boolean('status')->default(true)->nullable()->after('wikiDataId');
        });
    }

    public function down()
    {
        Schema::table('cities', function (Blueprint $table) {
            $table->dropColumn('status');
        });
    }
}
