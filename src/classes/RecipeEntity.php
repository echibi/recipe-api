<?php
/**
 * Created by jonas.
 * Project: recipe-api
 * Date: 2016-11-29
 */

namespace App;


class RecipeEntity {

	/**
	 * @var int
	 */
	protected $id;
	/**
	 * @var string
	 */
	protected $title;
	/**
	 * @var string
	 */
	protected $description;
	/**
	 * @var array
	 */
	protected $ingredients;

	/**
	 * Accept an array of data matching properties of this class
	 * and create the class
	 *
	 * @param array $data The data to use to create
	 */
	public function __construct( array $data ) {
		// no id if we're creating
		if ( isset( $data['id'] ) ) {
			$this->id = $data['id'];
		}

		$this->title = $data['title'];

		if ( isset( $data['description'] ) ) {
			$this->description = $data['description'];
		}

		if ( isset( $data['ingredients'] ) ) {
			$this->_setIngredients( $data['ingredients'] );
		}

	}

	public function getId() {
		return $this->id;
	}

	public function getTitle() {
		return $this->title;
	}

	public function getDescription() {
		return $this->description;
	}

	public function getShortDescription() {
		return substr( $this->description, 0, 20 );
	}

	public function getIngredients() {
		return $this->ingredients;
	}

	/**
	 * @param $ingredientData array
	 *
	 * @return void
	 */
	private function _setIngredients( $ingredientData ) {
		if ( ! empty( $ingredientData ) ) {
			foreach ( $ingredientData as $ingredient ) {
				if ( ! empty( $ingredient['name'] ) && ! empty( $ingredient['value'] ) && ! empty( $ingredient['unit'] ) ) {
					$this->ingredients[] = $ingredient;
				}
			}
		}
	}
}