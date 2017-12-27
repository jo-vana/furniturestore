<?php

namespace Drupal\range_filter\Form;

use Drupal\Core\Database\Database;
use Drupal\file\Entity\File;
use Drupal\Core\Entity\Query;
use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;

/**
 * Class RangeForm.
 */
class RangeForm extends FormBase {


	/**
	 * {@inheritdoc}
	 */
	public function getFormId () {
		return 'range_form';
	}

	/**
	 * {@inheritdoc}
	 */
	public function buildForm ( array $form, FormStateInterface $form_state ) {

		$form = [];

		$form[ '#method' ] = 'GET';

		$form ['filters']['min_price'] = array(
			'#type' => 'number',
			'#placeholder' => t('Min'),
			'#min' => 0,
			'#max' => 1000000,
			'#default_value' => isset( $_GET[ 'min_price' ] ) ? $_GET[ 'min_price' ] : '',
		);

		$form ['filters']['max_price'] = array(
			'#type' => 'number',
			'#placeholder' => t('Max'),
			'#min' => 0,
			'#max' => 1000000,
			'#default_value' => isset( $_GET[ 'max_price' ] ) ? $_GET[ 'max_price' ] : '',
		);

		return $form;
	}

	private function buildContent () {

		$query = \Drupal::database()->select( 'node_field_data', 'n' );
		$query->innerJoin('node__field_price', 'fp', 'n.nid = fp.entity_id');

		$min_price = $_GET[ 'min_price' ];

		if ( isset( $min_price ) ) {
			$query->condition( 'fp.field_price_value', $min_price, '>=' );
		}

		$max_price = $_GET['max_price'];
		if (!empty($max_price)) {
			if (isset($max_price)) {
				$query->condition('fp.field_price_value', $max_price, '<=');
			}
		}

	}

	public function build () {

		return array(
			'#theme'    => 'range_filter',
			'#content'  => $this->buildContent(),
			'#cache'    => [
				'max-age' => 0,
			],
		);
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
