<?php
/**
 * Created by PhpStorm.
 * User: POE9
 * Date: 15/11/2019
 * Time: 17:04
 */

namespace Drupal\annonce\Plugin\Task;

use Drupal\Core\Menu\LocalTaskDefault;

class AnnonceCacheTask extends LocalTaskDefault{

    /*
     *
     * vide les caches par utilisateur
     */
    public function getCacheContexts()
    {
        return ['user'];
    }
}