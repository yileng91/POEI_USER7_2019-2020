<?php
/**
 * Created by PhpStorm.
 * User: POE9
 * Date: 12/11/2019
 * Time: 16:23
 */
namespace Drupal\hello\Form;

use Drupal\Core\Ajax\AjaxResponse;
use Drupal\Core\Ajax\CssCommand;
use Drupal\Core\Ajax\HtmlCommand;
use Drupal\Core\Form\ConfigFormBase;
use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;

class AdminForm extends ConfigFormBase {

    public function getFormId()
    {
        return 'admin_form';
    }

    protected function getEditableConfigNames()
    {
        // TODO: Implement getEditableConfigNames() method.
        return ['hello.settings'];
    }

    public function buildForm(array $form, FormStateInterface $form_state)
    {
        // TODO: Implement buildForm() method.
        $form['description'] = [
            '#type' => 'item',
            '#markup' => $this->t('Config Stats User.'),
        ];
        $purge_days_number = $this->config('hello.settings')->get('purge_days_number');

        //ksm($purge_days_number);

        $form['purge_days_number'] = array(
            '#title' => $this->t('How long to keep user activity statistics'),
            '#type' => 'select',
            '#description' => $this->t('Purge per day.'),

            // (0, 1, 2, 7, 14, 30).
            '#options' => [
                'select' => $this->t('--- SELECT ---'),
                '0'=>$this->t('0'),
                '1' => $this->t('1'),
                '2' => $this->t('2'),
                '7' => $this->t('7'),
                '14' => $this->t('14'),
                '30' => $this->t('30')
            ],
            '#default_value' => $purge_days_number,
        );

        /*// Add a submit button that handles the submission of the form.
        $form['submit'] = [
            '#type' => 'submit',
            '#value' => $this->t('Save configuration'),
        ];

        return $form;
        */
        // met automatiquement le bouton submit 'Save configuration'
        return parent::buildForm($form, $form_state);
    }

    public function submitForm(array &$form, FormStateInterface $form_state)
    {
        $this->config('hello.settings')
        ->set('purge_days_number', $form_state->getValue('purge_days_number'))
        ->save();

        parent::submitForm($form, $form_state);
    }


}