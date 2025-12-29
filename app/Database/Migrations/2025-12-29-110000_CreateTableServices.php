<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

/**
 * Migration: Criar tabela de serviços
 * 
 * =========================================================================
 * ESTRUTURA DA TABELA SERVICES
 * =========================================================================
 * 
 * Tabela para cadastro de serviços oferecidos pelas unidades.
 * Os serviços são associados às unidades através do campo JSON
 * 'services' na tabela units.
 * 
 * CAMPOS:
 * - id          → Chave primária auto-incremento
 * - name        → Nome do serviço (ex: "Corte de Cabelo")
 * - description → Descrição detalhada do serviço
 * - duration    → Duração em minutos (ex: 30, 60, 90)
 * - price       → Preço do serviço em decimal
 * - active      → Status (1=ativo, 0=inativo)
 * - timestamps  → created_at, updated_at, deleted_at
 * 
 * ÍNDICES:
 * - PRIMARY KEY (id)
 * - INDEX (active)  → Filtragem por status
 * - INDEX (name)    → Busca por nome
 * 
 * @package    App\Database\Migrations
 */
class CreateTableServices extends Migration
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
            
            // Nome do serviço
            'name' => [
                'type'       => 'VARCHAR',
                'constraint' => 100,
                'null'       => false,
            ],
            
            // Descrição do serviço
            'description' => [
                'type' => 'TEXT',
                'null' => true,
            ],
            
            // Duração em minutos
            'duration' => [
                'type'       => 'INT',
                'constraint' => 11,
                'unsigned'   => true,
                'null'       => false,
                'default'    => 30,
                'comment'    => 'Duração em minutos',
            ],
            
            // Preço do serviço
            'price' => [
                'type'       => 'DECIMAL',
                'constraint' => '10,2',
                'null'       => false,
                'default'    => '0.00',
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
        
        // Índices para consultas frequentes
        $this->forge->addKey('active');
        $this->forge->addKey('name');

        // Criar tabela com engine InnoDB e charset utf8mb4
        $this->forge->createTable('services', true, [
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
        $this->forge->dropTable('services', true);
    }
}
