<?php
/**
 * Created by jonas.
 * Project: recipe-api
 * Date: 2016-12-06
 */

namespace App;

use App\Helpers\Utilities;

class RecipeModel {

	protected $db;

	public function __construct( $db ) {
		$this->db = $db;
	}

	/**
	 * @param \App\RecipeEntity $recipe
	 *
	 * @return bool|int Returns saved recipe ID on success and false on failure.
	 */
	public function create( RecipeEntity $recipe ) {

		// Connect to db and prepare inserts.
		$db = $this->db;

		$prepareRecipeInsert = $db->prepare(
			'INSERT INTO recipes ( title, description, created, updated, image1 )
				 VALUES (:title, :description, :created, :updated, :image1)'
		);

		$nowDatetime = date( 'Y-m-d H:i:s' );
		$saveOk      = $prepareRecipeInsert->execute(
			array(
				'title'       => $recipe->getTitle(),
				'description' => $recipe->getDescription(),
				'created'     => $nowDatetime,
				'updated'     => $nowDatetime,
				'image1'      => '' // Placeholder
			)
		);

		if ( true === $saveOk ) {

			// Save OK
			$recipeId = $db->lastInsertId();

			$prepareIngredientInsert    = $db->prepare(
				'INSERT INTO ingredients ( name, slug )
				 VALUES (:name, :slug)'
			);
			$prepareIngredientRelInsert = $db->prepare(
				'INSERT INTO ingredients_rel ( recipe_id, ingredient_id, value, unit )
				 VALUES (:recipe_id, :ingredient_id, :value, :unit)'
			);

			$ingredients = $recipe->getIngredients();

			if ( ! empty( $ingredients ) ) {
				foreach ( $ingredients as $ingredient ) {
					// Run query
					$prepareIngredientInsert->execute(
						array(
							'name' => $ingredient['name'],
							'slug' => Utilities::sanitize_title_with_dashes( $ingredient['name'] )
						)
					);
					$ingredientId = $db->lastInsertId();
					// Run rel query
					$prepareIngredientRelInsert->execute(
						array(
							'recipe_id'     => $recipeId,
							'ingredient_id' => $ingredientId,
							'value'         => $ingredient['value'],
							'unit'          => $ingredient['unit']
						)
					);
				}
			}

			return $recipeId;
		} else {
			return false;
		}
	}
}