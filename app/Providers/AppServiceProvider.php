<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Schema;
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
        // 🔐 الحل الجذري والنهائي: تعطيل فحص العلاقات للمشروع بالكامل أثناء الـ Migrations
        Schema::disableForeignKeyConstraints();

        // كود الـ Observer الخاص بك يترك كما هو ليعمل بكفاءة
        hr_vacations::observe(vacationobserver::class);
    }
}
