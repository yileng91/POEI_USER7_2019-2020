<?php

namespace Drupal\date_condition\Plugin\Condition;

use Drupal\Core\Condition\ConditionPluginBase;
use Drupal\Core\Datetime\DrupalDateTime;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Lock\NullLockBackend;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Provides a 'date conditio' condition to enable a condition based in module selected status.
 *
 * @Condition(
 *   id = "date_condition",
 *   label = @Translation("Date")
 * )
 *
 */
class DateCondition extends ConditionPluginBase {

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container, array $configuration, $plugin_id, $plugin_definition) {
    return new static(
      $configuration,
      $plugin_id,
      $plugin_definition
    );
  }

  /**
   * Creates a new DateCondition object.
   *
   * @param array $configuration
   *   The plugin configuration, i.e. an array with configuration values keyed
   *   by configuration option name. The special key 'context' may be used to
   *   initialize the defined contexts by setting it to an array of context
   *   values keyed by context names.
   * @param string $plugin_id
   *   The plugin_id for the plugin instance.
   * @param mixed $plugin_definition
   *   The plugin implementation definition.
   */
  public function __construct(array $configuration, $plugin_id, $plugin_definition) {
    parent::__construct($configuration, $plugin_id, $plugin_definition);
  }

  /**
   * {@inheritdoc}
   */
  public function buildConfigurationForm(array $form, FormStateInterface $form_state) {
    // Sort all modules by their names.
    /*$modules = system_rebuild_module_data();
    uasort($modules, 'system_sort_modules_by_info_name');

    $options = [NULL => t('Select a module')];
    foreach ($modules as $module_id => $module) {
      $options[$module_id] = $module->info['name'];
    }

    $form['module'] = [
      '#type' => 'select',
      '#title' => $this->t('Select a module to validate'),
      '#default_value' => $this->configuration['module'],
      '#options' => $options,
      '#description' => $this->t('Module selected status will be use to evaluate condition.'),
    ];*/

    $form['start_date'] = [
        '#type' => 'date',
        '#title' => $this->t('Start date module visibility'),
        '#format' => 'm/d/Y',
        '#description' => $this->t('i.e. 09/06/2016'),
        '#default_value' => $this->configuration['start_date'],

    ];

    $form['end_date'] = [
        '#type' => 'date',
        '#title' => $this->t('End date module visibility'),
        '#format' => 'm/d/Y',
        '#description' => $this->t('i.e. 09/06/2016'),
        '#default_value' => $this->configuration['end_date'],
      ];

    /*return parent::buildConfigurationForm($form, $form_state);*/
    return $form;
  }

  /**
   * {@inheritdoc}
   */
  public function submitConfigurationForm(array &$form, FormStateInterface $form_state) {
    //$this->configuration['module'] = $form_state->getValue('module');

    $this->configuration['start_date'] = $form_state->getValue('start_date');
    $this->configuration['end_date'] = $form_state->getValue('end_date');

    parent::submitConfigurationForm($form, $form_state);
  }

  /**
   * {@inheritdoc}
   */
  public function defaultConfiguration() {
    /*return ['module' => ''] + parent::defaultConfiguration();*/
    return [
        'start_date' => '',
        'end_date' => '',
        ] + parent::defaultConfiguration();
  }

  /**
   * Evaluates the condition and returns TRUE or FALSE accordingly.
   *
   * @return bool
   *   TRUE if the condition has been met, FALSE otherwise.
   */
  public function evaluate() {
    /*if (empty($this->configuration['module']) && !$this->isNegated()){
        return TRUE;
    }

    $module = $this->configuration['module'];
    $modules = system_rebuild_module_data();

    return $modules[$module]->status;
    */

    $date_today = new DrupalDateTime('today');
    $format_start_date = $this->configuration['start_date'] ? new DrupalDateTime($this->configuration['start_date']) : NULL;
    $format_end_date = $this->configuration['end_date'] ? new DrupalDateTime($this->configuration['end_date']) : NULL;

    return (!$format_start_date || ($format_start_date <= $date_today)) && (!$format_end_date || ($format_end_date >= $date_today));
  }

  public function validateConfigurationForm(array &$form, FormStateInterface $form_state)
  {
      // chaine non vide soit ''
      if(!empty($form_state->getValue('start_date')) && !empty($form_state->getValue('end_date'))){
          if(new DrupalDateTime($form_state->getValue('end_date')) < new DrupalDateTime($form_state->getValue('start_date'))){
              $form_state->setErrorByName('end_date', $this->t('End date error'));
          }
      }
  }

    /**
   * Provides a human readable summary of the condition's configuration.
   */
  public function summary() {
    $module = $this->getContextValue('module');
    $modules = system_rebuild_module_data();

    $status = ($modules[$module]->status)?t('enabled'):t('disabled');

    return t('The module @module is @status.', ['@module' => $module, '@status' => $status]);
  }

}
