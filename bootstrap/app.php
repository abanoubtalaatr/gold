<?php

use Inertia\Inertia;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Symfony\Component\HttpKernel\Exception\HttpException;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware) {
        $middleware->appendToGroup('web', [
            Illuminate\Session\Middleware\StartSession::class,
            App\Http\Middleware\LanguageManager::class,
            App\Http\Middleware\HandleInertiaRequests::class,
            Illuminate\Http\Middleware\AddLinkHeadersForPreloadedAssets::class,
        ]);

        $middleware->appendToGroup('api', [
            App\Http\Middleware\EnforceJson::class,
            App\Http\Middleware\SetLocale::class,
           // App\Http\Middleware\HandleUserActivity::class,
        ]);

        $middleware->alias([
            'auth' => App\Http\Middleware\Authenticate::class,
            'active' => App\Http\Middleware\CheckActiveAccount::class,
            'mobile_verified' => App\Http\Middleware\EnsureMobileIsVerified::class,
            'role' => Spatie\Permission\Middleware\RoleMiddleware::class,
            'permission' => Spatie\Permission\Middleware\PermissionMiddleware::class,
            'role_or_permission' => Spatie\Permission\Middleware\RoleOrPermissionMiddleware::class,
            'localize'                => \Mcamara\LaravelLocalization\Middleware\LaravelLocalizationRoutes::class,
            'localizationRedirect'    => \Mcamara\LaravelLocalization\Middleware\LaravelLocalizationRedirectFilter::class,
            'localeSessionRedirect'   => \Mcamara\LaravelLocalization\Middleware\LocaleSessionRedirect::class,
            'localeCookieRedirect'    => \Mcamara\LaravelLocalization\Middleware\LocaleCookieRedirect::class,
            'localeViewPath'          => \Mcamara\LaravelLocalization\Middleware\LaravelLocalizationViewPath::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions) {
        $exceptions->renderable(function (Throwable $exception, $request) {
            if ($request->is('api/*')) {
                if ($exception instanceof Illuminate\Auth\AuthenticationException) {
                    return response()->json(['message' => __('messages.unauthenticated')], 401);
                }

                if ($exception instanceof Symfony\Component\HttpKernel\Exception\NotFoundHttpException) {
                    return response()->json(['message' => __('messages.not_found')], 404);
                }

                if ($exception instanceof Illuminate\Validation\ValidationException) {
                    return response()->json([
                        'message' => __('messages.validation_error'),
                        'errors' => $exception->errors(),
                    ], 422);
                }

                if ($exception instanceof Symfony\Component\HttpKernel\Exception\UnauthorizedHttpException) {
                    return response()->json(['message' => __('messages.unauthorized')], 401);
                }

                if ($exception instanceof Symfony\Component\HttpKernel\Exception\HttpException) {
                    return response()->json(['message' => $exception->getMessage()], $exception->getStatusCode());
                }

                if ($exception instanceof Symfony\Component\HttpKernel\Exception\TooManyRequestsHttpException) {
                    return response()->json(['message' => __('messages.too_many_requests')], 429);
                }

                //return response()->json(['message' => __('messages.unexpected_error')], 500);

                return response()->json(['message' => $exception->getMessage()], 500);
            } else {
                if ($exception instanceof Symfony\Component\HttpKernel\Exception\NotFoundHttpException) {
                    return Inertia::render('Errors/404');
                    // code...
                }
                if ($exception instanceof Symfony\Component\HttpKernel\Exception\HttpException) {
                    dd($exception->getMessage());
                }

                if ($exception instanceof Symfony\Component\HttpKernel\Exception\UnauthorizedHttpException) {
                    return Inertia::render('Errors/401');
                    // code...
                }

                if ($exception instanceof Symfony\Component\HttpKernel\Exception\HttpException) {

                    return Inertia::render('Errors/'.$exception->getStatusCode());
                }

                if ($exception instanceof HttpException && $exception->getStatusCode() === 500) {
                    return Inertia::render('Errors/500');
                }
               
            }
           
            return null;
        });

    })->create();