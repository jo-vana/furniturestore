<?php


namespace Drupal\top_rated_products\Plugin\Block;

use Drupal\Core\Block\BlockPluginInterface;
use Drupal\file\Entity\File;
use Drupal\Core\Block\BlockBase;


/**
 * Provides a 'Top Rated Products' block.
 *
 * @Block(
 *   id = "top_rated_products",
 *   admin_label = @Translation("Top Rated Products Block"),
 *   category = @Translation("Blocks")
 * )
 */
class TopRatedProducts extends BlockBase implements BlockPluginInterface{
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

        $query->orderBy('field_price_value', 'DESC');
        $query->range(0, 3);

        $data = [];

        $results = $query->execute()->fetchAll();

        foreach ( $results as $result ) {
            $file = File::load($result->image);
            $url = \Drupal\image\Entity\ImageStyle::load('thumbnail')->buildUrl($file->getFileUri());
            $alias = \Drupal::service('path.alias_manager')->getAliasByPath('/node/'.$result->nid);
            $data[] = [
                'alias' => $alias,
                'title' => $result->title,
                'image' => $url,
                'field_price_value' => $result->field_price_value
            ];
        }

        return $data;
    }

    /**
     * {@inheritdoc}
     */
    public function build () {

        return array(
            '#theme'    => 'top_rated_products',
            '#content'  => $this->buildContent(),
            '#cache'    => [
                'max-age' => 0,
            ],
        );
    }


}