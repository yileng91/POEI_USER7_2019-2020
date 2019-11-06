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

/*
 * @return array
 */
class Hello2Controller extends ControllerBase{
    public function content($nodetype = NULL){

        $nodes_types = $this->entityTypeManager()->getStorage('node_type')->loadMultiple();
        //ksm($nodes_types);
        // liste les types de noeuds du site
        //ksm($this->entityTypeManager()->getDefinition());
        $node_type_item = [];
        foreach ($nodes_types as $nodes_type) {
            //$node_type_item[] = $nodes_type->label();
            // affichage des types de contenu
            $url = new Url('hello.page2',['nodetype' => $nodes_type->id()]);
            $node_type_link = new Link($nodes_type->label(), $url);
            $node_type_item[] = $node_type_link;
        }
        $node_type_list = [
            '#theme' => 'item_list',
            '#items' => $node_type_item,
            '#title' => $this->t('Filter by node type'),
        ];

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
        $items = [];
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
        // render array
        $list = [
            '#theme' => 'item_list',
            '#items' => $items,
            '#title' => $this->t('List')];
        $pager = ['#type' => 'pager'];
        /*return [
            $pager, $list, $pager
            ];*/

        return [
            'node_type_list' => $node_type_list,
            'list' => $list,
            'pager' => $pager,
            '#cache' => ['max-age','0'],
        ];

    }
}