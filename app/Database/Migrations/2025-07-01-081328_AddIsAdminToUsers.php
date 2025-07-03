<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddIsAdminToUsers extends Migration
{
// di dalam file migrasi baru
public function up()
{
    $this->forge->addColumn('users', [
        'is_admin' => ['type' => 'BOOLEAN', 'default' => false, 'after' => 'active'],
    ]);
}
public function down()
{
    $this->forge->dropColumn('users', 'is_admin');
}
}
