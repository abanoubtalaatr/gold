<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('notifications', function (Blueprint $table) {
            $table->string('title')->nullable();
            $table->text('message')->nullable();
            $table->enum('recipient_type', ['all', 'providers', 'individual'])->default('all');
            $table->uuid('recipient_id')->nullable();
            $table->enum('status', ['draft', 'scheduled', 'sent', 'failed'])->default('sent');
            $table->timestamp('scheduled_at')->nullable();
            $table->timestamp('sent_at')->nullable();
            $table->foreignId('created_by')->nullable()->constrained('users');

        });
    }

    public function down()
    {
        Schema::table('notifications', function (Blueprint $table) {
            $table->dropColumn([
                'title',
                'message',
                'recipient_type',
                'recipient_id',
                'status',
                'scheduled_at',
                'sent_at',
                'created_by',
            ]);
        });
    }
};
