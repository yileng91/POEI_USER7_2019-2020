<?php

namespace Drupal\annonce\Controller;

use Drupal\Component\Utility\Xss;
use Drupal\Core\Controller\ControllerBase;
use Drupal\Core\Datetime\DateFormatter;
use Drupal\Core\DependencyInjection\ContainerInjectionInterface;
use Drupal\Core\Render\Renderer;
use Drupal\Core\Url;
use Drupal\annonce\Entity\AnnonceInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Class AnnonceController.
 *
 *  Returns responses for Annonce routes.
 */
class AnnonceController extends ControllerBase implements ContainerInjectionInterface {


  /**
   * The date formatter.
   *
   * @var \Drupal\Core\Datetime\DateFormatter
   */
  protected $dateFormatter;

  /**
   * The renderer.
   *
   * @var \Drupal\Core\Render\Renderer
   */
  protected $renderer;

  /**
   * Constructs a new AnnonceController.
   *
   * @param \Drupal\Core\Datetime\DateFormatter $date_formatter
   *   The date formatter.
   * @param \Drupal\Core\Render\Renderer $renderer
   *   The renderer.
   */
  public function __construct(DateFormatter $date_formatter, Renderer $renderer) {
    $this->dateFormatter = $date_formatter;
    $this->renderer = $renderer;
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container) {
    return new static(
      $container->get('date.formatter'),
      $container->get('renderer')
    );
  }

  /**
   * Displays a Annonce revision.
   *
   * @param int $annonce_revision
   *   The Annonce revision ID.
   *
   * @return array
   *   An array suitable for drupal_render().
   */
  public function revisionShow($annonce_revision) {
    $annonce = $this->entityTypeManager()->getStorage('annonce')
      ->loadRevision($annonce_revision);
    $view_builder = $this->entityTypeManager()->getViewBuilder('annonce');

    return $view_builder->view($annonce);
  }

  /**
   * Page title callback for a Annonce revision.
   *
   * @param int $annonce_revision
   *   The Annonce revision ID.
   *
   * @return string
   *   The page title.
   */
  public function revisionPageTitle($annonce_revision) {
    $annonce = $this->entityTypeManager()->getStorage('annonce')
      ->loadRevision($annonce_revision);
    return $this->t('Revision of %title from %date', [
      '%title' => $annonce->label(),
      '%date' => $this->dateFormatter->format($annonce->getRevisionCreationTime()),
    ]);
  }

  /**
   * Generates an overview table of older revisions of a Annonce.
   *
   * @param \Drupal\annonce\Entity\AnnonceInterface $annonce
   *   A Annonce object.
   *
   * @return array
   *   An array as expected by drupal_render().
   */
  public function revisionOverview(AnnonceInterface $annonce) {
    $account = $this->currentUser();
    $annonce_storage = $this->entityTypeManager()->getStorage('annonce');

    $langcode = $annonce->language()->getId();
    $langname = $annonce->language()->getName();
    $languages = $annonce->getTranslationLanguages();
    $has_translations = (count($languages) > 1);
    $build['#title'] = $has_translations ? $this->t('@langname revisions for %title', ['@langname' => $langname, '%title' => $annonce->label()]) : $this->t('Revisions for %title', ['%title' => $annonce->label()]);

    $header = [$this->t('Revision'), $this->t('Operations')];
    $revert_permission = (($account->hasPermission("revert all annonce revisions") || $account->hasPermission('administer annonce entities')));
    $delete_permission = (($account->hasPermission("delete all annonce revisions") || $account->hasPermission('administer annonce entities')));

    $rows = [];

    $vids = $annonce_storage->revisionIds($annonce);

    $latest_revision = TRUE;

    foreach (array_reverse($vids) as $vid) {
      /** @var \Drupal\annonce\AnnonceInterface $revision */
      $revision = $annonce_storage->loadRevision($vid);
      // Only show revisions that are affected by the language that is being
      // displayed.
      if ($revision->hasTranslation($langcode) && $revision->getTranslation($langcode)->isRevisionTranslationAffected()) {
        $username = [
          '#theme' => 'username',
          '#account' => $revision->getRevisionUser(),
        ];

        // Use revision link to link to revisions that are not active.
        $date = $this->dateFormatter->format($revision->getRevisionCreationTime(), 'short');
        if ($vid != $annonce->getRevisionId()) {
          $link = $this->l($date, new Url('entity.annonce.revision', [
            'annonce' => $annonce->id(),
            'annonce_revision' => $vid,
          ]));
        }
        else {
          $link = $annonce->link($date);
        }

        $row = [];
        $column = [
          'data' => [
            '#type' => 'inline_template',
            '#template' => '{% trans %}{{ date }} by {{ username }}{% endtrans %}{% if message %}<p class="revision-log">{{ message }}</p>{% endif %}',
            '#context' => [
              'date' => $link,
              'username' => $this->renderer->renderPlain($username),
              'message' => [
                '#markup' => $revision->getRevisionLogMessage(),
                '#allowed_tags' => Xss::getHtmlTagList(),
              ],
            ],
          ],
        ];
        $row[] = $column;

        if ($latest_revision) {
          $row[] = [
            'data' => [
              '#prefix' => '<em>',
              '#markup' => $this->t('Current revision'),
              '#suffix' => '</em>',
            ],
          ];
          foreach ($row as &$current) {
            $current['class'] = ['revision-current'];
          }
          $latest_revision = FALSE;
        }
        else {
          $links = [];
          if ($revert_permission) {
            $links['revert'] = [
              'title' => $this->t('Revert'),
              'url' => $has_translations ?
              Url::fromRoute('entity.annonce.translation_revert', [
                'annonce' => $annonce->id(),
                'annonce_revision' => $vid,
                'langcode' => $langcode,
              ]) :
              Url::fromRoute('entity.annonce.revision_revert', [
                'annonce' => $annonce->id(),
                'annonce_revision' => $vid,
              ]),
            ];
          }

          if ($delete_permission) {
            $links['delete'] = [
              'title' => $this->t('Delete'),
              'url' => Url::fromRoute('entity.annonce.revision_delete', [
                'annonce' => $annonce->id(),
                'annonce_revision' => $vid,
              ]),
            ];
          }

          $row[] = [
            'data' => [
              '#type' => 'operations',
              '#links' => $links,
            ],
          ];
        }

        $rows[] = $row;
      }
    }

    $build['annonce_revisions_table'] = [
      '#theme' => 'table',
      '#rows' => $rows,
      '#header' => $header,
    ];

    return $build;
  }

}
