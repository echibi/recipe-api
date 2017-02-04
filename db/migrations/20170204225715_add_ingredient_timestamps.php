<?php

use Phinx\Migration\AbstractMigration;

class AddIngredientTimestamps extends AbstractMigration {
	public function change() {
		$table = $this->table( 'ingredients' );
		$table->addColumn( 'created', 'datetime' )
			->addColumn( 'updated', 'datetime', array( 'null' => true ) )
			->update();
	}
}
