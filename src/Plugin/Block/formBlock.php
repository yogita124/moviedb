<?php

  namespace Drupal\actormoviedb\Plugin\Block;

  use Drupal\Core\Routing;
  use Drupal\Core\Block\BlockBase;
  use Drupal\Core\Form\ConfigFormBase;
  use Drupal\Core\Form\FormStateInterface;
  use Drupal\file\Entity\File;

    /**
   * This class provides custom block for form block.
   * 
   * @Block(
   * id = "form_Block", 
   * admin_label = @Translation("Form"),
   * category = @Translation("form_block"),
   * )
   */
  class formBlock extends BlockBase {
    public function form_block() {
      // Fetch the configurations that are loaded in the db.
      $config = \Drupal::config('actormoviedb.settings');
      // Fetch title from that config.
      $title = $config->get('title');
      // Fetch description from that config.
      $description = $config->get('description');
      // Fetch image from that config.
      $imageId = $config->get('image');
      $imageEntityLoad = \Drupal\file\Entity\File::load($imageId[0]);
      // Fetch url of the image source.
      $image = $imageEntityLoad->url();
      // Return the fetched values and theme.
      return array(
        'title' => $title,
        'description' => $description,
        'image' => $image,
        'theme' => 'form_block',
      );
    }
    /**
     * To render block.
     *
     * @return void
     */
    public function build() {
      $returnVariable = $this->form_block();
      return array(
        '#theme' => $returnVariable['theme'],
        '#title' => $returnVariable['title'],
        '#description' => $returnVariable['description'],
        '#image' => $returnVariable['image'],
        '#cache' => ['max-age' => 0],
      );
    }
  }
