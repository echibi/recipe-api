<?php

use Phinx\Migration\AbstractMigration;

class ImagesTable extends AbstractMigration {
	public function change() {
		$images_table = $this->table( 'images' );
		$images_table->addColumn( 'filename', 'string' )
			->addColumn( 'alt', 'string' )
			->addColumn( 'mime_type', 'string' )
			->addColumn( 'ext', 'string' )
			->addColumn( 'system_path', 'string' )
			->addColumn( 'size', 'string' )
			->addColumn( 'created', 'datetime' )
			->addColumn( 'updated', 'datetime', array( 'null' => true ) )
			->create();
	}
}
