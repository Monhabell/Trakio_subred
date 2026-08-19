<?php

namespace App\Providers;

use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;
use App\Events\OtherTimesAdded;
use App\Listeners\OtherTimesAddedListener;

class EventServiceProvider extends ServiceProvider
{
    protected $listen = [
        // OtherTimesAdded::class => [
        //     OtherTimesAddedListener::class,
        // ],
    ];

    public function boot()
    {
        parent::boot();
    }
}
