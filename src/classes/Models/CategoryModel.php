<?php
/**
 * Created by Jonas Rensfeldt.
 * Date: 05/02/17
 */

namespace App\Models;


use App\Entities\CategoryEntity;
use App\Helpers\Utilities;

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

	/**
	 * @param $category CategoryEntity
	 *
	 * @return mixed
	 */
	public function create( $category ) {

		$now = date( 'Y-m-d H:i:s' );

		return $this->db->table( self::TABLE )->insert(
			array(
				'name'    => $category->name,
				'slug'    => Utilities::create_slug( $category->name ),
				'created' => $now,
				'updated' => $now,
				// 'image1'      => '',
			)
		);
	}

	/**
	 * @param                $id
	 * @param CategoryEntity $category
	 *
	 * @return bool
	 */
	public function update( $id, CategoryEntity $category ) {

		$item = $this->db->table( self::TABLE )->find( $id );

		if ( $item ) {
			$updateData = array(
				'name'   => $category->name,
				'slug'    => Utilities::create_slug( $category->name ),
				'updated' => date( 'Y-m-d H:i:s' ),
			);

			return $this->db->table( self::TABLE )->where( 'id', $id )->update( $updateData );
		}

		return false;
	}

}