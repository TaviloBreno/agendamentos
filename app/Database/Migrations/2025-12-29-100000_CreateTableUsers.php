<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

/**
 * Migration: Criar tabela de usuários
 * 
 * =========================================================================
 * ESTRUTURA DA TABELA USERS
 * =========================================================================
 * 
 * Tabela para autenticação e controle de acesso ao painel administrativo.
 * 
 * CAMPOS:
 * - id         → Chave primária auto-incremento
 * - name       → Nome completo do usuário
 * - email      → Email único para login
 * - password   → Senha hasheada (PASSWORD_DEFAULT)
 * - role       → Papel do usuário (super, admin, user)
 * - active     → Status (1=ativo, 0=inativo)
 * - timestamps → created_at, updated_at, deleted_at
 * 
 * ÍNDICES:
 * - PRIMARY KEY (id)
 * - UNIQUE (email) → Login único
 * - INDEX (role)   → Filtragem por papel
 * - INDEX (active) → Filtragem por status
 * 
 * @package    App\Database\Migrations
 */
class CreateTableUsers extends Migration
{
    /**
     * Executa a migration (cria a tabela)
     */
    public function up()
    {
        $this->forge->addField([
            // Chave primária
            'id' => [
                'type'           => 'INT',
                'constraint'     => 11,
                'unsigned'       => true,
                'auto_increment' => true,
            ],
            
            // Nome completo do usuário
            'name' => [
                'type'       => 'VARCHAR',
                'constraint' => 100,
                'null'       => false,
            ],
            
            // Email para login (único)
            'email' => [
                'type'       => 'VARCHAR',
                'constraint' => 150,
                'null'       => false,
            ],
            
            // Senha hasheada
            'password' => [
                'type'       => 'VARCHAR',
                'constraint' => 255,
                'null'       => false,
            ],
            
            // Papel do usuário no sistema
            'role' => [
                'type'       => 'ENUM',
                'constraint' => ['super', 'admin', 'user'],
                'default'    => 'user',
                'null'       => false,
            ],
            
            // Status ativo/inativo
            'active' => [
                'type'       => 'TINYINT',
                'constraint' => 1,
                'default'    => 1,
                'null'       => false,
            ],
            
            // Timestamps automáticos
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

        // Chave primária
        $this->forge->addKey('id', true);
        
        // Índice único para email (login)
        $this->forge->addUniqueKey('email');
        
        // Índices para consultas frequentes
        $this->forge->addKey('role');
        $this->forge->addKey('active');

        // Criar tabela com engine InnoDB e charset utf8mb4
        $this->forge->createTable('users', true, [
            'ENGINE'  => 'InnoDB',
            'CHARSET' => 'utf8mb4',
            'COLLATE' => 'utf8mb4_unicode_ci',
        ]);
    }

    /**
     * Reverte a migration (remove a tabela)
     */
    public function down()
    {
        $this->forge->dropTable('users', true);
    }
}
