<?php

use App\Modules\SuperAdmin\Providers\SuperAdminServiceProvider;
use App\Modules\Operator\Providers\OperatorServiceProvider;
use App\Modules\Koordinator\Providers\KoordinatorServiceProvider;
use App\Modules\Instruktur\Providers\InstrukturServiceProvider;
use App\Modules\Pengguna\Providers\PenggunaServiceProvider;
use App\Providers\AppServiceProvider;

return [
    AppServiceProvider::class,
    SuperAdminServiceProvider::class,
    OperatorServiceProvider::class,
    KoordinatorServiceProvider::class,
    InstrukturServiceProvider::class,
    PenggunaServiceProvider::class,
];