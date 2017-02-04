<?php
/**
 * Created by Jonas Rensfeldt.
 * Date: 04/02/17
 */

namespace App\Entities;


/**
 * Class IngredientEntity
 * @package App\Entities
 */
class IngredientEntity extends Entity {

	/**
	 * @var int
	 */
	public $id;
	/**
	 * @var string
	 */
	public $name;
	/**
	 * @var string
	 */
	public $slug;
	/**
	 * @var string
	 */
	public $value;
	/**
	 * @var string
	 */
	public $unit;
	/**
	 * @var string Date
	 */
	public $created;
	/**
	 * @var string Date
	 */
	public $updated;

	/**
	 * Accept an array of data matching properties of this class
	 * and create the class
	 *
	 * @param array|object $data The data to use to create
	 */
	public function __construct( $data ) {
		if ( is_object( $data ) ) {
			$data = $this->objectToArray( $data );
		}
		// no id if we're creating
		if ( isset( $data['id'] ) ) {
			$this->id = $data['id'];
		}

		$this->name = $data['name'];

		if ( isset( $data['value'] ) ) {
			$this->value = $data['value'];
		}
		if ( isset( $data['unit'] ) ) {
			$this->unit = $data['unit'];
		}
		if ( isset( $data['slug'] ) ) {
			$this->slug = $data['slug'];
		}
		if ( isset( $data['created'] ) ) {
			$this->created = $data['created'];
		}
		if ( isset( $data['updated'] ) ) {
			$this->updated = $data['updated'];
		}
	}
}