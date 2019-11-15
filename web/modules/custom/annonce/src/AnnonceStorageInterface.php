<?php

namespace Drupal\annonce;

use Drupal\Core\Entity\ContentEntityStorageInterface;
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
interface AnnonceStorageInterface extends ContentEntityStorageInterface {

  /**
   * Gets a list of Annonce revision IDs for a specific Annonce.
   *
   * @param \Drupal\annonce\Entity\AnnonceInterface $entity
   *   The Annonce entity.
   *
   * @return int[]
   *   Annonce revision IDs (in ascending order).
   */
  public function revisionIds(AnnonceInterface $entity);

  /**
   * Gets a list of revision IDs having a given user as Annonce author.
   *
   * @param \Drupal\Core\Session\AccountInterface $account
   *   The user entity.
   *
   * @return int[]
   *   Annonce revision IDs (in ascending order).
   */
  public function userRevisionIds(AccountInterface $account);

  /**
   * Counts the number of revisions in the default language.
   *
   * @param \Drupal\annonce\Entity\AnnonceInterface $entity
   *   The Annonce entity.
   *
   * @return int
   *   The number of revisions in the default language.
   */
  public function countDefaultLanguageRevisions(AnnonceInterface $entity);

  /**
   * Unsets the language for all Annonce with the given language.
   *
   * @param \Drupal\Core\Language\LanguageInterface $language
   *   The language object.
   */
  public function clearRevisionsLanguage(LanguageInterface $language);

}
