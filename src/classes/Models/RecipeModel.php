<?php
/**
 * Created by jonas.
 * Project: recipe-api
 * Date: 2016-12-06
 */

namespace App\Models;

use App\Entities\IngredientEntity;
use App\Entities\RecipeEntity;
use App\Helpers\Utilities;
use Interop\Container\ContainerInterface;
use Pixie\QueryBuilder;
use Pixie\QueryBuilder\QueryBuilderHandler;

/**
 * Class RecipeModel
 * @package App\Models
 */
class RecipeModel extends Model {

	/**
	 * @var \PDO
	 */
	protected $pdo;

	/**
	 * @var array Allowed fields to filter on.
	 */
	protected $fields;

	public function __construct( ContainerInterface $container ) {
		parent::__construct( $container );
		$this->pdo = $this->db->pdo();

		$this->fields = array(
			'title',
			'description',
			'updated',
			'image1',
			'created'
		);
	}

	/**
	 * Updates recipe.
	 *
	 * @param $id
	 * @param $data
	 *
	 * @return bool
	 */
	public function update( $id, $data ) {

		$rawData = $data;

		$item = $this->db->table( 'recipes' )->find( $id );

		if ( $item ) {

			unset( $data['ingredients'] );

			$this->db->table( 'recipes' )->where( 'id', $id )->update( $data );

			if ( isset( $rawData['ingredients'] ) ) {

				// Just remove old ingredients and create new connections?
				foreach ( $rawData['ingredients'] as $ingredient ) {
					// We have an id on the ingredient
					// Check if it exists.
					if ( isset( $ingredient['id'] ) ) {
						$ingredientRow = $this->db->table( 'ingredients_rel' )->find( $ingredient['id'] );
						if ( $ingredientRow ) {
							// Ingredient exists.

						}
					}
				}
				// $insertIds = QB::table('my_table')->insert( $data );

			}

			return true;
		} else {
			return false;
		}
	}

	/**
	 * Removes a recipe and its related rows.
	 *
	 * @param $id
	 *
	 * @return bool
	 */
	public function remove( $id ) {

		$item = $this->db->table( 'recipes' )->find( $id );

		if ( $item ) {
			$recipeDel = $this->db->table( 'recipes' );
			$recipeDel->where( 'id', '=', $id );
			$recipeDel->delete();

			$ingredientRelDel = $this->db->table( 'ingredients_rel' );
			$ingredientRelDel->where( 'recipe_id', '=', $id );
			$ingredientRelDel->delete();

			return true;
		} else {
			return false;
		}

	}

	/**
	 * Gets a list of recipes
	 *
	 * @param      array               sort,fields,offset,limit and where clauses
	 *
	 * @return array
	 */
	public function getList( $opts = array() ) {

		$mainQuery = $this->db->table( 'recipes' );

		// Set default limit
		if ( isset( $opts['limit'] ) ) {
			$limit = filter_var(
				$opts['limit'],
				FILTER_SANITIZE_NUMBER_INT
			);
			$mainQuery->limit( $limit );
		} else {
			$mainQuery->limit( 20 );
		}

		// Get and sanitize filters from the URL
		if ( !empty( $opts ) ) {
			$rawfilters = $opts;
			unset(
				$rawfilters['sort'],
				$rawfilters['fields'],
				$rawfilters['page'],
				$rawfilters['limit'],
				$rawfilters['ingredients']
			);
			foreach ( $rawfilters as $key => $value ) {
				$filters[$key] = filter_var( $value, FILTER_SANITIZE_STRING );
			}

		}

		// Add filters to the query
		if ( !empty( $filters ) ) {
			foreach ( $filters as $key => $value ) {
				if ( 'q' == $key ) {
					$mainQuery->where( 'title', 'LIKE', '%' . $value . '%' );
					$mainQuery->orWhere( 'description', 'LIKE', '%' . $value . '%' );
				} else {
					// Make sure the field exists
					if ( !in_array( $key, $this->fields ) ) {
						continue;
					}
					$mainQuery->where( $key, $value );
				}
			}
		}


		// Get and sanitize field list from the URL
		if ( !empty( $opts['fields'] ) ) {
			$fields = $opts['fields'];
			$fields = explode( ',', $fields );
			$fields = array_map(
				function ( $field ) {
					// Make sure the field exists
					if ( !in_array( $field, $this->fields ) ) {
						return false;
					}
					$field = filter_var( $field, FILTER_SANITIZE_STRING );

					return 'recipes.' . trim( $field );
				},
				$fields
			);
			// Remove empty items.
			$fields = array_filter( $fields );

			// Add field list to the query
			if ( is_array( $fields ) && !empty( $fields ) ) {
				$mainQuery->select( $fields );
			}
		} else {
			$mainQuery->select( 'recipes.*' );
		}


		// Manage sort options
		// sort=firstname => ORDER BY firstname ASC
		// sort=-firstname => ORDER BY firstname DESC
		// sort=-firstname,email =>
		// ORDER BY firstname DESC, email ASC

		if ( !empty( $opts['sort'] ) ) {
			$sort = $opts['sort'];
			$sort = explode( ',', $sort );
			$sort = array_map(
				function ( $s ) {
					// Make sure the field exists
					if ( !in_array( str_replace( '-', '', $s ), $this->fields ) ) {
						return false;
					}
					$s = filter_var( $s, FILTER_SANITIZE_STRING );

					return trim( $s );
				},
				$sort
			);
			// Remove empty items.
			$sort = array_filter( $sort );

			if ( !empty( $sort ) ) {
				foreach ( $sort as $expr ) {
					if ( '-' == substr( $expr, 0, 1 ) ) {
						$mainQuery->orderBy( substr( $expr, 1 ), 'desc' );
					} else {
						$mainQuery->orderBy( $expr, 'asc' );
					}
				}
			}
		}

		// Manage pagination
		if ( isset( $opts['page'] ) ) {
			$page = filter_var(
				$opts['page'],
				FILTER_SANITIZE_NUMBER_INT
			);
			if ( !empty( $page ) ) {
				$perPage = filter_var(
					$opts['limit'],
					FILTER_SANITIZE_NUMBER_INT
				);
				if ( empty( $perPage ) ) {
					$perPage = 20;
				}
				$mainQuery->limit( $perPage )
					->offset( $page * $perPage - $perPage );
			}
		}

		// Only fetch recipes with these ingredients.
		if ( !empty( $opts['ingredients'] ) ) {
			$excludedIngredientsRecipes = $this->findRecipeIngredients( $opts['ingredients'] );
			if ( !empty( $excludedIngredientsRecipes ) ) {
				$mainQuery->whereIn( 'recipes.id', $excludedIngredientsRecipes );
			} else {
				// If no recipe was found with the queried ingredients we shall return nothing.
				return array();
			}
		}

		// Only fetch recipes without these ingredients.
		if ( !empty( $opts['excludeIngredients'] ) ) {
			$excludedIngredientsRecipes = $this->findRecipeIngredients( $opts['excludeIngredients'] );
			if ( !empty( $excludedIngredientsRecipes ) ) {
				$mainQuery->whereNotIn( 'recipes.id', $excludedIngredientsRecipes );
			}
		}
		// Debug
		// $queryObj = $mainQuery->getQuery();
		// echo $queryObj->getRawSql();

		// Run recipes query
		$recipes = $mainQuery->get();

		// Remap recipes so that the ID is used as keys.
		$result = array_reduce( $recipes, function ( $result, $item ) {
			$result[$item->id] = $item;

			return $result;
		}, array() );

		// Get related ingredients
		if ( !empty( $result ) ) {

			$recipesId = array();
			foreach ( $result as $recipe ) {
				$recipesId[] = (int) $recipe->id;
			}

			$ingredientQuery = $this->db->table( 'ingredients_rel' );
			$ingredientQuery->join( 'ingredients', 'ingredients.id', '=', 'ingredients_rel.ingredient_id' );
			$ingredientQuery->whereIn( 'recipe_id', $recipesId );

			$ingredients = $ingredientQuery->get();

			if ( !empty( $ingredients ) ) {
				foreach ( $ingredients as $ingredient ) {
					if ( isset( $result[$ingredient->recipe_id] ) ) {
						$key                                                   = $ingredient->id;
						$result[$ingredient->recipe_id]->{'ingredients'}[$key] = $ingredient;
					}
				}
			}

		}

		return $result;
	}

	/**
	 * Fetches one recipe with all related info
	 *
	 * @param $id
	 *
	 * @return RecipeEntity on success false on failure.
	 */
	public function get( $id ) {

		$recipeData = $this->db->table( 'recipes' )->find( $id );

		if ( null === $recipeData ) {
			return null;
		}

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
		$ingredientsRel->where( 'recipe_id', '=', $recipeData->id );

		$ingredients = $ingredientsRel->get();

		if ( !empty( $ingredients ) ) {
			$recipeData->ingredients = $ingredients;
		}

		return new RecipeEntity( $recipeData );
	}

	/**
	 * @param  RecipeEntity $recipe
	 *
	 * @return bool|int Returns saved recipe ID on success and false on failure.
	 */
	public function create( $recipe ) {

		$now = date( 'Y-m-d H:i:s' );

		$savedId = $this->db->table( 'recipes' )->insert(
			array(
				'title'       => $recipe->title,
				'description' => $recipe->description,
				'created'     => $now,
				'updated'     => $now,
				'image1'      => '',
				'category_id' => $recipe->category_id
			)
		);

		if ( !empty( $savedId ) ) {

			// Save OK
			$ingredients = $recipe->ingredients;

			if ( !empty( $ingredients ) ) {
				$ingredientModel = new IngredientModel( $this->container );
				foreach ( $ingredients as $ingredient ) {
					$ingredient['slug'] = Utilities::sanitize_title_with_dashes( $ingredient['name'] );
					$ingredientObj      = new IngredientEntity( $ingredient );
					$ingredientModel->createRecipeIngredient( $savedId, $ingredientObj );
				}
			}

			return $savedId;
		} else {
			return false;
		}
	}

	private function findRecipeIngredients( $ingredients ) {
		$ingredients = explode( ',', $ingredients );
		$ingredients = array_map(
			function ( $ingredient ) {
				$ingredient = filter_var( $ingredient, FILTER_SANITIZE_STRING );

				return trim( $ingredient );
			},
			$ingredients
		);

		$ingredientsQuery = $this->db->table( 'ingredients_rel' );
		$ingredientsQuery->select( array(
			'ingredients.slug',
			'ingredients_rel.ingredient_id',
			'ingredients_rel.recipe_id',
			'ingredients_rel.id'
		) );
		$ingredientsQuery->leftJoin( 'ingredients', 'ingredients.id', '=', 'ingredients_rel.ingredient_id' );
		$ingredientsQuery->whereIn( 'slug', $ingredients );

		$result = $ingredientsQuery->get();
		if ( empty( $result ) ) {
			return array();
		} else {
			$ingredientsRecipeID = array();
			foreach ( $result as $ingr ) {
				$ingredientsRecipeID[$ingr->recipe_id] = $ingr->recipe_id;
			}

			return $ingredientsRecipeID;
		}


	}
}