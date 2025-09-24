<?php

$providers = [
    App\Providers\AppServiceProvider::class,
    App\Providers\SubscriptionServiceProvider::class,
];

if (app()->environment('local', 'testing')) {
    $providers[] = Laravel\Dusk\DuskServiceProvider::class;
}

return $providers;
