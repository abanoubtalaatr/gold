<?php

namespace App\Console\Commands;

use App\Services\PaymobService;
use Illuminate\Console\Command;

class TestPaymobCheckout extends Command
{
    protected $signature = 'paymob:test {amount=10}';
    protected $description = 'Test Paymob checkout integration with Payment Intention API';

    public function handle()
    {
        try {
            $amount = $this->argument('amount');
            $this->info("Testing Paymob checkout for amount: {$amount} SAR");

            $data = [
                'amount' => (float) $amount,
                'payment_methods' => [12, 'card', 'your_integration_name_or_id'],
                'items' => [
                    [
                        'name' => 'Item name 1',
                        'amount' => (float) $amount,
                        'description' => 'Watch',
                        'quantity' => 1,
                    ],
                ],
                'billing_data' => [
                    'apartment' => '6',
                    'first_name' => 'Ammar',
                    'last_name' => 'Sadek',
                    'street' => '938, Al-Jadeed Bldg',
                    'building' => '939',
                    'phone_number' => '+96824480228',
                    'country' => 'KSA',
                    'email' => 'AmmarSadek@gmail.com',
                    'floor' => '1',
                    'state' => 'Alkhuwair',
                ],
                'customer' => [
                    'first_name' => 'Ammar',
                    'last_name' => 'Sadek',
                    'email' => 'AmmarSadek@gmail.com',
                    'extras' => ['re' => '22'],
                ],
                'extras' => ['ee' => '22'],
                'notification_url' => 'https://webhook.site/c2ec5b1c-14ab-4827-add2-e550d9edf48b',
            ];

            $paymobService = new PaymobService();
            $clientSecret = $paymobService->getCheckoutUrl($data);

            $this->info('Checkout URL generated successfully:');
            $this->line("https://ksa.paymob.com/unifiedcheckout/?publicKey=" . config('services.paymob.public_key') . "&clientSecret={$clientSecret}");
            $this->info('Open the URL in a browser and use Paymob test cards (e.g., 5123450000000008, expiry 12/25, CVV 123) to complete the payment.');
        } catch (\Exception $e) {
            $this->error('Error: ' . $e->getMessage());
            $this->line('Check storage/logs/laravel.log for details.');
        }
    }
}