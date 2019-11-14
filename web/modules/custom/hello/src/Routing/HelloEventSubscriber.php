<?php
/**
 * Created by PhpStorm.
 * User: POE9
 * Date: 14/11/2019
 * Time: 10:13
 */

namespace Drupal\hello\Routing;

use Drupal\Core\Entity\EntityInterface;
use Drupal\Core\Routing\RouteSubscriberBase;
use Symfony\Component\Routing\RouteCollection;

/**
 * Listens to the dynamic route events.
 */
class HelloEventSubscriber extends RouteSubscriberBase {

    /**
     * {@inheritdoc}
     */
    public function alterRoutes(RouteCollection $collection)
    {
        // TODO: Implement alterRoutes() method.
        //ksm($collection);
        // Change path '/user/login' to '/login'.
        if ($route = $collection->get('entity.user.canonical')) {
            //$route->setPath('/login');
            $route->setRequirements(['_access_hello' => '10']);
        }

        if ($route = $collection->get('user.logout')) {
            //$route->setRequirement('_access', 'FALSE');
        }
    }
}