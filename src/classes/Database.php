<?php
/**
 * Created by Jonas Rensfeldt.
 * Date: 24/12/16
 */

namespace App;


class Database {
	/**
	 * @param string     $req       : the query on which link the values
	 * @param array      $array     : associative array containing the values ​​to bind
	 * @param array|bool $typeArray : associative array with the desired value for its corresponding key in $array
	 */
	function bindArrayValue( $req, $array, $typeArray = false ) {
		if ( is_object( $req ) && ( $req instanceof \PDOStatement ) ) {
			foreach ( $array as $key => $value ) {
				if ( $typeArray ) {
					$req->bindValue( ":$key", $value, $typeArray[$key] );
				} else {
					if ( is_int( $value ) ) {
						$param = \PDO::PARAM_INT;
					} elseif ( is_bool( $value ) ) {
						$param = \PDO::PARAM_BOOL;
					} elseif ( is_null( $value ) ) {
						$param = \PDO::PARAM_NULL;
					} elseif ( is_string( $value ) ) {
						$param = \PDO::PARAM_STR;
					} else {
						$param = FALSE;
					}

					if ( $param ) {
						$req->bindValue( ":$key", $value, $param );
					}
				}
			}
		}
	}

	/**
	 * @param array      $array     : associative array with keys we want to prepare
	 * @param string     $operator  : AND or OR
	 * @param array|bool $ignoreKey : array with keys to ignore.
	 *
	 * @return bool
	 */
	function whereArrayPrepare( $array, $operator = 'AND', $ignoreKey = false ) {

		// Remove keys we want to ignore.
		if ( is_array( $ignoreKey ) ) {
			foreach ( $ignoreKey as $key ) {
				if ( isset( $array[$key] ) ) {
					unset( $array[$key] );
				}
			}
		}

		if ( empty( $array ) ) {
			return false;
		}

		$placeholder = '';
		foreach ( $array as $key => $value ) {
			if ( '' !== $placeholder ) {
				$placeholder .= ' ' . $operator . ' ';
			}
			$placeholder .= $key . '= :' . $key;
		}

		return $placeholder;
	}
}