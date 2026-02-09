<?php

namespace App\Providers;

use App\Services\LoanService;
use Illuminate\Support\ServiceProvider;

class LoanServiceProvider extends ServiceProvider
{
    public function register()
    {
        $this->app->bind(LoanService::class, function () {
            return new LoanService;
        });
    }
}
