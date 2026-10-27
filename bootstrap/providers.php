<?php

use App\Providers\AppServiceProvider;
use App\Providers\AuthServiceProvider;
use App\Providers\FortifyServiceProvider;
use App\Providers\ShortcodeServiceProvider;
use App\Providers\ThemeServiceProvider;
use App\Providers\VoltServiceProvider;

return [
    AppServiceProvider::class,
    AuthServiceProvider::class,
    FortifyServiceProvider::class,
    VoltServiceProvider::class,
    ShortcodeServiceProvider::class,
    ThemeServiceProvider::class,
];
