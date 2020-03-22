<?php

namespace App\Providers;

use FeedIo\Adapter\Guzzle\Client as FeedIoClient;
use FeedIo\Feed;
use FeedIo\FeedIo;
use GuzzleHttp\Client as GuzzleClient;
use Psr\Log\LoggerInterface;
use Illuminate\Contracts\Support\DeferrableProvider;
use Illuminate\Support\ServiceProvider;

class FeedIoServiceProvider extends ServiceProvider implements DeferrableProvider
{
    /**
     * Register the application services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->singleton(FeedIoClient::class, function ($app) {
            return new FeedIoClient($app->make(GuzzleClient::class));
        });

        $this->app->singleton(FeedIo::class, function ($app) {
            return new FeedIo(
                $app->make(FeedIoClient::class),
                $app->make(LoggerInterface::class)
            );
        });

        $this->app->bind(Feed::class, function ($app) {
            return new Feed();
        });
    }

    /**
     * Get the services provided by the provider.
     *
     * @return array
     */
    public function provides()
    {
        return [
            FeedIoClient::class,
            FeedIo::class,
            Feed::class,
        ];
    }
}
