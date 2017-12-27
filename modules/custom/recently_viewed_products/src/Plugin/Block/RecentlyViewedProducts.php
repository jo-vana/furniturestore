<?php


namespace Drupal\recently_viewed_products\Plugin\Block;

use Drupal\Core\Block\BlockPluginInterface;
use Drupal\file\Entity\File;
use Drupal\Core\Block\BlockBase;
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

        $query = \Drupal::database()->select('node_field_data', 'n');
        $query->condition('n.type', 'furniture', '=');

        $query->innerJoin('node__field_furniture_image', 'fi', 'fi.entity_id = n.nid');

        $query->innerJoin('node__field_price', 'fp', 'fp.entity_id = n.nid' );

        $query->addField('n', 'nid');
        $query->addField('n', 'title');
        $query->addField('fi', 'field_furniture_image_target_id', 'image');
        $query->addField('fp', 'field_price_value');

        $node = [];

        $_SESSION['recently_viewed'][$node] = $node;

        $nodes_viewed = node_load_multiple($_SESSION['recently_viewed'][$node]);

//        $tempstore = \Drupal::service('user.private_tempstore')->get('recently_viewed_products');
//        $tempstore->get('data', $data);


        $nodes_viewed = $query->execute()->fetchAll();

        foreach ( $nodes_viewed as $result ) {
            $file = File::load($result->image);
            $url = \Drupal\image\Entity\ImageStyle::load('thumbnail')->buildUrl($file->getFileUri());
            $alias = \Drupal::service('path.alias_manager')->getAliasByPath('/node/'.$result->nid);

            $node[] = [
                'alias' => $alias,
                'title' => $result->title,
                'field_price_value' => $result->field_price_value,
                'image' => $url,
            ];
        }

        return $node;
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

