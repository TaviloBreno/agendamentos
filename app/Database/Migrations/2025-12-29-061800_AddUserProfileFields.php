<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

/**
 * Adiciona campos de perfil e configurações aos usuários
 */
class AddUserProfileFields extends Migration
{
    public function up()
    {
        $this->forge->addColumn('users', [
            'avatar' => [
                'type'       => 'VARCHAR',
                'constraint' => 255,
                'null'       => true,
                'after'      => 'email',
            ],
            'phone' => [
                'type'       => 'VARCHAR',
                'constraint' => 20,
                'null'       => true,
                'after'      => 'avatar',
            ],
            'bio' => [
                'type' => 'TEXT',
                'null' => true,
                'after' => 'phone',
            ],
            'settings' => [
                'type'    => 'JSON',
                'null'    => true,
                'after'   => 'bio',
                'comment' => 'Configurações do usuário em JSON',
            ],
            'last_login_at' => [
                'type' => 'DATETIME',
                'null' => true,
                'after' => 'settings',
            ],
            'last_activity_at' => [
                'type' => 'DATETIME',
                'null' => true,
                'after' => 'last_login_at',
            ],
        ]);
    }

    public function down()
    {
        $this->forge->dropColumn('users', [
            'avatar',
            'phone',
            'bio',
            'settings',
            'last_login_at',
            'last_activity_at',
        ]);
    }
}
