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
	const table = 'units';

	/**
	 * @return array
	 */
	public function getAll() {
		return $this->db->table( self::table )->get();
	}
}