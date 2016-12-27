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

	public function __construct( \PDO $db ) {
		$this->db = $db;
	}

	/**
	 * Gets a list of recipes
	 *
	 * @param      array           sort,fields,offset,limit and where clauses
	 * @param bool $includeRecipes Not implemented
	 *
	 * @return array
	 */
	public function getItems( $opts = array(), $includeRecipes = false ) {

		$selectPrepare = 'SELECT *';
		$wherePrepare  = '';
		$fromPrepare   = 'FROM recipes as r';
		$orderPrepare  = 'ORDER BY created ASC';
		$limitPrepare  = 'LIMIT 10';

		// Get and sanitize filters from the URL
		if ( !empty( $opts ) ) {
			$rawfilters = $opts;
			unset(
				$rawfilters['sort'],
				$rawfilters['fields'],
				$rawfilters['offset'],
				$rawfilters['limit']
			);
			foreach ( $rawfilters as $key => $value ) {
				$filters[$key] = filter_var( $value, FILTER_SANITIZE_STRING );
			}

		}

		// Add filters to the query
		if ( !empty( $filters ) ) {
			$wherePrepare = 'WHERE ';
			if ( isset( $filters['q'] ) ) {
				$wherePrepare .= '`title` LIKE :q OR `description` LIKE :q)';
			}
			$wherePrepare .= $this->whereArrayPrepare( $filters, 'AND', array( 'q' ) );
		}

		/*
		// Get and sanitize field list from the URL
		if ( $fields = $app->request->get( 'fields' ) ) {
			$fields = explode( ',', $fields );
			$fields = array_map(
				function ( $field ) {
					$field = filter_var( $field, FILTER_SANITIZE_STRING );

					return trim( $field );
				},
				$fields
			);
		}

		// Add field list to the query
		if ( is_array( $fields ) && !empty( $fields ) ) {
			// $results->selectMany( $fields );
		}

		*/
		// echo "<xmp style=\"text-align:left;\">" . print_r( $wherePrepare, true ) . "</xmp>";

		$prepareRecipes = $this->db->prepare(
			$selectPrepare
			. $fromPrepare
			. $wherePrepare
			. $orderPrepare
			. $limitPrepare
		);

		if( '' !== $wherePrepare ) {

		}
		// $prepareRecipes->bindParam( ':offset', intval( $offset ), \PDO::PARAM_INT );
		// $prepareRecipes->bindParam( ':limit', intval( $limit ), \PDO::PARAM_INT );
		$prepareRecipes->execute();

		return $prepareRecipes->fetchAll();
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