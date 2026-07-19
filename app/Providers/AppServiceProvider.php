<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Schema; // تأكد من وجود هذا السطر في الأعلى

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // 🔐 الحل الشامل: تعطيل فحص العلاقات للمشروع بالكامل أثناء الـ Migrations
        Schema::disableForeignKeyConstraints();
        
        // ... أي أكواد أخرى موجودة لديك في الدالة تتركها كما هي ...
    }
}
