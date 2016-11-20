<?php
/**
 * Created by Jonas Rensfeldt
 * Date: 20/11/16
 * Time: 15:21
 */

namespace App;

use \Interop\Container\ContainerInterface as ContainerInterface;



class RecipeMapper {

	protected $ci;

	//Constructor
	public function __construct( ContainerInterface $ci ) {
		$this->ci = $ci;
		$this->logger = $ci->get('logger');
	}

	public function getList( $request, $response, $args ) {
		//your code
		//to access items in the container... $this->ci->get('');

		$this->logger->info("accessed '/recipes/' route");

		echo 'loo';

	}

	public function method2( $request, $response, $args ) {
		//your code
		//to access items in the container... $this->ci->get('');
	}

	public function method3( $request, $response, $args ) {
		//your code
		//to access items in the container... $this->ci->get('');
	}
}