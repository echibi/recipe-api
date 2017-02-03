<?php

use Phinx\Seed\AbstractSeed;

class CategorySeeder extends AbstractSeed {
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
				'name'    => 'Huvudrätt',
				'slug'    => 'huvudratt',
				'created' => $now,
				'updated' => $now
			),
			array(
				'name'    => 'Bakverk',
				'slug'    => 'bakverk',
				'created' => $now,
				'updated' => $now
			),
			array(
				'name'    => 'Sylt',
				'slug'    => 'sylt',
				'created' => $now,
				'updated' => $now
			),
			array(
				'name'    => 'Sås',
				'slug'    => 'sas',
				'created' => $now,
				'updated' => $now
			),
		);

		$categories = $this->table( 'categories' );
		$categories->insert( $data )
			->save();

	}
}
