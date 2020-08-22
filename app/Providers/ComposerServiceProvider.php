<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

class ComposerServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
        //
    }

    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot()
    {
        //$categories = Category::all(['name', 'slug']);

        //Compartilhamento com VIEW SHARE
        //view()->share('categories', $categories);

        //Compartilhamento com VIEW COMPOSER, para algumas views em específico
        /*view()->composer(['welcome', 'single'], function($view){
            $view->with('categories', []);
        });*/

        //Compartilhamento com VIEW COMPOSER, para todas as views
        /*view()->composer('*', function($view) use($categories){
            $view->with('categories', $categories);
        });*/

        view()->composer('*', 'App\Http\Views\CategoryViewComposer@compose');
    }
}
