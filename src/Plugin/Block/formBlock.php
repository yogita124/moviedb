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
      $config = \Drupal::config('actormoviedb.settings');
      $title = $config->get('title');
      // kint($title);
      $description = $config->get('description');
      // kint($description);
      $imageId = $config->get('image');
      // kint($imageId);
      $imageEntityLoad = \Drupal\file\Entity\File::load($imageId[0]);
      $image = $imageEntityLoad->url();
      // kint($image);
      return array(
        'title' => $title,
        'description' => $description,
        'image' => $image,
        'theme' => 'form_block',
      );
    }
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