<?php
/**
 * Created by Jonas Rensfeldt.
 * Date: 09/02/17
 */

namespace App\Language;


/**
 * Class Language
 * @package App\Language
 */
class Language {
	/**
	 * @return string
	 */
	public function get() {
		return $_SESSION['lang'];
	}

	/**
	 * @param string $code
	 */
	public function set( $code ) {
		$_SESSION['lang'] = $code;
	}
}