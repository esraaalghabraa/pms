<?php

namespace App\Providers;

use Illuminate\Support\Facades\Validator;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Schema;

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
        Schema::defaultStringLength(191);
        $this->registerPolicies();
//        Gate::before(function ($user, $ability) {
//            return $user->hasRole('Owner') ? true : null;
//        });
        Validator::extend('enum',function ($attribute, $value, $parameters){
            $enumClass = "App\Enums\\" . $parameters[0];
            if (!class_exists($enumClass)) {
                return false;
            }

            return in_array($value, $enumClass::getConstants());
        });
    }
}
