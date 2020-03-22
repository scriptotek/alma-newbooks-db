<?php

namespace App\Listeners;

use Aacotroneo\Saml2\Events\Saml2LoginEvent;
use Aacotroneo\Saml2\Events\Saml2LogoutEvent;
use App\User;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Scriptotek\Alma\Client as AlmaClient;

class Saml2EventSubscriber
{

    public function __construct(AlmaClient $alma)
    {
        $this->alma = $alma;
    }

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

        $uio_id = $attrs['uid'][0];
        $primary_id = $uio_id . '@uio.no';  // @TODO: Move to config

        if (!$uio_id) {
            \Log::notice('No uid returned in SAML2 login event.');
            \Session::flash('error', 'An unknown error occured during login.');
            return;
        }

        $user = User::where('uio_id', '=', $uio_id)->first();
        if (is_null($user)) {

            try {
                $alma_user_data = $this->alma->users->get($primary_id);
            } catch (\Scriptotek\Alma\Exception\ClientException $e) {
                \Log::notice('Access denied for SAML user not found in Alma.', ['email' => $primary_id]);
                \Session::flash('error', 'The user isn\'t registed in Alma.');
                return;
            }

            if ($alma_user_data->status->value != 'ACTIVE' || $alma_user_data->user_group->desc != config('alma.employee_group')) {
                \Log::notice('Access denied for SAML user not in the "' . config('alma.employee_group') . '" group in Alma.',
                    ['email' => $primary_id]);
                \Session::flash('error', 'The Alma user isn\'t active or isn\'t in the employee group.');
                return;
            }

            \Log::notice('Registered new SAML user.', ['email' => $primary_id]);

            $user = new User();
            $user->activated = true;
            $user->uio_id = $uio_id;
            $user->name = $attrs['cn'][0];
            $user->email = $attrs['mail'][0];
            $user->alma_ids = $alma_user_data->getIds();
            \Session::flash('status', '🤗 Velkommen til ub-tilvekst! Vi fant deg i Alma med følgende ID-er: ' . implode(', ', $alma_user_data->getIds()));
        }

        $user->saml_id = $uid;
        $user->saml_session = $data->getSessionIndex();

        $user->save();

        auth()->login($user);
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
