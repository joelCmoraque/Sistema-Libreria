<?php

namespace App\Providers;

// use Illuminate\Support\Facades\Gate;

use App\Filament\Pages\Welcome;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use App\Models\User;
use App\Models\Product;
use App\Policies\UserPolicy;
use App\Policies\ProductPolicy;
use App\Policies\WelcomePolicy;
use Filament\Pages\Page;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * The model to policy mappings for the application.
     *
     * @var array<class-string, class-string>
     */
    protected $policies = [
        // 'App\Models\Model' => 'App\Policies\ModelPolicy',
        //User::class => DashboardPolicy::class,
        User::class => UserPolicy::class,
        Product::class => ProductPolicy::class,
        Welcome::class => WelcomePolicy::class,

    ];

    /**
     * Register any authentication / authorization services.
     */
    public function boot(): void
    {
        $this->registerPolicies();

        //
    }
}
