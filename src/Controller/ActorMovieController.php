<?php
  namespace Drupal\actormoviedb\Controller;
  use Drupal\Core\Controller\ControllerBase;
  use Drupal\node\Entity\Node;
  use Drupal\node\NodeInterface;
  use Drupal\paragraphs\Entity\Paragraph;
  use \Symfony\Component\HttpFoundation\Response;
  use \Symfony\Component\HttpFoundation\JsonResponse;
  use Drupal\Tests\system\Functional\System;
  use Drupal\block\Entity\Block;

  /**
   * Class that extends ControllerBase class and has functions that fetch the values of actor list, movie list, pop-up and search.
   */
  class ActorMovieController extends ControllerBase {
    /**
     * Function actor fetches the value for actor table
     *
     * @return array
     */
    public function actor() {
      // This parameter stores the name the type of content.
      $bundle = 'Actor';
      // Query that stores the node that are published and are of actor type in a variable.
      $query = \Drupal::entityQuery('node')
               ->condition('status', 1)
               ->condition('type', $bundle);
      // Query extracted is executed and saved in a variable.
      $nids = $query->execute();
      // When no query if generated.
      if (empty($nids)) {
        return array(
          '#markup' => 'No value found in Actor list',
        );
      }
      // When some query is generated.
      else {
        // More than one entity load.
        $load = entity_load_multiple('node', $nids);
        // For every entity values are fetched.
        foreach ($load as $nid) {
          // Get nid of actor.
          $actorNid = $nid->nid->value;
          // Get actor name.
          $actorName = $nid->title->value;
          // Get actor description.
          $actorDescription = $nid->get('field_actor_description')->value;
          // Get target id of the image of actor.
          $actorImageId = $nid->get('field_actor_image')->target_id;
          // Loads the target id into drupal file.
          $actorImageEntityLoad = \Drupal\file\Entity\File::load($actorImageId);
          // Fetch url of the image that is stored in the drupal file.
          $actorImage = $actorImageEntityLoad->url();
          // Get last movie of actor.
          $actorLastMovie = $nid->get('field_last_movie')->value;
          // Get the rating array.
          $ratingArray = $nid->get('field_rating')->getValue();
          // Get the value of rating from the array.
          $rating = $ratingArray[0]['rating'];
          // Find the rating out of 5.
          $rating = $rating/20;

          // An array variable that stores all the details fetched for every actor.
          $actorList[] = [
            'nid' => $actorNid,
            'actorName' => $actorName,
            'actorDescription' => $actorDescription,
            'actorImage' => $actorImage,
            'actorLastMovie' => $actorLastMovie,
            'rating' => $rating,
          ];
        }
        // Return array with the value of theme and the variable that stores all details is passed.
        return array(
          '#theme' => 'show_actor',
          '#actorList' => $actorList,
        );
      }
    }
    /**
     * Function movie fetches the value for movie table
     *
     * @return array
     */
    public function movie() {
      // This parameter stores the name the type of content.
      $bundle = 'Movie';
      // Query that stores the node that are published and are of movie type in a variable.
      $query = \Drupal::entityQuery('node')
               ->condition('status', 1)
               ->condition('type', $bundle);
      // Query extracted is executed and saved in a variable.
      $nids = $query->execute();
      // When no query if generated.
      if (empty($nids)) {
        return array(
          '#markup' => 'No data found in movie list',
        );
      }
      // When some query is generated.
      else {
        // More than one entity load.
        $load = entity_load_multiple('node', $nids);
        foreach ($load as $nid) {
          // Get nid of movie.
          $movieid = $nid->nid->value;
          // Get movie name.
          $movieTitle = $nid->title->value;
          // Get actor description.
          $movieDescription = $nid->get('field_movie_description')->value;
          // Get target id of the poster of the movie.
          $movieImageId = $nid->get('field_poster')->target_id;
          // Loads the target id into drupal file.
          $movieImageEntityLoad = \Drupal\file\Entity\File::load($movieImageId);
          // Fetch url of the image that is stored in the drupal file.
          $movieImage = $movieImageEntityLoad->url();
          // Get the release date of the movie.
          $movieReleaseDate = $nid->get('field_release_date')->value;
          // Get all the details of actors of the movie that is stored in paragraph.
          $movieStarId = $nid->field_starring->getValue();
          $movieActorName = array();
          foreach ($movieStarId as $value) {
            $paragraph = Paragraph::load($value['target_id']);
            // Extract the name of actors of movie from paragraph.
            $movieActorName[] = $paragraph->field_actor_name->value;
            // $actor_role = $paragraph->field_role->value;
          }
          // An array variable that stores all the details fetched for every movie.
          $movieList[] = [
            'nid' => $movieid,
            'movieTitle' => $movieTitle,
            'movieDescription'=> $movieDescription,
            'movieImage' => $movieImage,
            'movieReleaseDate' => $movieReleaseDate,
            'movieStarring' => $movieActorName,
          ];
        }
        
        // Return array with the value of theme and the variable that stores all details is passed.
        return array(
          '#theme' => 'show_movie',
          '#movieList' => $movieList,

        );
      }
    }
    /**
     * Function pop-up fetches the value for pop-up for co-actors
     *
     * @return array
     */
    public function pop_up($actorId = NULL, $movieId = NULL) {
      // Load actor id passed from url.
      $actor = entity_load('node', $actorId);
      // Load movie id passed form url.
      $movie = entity_load('node', $movieId);
      // Get actor name.
      $actorName = $actor->title->value;
      // Get target id for the image of actor.
      $actorImageId = $actor->field_actor_image->target_id;
      // Loads the target id into drupal file.
      $actorImageEntityLoad = \Drupal\file\Entity\File::load($actorImageId);
      // Fetch url of the image that is stored in the drupal file.  
      $actorImage = $actorImageEntityLoad->url();
      // Get the values of paragraph.
      $movieStarParagraph = $movie->field_starring->getValue();
      foreach ($movieStarParagraph as $star) {
        $paragraph = Paragraph::load($star['target_id']);
        // Get the co-star name.
        $coStar = $paragraph->field_actor_name->value;
        // Check if the co-star is equal to actor selected.
        if ($coStar == $actorName) {
          // Get the role of the actor in the movie.
          $role = $paragraph->field_role->value;
        }
      }
      // An array variable that stores all the details fetched for pop-up.
      $popUpData = [
        'actorName' => $actorName,
        'actorImage' => $actorImage,
        'actorRole' => $role,
      ];
      // Return the response.
      return new JsonResponse($popUpData);
    }
    /**
     * Function pop-up fetches the value for search
     *
     * @return array
     */
    public function show_search() {
      $form = $this->formBuilder()->getForm('Drupal\actormoviedb\Form\searchBox');
      $formRender = \Drupal::service('renderer')->render($form);
      $url = \Drupal::request()->query->get('textField');
      $query = \Drupal::entityQuery('node')
               ->condition('status', 1)
               ->condition('title', $url, 'CONTAINS');
      $queryExecute = $query->execute();
      $loadQuery = entity_load_multiple('node', $queryExecute);
      foreach($loadQuery as $value) {
        $nid = $value->id();
        $title = $value->title->value;
        $movie = 'movie';
        $actor = 'actor';
        if($value->type->target_id == $movie) {
          $nid = $value->nid->value;
          $description = $value->get('field_movie_description')->value;
          $movieImageId = $value->get('field_poster')->target_id;
          $movieImageEntityLoad = \Drupal\file\Entity\File::load($movieImageId);
          $image = $movieImageEntityLoad->url();
          $category = $movie;
          $item[] = [
            'nid' => $nid,
            'title' => $title,
            'description' => $description,
            'image' => $image,
            'category' => $category,
          ];
        }
        elseif($value->type->target_id == $actor) {
          $nid = $value->nid->value;
          $description = $value->get('field_actor_description')->value;
          $actorImageId = $value->get('field_actor_image')->target_id;
          $actorImageEntityLoad = \Drupal\file\Entity\File::load($actorImageId);
          $image = $actorImageEntityLoad->url();
          $category = $actor;
          $item[] = [
            'nid' => $nid,
            'title' => $title,
            'description' => $description,
            'image' => $image,
            'category' => $category,
          ];
        }
        else {
          testPageNotFound();
        }
        
      }
      return array(
        '#theme' => 'show_search',
        '#item' => $item,
      );
    }
    public function show_block() {
      $blockManager = \Drupal::service('plugin.manager.block');
      // You can hard code configuration or you load from settings.
      $config = [];
      $pluginBlock = $blockManager->createInstance('form_Block', $config);
      // Some blocks might implement access check.
      $accessResult = $pluginBlock->access(\Drupal::currentUser());
      // Return empty render array if user doesn't have access.
      // $access_result can be boolean or an AccessResult class
      if (is_object($accessResult) && $accessResult->isForbidden() || is_bool($accessResult) && !$accessResult) {
        // You might need to add some cache tags/contexts.
        return [];
      }
      $render = $pluginBlock->build();
      return $render;
    }
  }