<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\FaqController;
use App\Http\Controllers\Api\StateController;
use App\Http\Controllers\Api\CountryController;
use App\Http\Controllers\Api\PageApiController;
use App\Http\Controllers\Api\V1\OrderController;
use App\Http\Controllers\Api\V1\PriceController;
use App\Http\Controllers\Api\ContactUsController;
use App\Http\Controllers\Api\V1\BannerController;
use App\Http\Controllers\Api\V1\RatingController;
use App\Http\Controllers\Api\V1\AddressController;
use App\Http\Controllers\Api\V1\ContactController;
use App\Http\Controllers\Api\V1\InvoiceController;
use App\Http\Controllers\Api\SettingsApiController;
use App\Http\Controllers\Api\V1\FavoriteController;
use App\Http\Controllers\Api\V1\GoldPieceController;
use App\Http\Controllers\Api\V1\Auth\LoginController;
use App\Http\Controllers\Api\V1\Auth\ProfileController;
use App\Http\Controllers\Api\V1\Auth\PasswordController;
use App\Http\Controllers\Api\V1\Auth\RegisterController;
use App\Http\Controllers\Api\V1\Wallet\WalletController;
use App\Http\Controllers\Api\V1\LiquidationRequestController;
use App\Http\Controllers\Api\V1\NotificationSettingController;
use App\Http\Controllers\Api\V1\Auth\EmailVerificationController;
use App\Http\Controllers\Api\V1\Auth\PhoneVerificationController;
use App\Http\Controllers\Api\V1\GoldPiece\ConfirmSoldToVendorController;
use App\Http\Controllers\Api\V1\GoldPiece\ConfirmSendPieceToVendorController;


Route::get('settings', [SettingsApiController::class, 'index'])->name('settings');

Route::get('faqs', [FaqController::class, 'index'])->name('faqs');

Route::get('/gold-pieces', [GoldPieceController::class, 'index']);
Route::post('/price', [PriceController::class, 'index']);

Route::get('/mobile-formatted-prices', [PriceController::class, 'mobileFormattedPrices']);
Route::group(["prefix" => "auth"], function () {
    Route::post('/register', [RegisterController::class, 'register']);
    Route::post('/send_mobile_token', [RegisterController::class, 'sendMobileToken']);
    Route::post('/check_mobile_token', [RegisterController::class, 'checkMobileToken']);
    Route::post('/verify_email', [RegisterController::class, 'verifyAccountEmail']);

    Route::post('password/reset', [PasswordController::class, 'reset'])->name('password.reset');
    Route::post('login', [LoginController::class, 'login']);
    Route::post('social_login', [LoginController::class, 'social_login']);
});

//Mockup
Route::get('countries', [CountryController::class, 'index']);
Route::get('countries/{country}/states', [CountryController::class, 'states']);
Route::get('cities', [StateController::class, 'cities']);
Route::get('pages/{slug}', [PageApiController::class, 'show']);

Route::get('/gold-pieces/{goldPiece}', [GoldPieceController::class, 'show'])->name('gold_pieces.show')->middleware('optional.auth');
Route::post('/contact-us', [ContactUsController::class, 'store'])->name('contact-us');

// Debug route - remove after testing
Route::get('/debug-auth', function () {
    try {
        $token = request()->bearerToken();
        $headers = request()->headers->all();
        
        return response()->json([
            'has_auth_header' => request()->hasHeader('Authorization'),
            'bearer_token' => $token ? 'Token present' : 'No token',
            'token_length' => $token ? strlen($token) : 0,
            'headers' => array_keys($headers),
            'jwt_secret_set' => config('jwt.secret') ? 'Yes' : 'No',
            'auth_guard' => config('auth.guards.api'),
        ]);
    } catch (\Exception $e) {
        return response()->json(['error' => $e->getMessage()]);
    }
});

Route::group(['middleware' => ['mobile_verified', 'active', 'auth:api']], function () {

    // insert device token Routes
    Route::get('logout', [LoginController::class, 'logout']);

    Route::group(["prefix" => "auth"], function () {
        Route::get('profile', [ProfileController::class, 'index']);
        Route::post('profile', [ProfileController::class, 'update']);
        Route::post('profile/change-password', [ProfileController::class, 'changePassword']);
        Route::delete('profile/delete-account', [ProfileController::class, 'deleteAccount']);

        // Phone verification routes
        Route::post('phone/initiate-change', [PhoneVerificationController::class, 'initiateChange']);
        Route::post('phone/verify-update', [PhoneVerificationController::class, 'verifyAndUpdate']);
        Route::post('phone/resend-otp', [PhoneVerificationController::class, 'resendOtp']);

        Route::post('email/initiate-change', [EmailVerificationController::class, 'initiateChange']);
        Route::post('email/verify-update', [EmailVerificationController::class, 'verifyAndUpdate']);
        Route::post('email/resend-verification', [EmailVerificationController::class, 'resendVerification']);
    });


    Route::post('complain-or-suggestion', [ContactUsController::class, 'complainOrSuggestion'])->name('contact')->middleware('throttle:5,1');
    Route::get('complain-or-suggestion', [ContactUsController::class, 'index']);

    /*
    |--------------------------------------------------------------------------
    | Addresses Routes
    |--------------------------------------------------------------------------
    */

    Route::apiResource('addresses', AddressController::class);
    Route::post('addresses/{id}/set-default', [AddressController::class, 'setDefault']);


    /*
    |--------------------------------------------------------------------------
    | Gold Pieces....
    |--------------------------------------------------------------------------
    */
    
    Route::get('/my-gold-pieces', [GoldPieceController::class, 'myGoldPieces']);
    Route::post('/gold-pieces', [GoldPieceController::class, 'store']);
    Route::delete('/gold-pieces/{goldPiece}', [GoldPieceController::class, 'destroy']);
    Route::post('/gold-pieces/{goldPiece}/update', [GoldPieceController::class, 'update']);

    /*
    |--------------------------------------------------------------------------
    | Favorites
    |--------------------------------------------------------------------------
    */

    Route::get('/favorites', [FavoriteController::class, 'index']);
    Route::post('/gold-pieces/{goldPiece}/favorite', [FavoriteController::class, 'toggle']);

    /*
    |--------------------------------------------------------------------------
    | Orders....
    |--------------------------------------------------------------------------
    */

    Route::post('/orders', [OrderController::class, 'store']);
    Route::post('/orders/{order}/rental', [OrderController::class, 'updateRental']);
    Route::post('/orders/{order}/sale', [OrderController::class, 'updateSale']);
    Route::get('/orders', [OrderController::class, 'index']);
    Route::post('/orders/{order}/toggle-suspend-rental', [OrderController::class, 'toggleSuspendRental']);
    Route::post('/orders/{order}/toggle-suspend-sale', [OrderController::class, 'toggleSuspendSale']);
    Route::delete('/orders/{order}/rental', [OrderController::class, 'deleteRental']);
    Route::delete('/orders/{order}/sale', [OrderController::class, 'deleteSale']);
    Route::get('orders/{order}', [OrderController::class, 'show']);
    Route::post('orders/{order}/confirm-send-to-vendor', [ConfirmSendPieceToVendorController::class, 'index']);
    Route::post('orders/{order}/confirm-sold-to-vendor', [ConfirmSoldToVendorController::class, 'update']);
    Route::post('orders/{order}/cancel', [OrderController::class, 'cancel']);
    /*
    |--------------------------------------------------------------------------
    |  Notification...
    |--------------------------------------------------------------------------
    */

    Route::get('/notifications', [App\Http\Controllers\Api\V1\NotificationController::class, 'index']);
    Route::get('notifications/unread/count', [App\Http\Controllers\Api\V1\NotificationController::class, 'notificationCounts']);
    Route::get('/notifications/unread', [App\Http\Controllers\Api\V1\NotificationController::class, 'unread']);
    Route::post('notifications/{id}/read', [App\Http\Controllers\Api\V1\NotificationController::class, 'markAsRead']);
    Route::post('/notifications/read-all', [App\Http\Controllers\Api\V1\NotificationController::class, 'markAllAsRead']);
    Route::delete('/notifications/{id}', [App\Http\Controllers\Api\V1\NotificationController::class, 'destroy']);
    Route::delete('/notifications', [App\Http\Controllers\Api\V1\NotificationController::class, 'deleteAllNotifications']);
    Route::get('/notifications/poll', [App\Http\Controllers\Api\V1\NotificationController::class, 'poll']);
    Route::post('toggle-enable-notifications', NotificationSettingController::class);

    /*
    |--------------------------------------------------------------------------
    | Ratings....
    |--------------------------------------------------------------------------
    */

    Route::get('gold-pieces/{goldPiece}/ratings', [RatingController::class, 'index'])->name('ratings.index');
    Route::post('ratings', [RatingController::class, 'store'])->name('ratings.store');
    Route::post('ratings/{rating}', [RatingController::class, 'update'])->name('ratings.update');
    Route::delete('ratings/{rating}', [RatingController::class, 'destroy'])->name('ratings.destroy');
    Route::get('gold-pieces-will-finish-rental-soon', [GoldPieceController::class, 'goldPiecesWillFinishRentalSoon']);

    /*
    |--------------------------------------------------------------------------
    | Liquidation-requests....
    |--------------------------------------------------------------------------
    */
    Route::apiResource('liquidation-requests', LiquidationRequestController::class)->except(['update']);

    /*
    |--------------------------------------------------------------------------
    | complains....
    |--------------------------------------------------------------------------
    */

    Route::apiResource('/contacts', ContactController::class);

    /*
    |--------------------------------------------------------------------------
    | Price....
    |--------------------------------------------------------------------------
    */

    
    Route::post('/price/breakdown', [PriceController::class, 'priceBreakdown']);
    Route::post('/price/breakdown-by-type', [PriceController::class, 'priceBreakdownByType']);
    
    Route::get('/real-time-price', [PriceController::class, 'realTimePrice']);
    Route::get('/raw-real-time-price', [PriceController::class, 'rawRealTimePrice']);
    Route::get('/adjusted-prices', [PriceController::class, 'adjustedPrices']);
    Route::get('/structured-prices', [PriceController::class, 'structuredPrices']);
    
    Route::get('/price/carat', [PriceController::class, 'priceForCarat']);

    /*
    |--------------------------------------------------------------------------
    | Wallet....
    |--------------------------------------------------------------------------
    */
    Route::get('/wallet', [WalletController::class, 'index']);
    Route::post('wallet/charge', [WalletController::class, 'charge']);
});

Route::get('banners', [BannerController::class, 'index']);

Route::get('invoice/{order}', [InvoiceController::class, 'index'])->name('invoice.show');

