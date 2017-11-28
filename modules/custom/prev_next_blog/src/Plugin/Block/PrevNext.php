<?php


namespace Drupal\prev_next_blog\Plugin\Block;

use Drupal\node\Entity\Node;
use Drupal\Core\Block\BlockBase;


/**
 * Provides a 'Next Previous Blog' block.
 *
 * @Block(
 *   id = "prev_next_blog",
 *   admin_label = @Translation("Next Previous Blog Block"),
 *   category = @Translation("Blocks")
 * )
 */
class PrevNext extends BlockBase {
        /**
         * {@inheritdoc}
         */
    function build()
    {
        $current_node = \Drupal::routeMatch()->getParameter('node')->Id();
        # Next
        $query = \Drupal::database()->select('node', 'n');
        $query->addField('n', 'nid');
        $query->condition('n.nid', $current_node, '>');
        $query->condition('n.type', 'blog', '=');
        $query->range(0, 1);
        $previous = $query->execute()->fetchField();

        # Previous
        $query = \Drupal::database()->select('node', 'n');
        $query->addField('n', 'nid');
        $query->condition('n.nid', $current_node, '<');
        $query->condition('n.type', 'blog', '=');
        $query->orderBy('nid', 'DESC');
        $query->range(0, 1);
        $next = $query->execute()->fetchField();

        # Markup
        $markup = '<div>';
        if( isset($previous) && is_numeric($previous)) {
            $node = Node::load($previous);
            $markup .= '<a class="prev" href="/node/'.$previous.'">Prev <span class="title">' . $node->getTitle() . '</span></a> ';
        }
        if( isset($next)&& is_numeric($next)) {
            $node = Node::load($next);
            $markup .= '<a class="next" href="/node/'.$next.'">Next <span class="title">' . $node->getTitle() . '</span></a>';
        }
        $markup .= '</div>';

        # Cache
        return [
            '#markup' =>$markup,
            '#cache'   => ['max-age' => 0]
        ];

    }

}