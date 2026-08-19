<?php

namespace App\Providers;

use Illuminate\Support\Facades\Gate;
use Laravel\Telescope\IncomingEntry;
use Laravel\Telescope\Telescope;
use Laravel\Telescope\TelescopeApplicationServiceProvider;

class TelescopeServiceProvider extends TelescopeApplicationServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        // Telescope::night();

        $this->hideSensitiveRequestDetails();

        $isLocal = $this->app->environment(['local', 'staging']);

        // if ($isLocal && class_exists(\Laravel\Telescope\TelescopeServiceProvider::class)) {
        //     $this->app->register(\Laravel\Telescope\TelescopeServiceProvider::class);
        //     $this->app->register(TelescopeServiceProvider::class);
        // }

        // Telescope::filter(function (IncomingEntry $entry) use ($isLocal) {
        //     return $isLocal ||
        //            $entry->isReportableException() ||
        //            $entry->isFailedRequest() ||
        //            $entry->isFailedJob() ||
        //            $entry->isScheduledTask() ||
        //            $entry->hasMonitoredTag();
        // });

        Telescope::filter(function (IncomingEntry $entry) use ($isLocal) {
            return $isLocal || $entry->type === 'request' || $entry->isFailedRequest() || $entry->isReportableException();
        });
    }

    /**
     * Prevent sensitive request details from being logged by Telescope.
     */
    protected function hideSensitiveRequestDetails(): void
    {
        if ($this->app->environment('local')) {
            return;
        }

        Telescope::hideRequestParameters(['_token']);

        Telescope::hideRequestHeaders([
            'cookie',
            'x-csrf-token',
            'x-xsrf-token',
        ]);
    }

    /**
     * Register the Telescope gate.
     *
     * This gate determines who can access Telescope in non-local environments.
     */
    protected function gate(): void
    {
        Gate::define('viewTelescope', function ($user) {
            return in_array($user->email, [
                'gesinumeracion1@gmail.com'
            ]);
        });
    }
}
