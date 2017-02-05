<?php
/**
 * Created by Jonas Rensfeldt.
 * Date: 02/02/17
 */

namespace App\Validation\Rules;

use Respect\Validation\Validator as v;
use Respect\Validation\Rules\AbstractRule;
use Slim\Http\Request;

class IngredientsCheck extends AbstractRule {

	/**
	 * @param $input
	 *
	 * @return bool
	 */
	public function validate( $input ) {
		if ( !is_array( $input ) || empty( $input ) ) {
			return false;
		}
		foreach ( $input as $ingredient ) {
			if ( '' === trim( $ingredient['name'] ) && '' === trim( $ingredient['value'] ) ) {
				return false;
			}
		}

		return true;
	}
}