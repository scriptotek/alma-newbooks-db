<?php

namespace App\Providers;

use App\MailgunService;
use GuzzleHttp\Client;
use Illuminate\Support\ServiceProvider;

class MailgunServiceProvider extends ServiceProvider
{
    /**
     * Indicates if loading of the provider is deferred.
     *
     * @var bool
     */
    protected $defer = true;

    /**
     * Register the application services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->singleton(MailgunService::class, function ($app) {

            $http = new Client([
                'base_uri' => 'https://api.mailgun.net/v3/' . config('services.mailgun.domain') . '/',
                'timeout'  => 30.0,
                'headers' => [
                    'Accept' => 'application/json',
                ],
                'auth' => ['api', config('services.mailgun.secret')],
            ]);

            return new MailgunService($http);
        });
    }

    /**
     * Get the services provided by the provider.
     *
     * @return array
     */
    public function provides()
    {
        return [MailgunService::class];
    }
}
