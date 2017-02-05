<?php
/**
 * Created by Jonas Rensfeldt.
 * Date: 05/02/17
 */

namespace App\Models;


class UnitModel extends Model {
	/**
	 * @var string
	 */
	const TABLE = 'units';

	/**
	 * @return array
	 */
	public function getAll() {
		return $this->db->table( self::TABLE )->get();
	}
}