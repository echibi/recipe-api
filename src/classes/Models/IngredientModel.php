<?php
/**
 * Created by Jonas Rensfeldt.
 * Date: 03/02/17
 */

namespace App\Models;

use App\Entities\IngredientEntity;
use App\Helpers\Utilities;

class IngredientModel extends Model {

	const table = 'ingredients';
	const table_rel = 'ingredients_rel';

	/**
	 * @param $recipeId
	 *
	 * @return mixed
	 */
	public function getRecipeIngredients( $recipeId ) {
		$ingredientsRel = $this->db->table( 'ingredients_rel' );
		$ingredientsRel->select(
			array(
				'ingredients_rel.value',
				'ingredients_rel.unit',
				'i.id',
				'i.name',
				'i.slug'
			)
		);
		$ingredientsRel->join( [ 'ingredients', 'i' ], 'i.id', '=', 'ingredients_rel.ingredient_id' );
		$ingredientsRel->where( 'recipe_id', '=', $recipeId );

		return $ingredientsRel->get();
	}

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

	/**
	 * @param        $id
	 * @param string $field
	 *
	 * @return mixed
	 */
	public function get( $id, $field = 'id' ) {
		$query = $this->db->table( self::table );
		if ( is_array( $id ) ) {
			$query->whereIn( $field, $id );

			return $query->get();
		} else {
			return $query->find( $id, $field );
		}

	}

	/**
	 * @return mixed
	 */
	public function getList() {
		return $this->db->table( self::table )->get();
	}

	/**
	 * @param        $id
	 * @param string $field
	 *
	 * @return mixed
	 */
	public function delete( $id, $field = 'id' ) {
		return $this->db->table( self::table )->where( $id, '=', $field )->delete();
	}

	/**
	 * @param IngredientEntity $object
	 *
	 * @return mixed
	 */
	public function create( IngredientEntity $object ) {

		$now = date( 'Y-m-d H:i:s' );

		return $this->db->table( self::table )->insert(
			array(
				'name'    => $object->name,
				'slug'    => Utilities::create_slug( $object->name ),
				'created' => $now,
				'updated' => $now,
				// 'image1'      => '',
			)
		);
	}

	/**
	 * @param                  $id
	 * @param IngredientEntity $object
	 *
	 * @return bool
	 */
	public function update( $id, IngredientEntity $object ) {

		$item = $this->db->table( self::table )->find( $id );

		if ( $item ) {
			$updateData = array(
				'name'    => $object->name,
				'slug'    => Utilities::create_slug( $object->name ),
				'updated' => date( 'Y-m-d H:i:s' ),
			);

			return $this->db->table( self::table )->where( 'id', $id )->update( $updateData );
		}

		return false;
	}
}