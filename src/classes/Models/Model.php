<?php
/**
 * Created by Jonas Rensfeldt.
 * Date: 22/01/17
 */

namespace App\Models;


use Pixie\QueryBuilder\QueryBuilderHandler;

class Model {
	/**
	 * @var QueryBuilderHandler
	 */
	protected $db;

	/**
	 * @param QueryBuilderHandler $db
	 */
	function __construct( QueryBuilderHandler $db ) {
		$this->db = $db;
	}
}