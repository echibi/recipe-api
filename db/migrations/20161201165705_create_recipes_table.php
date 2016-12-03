<?php

use Phinx\Migration\AbstractMigration;

class CreateRecipesTable extends AbstractMigration {
	/**
	 * Change Method.
	 *
	 * Write your reversible migrations using this method.
	 *
	 * More information on writing migrations is available here:
	 * http://docs.phinx.org/en/latest/migrations.html#the-abstractmigration-class
	 *
	 * The following commands can be used in this method and Phinx will
	 * automatically reverse them when rolling back:
	 *
	 *    createTable
	 *    renameTable
	 *    addColumn
	 *    renameColumn
	 *    addIndex
	 *    addForeignKey
	 *
	 * Remember to call "create()" or "update()" and NOT "save()" when working
	 * with the Table class.
	 */
	public function change() {
		$recipe_table = $this->table( 'recipes' );
		$recipe_table->addColumn( 'title', 'string' )
			->addColumn( 'description', 'text' )
			->addColumn( 'image1', 'text' )
			->create();


		$ingredients_table = $this->table( 'ingredients' );
		$ingredients_table
			->addColumn( 'name', 'string' )
			->addColumn( 'slug', 'string' )
			->create();

		$ingredients_reltable = $this->table( 'ingredients_rel' );
		$ingredients_reltable->addColumn( 'recipe_id', 'integer' )
			->addColumn( 'ingredient_id', 'integer' )
			->addColumn( 'value', 'string' )
			->addColumn( 'unit', 'string' )
			->addForeignKey( 'recipe_id', $recipe_table )
			->addForeignKey( 'ingredient_id', $ingredients_table )
			->create();


	}
}
