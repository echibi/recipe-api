<?php

use Phinx\Migration\AbstractMigration;

class RenameImagesPathCol extends AbstractMigration {
	public function change() {
		$table = $this->table( 'images' );
		$table->renameColumn( 'system_path', 'path' )
			->update();
	}
}
