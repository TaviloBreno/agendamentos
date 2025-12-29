<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

/**
 * Cria tabela de notificações do sistema
 */
class CreateNotificationsTable extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'id' => [
                'type'           => 'INT',
                'constraint'     => 11,
                'unsigned'       => true,
                'auto_increment' => true,
            ],
            'user_id' => [
                'type'       => 'INT',
                'constraint' => 11,
                'unsigned'   => true,
            ],
            'type' => [
                'type'       => 'VARCHAR',
                'constraint' => 50,
                'comment'    => 'Tipo: appointment, message, system, payment, reminder',
            ],
            'title' => [
                'type'       => 'VARCHAR',
                'constraint' => 255,
            ],
            'message' => [
                'type' => 'TEXT',
            ],
            'icon' => [
                'type'       => 'VARCHAR',
                'constraint' => 50,
                'null'       => true,
                'default'    => 'fa-bell',
            ],
            'color' => [
                'type'       => 'VARCHAR',
                'constraint' => 20,
                'null'       => true,
                'default'    => 'primary',
            ],
            'link' => [
                'type'       => 'VARCHAR',
                'constraint' => 255,
                'null'       => true,
                'comment'    => 'URL para redirecionar ao clicar',
            ],
            'data' => [
                'type'    => 'JSON',
                'null'    => true,
                'comment' => 'Dados extras em JSON',
            ],
            'read_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
            'created_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
            'updated_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
        ]);

        $this->forge->addKey('id', true);
        $this->forge->addKey('user_id');
        $this->forge->addKey('type');
        $this->forge->addKey('read_at');
        $this->forge->addKey('created_at');
        $this->forge->addForeignKey('user_id', 'users', 'id', 'CASCADE', 'CASCADE');

        $this->forge->createTable('notifications', true);
    }

    public function down()
    {
        $this->forge->dropTable('notifications', true);
    }
}
