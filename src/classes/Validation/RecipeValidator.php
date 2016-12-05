<?php
/**
 * Created by jonas.
 * Project: recipe-api
 * Date: 2016-11-28
 */

namespace App\Validation;

use Respect\Validation\Validator as V;
use Respect\Validation\Exceptions\NestedValidationException;
use App\Helpers\Utilities as Util;

class RecipeValidator {
	/**
	 * List of constraints
	 *
	 * @var array
	 */
	protected $rules = [ ];

	/**
	 * List of customized messages
	 *
	 * @var array
	 */
	protected $messages = [ ];

	/**
	 * List of returned errors in case of a failing assertion
	 *
	 * @var array
	 */
	protected $errors = [ ];

	/**
	 * Inits rules and messages
	 */
	public function __construct() {
		$this->initRules();
		$this->initMessages();
	}

	/**
	 * Set the fields constraints
	 *
	 * @return void
	 */
	public function initRules() {

		$this->rules['title']       = V::stringType()->notEmpty()->setName( 'title' );
		$this->rules['description'] = V::optional( V::stringType() )->setName( 'description' );
		$this->rules['ingredients'] = V::optional( V::arrayType() )->setName( 'ingredients' );
		$this->rules['image1']      = V::optional( V::image() )->setName( 'image1' );
		$this->rules['videoUrl']    = V::optional( V::videoUrl() )->setName( 'video' );

	}

	/**
	 * Set user custom error messages
	 *
	 * @return void
	 */
	public function initMessages() {
		$this->messages = [
			'alpha'                 => '{{name}} must only contain alphabetic characters.',
			'alnum'                 => '{{name}} must only contain alpha numeric characters and dashes.',
			'numeric'               => '{{name}} must only contain numeric characters.',
			'noWhitespace'          => '{{name}} must not contain white spaces.',
			'length'                => '{{name}} must length between {{minValue}} and {{maxValue}}.',
			'email'                 => 'Please make sure you typed a correct email address.',
			'creditCard'            => 'Please make sure you typed a valid card number.',
			'date'                  => 'Make sure you typed a valid date for the {{name}} ({{format}}).',
			'password_confirmation' => 'Password confirmation doesn\'t match.'
		];
	}

	/**
	 * Assert validation rules.
	 *
	 * @param array $inputs
	 *   The inputs to validate.
	 *
	 * @return boolean
	 *   True on success; otherwise, false.
	 */
	public function assert( array $inputs ) {

		foreach ( $this->rules as $rule => $validator ) {
			try {
				$validator->assert( Util::array_get( $inputs, $rule ) );
			} catch ( NestedValidationException $ex ) {
				$this->errors = $ex->getMessages();

				return false;
			}

			// $ingredientRule = V::notEmpty()->setName( 'ingredient' );
			if ( 'ingredients' === $rule ) {
				$ingredients = Util::array_get( $inputs, $rule );
				foreach ( $ingredients as $ingredient ) {
					if ( empty( $ingredient['name'] ) || empty( $ingredient['value'] ) || empty( $ingredient['unit'] ) ) {
						$this->errors = 'Improper format for ingredient. Name, Value and Unit can\'t be empty';

						return false;
					}
				}
			}
		}

		return true;
	}

	/**
	 * Returns all errors.
	 *
	 * @return array
	 */
	public function errors() {
		return $this->errors;
	}
}