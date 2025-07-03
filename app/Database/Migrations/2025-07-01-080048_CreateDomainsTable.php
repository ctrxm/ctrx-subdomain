<?php namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateDomainsTable extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'id' => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true, 'auto_increment' => true],
            'domain_name' => ['type' => 'VARCHAR', 'constraint' => '255'],
            'zone_id' => ['type' => 'VARCHAR', 'constraint' => '255'],
            'is_active' => ['type' => 'BOOLEAN', 'default' => true],
            'created_at' => ['type' => 'DATETIME', 'null' => true],
            'updated_at' => ['type' => 'DATETIME', 'null' => true],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->createTable('domains');
    }

    public function down()
    {
        $this->forge->dropTable('domains');
    }
}
