<?php

namespace App\Providers;

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
            // \Log::info('Job ' . get_class($event->job) . ' processed.');
            if (is_a($event->job, HarvestPrintBooksReport::class)) {
                // TODO: dispatch FetchTemporaryLocationDocuments
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
