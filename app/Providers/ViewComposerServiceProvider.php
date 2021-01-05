<?php

namespace App\Providers;

use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;

class ViewComposerServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap the application services.
     *
     * @return void
     */
    public function boot()
    {
        View::composer('*', 'App\Http\ViewComposers\GlobalComposer');
        View::composer('partials.sidebar-profile', 'App\Http\ViewComposers\ProfileComposer');
        View::composer('partials.sidebar-menu-earn', 'App\Http\ViewComposers\MenuEarnComposer');
        View::composer('partials.featured-items', 'App\Http\ViewComposers\FeaturedItemsComposer');
        View::composer('partials.sidebar-menu-exchange', 'App\Http\ViewComposers\MenuCategoriesComposer');
    }

    /**
     * Register the application services.
     *
     * @return void
     */
    public function register()
    {
        //
    }
}
