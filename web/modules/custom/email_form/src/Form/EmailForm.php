<?php

namespace Drupal\email_form\Form;

use Drupal\Core\Form\FormStateInterface;
use Drupal\reusable_forms\Form\ReusableFormBase;


/**
 * Defines the EmailForm class.
 */
class EmailForm extends ReusableFormBase {

    /**
     * {@inheritdoc}.
     */
    public function getFormId() {
        return 'email_form';
    }

    /**
     * {@inheritdoc}.
     */
    public function buildForm(array $form, FormStateInterface $form_state) {
        /*$form['first_name'] = array(
            '#type' => 'textfield',
            '#title' => $this->t('First name'),
        );*/

        $form['email'] = array(
            '#type' => 'email',
            '#title' => $this->t('Email'),
        );

        $form = parent::buildForm($form, $form_state);

        return $form;
    }

    /**
     * {@inheritdoc}
     */
    public function submitForm(array &$form, FormStateInterface $form_state) {
        $mailManager = \Drupal::service('plugin.manager.mail');
        $mailManager->mail('email_form', 'node_mail', 'vangneva.ku@gmail.com', 'en', [], NULL, TRUE);
    }
}
