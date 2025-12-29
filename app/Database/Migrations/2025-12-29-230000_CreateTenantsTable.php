<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

/**
 * Migration: CreateTenantsTable
 * 
 * Cria a tabela de empresas/tenants para suporte multi-tenant.
 * Cada tenant representa uma empresa/clínica/estabelecimento.
 */
class CreateTenantsTable extends Migration
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
            'name' => [
                'type'       => 'VARCHAR',
                'constraint' => 150,
                'comment'    => 'Nome da empresa',
            ],
            'slug' => [
                'type'       => 'VARCHAR',
                'constraint' => 100,
                'comment'    => 'Slug único para URL (ex: clinica-exemplo)',
            ],
            'domain' => [
                'type'       => 'VARCHAR',
                'constraint' => 255,
                'null'       => true,
                'comment'    => 'Domínio personalizado (opcional)',
            ],
            'email' => [
                'type'       => 'VARCHAR',
                'constraint' => 150,
                'comment'    => 'Email principal da empresa',
            ],
            'phone' => [
                'type'       => 'VARCHAR',
                'constraint' => 20,
                'null'       => true,
            ],
            'document' => [
                'type'       => 'VARCHAR',
                'constraint' => 20,
                'null'       => true,
                'comment'    => 'CNPJ ou CPF',
            ],
            'logo' => [
                'type'       => 'VARCHAR',
                'constraint' => 255,
                'null'       => true,
                'comment'    => 'Caminho do logo',
            ],
            'address' => [
                'type'       => 'VARCHAR',
                'constraint' => 255,
                'null'       => true,
            ],
            'city' => [
                'type'       => 'VARCHAR',
                'constraint' => 100,
                'null'       => true,
            ],
            'state' => [
                'type'       => 'VARCHAR',
                'constraint' => 2,
                'null'       => true,
            ],
            'zip_code' => [
                'type'       => 'VARCHAR',
                'constraint' => 10,
                'null'       => true,
            ],
            'settings' => [
                'type'    => 'JSON',
                'null'    => true,
                'comment' => 'Configurações personalizadas em JSON',
            ],
            'plan' => [
                'type'       => 'ENUM',
                'constraint' => ['free', 'basic', 'professional', 'enterprise'],
                'default'    => 'free',
                'comment'    => 'Plano de assinatura',
            ],
            'plan_expires_at' => [
                'type'    => 'DATE',
                'null'    => true,
                'comment' => 'Data de expiração do plano',
            ],
            'max_users' => [
                'type'       => 'INT',
                'constraint' => 11,
                'default'    => 5,
                'comment'    => 'Limite de usuários',
            ],
            'max_units' => [
                'type'       => 'INT',
                'constraint' => 11,
                'default'    => 1,
                'comment'    => 'Limite de unidades',
            ],
            'max_appointments_month' => [
                'type'       => 'INT',
                'constraint' => 11,
                'default'    => 100,
                'comment'    => 'Limite de agendamentos por mês',
            ],
            'whatsapp_enabled' => [
                'type'       => 'TINYINT',
                'constraint' => 1,
                'default'    => 0,
            ],
            'payments_enabled' => [
                'type'       => 'TINYINT',
                'constraint' => 1,
                'default'    => 0,
            ],
            'status' => [
                'type'       => 'ENUM',
                'constraint' => ['active', 'inactive', 'suspended', 'trial'],
                'default'    => 'trial',
            ],
            'trial_ends_at' => [
                'type'    => 'DATE',
                'null'    => true,
                'comment' => 'Data fim do período trial',
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
        $this->forge->addUniqueKey('slug');
        $this->forge->addUniqueKey('domain');
        $this->forge->addKey('status');
        $this->forge->addKey('plan');

        $this->forge->createTable('tenants');
    }

    public function down()
    {
        $this->forge->dropTable('tenants');
    }
}
