<?php

use Phinx\Seed\AbstractSeed;

class UnitSeeder extends AbstractSeed {
	/**
	 * Run Method.
	 *
	 * Write your database seeder using this method.
	 *
	 * More information on writing seeders is available here:
	 * http://docs.phinx.org/en/latest/seeding.html
	 */
	public function run() {

		$now  = date( 'Y-m-d H:i:s' );
		$data = array(
			array(
				'name'     => 'st',
				'fullname' => 'Stycken',
				'created'  => $now,
				'updated'  => $now
			),
			array(
				'name'     => 'krm',
				'fullname' => 'Kryddmått',
				'created'  => $now,
				'updated'  => $now
			),
			array(
				'name'     => 'tsk',
				'fullname' => 'Tesked',
				'created'  => $now,
				'updated'  => $now
			),
			array(
				'name'     => 'msk',
				'fullname' => 'Matsked',
				'created'  => $now,
				'updated'  => $now
			),
			array(
				'name'     => 'dl',
				'fullname' => 'Deciliter',
				'created'  => $now,
				'updated'  => $now
			),
			array(
				'name'     => 'l',
				'fullname' => 'Liter',
				'created'  => $now,
				'updated'  => $now
			),
			array(
				'name'     => 'g',
				'fullname' => 'Gram',
				'created'  => $now,
				'updated'  => $now
			)
		);

		$categories = $this->table( 'units' );
		$categories->insert( $data )
			->save();
	}
}
