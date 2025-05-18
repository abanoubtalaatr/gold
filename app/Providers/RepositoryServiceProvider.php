<?php

namespace App\Providers;

use App\Interfaces\BaseInterface;
use App\Repositories\BaseRepository;
use App\Repositories\PageRepository;
use Illuminate\Support\ServiceProvider;
use App\Repositories\PageRepositoryInterface;

class RepositoryServiceProvider extends ServiceProvider
{
    public function register()
    {

        $this->app->bind(BaseInterface::class, BaseRepository::class);

        $this->app->bind(PageRepositoryInterface::class, PageRepository::class);
    }
}