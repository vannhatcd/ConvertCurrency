<?php

namespace App\Modules\ConvertCurrency\Providers;

use App\Modules\ConvertCurrency\Services\CurrencyService;
use Illuminate\Support\Facades\Facade;
use Illuminate\Support\ServiceProvider;

class ConvertCurrencyServiceProvider extends ServiceProvider
{
    protected $namespace = 'App\Modules\ConvertCurrency';

    public function register()
    {
        $this->app->singleton('currency', function () {
            return new CurrencyService();
        });

        Facade::shouldProxy('Currency');
    }

    public function boot()
    {
        $this->loadMigrationsFrom($this->modulePath('Database/Migrations'));
        $this->loadRoutesFrom($this->modulePath('Routes/web.php'));
    }

    private function modulePath($path)
    {
        return __DIR__ . '/' . $path;
    }
}
