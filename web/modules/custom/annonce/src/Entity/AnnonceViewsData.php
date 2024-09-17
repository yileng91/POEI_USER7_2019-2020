<?php

namespace Drupal\annonce\Entity;

use Drupal\views\EntityViewsData;

/**
 * Provides Views data for Annonce entities.
 */
class AnnonceViewsData extends EntityViewsData {

  /**
   * {@inheritdoc}
   */
  public function getViewsData() {
    $data = parent::getViewsData();

    // Additional information for Views integration, such as table joins, can be
    // put here.

      //$data['annonce_user_views'] = array();

      //$data['annonce_user_views']['table'] = array();

      $data['annonce_user_views']['table']['group'] = $this->t('Annonce history');

      $data['annonce_user_views']['table']['provider'] = 'annonce';

      $data['annonce_user_views']['table']['base'] = [
          // Identifier (primary) field in this table for Views.
          'field' => 'id',
          // Label in the UI.
          'title' => $this->t('Annonce history'),
          // Longer description in the UI. Required.
          'help' => $this->t('Annonce history contains historical.'),
          'weight' => -10,
      ];

      $data['annonce_user_views']['uid'] = [
          'title' => $this->t('Annonce User ID'),
          'help' => $this->t('Annonce User ID.'),
          'field' => ['id' => 'numeric'],
          'sort' => ['id' => 'standard'],
          'filter' => ['id' => 'numeric'],
          'argument' => ['id' => 'numeric'],
          'relationship' => [
              'base' => 'users_field_data',
              'base field' => 'uid',
              'id' => 'standard',
              'label' => $this->t('Annonce history UID -> User ID'),
          ],
      ];

      $data['annonce_user_views']['aid'] = [
          'title' => $this->t('Annonce ID'),
          'help' => $this->t('Annonce ID.'),
          'field' => ['id' => 'numeric'],
          'sort' => ['id' => 'standard'],
          'filter' => ['id' => 'numeric'],
          'argument' => ['id' => 'numeric'],
          'relationship' => [
              'base' => 'annonce_field_data',
              'base field' => 'id',
              'id' => 'standard',
              'label' => $this->t('Annonce history ID -> Annonce ID'),
          ],
      ];

      $data['annonce_user_views']['time'] = [
          'title' => $this->t('Annonce date time field'),
          'help' => $this->t('Annonce date time.'),
          'field' => ['id' => 'date'],
          'sort' => ['id' => 'date'],
          'filter' => ['id' => 'date'],
      ];

    return $data;
  }

}
