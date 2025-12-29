<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;
use CodeIgniter\Database\RawSql;

/**
 * Migration: CreateTableProfessionals
 * 
 * Cria a tabela de profissionais do sistema de agendamentos.
 * 
 * =========================================================================
 * ESTRUTURA DA TABELA
 * =========================================================================
 * 
 * - id: Chave primária auto-incremento
 * - unit_id: FK para unidade onde o profissional atende
 * - name: Nome completo do profissional
 * - email: E-mail único para contato/login
 * - phone: Telefone de contato
 * - specialty: Especialidade/cargo (ex: Médico, Dentista)
 * - bio: Biografia/descrição do profissional
 * - avatar: Caminho para foto do profissional
 * - services: JSON com IDs dos serviços que o profissional realiza
 * - active: Status do registro (0=inativo, 1=ativo)
 * - created_at: Data de criação (automático)
 * - updated_at: Data de atualização (automático)
 * 
 * @package    App\Database\Migrations
 */
class CreateTableProfessionals extends Migration
{
    /**
     * Executa a migration - cria a tabela
     */
    public function up(): void
    {
        $this->forge->addField([
            'id' => [
                'type'           => 'INT',
                'constraint'     => 11,
                'unsigned'       => true,
                'auto_increment' => true,
            ],
            'unit_id' => [
                'type'       => 'INT',
                'constraint' => 11,
                'unsigned'   => true,
                'null'       => true,
                'comment'    => 'Unidade onde o profissional atende',
            ],
            'name' => [
                'type'       => 'VARCHAR',
                'constraint' => 100,
                'comment'    => 'Nome completo do profissional',
            ],
            'email' => [
                'type'       => 'VARCHAR',
                'constraint' => 100,
                'comment'    => 'E-mail único para contato',
            ],
            'phone' => [
                'type'       => 'VARCHAR',
                'constraint' => 20,
                'null'       => true,
                'comment'    => 'Telefone de contato',
            ],
            'specialty' => [
                'type'       => 'VARCHAR',
                'constraint' => 100,
                'null'       => true,
                'comment'    => 'Especialidade ou cargo',
            ],
            'bio' => [
                'type'    => 'TEXT',
                'null'    => true,
                'comment' => 'Biografia ou descrição',
            ],
            'avatar' => [
                'type'       => 'VARCHAR',
                'constraint' => 255,
                'null'       => true,
                'comment'    => 'Caminho para foto do profissional',
            ],
            'services' => [
                'type'    => 'JSON',
                'null'    => true,
                'comment' => 'Array JSON com IDs dos serviços',
            ],
            'active' => [
                'type'       => 'TINYINT',
                'constraint' => 1,
                'default'    => 1,
                'comment'    => '0=Inativo, 1=Ativo',
            ],
            'created_at' => [
                'type'    => 'DATETIME',
                'null'    => true,
                'default' => new RawSql('CURRENT_TIMESTAMP'),
            ],
            'updated_at' => [
                'type'    => 'DATETIME',
                'null'    => true,
                'default' => new RawSql('CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP'),
            ],
        ]);

        // Chave primária
        $this->forge->addKey('id', true);
        
        // Índices para busca
        $this->forge->addKey('unit_id');
        $this->forge->addKey('active');
        
        // E-mail único
        $this->forge->addUniqueKey('email');

        // Cria a tabela
        $this->forge->createTable('professionals', true);

        // Adiciona FK para units (se existir)
        $this->db->query('
            ALTER TABLE professionals 
            ADD CONSTRAINT fk_professionals_unit 
            FOREIGN KEY (unit_id) REFERENCES units(id) 
            ON DELETE SET NULL ON UPDATE CASCADE
        ');
    }

    /**
     * Reverte a migration - remove a tabela
     */
    public function down(): void
    {
        // Remove FK primeiro
        $this->db->query('ALTER TABLE professionals DROP FOREIGN KEY fk_professionals_unit');
        
        $this->forge->dropTable('professionals', true);
    }
}
