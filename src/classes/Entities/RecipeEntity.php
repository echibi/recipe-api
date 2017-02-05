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
	 * @var ImageEntity
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

		$this->title       = $data['title'];
		$this->description = $data['description'];
		$this->ingredients = $data['ingredients'];
		$this->image1      = $data['image1'];
		$this->category_id = $data['category_id'];
		$this->created     = $data['created'];
		$this->updated     = $data['updated'];

	}
}