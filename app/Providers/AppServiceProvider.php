<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Models\Registrasi;
use App\Models\Pengaduan;
use App\Models\SurveyKepuasan;
use App\Observers\RegistrasiObserver;
use App\Observers\PengaduanObserver;
use App\Observers\SurveyKepuasanObserver;

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
        Registrasi::observe(RegistrasiObserver::class);
        Pengaduan::observe(PengaduanObserver::class);
        SurveyKepuasan::observe(SurveyKepuasanObserver::class);
    }
}
