<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Http\Resources\TreeResourceCollection;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        TreeResourceCollection::withoutWrapping();
    }
}
