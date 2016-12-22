<?php
/**
 * Created by jonas.
 * Project: recipe-api
 * Date: 2016-12-06
 */

namespace App;

use App\Helpers\Utilities;

class RecipeModel {

	/**
	 * @var \PDO
	 */
	protected $db;

	public function __construct( \PDO $db ) {
		$this->db = $db;
	}

	/**
	 * @param $id
	 *
	 * @return \App\RecipeEntity on success false on failure.
	 */
	public function getItem( $id ) {

		$prepareSelectItem = $this->db->prepare(
			'SELECT *
			 FROM recipes as r
			 WHERE r.id = :id'
		);

		$prepareSelectItem->execute( array( 'id' => $id ) );

		$recipeData = $prepareSelectItem->fetch();

		if( empty( $recipeData ) ) {
			return false;
		}

		$prepareSelectIngredients = $this->db->prepare(
			'SELECT i.id, i.name, i.slug, rel.value, rel.unit
			 FROM ingredients_rel as rel
			 JOIN ingredients as i ON ( i.id = rel.ingredient_id )
			 WHERE rel.recipe_id = :id'
		);

		$prepareSelectIngredients->execute( array( 'id' => $id ) );


		$ingredients = $prepareSelectIngredients->fetchAll();

		if ( !empty( $ingredients ) ) {
			$recipeData['ingredients'] = $ingredients;
		}

		return new RecipeEntity( $recipeData );
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

			if ( !empty( $ingredients ) ) {
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