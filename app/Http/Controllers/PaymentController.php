<?php

namespace App\Http\Controllers;

use App\Models\User;
use Inertia\Inertia;
use Illuminate\Http\Request;
use App\Services\PaymobService;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Log;

class PaymentController extends Controller
{
    protected $paymobService;

    public function callback(Request $request)
    {
        try {
            // Log the incoming request for debugging
            Log::info('Payment callback triggered', ['request_data' => $request->all()]);

            // Extract the transaction data from the request
            $transaction = $request->input('obj');
            if (!$transaction || !isset($transaction['success']) || !$transaction['success']) {
                Log::error('Invalid or failed transaction', ['transaction' => $transaction]);
                return response()->json(['status' => 'failed', 'message' => 'Invalid or failed transaction'], 400);
            }

            // Extract payment details
            $amountCents = $transaction['amount_cents'] ?? 0;
            $amount = $amountCents / 100; // Convert cents to SAR
            $phoneNumber = $transaction['order']['shipping_data']['phone_number'] ?? null;
            $transactionId = $transaction['id'] ?? null;

            if (!$phoneNumber) {
                Log::error('Phone number not found in transaction', ['transaction' => $transaction]);
                return response()->json(['status' => 'failed', 'message' => 'Phone number not found'], 400);
            }

            // Find the user by phone number
            $user = User::where('phone_number', $phoneNumber)->first();
            if (!$user) {
                Log::error('User not found', ['phone_number' => $phoneNumber]);
                return response()->json(['status' => 'failed', 'message' => 'User not found'], 404);
            }

            // Update user debt by subtracting the payment amount
            $newDebt = max(0, $user->debt - $amount); // Ensure debt doesn't go negative
            $user->update(['debt' => $newDebt]);

            // Log the successful update
            Log::info('User debt updated successfully', [
                'user_id' => $user->id,
                'phone_number' => $phoneNumber,
                'transaction_id' => $transactionId,
                'amount' => $amount,
                'new_debt' => $newDebt,
            ]);

            // Redirect to the success page with payment details
            return redirect()->route('vendor.payment.success', [
                'transaction_id' => $transactionId,
                'amount' => $amount,
                'currency' => $transaction['currency'] ?? 'SAR',
                'order_id' => $transaction['order']['id'] ?? null,
            ]);

        } catch (\Exception $e) {
            Log::error('Payment callback processing failed', [
                'error' => $e->getMessage(),
                'request_data' => $request->all(),
            ]);
            return response()->json(['status' => 'error', 'message' => 'Internal server error'], 500);
        }
    }
    public function notification(Request $request)
    {
        return Inertia::render('Vendor/Success_payment');
    }
    
    public function __construct(PaymobService $paymobService)
    {
        $this->paymobService = $paymobService;
    }

    /**
     * Initiate Checkout (API)
     */
    public function initiateCheckout(Request $request): JsonResponse
    {
        $request->validate([
            'amount' => 'required|numeric|min:1',
        ]);

        try {
            $amount = $request->input('amount');
            $checkoutUrl = $this->paymobService->getCheckoutUrl($amount);

            return response()->json(['checkout_url' => $checkoutUrl]);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    /**
     * Redirect to Checkout (Browser Testing)
     */
    public function redirectToCheckout(Request $request)
    {
        $request->validate([
            'amount' => 'required|numeric|min:1',
        ]);

        try {
            $amount = $request->input('amount');
            $checkoutUrl = $this->paymobService->getCheckoutUrl($amount);

            return redirect($checkoutUrl);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }
}