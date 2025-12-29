<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

/**
 * Cria tabelas para sistema de mensagens entre usuários
 */
class CreateMessagesTable extends Migration
{
    public function up()
    {
        // Tabela de conversas
        $this->forge->addField([
            'id' => [
                'type'           => 'INT',
                'constraint'     => 11,
                'unsigned'       => true,
                'auto_increment' => true,
            ],
            'subject' => [
                'type'       => 'VARCHAR',
                'constraint' => 255,
                'null'       => true,
            ],
            'type' => [
                'type'       => 'ENUM',
                'constraint' => ['direct', 'group'],
                'default'    => 'direct',
            ],
            'created_by' => [
                'type'       => 'INT',
                'constraint' => 11,
                'unsigned'   => true,
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
        $this->forge->addKey('created_by');
        $this->forge->addForeignKey('created_by', 'users', 'id', 'CASCADE', 'CASCADE');
        $this->forge->createTable('conversations', true);

        // Tabela de participantes da conversa
        $this->forge->addField([
            'id' => [
                'type'           => 'INT',
                'constraint'     => 11,
                'unsigned'       => true,
                'auto_increment' => true,
            ],
            'conversation_id' => [
                'type'       => 'INT',
                'constraint' => 11,
                'unsigned'   => true,
            ],
            'user_id' => [
                'type'       => 'INT',
                'constraint' => 11,
                'unsigned'   => true,
            ],
            'last_read_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
            'is_muted' => [
                'type'       => 'TINYINT',
                'constraint' => 1,
                'default'    => 0,
            ],
            'joined_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
            'left_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
        ]);

        $this->forge->addKey('id', true);
        $this->forge->addKey(['conversation_id', 'user_id'], false, true, 'unique_participant');
        $this->forge->addForeignKey('conversation_id', 'conversations', 'id', 'CASCADE', 'CASCADE');
        $this->forge->addForeignKey('user_id', 'users', 'id', 'CASCADE', 'CASCADE');
        $this->forge->createTable('conversation_participants', true);

        // Tabela de mensagens
        $this->forge->addField([
            'id' => [
                'type'           => 'INT',
                'constraint'     => 11,
                'unsigned'       => true,
                'auto_increment' => true,
            ],
            'conversation_id' => [
                'type'       => 'INT',
                'constraint' => 11,
                'unsigned'   => true,
            ],
            'sender_id' => [
                'type'       => 'INT',
                'constraint' => 11,
                'unsigned'   => true,
            ],
            'message' => [
                'type' => 'TEXT',
            ],
            'attachment' => [
                'type'       => 'VARCHAR',
                'constraint' => 255,
                'null'       => true,
            ],
            'attachment_type' => [
                'type'       => 'VARCHAR',
                'constraint' => 50,
                'null'       => true,
                'comment'    => 'image, document, audio',
            ],
            'is_system' => [
                'type'       => 'TINYINT',
                'constraint' => 1,
                'default'    => 0,
                'comment'    => 'Mensagem automática do sistema',
            ],
            'created_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
            'updated_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
            'deleted_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
        ]);

        $this->forge->addKey('id', true);
        $this->forge->addKey('conversation_id');
        $this->forge->addKey('sender_id');
        $this->forge->addKey('created_at');
        $this->forge->addForeignKey('conversation_id', 'conversations', 'id', 'CASCADE', 'CASCADE');
        $this->forge->addForeignKey('sender_id', 'users', 'id', 'CASCADE', 'CASCADE');
        $this->forge->createTable('messages', true);
    }

    public function down()
    {
        $this->forge->dropTable('messages', true);
        $this->forge->dropTable('conversation_participants', true);
        $this->forge->dropTable('conversations', true);
    }
}
