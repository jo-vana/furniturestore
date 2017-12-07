<?php


namespace Drupal\blog_list_page\Plugin\Block;

use Drupal\Core\Block\BlockPluginInterface;
use Drupal\file\Entity\File;
use Drupal\Core\Block\BlockBase;


/**
 * Provides a 'Blog List Page' block.
 *
 * @Block(
 *   id = "blog_list_page",
 *   admin_label = @Translation("Blog List Block"),
 *   category = @Translation("Blocks")
 * )
 */
class BlogListPage extends BlockBase implements BlockPluginInterface{
        /**
         * {@inheritdoc}
         */
    function buildContent()
    {
        $query = \Drupal::database()->select('node_field_data', 'n');
        $query->condition('n.type', 'blog', '=');

        $query->innerJoin('node__field_blog_image', 'bi', 'bi.entity_id = n.nid');

        $query->innerJoin('node__field_date_blog', 'fdb', 'fdb.entity_id = n.nid');

        $query->innerJoin('node__body', 'b', 'b.entity_id = n.nid');

        $query->innerJoin('node__field_categories', 'fc', 'fc.entity_id = n.nid');

        $query->innerJoin('taxonomy_term_field_data', 't', 'fc.field_categories_target_id = t.tid');

        $query->innerJoin('node__field_blog_author', 'ba', 'ba.entity_id = n.nid');

        $query->innerJoin('users_field_data', 'u', 'ba.field_blog_author_target_id = u.uid');

        $query->addField('n', 'nid');
        $query->addField('t', 'tid');
        $query->addField('u', 'uid');
        $query->addField('n', 'title');
        $query->addField('b', 'body_value');
        $query->addField('bi', 'field_blog_image_target_id', 'image');
        $query->addField('t', 'name', 'taxonomy_name');
        $query->addField('fdb', 'field_date_blog_value');
        $query->addField('u', 'name', 'username');

        $data = [];

        $results = $query->execute()->fetchAll();

        // \PDO::FETCH_GROUP

        foreach ( $results as $result ) {
            $file = File::load($result->image);
            $url = \Drupal\image\Entity\ImageStyle::load('blog_default_img')->buildUrl($file->getFileUri());
            $alias = \Drupal::service('path.alias_manager')->getAliasByPath('/node/'.$result->nid);
            $alias2 = \Drupal::service('path.alias_manager')->getAliasByPath('/'.$result->taxonomy_name);
            $alias3 = \Drupal::service('path.alias_manager')->getAliasByPath('/user/'.$result->uid);

            $alias_tax = str_replace(' ', '-', $alias2);

            $data[] = [
                'alias' => $alias,
                'alias2' => $alias_tax,
                'alias3' => $alias3,
                'title' => $result->title,
                'body' =>  substr(strip_tags(str_replace(array("\r", "\n"), '', $result->body_value)), 0, 140),
                'taxonomy_name' =>  [
                    'name'  => strip_tags($result->taxonomy_name),
                    'url'   => $alias2
                ],
                'image' => $url,
                'field_date_blog_value' => date('F d, Y', $result->field_date_blog_value),
                'username' => $result->username,
            ];

        }

        return $data;
    }

    /**
     * {@inheritdoc}
     */
    public function build () {

        return array(
            '#theme'    => 'blog_list_page',
            '#content'  => $this->buildContent(),
            '#cache'    => [
                'max-age' => 0,
            ],
        );
    }


}