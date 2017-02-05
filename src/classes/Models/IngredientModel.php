<?php
/**
 * Created by Jonas Rensfeldt.
 * Date: 03/02/17
 */

namespace App\Models;

use App\Entities\IngredientEntity;

class IngredientModel extends Model {

	const table = 'ingredients';
	const table_rel = 'ingredients_rel';

	/**
	 * Create an ingredient that's connected to a recipe.
	 *
	 * @param int              $recipeId
	 * @param IngredientEntity $ingredient
	 */
	public function createRecipeIngredient( $recipeId, IngredientEntity $ingredient ) {

		$existingIngredient = $this->db->table( self::table )->find( $ingredient->slug, 'slug' );
		// Only create a new ingredient if the slug doesn't already exists.
		if ( null === $existingIngredient ) {
			$now          = date( 'Y-m-d H:i:s' );
			$ingredientId = $this->db->table( self::table )->insert(
				array(
					'name'    => $ingredient->name,
					'slug'    => $ingredient->slug,
					'created' => $now,
					'updated' => $now
				)
			);
		} else {
			// TODO:: Maybe update the existing one? If the name uses different uppercase etc.
			$ingredientId = $existingIngredient->id;
		}

		// Create Recipe -> Ingredient Relation.
		return $this->db->table( self::table_rel )->insert(
			array(
				'recipe_id'     => $recipeId,
				'ingredient_id' => $ingredientId,
				'value'         => $ingredient->value,
				'unit'          => $ingredient->unit,
			)
		);
	}

	/**
	 * @param $recipeId
	 *
	 * @return mixed
	 */
	public function removeRecipeIngredients( $recipeId ) {
		$ingredientRelDel = $this->db->table( self::table_rel );
		$ingredientRelDel->where( 'recipe_id', '=', $recipeId );

		return $ingredientRelDel->delete();
	}
}