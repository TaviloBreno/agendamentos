<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

/**
 * Migration: Criar tabela de filas de notificação
 * 
 * =========================================================================
 * ESTRUTURA DA TABELA NOTIFICATION_QUEUE
 * =========================================================================
 * 
 * Tabela para gerenciar filas de notificações (WhatsApp, Email, etc)
 * 
 * CAMPOS:
 * - id             → Chave primária
 * - type           → Tipo de notificação (whatsapp, email, sms)
 * - recipient      → Destinatário (telefone, email)
 * - subject        → Assunto (para email)
 * - message        → Conteúdo da mensagem
 * - data           → Dados extras em JSON
 * - status         → Status (pending, processing, sent, failed)
 * - attempts       → Número de tentativas
 * - max_attempts   → Máximo de tentativas
 * - error_message  → Mensagem de erro (se falhou)
 * - scheduled_at   → Data/hora agendada para envio
 * - sent_at        → Data/hora do envio
 * - timestamps     → created_at, updated_at
 * 
 * @package    App\Database\Migrations
 */
class CreateNotificationQueueTable extends Migration
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
            'type' => [
                'type'       => 'ENUM',
                'constraint' => ['whatsapp', 'email', 'sms'],
                'default'    => 'whatsapp',
                'null'       => false,
            ],
            'recipient' => [
                'type'       => 'VARCHAR',
                'constraint' => 100,
                'null'       => false,
            ],
            'subject' => [
                'type'       => 'VARCHAR',
                'constraint' => 255,
                'null'       => true,
            ],
            'message' => [
                'type' => 'TEXT',
                'null' => false,
            ],
            'data' => [
                'type' => 'JSON',
                'null' => true,
            ],
            'status' => [
                'type'       => 'ENUM',
                'constraint' => ['pending', 'processing', 'sent', 'failed'],
                'default'    => 'pending',
                'null'       => false,
            ],
            'attempts' => [
                'type'       => 'TINYINT',
                'constraint' => 3,
                'unsigned'   => true,
                'default'    => 0,
            ],
            'max_attempts' => [
                'type'       => 'TINYINT',
                'constraint' => 3,
                'unsigned'   => true,
                'default'    => 3,
            ],
            'error_message' => [
                'type' => 'TEXT',
                'null' => true,
            ],
            'scheduled_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
            'sent_at' => [
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
        $this->forge->addKey('status');
        $this->forge->addKey('type');
        $this->forge->addKey('scheduled_at');
        $this->forge->addKey(['status', 'scheduled_at'], false, false, 'idx_status_scheduled');
        
        $this->forge->createTable('notification_queue');
    }

    public function down()
    {
        $this->forge->dropTable('notification_queue');
    }
}
