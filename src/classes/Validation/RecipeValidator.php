<?php
/**
 * Created by jonas.
 * Project: recipe-api
 * Date: 2016-11-28
 */

namespace App\Validation;

use Respect\Validation\Validator as v;
use Respect\Validation\Exceptions\NestedValidationException;
use App\Helpers\Utilities as Util;
use Slim\Http\Request;

class RecipeValidator extends Validator {
	/**
	 * List of constraints
	 *
	 * @var array
	 */
	protected $rules = [ ];

	/**
	 * Init rules
	 */
	public function __construct() {
		$this->initRules();
	}

	/**
	 * Set the fields constraints
	 *
	 * @return void
	 */
	public function initRules() {

		$this->rules['title']       = v::notEmpty();
		$this->rules['ingredients'] = v::IngredientsCheck();
		$this->rules['image1']      = v::optional( v::image() );
		$this->rules['category_id'] = v::intVal();
		// $this->rules['description'] = '';
		// $this->rules['videoUrl']    = V::optional( V::videoUrl() )->setName( 'video' );

	}

	/**
	 * @param Request $request
	 * @param array   $rules
	 *
	 * @return $this
	 */
	public function validate( Request $request, array $rules = array() ) {
		if ( !empty( $rules ) ) {
			$this->rules = $rules;
		}

		// Need to validate files separately
		$files = $request->getUploadedFiles();

		if ( !empty( $files ) ) {
			foreach ( $files as $key => $fileObject ) {
				if ( isset( $this->rules[$key] ) ) {
					try {
						$this->rules[$key]->setName( ucfirst( $key ) )->assert( $fileObject->file );
					} catch ( NestedValidationException $e ) {
						$this->errors[$key] = $e->getMessages();
					}
				}
			}
		}

		// After we have checked all uploads that recipes will allow;
		// Run our parent function also to set session etc.
		parent::validate( $request, $this->rules );

		return $this;
	}

	/**
	 * @deprecated Old assert
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
}