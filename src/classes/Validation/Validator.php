<?php
/**
 * Created by Jonas Rensfeldt.
 * Date: 22/01/17
 */

namespace App\Validation;

use Respect\Validation\Validator as Respect;
use Respect\Validation\Exceptions\NestedValidationException;
use Slim\Http\Request;

class Validator {
	/**
	 * @var array
	 */
	protected $errors;

	/**
	 * @param Request $request
	 * @param array   $rules
	 *
	 * @return Validator $this
	 */
	public function validate( Request $request, array $rules ) {
		foreach ( $rules as $field => $rule ) {
			try {

				$rule->setName( ucfirst( $field ) )->assert( $request->getParam( $field ) );
			} catch ( NestedValidationException $e ) {
				$this->errors[$field] = $e->getMessages();
			}
		}
		$_SESSION['form_errors'] = $this->errors;

		return $this;
	}

	/**
	 * @return array
	 */
	public function errors() {
		return $this->errors;
	}

	/**
	 * @return bool
	 */
	public function failed() {
		return !empty( $this->errors );
	}
}