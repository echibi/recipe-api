<?php
/**
 * Created by jonas.
 * Project: recipe-api
 * Date: 2016-12-06
 */

namespace App;

use App\Helpers\Utilities;

class RecipeModel extends Database {

	/**
	 * @var \PDO
	 */
	protected $db;

	public function __construct() {
		// TODO:: Move away from this. Use the builder if possible.
		if ( class_exists( '\\QB' ) ) {
			$this->db = \QB::pdo();
		}

		$this->fields = array(
			'title',
			'description',
			'updated',
			'image1',
			'created'
		);
	}

	/**
	 * Gets a list of recipes
	 *
	 * @param      array               sort,fields,offset,limit and where clauses
	 * @param bool $includeIngredients Not implemented
	 *
	 * @return array
	 */
	public function getItems( $opts = array() ) {

		$mainQuery = \QB::table( 'recipes' );

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

					return trim( $field );
				},
				$fields
			);
			// Remove empty items.
			$fields = array_filter( $fields );

			// Add field list to the query
			if ( is_array( $fields ) && !empty( $fields ) ) {
				$mainQuery->select( $fields );
			}

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

			$ingredientQuery = \QB::table( 'ingredients_rel' );
			$ingredientQuery->join( 'ingredients', 'ingredients.id', '=', 'ingredients_rel.id' );
			$ingredientQuery->whereIn( 'recipe_id', $recipesId );


			$queryObj = $ingredientQuery->getQuery();
			echo $queryObj->getRawSql();


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


		/* */
		$queryObj = $mainQuery->getQuery();
		echo $queryObj->getRawSql();

		/**/

		$result = Utilities::objectToArray( $result );

		return $result;

	}

	/**
	 * Fetches one recipe with all related info
	 *
	 * @param $id
	 *
	 * @return \App\RecipeEntity on success false on failure.
	 */
	public function getItem( $id ) {

		$prepareSelectItem = $this->db->prepare(
			'SELECT *
			 FROM recipes AS r
			 WHERE r.id = :id'
		);

		$prepareSelectItem->execute( array( 'id' => $id ) );

		$recipeData = $prepareSelectItem->fetch();

		if ( empty( $recipeData ) ) {
			return false;
		}

		$prepareSelectIngredients = $this->db->prepare(
			'SELECT i.id, i.name, i.slug, rel.value, rel.unit
			 FROM ingredients_rel AS rel
			 JOIN ingredients AS i ON ( i.id = rel.ingredient_id )
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