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

        $query->innerJoin('node__field_fur_image', 'fi', 'fi.entity_id = n.nid');

        $query->condition('fi.delta', 0, '=');

        $query->innerJoin('node__field_categories', 'fc', 'fc.entity_id = n.nid' );

        $query->innerJoin('taxonomy_term_field_data', 't', 'fc.field_categories_target_id = t.tid' );

        $query->innerJoin('node__field_price', 'fp', 'fp.entity_id = n.nid' );

        $query->addField('n', 'nid');
        $query->addField('t', 'tid');
        $query->addField('n', 'title');
        $query->addField('fi', 'field_fur_image_target_id', 'image');
        $query->addField('t', 'name', 'taxonomy_name');
        $query->addField('fp', 'field_price_value');

        $data = [];

        $results = $query->execute()->fetchAll(\PDO::FETCH_GROUP);

        foreach($results as $key => $result ) {
            $entry = [];
            foreach( $result as $node ) {
                $file = File::load($node->image);
                $url = \Drupal\image\Entity\ImageStyle::load('furniture_default_img')->buildUrl($file->getFileUri());
                $alias = \Drupal::service('path.alias_manager')->getAliasByPath('/node/' . $key);
                $alias2 = \Drupal::service('path.alias_manager')->getAliasByPath('/' . $node->taxonomy_name);

                $alias_tax = str_replace(' ', '-', $alias2);

                $entry['nid'] = $alias;
                $entry['tid'] = $alias_tax;
                $entry['title'] = $node->title;
                $entry['field_price_value'] = $node->field_price_value;
                $entry['taxonomy_name'][] =[
                    'name'  => strip_tags($node->taxonomy_name),
                    'url'   => $alias_tax
                ];
                $entry['image'] = $url;
            }
            $data[] = $entry;

            if (count($data) > 6){
                if( !empty($data[6])) {
                    unset($data[6]);
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
            '#theme'    => 'our_collections',
            '#content'  => $this->buildContent(),
            '#cache'    => [
                'max-age' => 0,
            ],
        );
    }


}