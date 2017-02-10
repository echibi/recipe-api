<?php

use Phinx\Migration\AbstractMigration;

class AddRecipeSlug extends AbstractMigration
{
    public function change()
    {
        $table = $this->table( 'recipes' );
        $table->addColumn( 'slug', 'string' )
            ->update();
    }
}
