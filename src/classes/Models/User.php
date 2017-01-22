<?php
/**
 * Created by Jonas Rensfeldt.
 * Date: 22/01/17
 */

namespace App\Models;


class User extends Model {
	/**
	 * @param $id
	 *
	 * @return null|\stdClass
	 */
	public function get( $id ) {
		return $this->db->table( 'api_users' )->find( $id );
	}

	public function create( $data ) {

	}
}