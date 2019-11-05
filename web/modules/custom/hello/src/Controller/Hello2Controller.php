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
class Hello2Controller extends ControllerBase{
    public function content($nodetype = NULL){

        // manipuler les noeuds
        //$nodes = $this->entityTypeManager()->getStorage('node')->loadMultiple();
        $nodes_storages = $this->entityTypeManager()->getStorage('node');
        // requête sur les noeuds
        $query = $nodes_storages->getQuery();
        // filtre par URL
        if($nodetype){
            $query->condition('type', $nodetype);
        }
        // récupère les ids des noeuds
        $nids = $query->pager()->execute();
        $nodes = $nodes_storages->loadMultiple($nids);
        //ksm($nodes);
        $items[] = '';
        foreach ($nodes as $node) {
            $items[] = $node->toLink();
        }
        /*
        $message = $this->t('You name is @username',[
            '@username' => $this->currentUser()->getDisplayName(),
        ]);

        return ['#type' => 'markup',
        '#markup' => $message];*/
        /*return [
            '#theme' => 'item_list',
            '#items' => $items];*/
        $list = [
            '#theme' => 'item_list',
            '#items' => $items];
        $pager = ['#type' => 'pager'];
        return [
            $pager, $list, $pager
            ];

    }
}