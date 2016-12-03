<?php

use Phinx\Seed\AbstractSeed;

class UserSeeder extends AbstractSeed {
	/**
	 * Run Method.
	 *
	 * Write your database seeder using this method.
	 *
	 * More information on writing seeders is available here:
	 * http://docs.phinx.org/en/latest/seeding.html
	 */
	public function run() {

		$data = array(
			array(
				'username'      => 'admin',
				'password'      => password_hash( 'password', PASSWORD_DEFAULT ),
				'email'         => 'no-reply@rensfeldt.com',
				'created'       => date( 'Y-m-d H:i:s' ),
			)
		);

		$users = $this->table( 'api_users' );
		$users->insert( $data )
			->save();

	}
}
