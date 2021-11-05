<?php

namespace App\EventListener;

use App\AppEvents;
use App\Event\AlertEvent;
use App\Service\Mailer;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;

class AlertListener implements EventSubscriberInterface{

    private TokenStorageInterface $tokenStorage;
    private Mailer $mailer;

    public function __construct(TokenStorageInterface $tokenStorage,Mailer $mailer) {
        $this->tokenStorage = $tokenStorage;
        $this->mailer = $mailer;
    }

  /**
   * Undocumented function
   *
   * @return array
   */
    public static function getSubscribedEvents(): array
    {
        return [
            AppEvents::GENERATE_ALERT => 'onGenerateAlert',
        ];
    }

    
    /**
     * Undocumented function
     *
     * @param AlertEvent $alertEvent
     * @return void
     */
    public function onGenerateAlert(AlertEvent $alertEvent):void
    {
        $this->mailer->alertNotification($alertEvent->getJob());
    }

}