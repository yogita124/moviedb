<?php
  namespace Drupal\actormoviedb\Form;

  use Drupal\Core\Form\FormBase;
  use Drupal\Core\Form\FormStateInterface;
  use Drupal\Core\Url;

  class searchBox extends FormBase {
    public function buildForm(array $form, FormStateInterface $form_state) {
      $form['search'] = [
        '#type' => 'textfield',
        '#title' => $this->t('Search'),
      ];
      $form['submit'] = [
        '#type' => 'submit',
        '#value' => $this->t('Submit'),
      ];
      return $form;
    }
    public function getFormId() {
      return 'resume_form';
    }
    public function submitForm(array &$form, FormStateInterface $form_state) {
      $userInput = $form_state->getUserInput();
      $textField = $userInput['search'];
      $url = Url::fromRoute('search', [], ['query' => ['textField' => $textField]]);
      $form_state->setRedirectUrl($url);
    }
  }