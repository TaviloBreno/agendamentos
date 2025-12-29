<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;
use CodeIgniter\Database\RawSql;

/**
 * Migration: CreateTableClients
 * 
 * =========================================================================
 * PROPÓSITO
 * =========================================================================
 * 
 * Cria a tabela `clients` para armazenar os clientes que fazem agendamentos.
 * 
 * ESTRUTURA:
 * - id: Chave primária auto-incremento
 * - name: Nome completo do cliente
 * - email: Email único para contato
 * - phone: Telefone de contato
 * - cpf: CPF único do cliente (opcional)
 * - birth_date: Data de nascimento
 * - gender: Gênero (M/F/O)
 * - address: Endereço completo
 * - city: Cidade
 * - state: Estado (UF)
 * - zip_code: CEP
 * - notes: Observações sobre o cliente
 * - active: Status (ativo/inativo)
 * - created_at/updated_at/deleted_at: Timestamps
 */
class CreateTableClients extends Migration
{
    public function up(): void
    {
        $this->forge->addField([
            // Chave primária
            'id' => [
                'type'           => 'INT',
                'constraint'     => 11,
                'unsigned'       => true,
                'auto_increment' => true,
            ],
            
            // Nome completo do cliente
            'name' => [
                'type'       => 'VARCHAR',
                'constraint' => 150,
                'null'       => false,
            ],
            
            // Email único para contato
            'email' => [
                'type'       => 'VARCHAR',
                'constraint' => 150,
                'null'       => false,
            ],
            
            // Telefone de contato
            'phone' => [
                'type'       => 'VARCHAR',
                'constraint' => 20,
                'null'       => true,
            ],
            
            // CPF único (opcional)
            'cpf' => [
                'type'       => 'VARCHAR',
                'constraint' => 14,
                'null'       => true,
            ],
            
            // Data de nascimento
            'birth_date' => [
                'type' => 'DATE',
                'null' => true,
            ],
            
            // Gênero (M = Masculino, F = Feminino, O = Outro)
            'gender' => [
                'type'       => 'ENUM',
                'constraint' => ['M', 'F', 'O'],
                'null'       => true,
            ],
            
            // Endereço completo
            'address' => [
                'type'       => 'VARCHAR',
                'constraint' => 255,
                'null'       => true,
            ],
            
            // Cidade
            'city' => [
                'type'       => 'VARCHAR',
                'constraint' => 100,
                'null'       => true,
            ],
            
            // Estado (UF)
            'state' => [
                'type'       => 'CHAR',
                'constraint' => 2,
                'null'       => true,
            ],
            
            // CEP
            'zip_code' => [
                'type'       => 'VARCHAR',
                'constraint' => 10,
                'null'       => true,
            ],
            
            // Observações sobre o cliente
            'notes' => [
                'type' => 'TEXT',
                'null' => true,
            ],
            
            // Status ativo/inativo
            'active' => [
                'type'       => 'TINYINT',
                'constraint' => 1,
                'default'    => 1,
                'null'       => false,
            ],
            
            // Timestamps
            'created_at' => [
                'type'    => 'DATETIME',
                'null'    => false,
                'default' => new RawSql('CURRENT_TIMESTAMP'),
            ],
            'updated_at' => [
                'type'    => 'DATETIME',
                'null'    => true,
            ],
            'deleted_at' => [
                'type'    => 'DATETIME',
                'null'    => true,
            ],
        ]);

        // Definir chave primária
        $this->forge->addKey('id', true);
        
        // Índices únicos
        $this->forge->addUniqueKey('email', 'uk_clients_email');
        $this->forge->addUniqueKey('cpf', 'uk_clients_cpf');
        
        // Índices para busca
        $this->forge->addKey('name', false, false, 'idx_clients_name');
        $this->forge->addKey('phone', false, false, 'idx_clients_phone');
        $this->forge->addKey('active', false, false, 'idx_clients_active');

        // Criar tabela com charset UTF-8
        $this->forge->createTable('clients', true, [
            'ENGINE' => 'InnoDB',
        ]);
    }

    public function down(): void
    {
        $this->forge->dropTable('clients', true);
    }
}
