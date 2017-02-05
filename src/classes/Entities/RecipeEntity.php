<?php
/**
 * Created by jonas.
 * Project: recipe-api
 * Date: 2016-11-29
 */

namespace App\Entities;

use Slim\Http\UploadedFile;


/**
 * Class RecipeEntity
 * @package App\Entities
 */
class RecipeEntity extends Entity {

	/**
	 * @var int
	 */
	public $id;
	/**
	 * @var string
	 */
	public $title;
	/**
	 * @var string
	 */
	public $description;
	/**
	 * @var array
	 */
	public $ingredients;
	/**
	 * @var array
	 */
	public $images;
	/**
	 * @var array
	 */
	public $image1;
	/**
	 * @var int
	 */
	public $category_id;
	/**
	 * @var string Y-m-d H:i:s
	 */
	public $created;
	/**
	 * @var string Y-m-d H:i:s
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

		$this->title = $data['title'];

		if ( isset( $data['description'] ) ) {
			$this->description = $data['description'];
		}

		if ( isset( $data['ingredients'] ) ) {
			$this->ingredients = $data['ingredients'];
		}

		if ( isset( $data['image1'] ) ) {
			$this->images[] = $data['image1'];
			$this->image1   = $data['image1'];
		}

		if ( isset( $data['category_id'] ) ) {
			$this->category_id = $data['category_id'];
		}

		if ( isset( $data['created'] ) ) {
			$this->created = $data['created'];
		}
		if ( isset( $data['updated'] ) ) {
			$this->updated = $data['updated'];
		}
	}

	/**
	 * @return string
	 */
	public function getMainImage() {
		// Temporary..
		if ( isset( $this->image1 ) ) {
			return $this->image1;
		} else {
			return '';
		}
	}

}