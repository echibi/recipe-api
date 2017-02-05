<?php

use Phinx\Migration\AbstractMigration;

class UnitsTable extends AbstractMigration
{
    public function change()
    {
        $table = $this->table( 'units' );
        $table->addColumn( 'name', 'string' )
            ->addColumn( 'fullname', 'string' )
            ->addColumn( 'created', 'datetime' )
            ->addColumn( 'updated', 'datetime', array( 'null' => true ) )
            ->create();
    }
}
