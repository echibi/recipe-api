<?php
/**
 * Created by jonas.
 * Project: recipe-api
 * Date: 2016-11-28
 */

namespace App\Helpers;


class Utilities {

	/**
	 * Get an item from an array using "dot" notation.
	 *
	 * @param  array $array
	 * @param  string $key
	 * @param  mixed $default
	 *
	 * @return mixed
	 */
	public static function array_get( $array, $key, $default = null ) {
		if ( is_null( $key ) ) {
			return $array;
		}
		if ( isset( $array[ $key ] ) ) {
			return $array[ $key ];
		}
		foreach ( explode( '.', $key ) as $segment ) {
			if ( ! is_array( $array ) || ! array_key_exists( $segment, $array ) ) {
				return value( $default );
			}
			$array = $array[ $segment ];
		}

		return $array;
	}
}