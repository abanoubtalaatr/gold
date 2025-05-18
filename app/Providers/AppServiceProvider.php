<?php

namespace App\Providers;

use App\Models\User;
use App\Services\SMS\Msegat;
use App\Observers\RoleObserver;
use App\Observers\UserObserver;
use Spatie\Permission\Models\Role;
use Illuminate\Support\Facades\App;
use App\Repositories\PageRepository;
use App\Observers\PermissionObserver;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\ServiceProvider;
use Spatie\Permission\Models\Permission;
use App\Repositories\PageRepositoryInterface;
use App\Models\Notification as CustomNotification;
use Illuminate\Notifications\DatabaseNotification;
use Illuminate\Database\Eloquent\Relations\Relation;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //$this->app->bind(SpatieRole::class, Role::class);
        $this->app->bind(PageRepositoryInterface::class, PageRepository::class);

        $this->app->singleton(Msegat::class, function ($app) {
            return new Msegat(
                config('services.msegat.msegat_base_url'),
                config('services.msegat.msegat_user_name'),
                config('services.msegat.msegat_key'),
                config('services.msegat.msegat_sender_name')
            );
        });
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        ini_set('memory_limit', env('PHP_MEMORY_LIMIT', '1024M'));

        //dd(session()->get('locale'));
        User::observe(UserObserver::class);
        Role::observe(RoleObserver::class);
        Permission::observe(PermissionObserver::class);
        App::setLocale(Session::get('locale', config('app.locale')));

        Relation::enforceMorphMap([
            'App\Models\User' => \App\Models\User::class,
            'App\Models\Role' => \App\Models\Role::class,
            'App\Models\Settings' => \App\Models\Setting::class,
            'App\Models\Page' => \App\Models\Page::class,
            'App\Models\PageSection' => \App\Models\PageSection::class,
            'App\Models\GoldPiece' => \App\Models\GoldPiece::class,
        ]);

        
    }
}
