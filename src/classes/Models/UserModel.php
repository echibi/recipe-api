<?php
/**
 * Created by Jonas Rensfeldt.
 * Date: 22/01/17
 */

namespace App\Models;


class UserModel extends Model {
	/**
	 * @const string Database Table
	 */
	const TABLE = 'api_users';

	/**
	 * @param $id
	 *
	 * @return null|\stdClass
	 */
	public function get( $id, $field = 'id' ) {
		return $this->db->table( self::TABLE )->find( $id, $field );
	}

	public function create( $data ) {

	}
}