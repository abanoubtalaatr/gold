<?php

use App\Models\User;
use Inertia\Inertia;
use App\Events\NotificationSent;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\FaqController;
use App\Http\Controllers\LangController;
use App\Http\Controllers\UsersController;
use App\Http\Controllers\ExportController;
use App\Http\Controllers\PageWebController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\SettingsController;
<<<<<<< HEAD
use App\Http\Controllers\UsersController;
use App\Http\Controllers\Vendor\Auth\RegisterController;
use App\Http\Controllers\Vendor\BranchController;
use App\Http\Controllers\Vendor\GoldPieceController;
use App\Http\Controllers\Vendor\OrderController;
use App\Http\Controllers\Vendor\OrderRentalController;
use App\Http\Controllers\Vendor\OrderSalesController;
use App\Http\Controllers\Vendor\RentalRequestController;
=======
use App\Http\Controllers\DashboardController;
>>>>>>> a8584d55d7d0a93294bf9bfe2b8f6952eedc2495
use App\Http\Controllers\Vendor\RoleController;
use App\Http\Controllers\Vendor\UserController;
use App\Http\Controllers\NotificationController;
use App\Http\Controllers\Vendor\OrderController;
use App\Http\Controllers\Vendor\BranchController;
use App\Http\Controllers\Vendor\VerifyController;
use App\Http\Controllers\Banners\BannerController;
use App\Http\Controllers\Vendor\ServiceController;
use App\Http\Controllers\Contacts\ContactController;
use App\Http\Controllers\Vendor\GoldPieceController;
use App\Http\Controllers\Vendor\Auth\RegisterController;
use App\Http\Controllers\Vendor\RentalRequestController;








Route::get('/', [DashboardController::class, 'index'])->middleware(['auth', 'verified'])->name('dashboard');
Route::get('/dashboard', [DashboardController::class, 'index'])->middleware(['auth', 'verified'])->name('dashboard');

Route::get('lang/change', [LangController::class, 'change'])->name('changeLang');

/************************************************************************ */

Route::middleware(['auth'])->group(function () {

    Route::resource('notification', NotificationController::class)
        ->middleware('auth')
        ->only(['index']);

    /************************************************************************ */

    Route::resource('users', UsersController::class);
    Route::post('users/{user}/activate', [UsersController::class, 'activate'])->name('activate');
    Route::post('users/{user}', [UsersController::class, 'update'])->name('users.update'); //  inertia does not support send files using put request


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
    Route::post('/banners/{banner}', [BannerController::class, 'update'])->name('banners.update');

    /************************************************************************ */

    Route::resource('faqs', FaqController::class);
    Route::post('faqs/{faq}/activate', [FaqController::class, 'activate'])->name('faqs.activate');
    Route::post('/faqs/{faq}', [FaqController::class, 'update'])->name('faqs.update');

});


/************************************************************************ */

Route::get('/export-users', [ExportController::class, 'export'])->name('export.users');
/************************************************************************ */

Route::prefix('vendor')->name('vendor.')->group(function () {
    Route::middleware('guest')->group(function () {
        Route::get('create-account', [RegisterController::class, 'create'])
            ->name('register');  // Fixed typo in name

        Route::post('create-account', [RegisterController::class, 'store'])
            ->name('vendor.register');  // This matches your form submission

        Route::get('verify', [VerifyController::class, 'show'])->name('verify');
        Route::post('verify', [VerifyController::class, 'verify'])->name('verify.submit');
        Route::post('verify/resend', [VerifyController::class, 'resend'])->name('verify.resend');
        Route::get('login', [App\Http\Controllers\Vendor\Auth\LoginController::class, 'create'])
            ->name('login');
        Route::post('login', [App\Http\Controllers\Vendor\Auth\LoginController::class, 'store']);

        // Password Reset Routes
        Route::get('forgot-password', [App\Http\Controllers\Vendor\Auth\PasswordResetLinkController::class, 'create'])
            ->name('password.request');
        Route::post('forgot-password', [App\Http\Controllers\Vendor\Auth\PasswordResetLinkController::class, 'store'])
            ->name('password.email');
        Route::get('reset-password', [App\Http\Controllers\Vendor\Auth\NewPasswordController::class, 'create'])
            ->name('password.reset');
        Route::post('reset-password', [App\Http\Controllers\Vendor\Auth\NewPasswordController::class, 'store'])
            ->name('password.store');
        Route::get('reset-password/otp/form', [App\Http\Controllers\Vendor\Auth\PasswordResetLinkController::class, 'otpForm'])
            ->name('password.otp.form');
        Route::post('verify-otp', [App\Http\Controllers\Vendor\Auth\PasswordResetLinkController::class, 'verifyOtp'])
            ->name('password.verify-otp');
        Route::post('resend-otp', [App\Http\Controllers\Vendor\Auth\PasswordResetLinkController::class, 'resendOtp'])
            ->name('password.resend-otp');
    });

    Route::middleware(['auth', 'role:vendor'])->group(function () {
        Route::post('logout', [App\Http\Controllers\Vendor\Auth\LoginController::class, 'destroy'])
            ->name('logout');

        Route::get('/dashboard', [App\Http\Controllers\Vendor\DashboardController::class, 'index'])->name('dashboard');
    });
});


Route::middleware(['auth', 'verified'])->prefix('vendor')->name('vendor.')->group(function () {

    Route::resource('branches', BranchController::class);
    Route::patch('branches/{branch}/toggle-status', [BranchController::class, 'toggleStatus'])
        ->name('branches.toggle-status');
    Route::resource('services', ServiceController::class);
    Route::patch('services/{service}/toggle-status', [ServiceController::class, 'toggleStatus'])
        ->name('services.toggle-status');

    Route::resource('gold-pieces', GoldPieceController::class)
        ->names('gold-pieces');

    Route::patch('gold-pieces/{goldPiece}/toggle-status', [GoldPieceController::class, 'toggleStatus'])
        ->name('gold-pieces.toggle-status');


    Route::get('/orders', [OrderController::class, 'index'])->name('orders.index');
    Route::post('/orders/{orderId}/accept', [OrderController::class, 'accept'])->name('orders.accept');
    Route::post('/orders/{orderId}/reject', [OrderController::class, 'reject'])->name('orders.reject');
    Route::patch('/orders/{orderId}/status', [OrderController::class, 'updateStatus'])->name('orders.updateStatus');


    route::controller(OrderRentalController::class)->group(function () {
        Route::get('/rental-orders', action: 'index')->name('orders.rental.index');
        Route::post('/rental-orders/{orderId}/accept', 'accept')->name('orders.rental.accept');
        Route::post('/rental-orders/{orderId}/reject', 'reject')->name('orders.rental.reject');
        Route::patch('/rental-orders/{orderId}/status', 'updateStatus')->name('orders.rental.updateStatus');

    });


    route::controller(OrderSalesController::class)->group(function () {
        Route::get('/sale-orders', action: 'index')->name('orders.sale.index');
        Route::post('/sale-orders/{orderId}/accept', 'accept')->name('orders.sales.accept');
        Route::post('/sale-orders/{orderId}/reject', 'reject')->name('orders.sales.reject');
        Route::patch('/sale-orders/{orderId}/status', 'updateStatus')->name('orders.sales.updateStatus');

    });
    Route::get('/rental-requests', [RentalRequestController::class, 'index'])->name('rental-requests.index');

    Route::resource('roles', \App\Http\Controllers\Vendor\RoleController::class);
    Route::get('roles/{role}/delete', [\App\Http\Controllers\Vendor\RoleController::class, 'destroy']);
    Route::get('roles/{roleId}/give-permissions', [\App\Http\Controllers\Vendor\RoleController::class, 'addPermissionToRole']);
    Route::put('roles/{roleId}/give-permissions', [\App\Http\Controllers\Vendor\RoleController::class, 'givePermissionToRole']);
    Route::post('roles/{role}/activate', [\App\Http\Controllers\Vendor\RoleController::class, 'activate'])->name('roles.activate');

    Route::resource('users', UserController::class);
    Route::post('users/{user}/activate', [UserController::class, 'activate'])->name('activate');
    Route::post('users/{user}', [UserController::class, 'update'])->name('users.update'); //  inertia does not support send files using put request
});


<<<<<<< HEAD
require __DIR__ . '/auth.php';
=======
require __DIR__ . '/auth.php';


    Route::get('/test-notification', function () {
        $user = User::find(1);
        $notification = new NotificationSent();
        $notification->broadcast();

        return response()->json(['message' => 'Notification sent successfully']);
    });
>>>>>>> a8584d55d7d0a93294bf9bfe2b8f6952eedc2495
