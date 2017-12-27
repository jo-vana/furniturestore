<?php


namespace Drupal\hero_slider\Plugin\Block;

use Drupal\Core\Block\BlockPluginInterface;
use Drupal\file\Entity\File;
use Drupal\Core\Block\BlockBase;


/**
 * Provides a 'Hero Slider' block.
 *
 * @Block(
 *   id = "hero_slider",
 *   admin_label = @Translation("Hero Slider Block"),
 *   category = @Translation("Blocks")
 * )
 */
class HeroSlider extends BlockBase implements BlockPluginInterface{
        /**
         * {@inheritdoc}
         */
    function buildContent()
    {
        $query = \Drupal::database()->select('node_field_data', 'n');
        $query->condition('n.type', 'hero_slider', '=');

        $query->innerJoin('node__field_hero_image', 'hi', 'hi.entity_id = n.nid');

        $query->innerJoin('node__field_explore_store', 'es', 'es.entity_id = n.nid');

        $query->addField('n', 'nid');
        $query->addField('n', 'title');
        $query->addField('hi', 'field_hero_image_target_id', 'image');
        $query->addField('es', 'field_date_blog_value', 'date');

        $data = [];

        $results = $query->execute()->fetchAll();

        foreach ( $results as $result ) {
            $file = File::load($result->image);
            $url = \Drupal\image\Entity\ImageStyle::load('default')->buildUrl($file->getFileUri());
            $alias = \Drupal::service('path.alias_manager')->getAliasByPath('/node/'.$result->nid);

            $date = date('F d, Y',strtotime($result->date));

            $data[] = [
                'alias' => $alias,
                'title' => $result->title,
                'image' => $url,
                'date' => $date,
            ];
        }

        return $data;
    }

    /**
     * {@inheritdoc}
     */
    public function build () {

        return array(
            '#theme'    => 'hero_slider',
            '#content'  => $this->buildContent(),
            '#cache'    => [
                'max-age' => 0,
            ],
        );
    }


}