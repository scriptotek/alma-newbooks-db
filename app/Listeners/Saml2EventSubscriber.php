<?php

namespace App\Listeners;

use Aacotroneo\Saml2\Events\Saml2LoginEvent;
use Aacotroneo\Saml2\Events\Saml2LogoutEvent;
use App\User;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;

class Saml2EventSubscriber
{
    /**
     * Handle the event.
     *
     * @param  Saml2LoginEvent  $event
     * @return void
     */
    public function onUserLogin(Saml2LoginEvent $event)
    {
        $data = $event->getSaml2User();
        $uid = $data->getUserId();
        $attrs = $data->getAttributes();


        $user = User::firstOrNew([ 'email' => $attrs['EmailAddress'][0] ]);
        $user->name = $attrs['FirstName'][0] . ' ' . $attrs['LastName'][0];
        $user->email = $attrs['EmailAddress'][0];

        $user->saml_id = $uid;
        $user->saml_session = $data->getSessionIndex();

        $user->save();

        \Auth::login($user);
    }

    /**
     * Handle the event.
     *
     * @param  Saml2LogoutEvent  $event
     * @return void
     */
    public function onUserLogout(Saml2LogoutEvent $event)
    {
        // die('Got logout event');

        $user = \Auth::user();
        if ($user) {
            $user->saml_id = null;
            $user->saml_session = null;
            $user->save();
        }
        \Auth::logout();
        \Session::save();
    }

    /**
     * Register the listeners for the subscriber.
     *
     * @param  Illuminate\Events\Dispatcher  $events
     */
    public function subscribe($events)
    {
        $events->listen(
            'Aacotroneo\Saml2\Events\Saml2LoginEvent',
            'App\Listeners\Saml2EventSubscriber@onUserLogin'
        );
        $events->listen(
            'Aacotroneo\Saml2\Events\Saml2LogoutEvent',
            'App\Listeners\Saml2EventSubscriber@onUserLogout'
        );
    }
}
