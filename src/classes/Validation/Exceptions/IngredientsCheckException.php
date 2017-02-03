<?php
/**
 * Created by Jonas Rensfeldt.
 * Date: 02/02/17
 */

namespace App\Validation\Exceptions;

use Respect\Validation\Exceptions\ValidationException;

class IngredientsCheckException extends ValidationException {
	public static $defaultTemplates = [
		self::MODE_DEFAULT => [
			self::STANDARD => 'Need at least one ingredient',
		],
	];
}