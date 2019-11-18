<?php

namespace Drupal\annonce\EventSubscriber;

use Drupal\Component\Datetime\TimeInterface;
use Drupal\Core\Routing\CurrentRouteMatch;
use Drupal\Core\Session\AccountProxyInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\EventDispatcher\Event;
use Symfony\Component\HttpKernel\KernelEvents;
use Drupal\Core\Database\Connection;

class AnnonceEventSubscriber implements EventSubscriberInterface{

    protected $user;
    protected $current_route_match;
    protected $database;
    protected $time;

    // implémente le service User
    function __construct(AccountProxyInterface $user, CurrentRouteMatch $current_route_match, TimeInterface $time, Connection $database){
        $this->currentUser = $user;
        $this->currentRouteMatch = $current_route_match;
        $this->time = $time;
        $this->connection = $database;
    }

    static function getSubscribedEvents(){
        $event[KernelEvents::REQUEST][] = array('onRequest');

        return $event;
    }

    public function onRequest(Event $event){

        if($this->currentRouteMatch->getRouteName() == "entity.annonce.canonical") {

            //$this->time->getCurrentTime();
            //$this->currentUser->id();
            //ksm($this->currentRouteMatch);
            //ksm($this->currentRouteMatch->getParameter('annonce'));
            $annonce = $this->currentRouteMatch->getParameter('annonce');
            //ksm($annonce->id());

            $this->connection->insert('annonce_user_views')
                ->fields([
                    'uid' => $this->currentUser->id(),
                    //'time' => $this->time->getCurrentTime(),
                    'time' => $this->time->getRequestTime(),
                    'aid' => $annonce->id(),
                ])->execute();

            //drupal_set_message('Event for ');
        }

    }
}