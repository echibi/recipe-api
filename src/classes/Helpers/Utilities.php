<?php
/**
 * Created by jonas.
 * Project: recipe-api
 * Date: 2016-11-28
 */

namespace App\Helpers;

use \Closure;


class Utilities {

	/**
	 * Get an item from an array using "dot" notation.
	 *
	 * @param  array  $array
	 * @param  string $key
	 * @param  mixed  $default
	 *
	 * @return mixed
	 */
	public static function array_get( $array, $key, $default = null ) {
		if ( is_null( $key ) ) {
			return $array;
		}
		if ( isset( $array[$key] ) ) {
			return $array[$key];
		}
		foreach ( explode( '.', $key ) as $segment ) {
			if ( !is_array( $array ) || !array_key_exists( $segment, $array ) ) {
				return self::value( $default );
			}
			$array = $array[$segment];
		}

		return $array;
	}

	/**
	 * Sanitizes a title, replacing whitespace and a few other characters with dashes.
	 *
	 * Limits the output to alphanumeric characters, underscore (_) and dash (-).
	 * Whitespace becomes a dash.
	 *
	 * Borrowed from Wordpress
	 *
	 * @param string $title The title to be sanitized.
	 *
	 * @return string The sanitized title.
	 */
	public static function create_slug( $title ) {
		$title = strip_tags( $title );
		// Preserve escaped octets.
		$title = preg_replace( '|%([a-fA-F0-9][a-fA-F0-9])|', '---$1---', $title );
		// Remove percent signs that are not part of an octet.
		$title = str_replace( '%', '', $title );
		// Restore octets.
		$title = preg_replace( '|---([a-fA-F0-9][a-fA-F0-9])---|', '%$1', $title );

		if ( function_exists( 'mb_strtolower' ) ) {
			$title = mb_strtolower( $title, 'UTF-8' );
		}

		$title = strtolower( $title );

		$title = preg_replace( '/&.+?;/', '', $title ); // kill entities
		$title = str_replace( '.', '-', $title );

		$title = preg_replace( array( '/å/', '/ä/', '/ö/' ), array( 'a', 'a', 'o' ), $title );
		$title = preg_replace( '/[^%a-z0-9 _-]/', '', $title );

		$title = preg_replace( '/\s+/', '-', $title );
		$title = preg_replace( '|-+|', '-', $title );
		$title = trim( $title, '-' );

		return $title;
	}

	/**
	 * Return the default value of the given value.
	 *
	 * @param  mixed $value
	 *
	 * @return mixed
	 */
	public static function value( $value ) {
		return $value instanceof Closure ? $value() : $value;
	}
}