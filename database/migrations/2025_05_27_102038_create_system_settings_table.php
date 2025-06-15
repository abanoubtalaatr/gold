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
        Schema::create('system_settings', function (Blueprint $table) {
            $table->id();

            // Commission Settings
            $table->decimal('platform_commission_percentage', 5, 2)->default(0);
            $table->decimal('merchant_commission_percentage', 5, 2)->default(0);

            // Tax Settings
            $table->decimal('tax_percentage', 5, 2)->default(0);

            // Gold Settings
            $table->decimal('gold_purchase_price', 10, 2)->default(0);
            $table->decimal('gold_purchase_additional_percentage', 5, 2)->default(0); // Can be positive, negative or zero
            $table->decimal('gold_rental_price_percentage', 5, 2)->default(0); // Positive only
            $table->decimal('gold_rental_insurance_percentage', 5, 2)->default(0); // Positive only

            // Order Settings
            $table->decimal('booking_insurance_amount', 10, 2)->default(0);
            $table->decimal('minimum_payout_amount', 10, 2)->default(0); // In SAR, positive
            $table->integer('max_delivery_time_hours')->default(24); // In hours, positive

            // Website Settings
            $table->text('privacy_policy')->nullable();
            $table->text('terms_conditions')->nullable();

            // Contact Information
            $table->string('contact_phone')->nullable();
            $table->string('contact_email')->nullable();
            $table->text('location_map')->nullable();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('system_settings');
    }
};
