<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\models\hr\hr_vacations;
use App\observers\vacationobserver;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        //
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        hr_vacations::observe(vacationobserver::class);
    }
}
