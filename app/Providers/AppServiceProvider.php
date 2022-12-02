<?php

namespace App\Providers;

use App\Settings;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\ServiceProvider;

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
        if (Schema::hasTable('settings')) {
            $settings = Settings::pluck('value', 'name');
            if (count($settings)) {
                foreach ($settings as $name => $value) {
                    config([$name => $value]);
                }
            }
        }
    }
}
