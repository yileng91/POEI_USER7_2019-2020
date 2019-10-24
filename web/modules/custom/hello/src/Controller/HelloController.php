<?php
/**
 * Created by PhpStorm.
 * User: POE9
 * Date: 24/10/2019
 * Time: 16:29
 */

namespace Drupal\hello\Controller;

use Drupal\Core\Controller\ControllerBase;

/*
 * @return array
 */
class HelloController extends ControllerBase{
    public function content(){
        /*return ['#markup' => $this->t('Hello par le Controller '),
            ];*/
        //return ['#markup' =>$this->t('Hello par le Controller '.$this->currentUser()->getAccountName())];
        //return ['#markup' =>$this->t('Hello par le Controller '.$this->currentUser()->getDisplayName())];
        $message = $this->t('You name is @username',[
            '@username' => $this->currentUser()->getDisplayName(),
        ]);
        //return ['#markup' => $message];
        return ['#type' => 'markup',
        '#markup' => $message];
    }
}