<?php

namespace App\Http\Controllers\Api\V1\Wallet;

use Illuminate\Http\Request;
use App\Services\WalletService;
use App\Traits\ApiResponseTrait;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use App\Http\Resources\WalletResource;
use App\Http\Requests\Api\V1\ChargeWalletRequest;
use App\Http\Resources\WalletTransactionResource;

class WalletController extends Controller
{
    use ApiResponseTrait;

    public function __construct(private WalletService $walletService)
    {
    }

    public function index(Request $request)
    {
        $wallet = $this->walletService->getWallet(Auth::user());
        $transactions = $this->walletService->getTransactions($request->user(), $request->only(['type', 'status']));

        return $this->successResponse([
            'wallet' => new WalletResource($wallet),
            'transactions' => WalletTransactionResource::collection($transactions),
        ]);
    }

    public function charge(ChargeWalletRequest $request)
    {
        $this->validate($request, [
            'amount' => 'required|numeric|min:0',
            'description' => 'nullable|string',
        ]);

        // $this->walletService->charge($request->user(), $request->amount, $request->description);

        return $this->successResponse([
            'message' => __('mobile.Wallet charged successfully'),
        ]);
    }
}
