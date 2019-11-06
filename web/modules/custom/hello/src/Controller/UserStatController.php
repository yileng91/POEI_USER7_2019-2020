<?php
/**
 * Created by PhpStorm.
 * User: POE9
 * Date: 24/10/2019
 * Time: 16:29
 */

namespace Drupal\hello\Controller;

use Drupal\Core\Controller\ControllerBase;
use Drupal\Core\Link;
use Drupal\Core\Url;
use \Drupal\user\UserInterface;
/*
 * @return array
 */
class UserStatController extends ControllerBase
{
    public function content(UserInterface $user)
    {
        //ksm($user);
        // requête vers la table
        $user_id = $user->id();
        $database = \Drupal::database();
        $data_user = $database->select('hello_user_statistics', 'h')
            ->fields('h', array('time','action'))
            ->condition('uid', $user_id)
            ->execute();

        $datas = $data_user->fetchAll();
        //ksm($datas);

        // création d'un tableau de stats
        $stats = [];
        $date_formatter = \Drupal::service('date.formatter');
        foreach ($datas as $data) {
            $Action = $data->action == 1 ? $this->t('Login') : $this->t('Logout');
            /*$action = $data->action;
            if($action == '1'){
                $Action = $this->t('Login');
            }
            else{
                $Action = $this->t('Logout');
            }*/
            $time_log = $date_formatter->format($data->time,'custom','H:i:s\s');
            $stats[] = [$Action,$time_log];
        }

        // affiche dans un tableau
        return ['#type' => 'table',
                /*'#header'=> ['Action', 'Time'],*/
                '#header'=> [$this->t('Action'), $this->t('Time')],
                '#rows' => $stats,
                '#empty' => $this->t('Not Connections'),
                '#cache' => ['max-age','0'],
            ];
    }
}