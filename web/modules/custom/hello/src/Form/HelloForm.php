<?php
/**
 * Created by PhpStorm.
 * User: POE9
 * Date: 12/11/2019
 * Time: 10:04
 */

namespace Drupal\hello\Form;

use Drupal\Core\Ajax\AjaxResponse;
use Drupal\Core\Ajax\CssCommand;
use Drupal\Core\Ajax\HtmlCommand;
use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;

class HelloForm extends FormBase{

    public function getFormId()
    {
        return 'hello_form';
    }

    public function buildForm(array $form, FormStateInterface $form_state)
    {
        // TODO: Implement buildForm() method.
        $form['description'] = [
            '#type' => 'item',
            '#markup' => $this->t('Please enter the title and accept the terms of use of the site.'),
        ];

        //$buidls = $form_state->getRebuildInfo('result_total');
        //$buidls = $form_state->getRebuildInfo()['result_total'];
        //if (!empty($buidls)) {
        if (isset($form_state->getRebuildInfo()['result_total'])) {

            //ksm($buidls['result_total']);

            $form['result'] = [
                '#type' => 'html_tag',
                '#tag' => 'h1',
                //'#value' => $this->t('Result : '.$buidls['result_total']),
                '#value' => $this->t('Result : ').$form_state->getRebuildInfo()['result_total'],
            ];
        }

        $form['first_value'] = [
            '#type' => 'textfield',
            '#title' => $this->t('First value'),
            '#ajax' => array(
                'callback' => array($this, 'validateTextAjax'),
               /* 'event' => 'change'*/
                'event' => 'keyup'
            ),
           /* '#suffix' => array('text-message' => '<span class="text-message"></span>',
                                'text-message-validate' => '<span class="text-message-validate"></span>',
            ),*/
            /*'#suffix' => '<span class="text-message-validate"></span>',*/
            '#suffix' => '<span class="error-message-first-value"></span>',
            '#description' => $this->t('Enter first value.'),
            '#required' => TRUE,
        ];

        $form['operation']= array(

            '#type' => 'radios',
            '#title' => t('Operation'),
            //'#default_value' => '0',
            '#default_value' => 'add',
            /*'#options' => array($this->t('Ajouter'), $this->t('Soustrac'), $this->t('Multiply'), $this->t('Divide')),*/
            '#options' => [
                'add' => $this->t('Ajouter'),
                'sous' => $this->t('Soustrac'),
                'multi' => $this->t('Multiply'),
                'div'=> $this->t('Divide')
                ],
            '#description' => $this->t('Chosse operation.'),
        );

        $form['second_value'] = [
            '#type' => 'textfield',
            '#title' => $this->t('Second value'),
            '#ajax' => array(
                'callback' => array($this, 'validateTextAjax'),
                /* 'event' => 'change'*/
                'event' => 'keyup'
            ),
            /* '#suffix' => array('text-message' => '<span class="text-message"></span>',
                                 'text-message-validate' => '<span class="text-message-validate"></span>',
             ),*/
            /*'#suffix' => '<span class="text-message-validate"></span>',*/
            '#suffix' => '<span class="error-message-second-value"></span>',
            '#description' => $this->t('Enter second value.'),
            '#required' => TRUE,
        ];

        // Add a submit button that handles the submission of the form.
        $form['submit'] = [
            '#type' => 'submit',
            '#value' => $this->t('Calculate'),
        ];

        return $form;
    }

    public function validateForm(array &$form, FormStateInterface $form_state)
    {
        parent::validateForm($form, $form_state); // TODO: Change the autogenerated stub

        $value1 = $form_state->getValue('first_value');
        $value2 = $form_state->getValue('second_value');
        $operation = $form_state->getValue('operation');

        if(!is_numeric($value1)){
            $form_state->setErrorByName('first_value', $this->t('Value first must be numeric!!'));
        }

        if(!is_numeric($value2)){
            $form_state->setErrorByName('second_value', $this->t('Value second must be numeric!!'));
        }

        if(empty($value2) && $operation == 'div'){
            $form_state->setErrorByName('second_value', $this->t('Value second is not 0!'));
        }

    }

    public function submitForm(array &$form, FormStateInterface $form_state)
    {
        // state API
        \Drupal::state()->set('hello_form_submission_time', REQUEST_TIME);
        //$val = \Drupal::state()->get('key');
        //ksm($val);

        // TODO: Implement submitForm() method.
        $value1 = $form_state->getValue('first_value');
        $value2 = $form_state->getValue('second_value');

        $total = '';
        $operation = $form_state->getValue('operation');
        //ksm($operation);
        /*if ($operation == 'add')
            $total = $value1 + $value2;
        elseif ($operation == 'sous')
            $total = $value1 - $value2;
        elseif ($operation == 'multi')
            $total = $value1 * $value2;
        elseif ($operation == 'div')
            $total = $value1 / $value2;*/


        switch ($operation){
            case 'add':
                $total = $value1 + $value2;
                break;
            case 'sous':
                $total = $value1 - $value2;
                break;
            case 'multi':
                $total = $value1 * $value2;
                break;
            case 'div':
                $total = $value1 / $value2;
                break;
        }

        $messenger = \Drupal::messenger();
        $messenger->addMessage('Result total: '.$total);

        /*$form_state->setValue('result_total', $total);*/
        $form_state->addRebuildInfo('result_total', $total);
        // retourne sur le formulaire avec les valeurs saisies
        $form_state->setRebuild();
    }

    public function validateTextAjax(array &$form, FormStateInterface $form_state){
        /*if(!is_numeric($form_state->getValue('first_value')) || !is_numeric($form_state->getValue('second_value'))){
            $css = ['border' => '2px solid red'];
            //$message = 'Ajax message: '.$form_state->getValue('first_value');
            $message2 = $this->t('input NOK');
        }else{
            $css = ['border' => '2px solid green'];
            //$message = 'Ajax message: '.$form_state->getValue('first_value');
            $message2 = $this->t('input OK');
        }



        $response = new AjaxResponse();
        if(!empty($form_state->getValue('first_value'))){
            $response->addCommand(new CssCommand('#edit-first-value', $css));
            //$message2 = 'Ajax message: ' . $form_state->getValue('first_value');
        }
        if(!empty($form_state->getValue('second_value'))){
            $response->addCommand(new CssCommand('#edit-second-value', $css));
            //$message2 = 'Ajax message: ' . $form_state->getValue('second_value');
        }
        $response->addCommand(new HtmlCommand('.text-message', $message));
        $response->addCommand(new HtmlCommand('.text-message-validate', $message2));*/

        // correction
        $response = new AjaxResponse();
        $field = $form_state->getTriggeringElement()['#name'];
        if (is_numeric($form_state->getValue($field))) {
            $css = ['border' => '2px solid green'];
            $message = $this->t('OK!');
        } else {
            $css = ['border' => '2px solid red'];
            $message = $this->t('%field must be numeric!', ['%field' => $form[$field]['#title']]);
        }

        $response->AddCommand(new CssCommand("[name=$field]", $css));
        $response->AddCommand(new HtmlCommand('.error-message-' . str_replace('_', '-', $field), $message));

        return $response;
    }
}