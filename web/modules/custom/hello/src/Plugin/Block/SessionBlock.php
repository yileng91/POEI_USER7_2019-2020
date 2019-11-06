<?php
/**
 * Created by PhpStorm.
 * User: POE9
 * Date: 05/11/2019
 * Time: 10:06
 */
namespace Drupal\hello\Plugin\Block;

use Drupal\Core\Block\BlockBase;

/**
 *  Provides a session block
 *  @Block(
 *   id = "hello_session_block",
 *   admin_label = @Translation("Session actives")
 * )
 */

class SessionBlock extends BlockBase{
    /**
     * Implements Drupal\Core\Block\BlockBase::build().
     */
    public function build(){

        $database = \Drupal::database();
        $session = $database->select('sessions', 's')
                            ->countQuery()
                            ->execute();

        $session_total = $session->fetchField();

        return [
            '#markup' => $this->t('Nb session : %session.', [
                '%session' => $session_total,

            ]),
            '#cache' => [
                'keys' => ['hello_session_block'],
                'max-age' => '3',
            ],
        ];
    }
}