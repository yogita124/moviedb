<?php
  namespace Drupal\actormoviedb\Form;

  use Drupal\Core\Form\FormStateInterface;
  use Drupal\Core\Form\ConfigFormBase;
  use Drupal\Core\Url;
  use Drupal\file\Entity\File;

  class configForm extends ConfigFormBase {
    /**
     * Get the settings file from database.
     *
     * @return void
     */
    protected function getEditableConfigNames() {
      return 'actormoviedb.settings';
    }
    /**
     * Get the id of the form.
     *
     * @return void
     */
    public function getFormId() {
      return 'settings_form';
    }
    /**
     * This makes the base of the form
     *
     * @param array $form
     * @param FormStateInterface $form_state
     * @return void
     */
    public function buildForm(array $form, FormStateInterface $form_state) {
      // Fetch the settings file.
      $config = $this->config('actormoviedb.settings');
      // Set title field in the settings file.
      $form['title'] = array(
        '#type' => 'textfield',
        '#title' => $this->t('Title'),
        '#default_value' => $config->get('title'),
      );
      // Set description field in the settings file.
      $form['description'] = array(
        '#type' => 'textarea',
        '#title' => $this->t('Description'),
        '#default_value' => $config->get('description'),
      );
      // Set image field in the settings file.
      $form['image'] = array(
        '#type' => 'managed_file',
        '#title' => $this->t('Image'),
        '#upload_location' => 'public://image',
        '#default_value' => $config->get('image', 0),
      );
      return parent::buildForm($form, $form_state);
    }
    /**
     * This function check the validations
     *
     * @param array $form
     * @param FormStateInterface $form_state
     * @return void
     */
    public function validateForm(array &$form, FormStateInterface $form_state) {
      parent::validateForm($form, $form_state);
    }
    /**
     * This manages the process when the form is submitted.
     *
     * @param array $form
     * @param FormStateInterface $form_state
     * @return void
     */
    public function submitForm(array &$form, FormStateInterface $form_state) {
      // Get the value of the image.
      $var = $form_state->getValue('image', 0);
      // Load the image.
      $file = File::load($var[0]);
      // Set the image in the folder.
      $file->setPermanent();
      $file->save();
      // Set the field value in the config files.
      $this->configFactory->getEditable('actormoviedb.settings')
           ->set('title', $form_state->getValue('title'))
           ->set('description', $form_state->getValue('description'))
           ->set('image', $form_state->getValue('image', 0))
           ->save();
      parent::submitForm($form, $form_state);
    }
  }
