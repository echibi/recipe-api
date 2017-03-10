<?php
/**
 * Created by Jonas Rensfeldt.
 * Date: 05/02/17
 */

namespace App\Models;


class CategoryModel extends Model {
	const TABLE = 'categories';

	/**
	 * @return mixed
	 */
	public function getList() {
		return $this->db->table( self::TABLE )->get();
	}

	/**
	 * @param        $id
	 * @param string $field
	 *
	 * @return mixed
	 */
	public function get( $id, $field = 'id' ) {
		return $this->db->table( self::TABLE )->find( $id, $field );
	}

}