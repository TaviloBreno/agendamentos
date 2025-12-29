<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

/**
 * Migration: AddTenantIdToTables
 * 
 * Adiciona tenant_id em todas as tabelas relevantes para suporte multi-tenant.
 */
class AddTenantIdToTables extends Migration
{
    /**
     * Tabelas que receberão a coluna tenant_id
     */
    protected array $tables = [
        'users',
        'units',
        'services',
        'professionals',
        'appointments',
        'clients',
        'notification_queue',
    ];

    public function up()
    {
        foreach ($this->tables as $table) {
            // Verificar se a tabela existe
            if (!$this->db->tableExists($table)) {
                continue;
            }

            // Verificar se a coluna já existe
            if ($this->db->fieldExists('tenant_id', $table)) {
                continue;
            }

            $this->forge->addColumn($table, [
                'tenant_id' => [
                    'type'       => 'INT',
                    'constraint' => 11,
                    'unsigned'   => true,
                    'null'       => true,
                    'after'      => 'id',
                    'comment'    => 'FK para tabela tenants',
                ],
            ]);

            // Adicionar índice
            $this->db->query("ALTER TABLE `{$table}` ADD INDEX `idx_{$table}_tenant` (`tenant_id`)");
            
            // Adicionar FK (comentado para permitir migração sem dados)
            // $this->db->query("ALTER TABLE `{$table}` ADD CONSTRAINT `fk_{$table}_tenant` FOREIGN KEY (`tenant_id`) REFERENCES `tenants`(`id`) ON DELETE CASCADE");
        }
    }

    public function down()
    {
        foreach ($this->tables as $table) {
            if (!$this->db->tableExists($table)) {
                continue;
            }

            if (!$this->db->fieldExists('tenant_id', $table)) {
                continue;
            }

            // Remover FK se existir
            try {
                $this->db->query("ALTER TABLE `{$table}` DROP FOREIGN KEY `fk_{$table}_tenant`");
            } catch (\Exception $e) {
                // FK pode não existir
            }

            // Remover índice
            try {
                $this->db->query("ALTER TABLE `{$table}` DROP INDEX `idx_{$table}_tenant`");
            } catch (\Exception $e) {
                // Index pode não existir
            }

            $this->forge->dropColumn($table, 'tenant_id');
        }
    }
}
