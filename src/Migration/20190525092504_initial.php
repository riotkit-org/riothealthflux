<?php declare(strict_types=1);

use Phinx\Migration\AbstractMigration;

class Initial extends AbstractMigration
{
    public function change()
    {
        $table = $this->table('nodes_history');
        $table->addColumn('name', 'string', [
            'length' => 254
        ]);

        $table->addColumn('url', 'text');
        $table->addColumn('status', 'boolean');
        $table->addColumn('action_time', 'datetime');

        $table->create();
    }
}
