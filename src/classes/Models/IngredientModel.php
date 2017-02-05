<?php
/**
 * Created by Jonas Rensfeldt.
 * Date: 03/02/17
 */

namespace App\Models;

use App\Entities\IngredientEntity;

class IngredientModel extends Model {

	/**
	 * Create an ingredient that's connected to a recipe.
	 *
	 * @param int              $recipeId
	 * @param IngredientEntity $ingredient
	 */
	public function createRecipeIngredient( $recipeId, IngredientEntity $ingredient ) {

		$existingIngredient = $this->db->table( 'ingredients' )->find( $ingredient->slug, 'slug' );
		// Only create a new ingredient if the slug doesn't already exists.
		if ( null === $existingIngredient ) {
			$now          = date( 'Y-m-d H:i:s' );
			$ingredientId = $this->db->table( 'ingredients' )->insert(
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
		return $this->db->table( 'ingredients_rel' )->insert(
			array(
				'recipe_id'     => $recipeId,
				'ingredient_id' => $ingredientId,
				'value'         => $ingredient->value,
				'unit'          => $ingredient->unit,
			)
		);
	}
}