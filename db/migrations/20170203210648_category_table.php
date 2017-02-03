<?php

use Phinx\Migration\AbstractMigration;

class CategoryTable extends AbstractMigration {
	public function change() {
		$category_table = $this->table( 'categories' );
		$category_table->addColumn( 'name', 'string' )
			->addColumn( 'slug', 'string' )
			->addColumn( 'created', 'datetime' )
			->addColumn( 'updated', 'datetime', array( 'null' => true ) )
			->create();
	}
}
