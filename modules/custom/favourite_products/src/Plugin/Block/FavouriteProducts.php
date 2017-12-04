<?php


namespace Drupal\favourite_products\Plugin\Block;

use Drupal\Core\Block\BlockPluginInterface;
use Drupal\file\Entity\File;
use Drupal\Core\Block\BlockBase;


/**
 * Provides a 'Favourite Products' block.
 *
 * @Block(
 *   id = "favourite_products",
 *   admin_label = @Translation("Favourite Products Block"),
 *   category = @Translation("Blocks")
 * )
 */
class FavouriteProducts extends BlockBase implements BlockPluginInterface{
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

        $data = [];

        $results = $query->execute()->fetchAll();

        foreach ( $results as $result ) {
            $file = File::load($result->image);
            $url = \Drupal\image\Entity\ImageStyle::load('furniture_default_img')->buildUrl($file->getFileUri());
            $alias = \Drupal::service('path.alias_manager')->getAliasByPath('/node/'.$result->nid);
            $data[] = [
                'alias' => $alias,
                'title' => $result->title,
                'field_price_value' => $result->field_price_value,
                'image' => $url,
            ];
        }

        return $data;
    }

    /**
     * {@inheritdoc}
     */
    public function build () {

        return array(
            '#theme'    => 'favourite_products',
            '#content'  => $this->buildContent(),
            '#cache'    => [
                'max-age' => 0,
            ],
        );
    }


}