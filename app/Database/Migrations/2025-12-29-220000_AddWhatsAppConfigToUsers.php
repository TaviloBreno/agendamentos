<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

/**
 * Migration: Adicionar campos de configuração WhatsApp aos usuários
 * 
 * =========================================================================
 * NOVOS CAMPOS NA TABELA USERS
 * =========================================================================
 * 
 * - whatsapp_enabled      → Se notificações WhatsApp estão habilitadas
 * - whatsapp_provider     → Provider (ultramsg, evolution, twilio, z-api)
 * - whatsapp_instance_id  → ID da instância
 * - whatsapp_token        → Token de autenticação
 * - whatsapp_phone        → Número do WhatsApp conectado
 * - whatsapp_status       → Status da conexão (connected, disconnected)
 * 
 * @package    App\Database\Migrations
 */
class AddWhatsAppConfigToUsers extends Migration
{
    public function up()
    {
        $this->forge->addColumn('users', [
            'whatsapp_enabled' => [
                'type'       => 'TINYINT',
                'constraint' => 1,
                'default'    => 0,
                'null'       => false,
                'after'      => 'reset_expires_at',
            ],
            'whatsapp_provider' => [
                'type'       => 'ENUM',
                'constraint' => ['ultramsg', 'evolution', 'twilio', 'z-api'],
                'null'       => true,
                'after'      => 'whatsapp_enabled',
            ],
            'whatsapp_instance_id' => [
                'type'       => 'VARCHAR',
                'constraint' => 100,
                'null'       => true,
                'after'      => 'whatsapp_provider',
            ],
            'whatsapp_token' => [
                'type'       => 'VARCHAR',
                'constraint' => 255,
                'null'       => true,
                'after'      => 'whatsapp_instance_id',
            ],
            'whatsapp_phone' => [
                'type'       => 'VARCHAR',
                'constraint' => 20,
                'null'       => true,
                'after'      => 'whatsapp_token',
            ],
            'whatsapp_status' => [
                'type'       => 'ENUM',
                'constraint' => ['connected', 'disconnected', 'pending'],
                'default'    => 'disconnected',
                'null'       => false,
                'after'      => 'whatsapp_phone',
            ],
        ]);
    }

    public function down()
    {
        $this->forge->dropColumn('users', [
            'whatsapp_enabled',
            'whatsapp_provider', 
            'whatsapp_instance_id',
            'whatsapp_token',
            'whatsapp_phone',
            'whatsapp_status',
        ]);
    }
}
