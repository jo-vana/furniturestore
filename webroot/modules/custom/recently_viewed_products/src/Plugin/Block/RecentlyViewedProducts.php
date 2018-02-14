<?php


namespace Drupal\recently_viewed_products\Plugin\Block;

use Drupal\Core\Block\BlockPluginInterface;
use Drupal\file\Entity\File;
use Drupal\Core\Block\BlockBase;
use Drupal\node\Entity\Node;
use Drupal\user\PrivateTempStoreFactory;
use Symfony\Component\DependencyInjection\ContainerInterface;


/**
 * Provides a 'Recently Viewed Products' block.
 *
 * @Block(
 *   id = "recently_viewed_products",
 *   admin_label = @Translation("Recently Viewed Products Block"),
 *   category = @Translation("Blocks")
 * )
 */
class RecentlyViewedProducts extends BlockBase implements BlockPluginInterface{
        /**
         * {@inheritdoc}
         */
    function buildContent()
    {

        $raw_nodes = $_SESSION['recently_viewed'];

        # Sorting by newest
        arsort($raw_nodes);

        # Lasting 3 Hours
        $ctr_timestamp = strtotime('3 hours ago');

        foreach ($raw_nodes as $node_id => $raw_node){
            if($raw_node < $ctr_timestamp){
                unset($raw_nodes[$node_id]);
            }
        }

        $node_ids = array_keys($raw_nodes);

        # Loading Multiple Node Ids
        $nodes = Node::loadMultiple($node_ids);

        $results = [];
        foreach ( $nodes as $node ) {

            $title = $node->getTitle();
            $image = $node->get('field_fur_image')->getValue();
            $file = File::load($image[0]['target_id']);
            $image_url = \Drupal\image\Entity\ImageStyle::load('thumbnail')->buildUrl($file->getFileUri());
            $price = $node->get('field_price')->getValue()[0]['value'];

            $alias_node = \Drupal::service('path.alias_manager')->getAliasByPath('/node/'.$node->id());
            $results[] = [
                'alias' => $alias_node,
                'title' => $title,
                'price' => $price,
                'image_url' => $image_url,
            ];
        }

        return $results;
    }

    /**
     * {@inheritdoc}
     */
    public function build () {

        return array(
            '#theme'    => 'recently_viewed_products',
            '#content'  => $this->buildContent(),
            '#cache'    => [
                'max-age' => 0,
            ],
        );
    }


}

