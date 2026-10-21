<?php

use App\Providers\AppServiceProvider;

return [
    AppServiceProvider::class,
    App\Providers\OperationsFeaturesServiceProvider::class,
    App\Providers\StaffFeaturesServiceProvider::class,
    App\Providers\FinanceFeaturesServiceProvider::class,
    App\Providers\CommsBrandingFeaturesServiceProvider::class,
    App\Providers\PlatformFeaturesServiceProvider::class,
];
