<?php
/**
 * Created by Jonas Rensfeldt.
 * Date: 22/01/17
 */

namespace App\Auth;


use Interop\Container\ContainerInterface;
use App\Models\UserModel;

class Auth {
	/**
	 * @var ContainerInterface
	 */
	protected $container;

	/**
	 * @param ContainerInterface $container
	 */
	function __construct( ContainerInterface $container ) {
		$this->container = $container;
		$this->db        = $this->container->get( 'db' );
	}

	/**
	 * @return null|\stdClass
	 */
	public function currentUser() {
		if ( !isset( $_SESSION['user'] ) ) {
			return null;
		}
		/**
		 * @var UserModel $user
		 */
		$user = $this->container->get( 'UserModel' );

		return $user->get( $_SESSION['user'] );
	}

	/**
	 * @return bool
	 */
	public function check() {
		return isset( $_SESSION['user'] );
	}

	/**
	 * @param $username
	 * @param $password
	 *
	 * @return bool
	 */
	public function attempt( $username, $password ) {
		/**
		 * @var UserModel $userModel
		 */
		$userModel = $this->container->get( 'UserModel' );
		$user      = $userModel->get( $username, 'username' );

		if ( !$user ) {
			return false;
		}

		if ( password_verify( $password, $user->password ) ) {
			$_SESSION['user'] = $user->id;

			return true;
		}

		return false;

	}

	/**
	 * Logout the user
	 */
	public function logout() {
		unset( $_SESSION['user'] );
	}
}