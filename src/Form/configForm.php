<?php
  namespace Drupal\actormoviedb\Form;

  use Drupal\Core\Form\FormStateInterface;
  use Drupal\Core\Form\ConfigFormBase;
  use Drupal\Core\Url;
  use Drupal\file\Entity\File;

  class configForm extends ConfigFormBase {
    protected function getEditableConfigNames() {
      return 'actormoviedb.settings';
    }

    public function getFormId() {
      return 'settings_form';
    }

    public function buildForm(array $form, FormStateInterface $form_state) {
      $config = $this->config('actormoviedb.settings');
      $form['title'] = array(
        '#type' => 'textfield',
        '#title' => $this->t('Title'),
      );
      $form['description'] = array(
        '#type' => 'textarea',
        '#title' => $this->t('Description'),
      );
      $form['image'] = array(
        '#type' => 'managed_file',
        '#title' => $this->t('Image'),
        '#upload_location' => 'public://image'
      );
      return parent::buildForm($form, $form_state);
    }

    public function validateForm(array &$form, FormStateInterface $form_state) {
      parent::validateForm($form, $form_state);
    }

    public function submitForm(array &$form, FormStateInterface $form_state) {
      $var = $form_state->getValue('image', 0);
      $file = File::load($var[0]);
      $file->setPermanent();
      $file->save();
      $this->configFactory->getEditable('actormoviedb.settings')
           ->set('title', $form_state->getValue('title'))
           ->set('description', $form_state->getValue('description'))
           ->set('image', $form_state->getValue('image', 0))
           ->save();
      parent::submitForm($form, $form_state);
    }
  }