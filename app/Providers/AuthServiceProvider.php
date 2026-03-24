<?php

namespace App\Providers;

use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Gate;
use App\Models\Link;
use App\Policies\LinkPolicy;
use App\Models\User;
use App\Models\QrCode;
use App\Models\Scan;
use App\Models\Device;
use App\Policies\UserPolicy;
use App\Policies\QrCodePolicy;
use App\Policies\ScanPolicy;
use App\Policies\DevicePolicy;

class AuthServiceProvider extends ServiceProvider
{
    protected $policies = [
        Link::class => LinkPolicy::class,
        User::class => UserPolicy::class,
        QrCode::class => QrCodePolicy::class,
        Scan::class => ScanPolicy::class,
        Device::class => DevicePolicy::class,
    ];

    public function boot()
    {
        $this->registerPolicies();
    }
}
