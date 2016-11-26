<?php

namespace App\Providers;

use App\Jobs\HarvestPrintBooksReport;
use App\Jobs\HarvestTemporaryLocationReport;
use Illuminate\Support\Facades\Queue;
use Illuminate\Support\ServiceProvider;
use Illuminate\Queue\Events\JobProcessed;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        Queue::after(function (JobProcessed $event) {
            $cmd = array_get($event->job->payload(), 'data.commandName');
            if ($cmd == HarvestPrintBooksReport::class) {
                dispatch(new HarvestTemporaryLocationReport());
            }
        });
    }

    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        //
    }
}
