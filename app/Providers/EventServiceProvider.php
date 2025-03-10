<?php

namespace App\Providers;

use App\Models\Output;
use App\Models\Product;
use App\Observers\StockObserver;
use App\Observers\StockOutObserver;
use Illuminate\Auth\Events\Registered;
use Illuminate\Auth\Listeners\SendEmailVerificationNotification;
use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Event;

class EventServiceProvider extends ServiceProvider
{
    /**
     * The event to listener mappings for the application.
     *
     * @var array<class-string, array<int, class-string>>
     */
    protected $listen = [
        Registered::class => [
            SendEmailVerificationNotification::class,
            'App\Listeners\NotifyLowStockProducts',
        ],
    ];

    /**
     * Register any events for your application.
     */
    public function boot(): void
    {
        //
        Product::observe( StockObserver::class);
        Output::observe( StockOutObserver::class);
    }

    /**
     * Determine if events and listeners should be automatically discovered.
     */
    public function shouldDiscoverEvents(): bool
    {
        return false;
    }
}
