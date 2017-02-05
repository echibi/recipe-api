<?php
/**
 * Created by Jonas Rensfeldt.
 * Date: 05/02/17
 */

namespace App\Models;


class CategoryModel extends Model {
	/**
	 * @return mixed
	 */
	public function getAll() {
		return $this->db->table( 'categories' )->get();
	}

}