<?php


namespace Drupal\our_collections\Plugin\Block;

use Drupal\Core\Block\BlockPluginInterface;
use Drupal\file\Entity\File;
use Drupal\Core\Block\BlockBase;


/**
 * Provides a 'Our Collections' block.
 *
 * @Block(
 *   id = "our_collections",
 *   admin_label = @Translation("Our Collections Block"),
 *   category = @Translation("Blocks")
 * )
 */
class OurCollections extends BlockBase implements BlockPluginInterface{
        /**
         * {@inheritdoc}
         */
    function buildContent()
    {
        $query = \Drupal::database()->select('node_field_data', 'n');
        $query->condition('n.type', 'furniture', '=');

        $query->innerJoin('node__field_furniture_image', 'fi', 'fi.entity_id = n.nid');

        $query->innerJoin('node__field_categories', 'fc', 'fc.entity_id = n.nid' );

        $query->innerJoin('taxonomy_term_field_data', 't', 'fc.field_categories_target_id = t.tid' );

        $query->innerJoin('node__field_price', 'fp', 'fp.entity_id = n.nid' );

        $query->addField('n', 'nid');
        $query->addField('n', 'title');
        $query->addField('fi', 'field_furniture_image_target_id', 'image');
        $query->addField('t', 'name');
        $query->addField('fp', 'field_price_value');

        $query->range(0, 6);

        $data = [];

        $results = $query->execute()->fetchAll();

        foreach ( $results as $result ) {
            $file = File::load($result->image);
            $url = \Drupal\image\Entity\ImageStyle::load('furniture_default_img')->buildUrl($file->getFileUri());
            $alias = \Drupal::service('path.alias_manager')->getAliasByPath('/node/'.$result->nid);
            $data[] = [
                'alias' => $alias,
                'title' => $result->title,
                'name' => $result->name,
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
            '#theme'    => 'our_collections',
            '#content'  => $this->buildContent(),
            '#cache'    => [
                'max-age' => 0,
            ],
        );
    }


}