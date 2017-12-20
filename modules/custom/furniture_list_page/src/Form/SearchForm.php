<?php

namespace Drupal\furniture_list_page\Form;

use Drupal\Core\Database\Database;
use Drupal\file\Entity\File;
use Drupal\Core\Entity\Query;
use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;

/**
 * Class SearchForm.
 */
class SearchForm extends FormBase {


	/**
	 * {@inheritdoc}
	 */
	public function getFormId () {
		return 'search_form';
	}

	/**
	 * {@inheritdoc}
	 */
	public function buildForm ( array $form, FormStateInterface $form_state ) {

		$form = [];

		$form[ '#method' ] = 'GET';

		$form[ 'filters' ] = [
			'#type'        => 'fieldset',
			'#title'       => t( 'filters' ),
			'#collapsible' => true,
			'#attributes'  => array( 'class' => array( 'inline' ) ),
		];

		$options = [
			0 => 'Default sorting',
			1 => 'Sort by popularity',
			2 => 'Sort by average rating',
			3 => 'Sort by newness',
			4 => 'Sort by price low to high',
			5 => 'Sort by price high to low',
		];

		$form[ 'filters' ][ 'sort' ] = [
			'#type'          => 'select',
			'#options'       => $options,
			'#default_value' => isset( $_GET[ 'sort' ] ) ? $_GET[ 'sort' ] : '',
		];

		$form[ 'filters' ][ 'actions' ][ '#type' ] = 'actions';
		$form[ 'filters' ][ 'actions' ][ 'submit' ] = [
			'#type'  => 'submit',
			'#value' => $this->t( 'Submit' ),
		];

		$data = $this->buildContent();

		$form[ 'content' ][ 'data' ] = $data;

		$form[ '#theme' ] = 'furniture_list_page';

		$form[ 'pager' ] = array(
			'#type' => 'pager',
		);

		return $form;
	}

	private function buildContent () {

		$query = \Drupal::database()->select( 'node_field_data', 'n' );
		$query->condition( 'n.type', 'furniture', '=' );

		$query->innerJoin('node__field_price', 'fp', 'fp.entity_id = n.nid' );

		$query->innerJoin('node__field_fur_image', 'fi', 'fi.entity_id = n.nid');

		$query->condition('fi.delta', 0, '=');

		$query->innerJoin('node__field_categories', 'fc', 'fc.entity_id = n.nid' );

		$query->innerJoin('taxonomy_term_field_data', 't', 'fc.field_categories_target_id = t.tid' );

		$query->innerJoin('node_counter', 'nc', 'nc.nid = n.nid');

		if ( !empty( $_GET[ 'title' ] ) ) {
			$query->condition( 'title', $_GET[ 'title' ], 'LIKE' );
		}

		$query->addField('n', 'nid');
		$query->addField('n', 'status');
		$query->addField('n', 'title');
		$query->addField('t', 'tid');
		$query->addField('t', 'name', 'taxonomy_name');
		$query->addField('fi', 'field_fur_image_target_id', 'image');
		$query->addField('fp', 'field_price_value', 'price');

		$sort = 0;
		if ( isset( $_GET[ 'sort' ] ) ) {
			$sort = $_GET[ 'sort' ];
		}

		if ( $sort == 0 ) {
			$query->orderBy( 'nid', 'ASC');
		} else if ( $sort == 1 ) {
			$query->orderBy( 'totalcount', 'DESC' );
		} else if ( $sort == 2 ) {
			$query->innerJoin('node__field_reviews', 'fr', 'fr.entity_id = n.nid');
			$query->innerJoin('comment_entity_statistics', 'ces', 'fr.entity_id = ces.entity_id');
			$query->innerJoin('comment_field_data', 'cfd', 'ces.entity_id = cfd.entity_id');
			$query->innerJoin('comment__field_your_rating', 'cff', 'cfd.cid = cff.entity_id');
			$query->orderBy( 'field_your_rating_rating', 'DESC' );
		} else if ( $sort == 3 ) {
			$query->orderBy( 'nid', 'DESC' );
		} else if ( $sort == 4 ) {
			$query->orderBy( 'price', 'ASC' );
		} else if ( $sort == 5) {
			$query->orderBy( 'price', 'DESC' );
		}

		$data = [];

		$results = $query->execute()->fetchAll(\PDO::FETCH_GROUP);

		foreach($results as $key => $result ) {
			$entry = [];
			foreach( $result as $node ) {
				$file = File::load($node->image);
				$url = \Drupal\image\Entity\ImageStyle::load('furniture_teaser_img')->buildUrl($file->getFileUri());
				$alias = \Drupal::service('path.alias_manager')->getAliasByPath('/node/' . $key);
				$alias2 = \Drupal::service('path.alias_manager')->getAliasByPath('/' . $node->taxonomy_name);

				$alias_tax = str_replace(' ', '-', $alias2);

				$entry['nid'] = $alias;
				$entry['tid'] = $alias_tax;
				$entry['title'] = $node->title;
				$entry['price'] = $node->price;
				$entry['taxonomy_name'][] =[
					'name'  => strip_tags($node->taxonomy_name),
					'url'   => $alias_tax
				];
				$entry['image'] = $url;
 			}
			$data[] = $entry;
		}

		$data['counters'] = count($data);

		return $data;
	}

	/**
	 * {@inheritdoc}
	 */
	public function validateForm ( array &$form, FormStateInterface $form_state ) {
		parent::validateForm( $form, $form_state );
	}

	/**
	 * {@inheritdoc}
	 */
	public function submitForm ( array &$form, FormStateInterface $form_state ) {
		// Display result.
		foreach ( $form_state->getValues() as $key => $value ) {
			drupal_set_message( $key . ': ' . $value );
		}

	}
}
