<?php

namespace App\Providers;

use Illuminate\Support\Facades\Event;
use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;

class EventServiceProvider extends ServiceProvider
{
    /**
     * The event listener mappings for the application.
     *
     * @var array
     */
    protected $listen = [
        'App\Events\CustomerVerifiedEvent' => [
            'App\Listeners\CustomerVerifiedListener',
        ],
        'App\Events\CarInsuranceCreated' => [
            'App\Listeners\IsCarInsuranceLead',
        ],
        'App\Events\CustomerCheckoutEvent' => [
            'App\Listeners\CustomerCheckoutListener',
        ],
    ];

    /**
     * Register any events for your application.
     */
    public function boot()
    {
        parent::boot();
    }
}
