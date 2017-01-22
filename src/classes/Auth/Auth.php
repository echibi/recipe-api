<?php
/**
 * Created by Jonas Rensfeldt.
 * Date: 22/01/17
 */

namespace App\Auth;


use Interop\Container\ContainerInterface;
use App\Models\User;

class Auth {
	/**
	 * @var ContainerInterface
	 */
	protected $container;

	function __construct( ContainerInterface $container ) {
		$this->container = $container;
		$this->db        = $this->container->get( 'db' );
	}

	public function current_user() {
		$user = new User( $this->db );

		return $user->get( $_SESSION['id'] );
	}

	public function check() {
		return isset( $_SESSION['user'] );
	}

	public function attempt( $username, $password ) {
		$user = $this->db->table( 'api_users' )->where( 'username', '=', $username )->first();

		if ( !$user ) {
			return false;
		}

		if ( password_verify( $password, $user->password ) ) {
			$_SESSION['user'] = $user->id;

			return true;
		}

	}
}