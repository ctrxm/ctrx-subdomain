<?php namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateSubdomainsTable extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'id' => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true, 'auto_increment' => true],
            'user_id' => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true],
            'domain_id' => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true],
            'cloudflare_record_id' => ['type' => 'VARCHAR', 'constraint' => '255'],
            'name' => ['type' => 'VARCHAR', 'constraint' => '100'], // Hanya nama subdomainnya, misal: 'blog'
            'type' => ['type' => 'VARCHAR', 'constraint' => '10'], // A, CNAME, dll.
            'content' => ['type' => 'VARCHAR', 'constraint' => '255'], // IP atau domain tujuan
            'proxied' => ['type' => 'BOOLEAN', 'default' => true],
            'created_at' => ['type' => 'DATETIME', 'null' => true],
            'updated_at' => ['type' => 'DATETIME', 'null' => true],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->addForeignKey('user_id', 'users', 'id', 'CASCADE', 'CASCADE');
        $this->forge->addForeignKey('domain_id', 'domains', 'id', 'CASCADE', 'CASCADE');
        $this->forge->createTable('subdomains');
    }

    public function down()
    {
        $this->forge->dropTable('subdomains');
    }
}
