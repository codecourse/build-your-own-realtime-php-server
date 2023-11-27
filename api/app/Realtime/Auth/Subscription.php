<?php

namespace App\Realtime\Auth;

use App\Models\Authorization;
use Thruway\Event\MessageEvent;
use Thruway\Event\NewRealmEvent;
use Thruway\Module\RealmModuleInterface;
use Thruway\Module\RouterModule;

class Subscription extends RouterModule implements RealmModuleInterface
{
    public function getSubscribedRealmEvents()
    {
        return [
            'SendSubscribedMessageEvent' => ['handleSubscribedMessage', 5]
        ];
    }

    public function handleSubscribedMessage(MessageEvent $event)
    {
        $subscription = $event->session->getRealm()->getBroker()->getSubscriptionById(
            $event->message->getSubscriptionId()
        );

        $authorization = Authorization::byChannelAndSessionId(
            $subscription->getUri(), $event->session->getSessionId()
        )->first();

        if (strpos($subscription->getUri(), 'private') > -1 && !$authorization) {
            $subscription->getSubscriptionGroup()->removeSubscription($subscription);
        }
    }

    public function handleSendWelcomeMessage(MessageEvent $event)
    {
        //
    }

    public static function getSubscribedEvents()
    {
        return [
            'new_realm' => ['handleNewRealm', 10],
        ];
    }

    public function handleNewRealm(NewRealmEvent $event)
    {
        $this->realms[$event->realm->getRealmName()] = $event->realm;

        $event->realm->addModule($this);
    }
}