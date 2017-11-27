<?php

namespace Drupal\search_page\Form;

use Drupal\Core\Database\Database;
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

		$form[ 'filters' ][ 'title' ] = [
			'#type'          => 'textfield',
			'#title'         => t( 'Record title' ),
			'#size'          => 32,
			'#default_value' => isset( $_GET[ 'title' ] ) ? $_GET[ 'title' ] : '',
		];

		$options = [
			2 => '--',
			0 => 'Unpublished',
			1 => 'Published',
		];

		$form[ 'filters' ][ 'published' ] = [
			'#type'          => 'select',
			'#title'         => t( 'Published' ),
			'#options'       => $options,
			'#default_value' => isset( $_GET[ 'published' ] ) ? $_GET[ 'published' ] : '',
		];

		$options = [
			0 => 'Node ID ASC',
			1 => 'Node ID DESC',
			2 => 'Title A-Z',
			3 => 'Title Z-A',
			4 => 'Published First',
			5 => 'Unpublished First',
		];

		$form[ 'filters' ][ 'sort' ] = [
			'#type'          => 'select',
			'#title'         => t( 'Sort' ),
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

		$form[ '#theme' ] = 'search_page';

		$form[ 'pager' ] = array(
			'#type' => 'pager',
		);

		return $form;
	}

	private function buildContent () {

		$query = \Drupal::database()->select( 'node_field_data', 'n' );
		$query->condition( 'n.type', 'poc', '=' );

		if ( !empty( $_GET[ 'title' ] ) ) {
			$query->condition( 'title', $_GET[ 'title' ], 'LIKE' );
		}
		if ( isset( $_GET[ 'published' ] ) && $_GET[ 'published' ] != 2 ) {
			$query->condition( 'status', intval( $_GET[ 'published' ] ), '=' );
		}

		$query->addField( 'n', 'title' );
		$query->addField( 'n', 'status' );
		$query->addField( 'n', 'nid' );

		$query = $query->extend( 'Drupal\Core\Database\Query\PagerSelectExtender' )->limit( 1 );

		$sort = 0;
		if ( isset( $_GET[ 'sort' ] ) ) {
			$sort = $_GET[ 'sort' ];
		}

		if ( $sort == 0 ) {
			$query->orderBy( 'nid', 'ASC' );
		} else if ( $sort == 1 ) {
			$query->orderBy( 'nid', 'DESC' );
		} else if ( $sort == 2 ) {
			$query->orderBy( 'title', 'ASC' );
		} else if ( $sort == 3 ) {
			$query->orderBy( 'title', 'DESC' );
		} else if ( $sort == 4 ) {
			$query->orderBy( 'status', 'ASC' );
		} else if ( $sort == 5 ) {
			$query->orderBy( 'status', 'DESC' );
		}

		$data = [];

		$results = $query->execute()->fetchAll();

		foreach ( $results as $result ) {
			$data[] = [
				'nid'       => $result->nid,
				'title'     => $result->title,
				'published' => intval( $result->status ),
			];
		}

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
