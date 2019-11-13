<?php
namespace Drupal\hello\Access;

use Drupal\Component\Datetime\TimeInterface;
use Drupal\Core\Access\AccessCheckInterface;
use Drupal\Core\Access\AccessResult;
use Drupal\Core\Session\AccountInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Route;

class HelloAccessCheck implements AccessCheckInterface{

    protected $time;

    // service TimeInterface qui ne change pas
    function __construct(TimeInterface $time){
        $this->time = $time;
    }

    public function applies(Route $route)
    {
        // TODO: Implement applies() method.
        return NULL;
    }

    public function access(Route $route, Request $request = NULL, AccountInterface $account)
    {
        $param = $route->getRequirement('_access_hello');

        /*if ($account->isAnonymous()) {
            $access = AccessResult::forbidden()->cachePerUser();
        }
        else {

            ksm($account->getAccount()->created);
            $create_timestamp = $account->getAccount()->created;

            if ((REQUEST_TIME - $create_timestamp) > ($param * 3600)) {
                $access = AccessResult::allowed()->cachePerUser();
            }
            else {
                $access = AccessResult::forbidden()->cachePerUser();
            }
        }

        return $access;*/

        if (!$account->isAnonymous() && ($this->time->getCurrentTime() - $account->getAccount()->created > $param * 3600 )){
            return AccessResult::allowed()->cachePerUser();
        }

        return AccessResult::forbidden()->cachePerUser();
    }
}