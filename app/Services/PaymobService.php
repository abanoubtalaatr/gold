<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class PaymobService
{
    protected $apiKey;
    protected $publicKey;
    protected $baseUrl = 'https://ksa.paymob.com';
    protected $integrationId;

    public function __construct()
    {
        $this->apiKey = config('services.paymob.api_key');
        $this->publicKey = config('services.paymob.public_key');
        $this->integrationId = config('services.paymob.integration_id');
        
    }

    /**
     * Create Payment Intention
     */
    public function createPaymentIntention(array $data)
    {
        
        $response = Http::withHeaders([
            'Authorization' => 'Token ' . $this->apiKey,
            'Content-Type' => 'application/json',
        ])->post("{$this->baseUrl}/v1/intention/", [
            'amount' => $data['amount'], // Convert to cents
            'currency' => 'SAR',
            'payment_methods' => [intval($this->integrationId) ],
            'items' => $data['items'],
            'billing_data' => $data['billing_data'],
            'customer' => $data['customer'],
            'extras' => $data['extras'],
            'notification_url' => $data['notification_url'],
        ]);

        if ($response->failed()) {
            $error = $response->json()['message'] ?? 'Unknown error';
            
            throw new \Exception('Payment intention failed: ' . $error);
        }

        return $response->json()['client_secret'];
    }

    /**
     * Get Unified Checkout URL
     */
    public function getCheckoutUrl(array $data)
    {
        try {
            // Step 1: Create Payment Intention
            $clientSecret = $this->createPaymentIntention($data);

            // Step 2: Construct Unified Checkout URL
            $checkoutUrl = "https://ksa.paymob.com/unifiedcheckout/?publicKey={$this->publicKey}&clientSecret={$clientSecret}";
            Log::info('Paymob checkout URL generated: ' . $checkoutUrl);

            return $clientSecret;
        } catch (\Exception $e) {
            Log::error('Paymob checkout URL generation failed: ' . $e->getMessage());
            throw $e;
        }
    }
}