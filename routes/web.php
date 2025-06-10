<?php

use App\Http\Controllers\Admin\WalletController as AdminWalletController;
use App\Http\Controllers\Admin\AdminWalletController as SuperAdminWalletController;
use App\Events\NotificationSent;
use App\Http\Controllers\Admin\ComplaintController;
use App\Http\Controllers\Admin\SystemSettingsController;
use App\Http\Controllers\Admin\VendorController;
use App\Http\Controllers\Banners\BannerController;
use App\Http\Controllers\Contacts\ContactController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ExportController;
use App\Http\Controllers\FaqController;
use App\Http\Controllers\LangController;
use App\Http\Controllers\NotificationController;
use App\Http\Controllers\PageWebController;
use App\Http\Controllers\PermissionController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\SettingsController;
use App\Http\Controllers\UsersController;
use App\Http\Controllers\Vendor\Auth\RegisterController;
use App\Http\Controllers\Vendor\BranchController;
use App\Http\Controllers\Vendor\ContactController as VendorContactController;
use App\Http\Controllers\Vendor\GoldPieceController;
use App\Http\Controllers\Admin\GoldPieceController as AdminGoldPieceController;
use App\Http\Controllers\Vendor\OrderController;
use App\Http\Controllers\Vendor\OrderRentalController;
use App\Http\Controllers\Vendor\OrderSalesController;
use App\Http\Controllers\Vendor\RentalRequestController;
use App\Http\Controllers\Vendor\ReportController;
use App\Http\Controllers\Vendor\RoleController;
use App\Http\Controllers\Vendor\ServiceController;
use App\Http\Controllers\Vendor\SettlementController;
use App\Http\Controllers\Vendor\StatisticsController;
use App\Http\Controllers\Vendor\StoreController;
use App\Http\Controllers\Vendor\UserController;
use App\Http\Controllers\Vendor\VerifyController;
use App\Http\Controllers\Vendor\WalletController;
use App\Models\Country;
use App\Models\State;
use App\Models\User;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;
use App\Http\Controllers\LandingController;



// Landing page route (accessible to everyone)
Route::get('/', [LandingController::class, 'index'])->name('landing');

Route::post('/contact', [LandingController::class, 'contact'])->name('landing.contact');
Route::get('privacy', [LandingController::class, 'show'])->name('privacy')->defaults('slug', 'privacy');
Route::get('terms', [LandingController::class, 'show'])->name('terms')->defaults('slug', 'terms');
// Dashboard route for authenticated users
Route::get('/dashboard', [DashboardController::class, 'index'])->middleware(['auth', 'verified'])->name('dashboard');

Route::get('lang/change', [LangController::class, 'change'])->name('changeLang');

/************************************************************************ */

Route::middleware(['auth'])->group(function () {

    Route::resource('notification', NotificationController::class)
        ->middleware('auth')
        ->only(['index']);
    Route::prefix('notifications')->group(function () {
        Route::post('/{notification}/read', [NotificationController::class, 'markAsRead'])->name('notifications.read');
        Route::post('/read-all', [NotificationController::class, 'markAllAsRead'])->name('notifications.read-all');
        Route::get('/unread-count', [NotificationController::class, 'getUnreadCount'])->name('notifications.unread-count');

        // Add polling endpoint for real-time notifications
        Route::get('/poll', function () {
            $user = request()->user();
            if (!$user) {
                return response()->json(['error' => 'Unauthorized'], 401);
            }

            $lastCheck = request()->get('last_check');

            // If no last_check provided, use 5 minutes ago to catch recent notifications
            if (!$lastCheck) {
                $since = now()->subMinutes(5);
            } else {
                try {
                    $since = \Carbon\Carbon::parse($lastCheck);
                    // Ensure we don't go too far back to avoid performance issues
                    $maxLookback = now()->subHours(1);
                    if ($since->lt($maxLookback)) {
                        $since = $maxLookback;
                    }
                } catch (\Exception $e) {
                    // If invalid date, use 5 minutes ago as fallback
                    $since = now()->subMinutes(5);
                }
            }

            // Get only unread notifications created AFTER the last check time
            $newNotifications = $user->notifications()
                ->where('created_at', '>', $since)
                ->whereNull('read_at')
                ->orderBy('created_at', 'desc')
                ->limit(50) // Limit to prevent overwhelming the frontend
                ->get()
                ->map(function ($notification) {
                    return [
                        'id' => $notification->id,
                        'type' => $notification->type,
                        'data' => $notification->data,
                        'created_at' => $notification->created_at->toISOString(),
                        'time_ago' => $notification->created_at->diffForHumans(),
                    ];
                });

            // Get total unread count
            $totalUnread = $user->unreadNotifications()->count();

            return response()->json([
                'notifications' => $newNotifications,
                'count' => $newNotifications->count(),
                'total_unread' => $totalUnread,
                'timestamp' => now()->toISOString(),
                'since' => $since->toISOString(),
                'next_check_from' => $newNotifications->count() > 0 ?
                    $newNotifications->first()['created_at'] :
                    now()->toISOString(),
                'debug' => [
                    'user_id' => $user->id,
                    'last_check_provided' => $lastCheck,
                    'since_parsed' => $since->toISOString(),
                    'notifications_found' => $newNotifications->count(),
                    'server_time' => now()->toISOString(),
                ]
            ]);
        })->name('notifications.poll');
    });

    // Add vendor notifications count route
    Route::prefix('vendor')->group(function () {
        Route::get('/notifications/count', [NotificationController::class, 'getVendorNotificationCount'])->name('vendor.notifications.count');
    });


    /************************************************************************ */

    Route::resource('users', UsersController::class);
    Route::post('users/{user}/activate', [UsersController::class, 'activate'])->name('activate');
    Route::post('users/{user}/update', [UsersController::class, 'update'])->name('users.update-post'); //  inertia does not support send files using put request


    /************************************************************************ */


    Route::resource('permissions', App\Http\Controllers\PermissionController::class);
    Route::get('permissions/{permissionId}/delete', [App\Http\Controllers\PermissionController::class, 'destroy']);


    /************************************************************************ */

    Route::resource('roles', App\Http\Controllers\RoleController::class);
    Route::get('roles/{roleId}/delete', [App\Http\Controllers\RoleController::class, 'destroy']);
    Route::get('roles/{roleId}/give-permissions', [App\Http\Controllers\RoleController::class, 'addPermissionToRole']);
    Route::put('roles/{roleId}/give-permissions', [App\Http\Controllers\RoleController::class, 'givePermissionToRole']);
    Route::post('roles/{role}/activate', [App\Http\Controllers\RoleController::class, 'activate'])->name('roles.activate');

    /************************************************************************ */

    Route::get('/pages/{slug}/edit', [PageWebController::class, 'edit'])->name('pages.edit');
    Route::post('/pages/{id}', [PageWebController::class, 'update'])->name('pages.update');


    /************************************************************************ */

    Route::get('/settings', [SettingsController::class, 'index'])->name('settings.index');
    Route::post('/settings', [SettingsController::class, 'update'])->name('settings.update');


    /******************************* contacts***************************************** */

    Route::resource('contacts', ContactController::class);
    Route::post('/contacts/{contact}/read', [ContactController::class, 'read'])->name('contacts.read');


    /************************************************************************ */

    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::post('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    /*********************************banners*************************************** */

    Route::resource('banners', BannerController::class);
    Route::post('banners/{banner}/activate', [BannerController::class, 'activate'])->name('ba.activate');

    /************************************************************************ */

    Route::resource('faqs', FaqController::class);
    Route::post('faqs/{faq}/activate', [FaqController::class, 'activate'])->name('faqs.activate');
});


/************************************************************************ */

Route::get('/export-users', [ExportController::class, 'export'])->name('export.users');
/************************************************************************ */

Route::middleware(['role:admin|superadmin'])->group(function () {


    Route::middleware(['auth', 'verified'])->group(function () {
        // System Settings
        Route::get('/system-settings', [SystemSettingsController::class, 'index'])->name('system-settings');
        Route::put('/system-settings', [SystemSettingsController::class, 'updateSettings'])->name('system-settings.update');

        // Slider Routes
        Route::post('/system-settings/sliders', [SystemSettingsController::class, 'storeSlider'])->name('system-settings.sliders.store');
        Route::put('/system-settings/sliders/{slider}', [SystemSettingsController::class, 'updateSlider'])->name('system-settings.sliders.update');
        Route::delete('/system-settings/sliders/{slider}', [SystemSettingsController::class, 'destroySlider'])->name('system-settings.sliders.destroy');


        /************************************************************************ */



        Route::resource('admin/vendors', VendorController::class)->except('update');

        Route::post('/vendors/{vendor}/update', [VendorController::class, 'update'])->name('vendors.update');
        // Vendor status management
        Route::post('/vendors/{vendor}/approve', [VendorController::class, 'approve'])->name('vendors.approve');
        Route::post('/vendors/{vendor}/reject', [VendorController::class, 'reject'])->name('vendors.reject');
        Route::patch('/vendors/{vendor}/toggle-status', [VendorController::class, 'toggleStatus'])->name('vendors.toggle-status');
    });


    /************************************************************************ */



    Route::middleware(['auth', 'verified'])->prefix('admin')->group(function () {
        // Vendor Wallet
        Route::get('/vendors/{vendor}/wallet', action: [AdminWalletController::class, 'show'])
            ->name('admin.wallet.show');

        Route::post('/wallet/adjust', [AdminWalletController::class, 'adjustBalance'])
            ->name('admin.wallet.adjust');

        Route::get('/wallet/{wallet}/transactions', [AdminWalletController::class, 'transactions'])
            ->name('admin.wallet.transactions');

        // Settlement Requests
        Route::put('/settlement/{settlement}/approve', [AdminWalletController::class, 'approveSettlement'])
            ->name('admin.settlement.approve');

        Route::put('/settlement/{settlement}/reject', [AdminWalletController::class, 'rejectSettlement'])
            ->name('admin.settlement.reject');

        Route::get('/wallet', [SuperAdminWalletController::class, 'index'])->name('wallet.index');
    });

    /************************************************************************ */


    Route::prefix('admin')->middleware(['auth', 'verified'])->group(function () {
        Route::get('/complaints', [ComplaintController::class, 'index'])->name('admin.complaints.index');
        Route::post('/complaints/{complaint}/reply', [ComplaintController::class, 'reply'])->name('admin.complaints.reply');
        Route::patch('/complaints/{complaint}/status', [ComplaintController::class, 'updateStatus'])->name('admin.complaints.update-status');


        /************************************************************************ */


        Route::resource('gold-pieces', AdminGoldPieceController::class)
            ->names('admin.gold-pieces')->except('update');

        Route::post('gold-pieces/{goldPiece}/update', [AdminGoldPieceController::class, 'update'])
            ->name('admin.gold-pieces.update');

        Route::patch('gold-pieces/{goldPiece}/toggle-status', [AdminGoldPieceController::class, 'toggleStatus'])
            ->name('admin.gold-pieces.toggle-status');
    });


    /************************************************************************ */


    Route::middleware(['auth'])->prefix('admin')->group(function () {
        // ... other admin routes

        Route::get('/reports', [\App\Http\Controllers\Admin\ReportController::class, 'index'])
            ->name('admin.reports.index');

        Route::post('/reports/generate', [\App\Http\Controllers\Admin\ReportController::class, 'generate'])
            ->name('admin.reports.generate');
    });

    /************************************************************************ */



    Route::middleware(['auth', 'verified'])->prefix('admin')->group(function () {
        // Orders
        Route::get('/orders', [\App\Http\Controllers\Admin\OrderController::class, 'index'])->name('admin.orders.index');
        Route::get('/orders/{id}/{type}', [\App\Http\Controllers\Admin\OrderController::class, 'show'])->name('admin.orders.show');
    });


    Route::middleware(['auth', 'verified'])->prefix('admin/orders')->group(function () {
        Route::get('/rental', [\App\Http\Controllers\Admin\OrderController::class, 'rentalIndex'])->name('admin.orders.rental.index');
        Route::get('/rental/{id}', [\App\Http\Controllers\Admin\OrderController::class, 'showRental'])->name('admin.orders.rental.show');

        Route::get('/sale', [\App\Http\Controllers\Admin\OrderController::class, 'saleIndex'])->name('admin.orders.sale.index');
        Route::get('/sale/{id}', [\App\Http\Controllers\Admin\OrderController::class, 'showSale'])->name('admin.orders.sale.show');
    });
    /************************************************************************ */

    //

    Route::middleware(['auth', 'verified'])->prefix('admin')->group(function () {
        // Cities
        Route::get('/cities', [\App\Http\Controllers\Admin\CityController::class, 'index'])->name('admin.cities.index');
        Route::get('/cities/create', [\App\Http\Controllers\Admin\CityController::class, 'create'])->name('admin.cities.create');
        Route::post('/cities', [\App\Http\Controllers\Admin\CityController::class, 'store'])->name('admin.cities.store');
        Route::get('/cities/{city}', [\App\Http\Controllers\Admin\CityController::class, 'show'])->name('admin.cities.show');
        Route::get('/cities/{city}/edit', [\App\Http\Controllers\Admin\CityController::class, 'edit'])->name('admin.cities.edit');
        Route::put('/cities/{city}', [\App\Http\Controllers\Admin\CityController::class, 'update'])->name('admin.cities.update');
        Route::delete('/cities/{city}', [\App\Http\Controllers\Admin\CityController::class, 'destroy'])->name('admin.cities.destroy');
        Route::patch('/cities/{city}/toggle-status', [\App\Http\Controllers\Admin\CityController::class, 'toggleStatus'])->name('admin.cities.toggle-status');
    });


});

/************************************************************************ */



require base_path('routes/vendor.php');
require __DIR__ . '/auth.php';


// Catch-all route for dynamic pages (must be last)
Route::get('/{slug}', [LandingController::class, 'show'])->name('pages.show');