<?php
/**
 * Created by Jonas Rensfeldt.
 * Date: 27/01/17
 */

namespace App\Entities;


/**
 * Class Entity
 * @package App\Entities
 */
class Entity {
	/**
	 * @param $data
	 *
	 * @return array
	 */
	public function objectToArray( $data ) {
		if ( is_object( $data ) ) {
			$data = get_object_vars( $data );
		}

		if ( is_array( $data ) ) {
			return array_map( array( $this, 'objectToArray' ), $data );
		}

		return $data;
	}
}