<?php

use Phinx\Migration\AbstractMigration;

class AddRecipeCategoryId extends AbstractMigration {
	public function change() {
		$table = $this->table( 'recipes' );
		$table->addColumn( 'category_id', 'integer' )
			->update();
	}
}
