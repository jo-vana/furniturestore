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

        $query->innerJoin('node__field_fur_image', 'fi', 'fi.entity_id = n.nid');

        $query->condition('fi.delta', 0, '=');

        $query->innerJoin('node__field_price', 'fp', 'fp.entity_id = n.nid' );

        $query->innerJoin('node_counter', 'nc', 'nc.nid = n.nid');

        $query->orderBy( 'totalcount', 'DESC' );

        $query->addField('n', 'nid');
        $query->addField('n', 'title');
        $query->addField('fi', 'field_fur_image_target_id', 'image');
        $query->addField('fp', 'field_price_value');

        $data = [];

        $results = $query->execute()->fetchAll();

        foreach ( $results as $result ) {
            $file = File::load($result->image);
            $url = \Drupal\image\Entity\ImageStyle::load('furniture_fav_321x321')->buildUrl($file->getFileUri());
            $alias_node = \Drupal::service('path.alias_manager')->getAliasByPath('/node/'.$result->nid);
            $data[] = [
                'alias' => $alias_node,
                'title' => $result->title,
                'field_price_value' => $result->field_price_value,
                'image' => $url,
            ];
            if (count($data) > 8){
                if( !empty($data[7])) {
                    unset($data[7]);
                }
                return $data;
            }
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