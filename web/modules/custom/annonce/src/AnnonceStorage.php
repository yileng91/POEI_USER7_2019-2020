<?php

namespace Drupal\annonce;

use Drupal\Core\Entity\Sql\SqlContentEntityStorage;
use Drupal\Core\Session\AccountInterface;
use Drupal\Core\Language\LanguageInterface;
use Drupal\annonce\Entity\AnnonceInterface;

/**
 * Defines the storage handler class for Annonce entities.
 *
 * This extends the base storage class, adding required special handling for
 * Annonce entities.
 *
 * @ingroup annonce
 */
class AnnonceStorage extends SqlContentEntityStorage implements AnnonceStorageInterface {

  /**
   * {@inheritdoc}
   */
  public function revisionIds(AnnonceInterface $entity) {
    return $this->database->query(
      'SELECT vid FROM {annonce_revision} WHERE id=:id ORDER BY vid',
      [':id' => $entity->id()]
    )->fetchCol();
  }

  /**
   * {@inheritdoc}
   */
  public function userRevisionIds(AccountInterface $account) {
    return $this->database->query(
      'SELECT vid FROM {annonce_field_revision} WHERE uid = :uid ORDER BY vid',
      [':uid' => $account->id()]
    )->fetchCol();
  }

  /**
   * {@inheritdoc}
   */
  public function countDefaultLanguageRevisions(AnnonceInterface $entity) {
    return $this->database->query('SELECT COUNT(*) FROM {annonce_field_revision} WHERE id = :id AND default_langcode = 1', [':id' => $entity->id()])
      ->fetchField();
  }

  /**
   * {@inheritdoc}
   */
  public function clearRevisionsLanguage(LanguageInterface $language) {
    return $this->database->update('annonce_revision')
      ->fields(['langcode' => LanguageInterface::LANGCODE_NOT_SPECIFIED])
      ->condition('langcode', $language->getId())
      ->execute();
  }

}
