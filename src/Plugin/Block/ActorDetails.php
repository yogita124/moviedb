<?php
  namespace Drupal\actormoviedb\Plugin\Block;

  use Drupal\node\Entity\Node;
  use Drupal\node\NodeInterface;
  use Drupal\paragraphs\Entity\Paragraph;
  use \Symfony\Component\HttpFoundation\Response;
  use \Symfony\Component\HttpFoundation\JsonResponse;
  use Drupal\Core\Routing;
  use Drupal\Core\Block\BlockBase;
  use Drupal\actormoviedb\Controller as Controller;

  /**
   * This class provides custom block for actor-movie list.
   * 
   * @Block(
   * id = "actor_details", 
   * admin_label = @Translation("Actor Details"),
   * category = @Translation("actor_movie"),
   * )
   */

  class ActorDetails extends BlockBase {
    public function actor_movie($node) {
      // $nodeId = $node->id();
      $actorDetails = Node::load($node);
      $actorName = $actorDetails->title->value;
      $bundle = 'Movie';
      $query = \Drupal::entityQuery('node')
               ->condition('status', 1)
               ->condition('type', $bundle)
               ->condition('field_starring.entity:paragraph.field_actor_name.value', $actorName);
      $movieId = $query->execute();
      // kint($movieId);
      $movieId = entity_load_multiple('node', $movieId);
      foreach ($movieId as $id) {
        $mid = $id->id();
        $movieName = $id->title->value;
        $movieDescription = $id->get('field_movie_description')->value;
        $movieImageId = $id->get('field_poster')->target_id;
        $movieImageEntityLoad = \Drupal\file\Entity\File::load($movieImageId);
        $movieImage = $movieImageEntityLoad->url();
        $movieReleaseDate = $id->get('field_release_date')->value;
        $movieStarId = $id->field_starring->getValue();
        $movieActorDetail = array();
        // $actorImageId = $id->get('field_image')->target_id;
        // $actorImageEntityLoad = \Drupal\file\Entity\File::load($actorImageId);
        // $actorImage = $actorImageEntityLoad->url();
        $flag = 0;
        foreach ($movieStarId as $value) {
          $paragraph = Paragraph::load($value['target_id']);
          $coStar = $paragraph->field_actor_name->value;
          
          if ($coStar != $actorName) {
            $bundle = 'Actor';
            $query = \Drupal::entityQuery('node')
                   ->condition('status', 1)
                   ->condition('type', $bundle)
                   ->condition('title', $coStar); 
            $coActorId = $query->execute();
            $coActorIds = key($coActorId);
            $movieActorDetail[$flag]['Name'] = $coStar;
            $movieActorDetail[$flag]['Id'] = $coActorIds;
            $flag++;
            // $actor_role = $paragraph->field_role->value;
          }
        }
        $movieList[] = [
          'movieId' => $mid,
          'movieName' => $movieName,
          'movieDescription' => $movieDescription,
          'movieImage' => $movieImage,
          'movieReleaseDate' => $movieReleaseDate,
          'movieStarring' => $movieActorDetail,
        ];

      }

      return array(
        'title' => $actorName,
        'theme' => 'show_actor_movie',
        'movieList' => $movieList,
      );
    }
    public function build() {
      $node = \Drupal::routeMatch()->getParameter('node');
      $node = $node->id();
      $returnVariable = $this->actor_movie($node);
      return array(
        '#theme' => $returnVariable['theme'],
        '#title' => $returnVariable['title'],
        '#movieList' => $returnVariable['movieList'],
        '#cache' => ['max-age' => 0],
      );
    }
  }
