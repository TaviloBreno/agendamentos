<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;
use CodeIgniter\Database\RawSql;

/**
 * Migration: CreateTableAppointments
 * 
 * Cria a tabela de agendamentos do sistema.
 * 
 * =========================================================================
 * ESTRUTURA DA TABELA
 * =========================================================================
 * 
 * - id: Chave primária auto-incremento
 * - unit_id: FK para unidade do agendamento
 * - professional_id: FK para profissional que atenderá
 * - service_id: FK para serviço agendado
 * - client_name: Nome do cliente
 * - client_email: E-mail do cliente
 * - client_phone: Telefone do cliente
 * - date: Data do agendamento
 * - start_time: Horário de início
 * - end_time: Horário de término
 * - status: Status (scheduled, confirmed, completed, cancelled, no_show)
 * - notes: Observações do agendamento
 * - created_at: Data de criação
 * - updated_at: Data de atualização
 * 
 * @package    App\Database\Migrations
 */
class CreateTableAppointments extends Migration
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
                'comment'    => 'Unidade do agendamento',
            ],
            'professional_id' => [
                'type'       => 'INT',
                'constraint' => 11,
                'unsigned'   => true,
                'comment'    => 'Profissional que atenderá',
            ],
            'service_id' => [
                'type'       => 'INT',
                'constraint' => 11,
                'unsigned'   => true,
                'comment'    => 'Serviço agendado',
            ],
            'client_name' => [
                'type'       => 'VARCHAR',
                'constraint' => 100,
                'comment'    => 'Nome do cliente',
            ],
            'client_email' => [
                'type'       => 'VARCHAR',
                'constraint' => 100,
                'comment'    => 'E-mail do cliente',
            ],
            'client_phone' => [
                'type'       => 'VARCHAR',
                'constraint' => 20,
                'null'       => true,
                'comment'    => 'Telefone do cliente',
            ],
            'date' => [
                'type'    => 'DATE',
                'comment' => 'Data do agendamento',
            ],
            'start_time' => [
                'type'    => 'TIME',
                'comment' => 'Horário de início',
            ],
            'end_time' => [
                'type'    => 'TIME',
                'comment' => 'Horário de término',
            ],
            'status' => [
                'type'       => 'ENUM',
                'constraint' => ['scheduled', 'confirmed', 'completed', 'cancelled', 'no_show'],
                'default'    => 'scheduled',
                'comment'    => 'Status do agendamento',
            ],
            'notes' => [
                'type'    => 'TEXT',
                'null'    => true,
                'comment' => 'Observações',
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
        
        // Índices para busca e filtros
        $this->forge->addKey('unit_id');
        $this->forge->addKey('professional_id');
        $this->forge->addKey('service_id');
        $this->forge->addKey('date');
        $this->forge->addKey('status');
        
        // Índice composto para busca de agenda
        $this->forge->addKey(['professional_id', 'date', 'start_time']);

        // Cria a tabela
        $this->forge->createTable('appointments', true);

        // Adiciona FKs
        $this->db->query('
            ALTER TABLE appointments 
            ADD CONSTRAINT fk_appointments_unit 
            FOREIGN KEY (unit_id) REFERENCES units(id) 
            ON DELETE CASCADE ON UPDATE CASCADE
        ');
        
        $this->db->query('
            ALTER TABLE appointments 
            ADD CONSTRAINT fk_appointments_professional 
            FOREIGN KEY (professional_id) REFERENCES professionals(id) 
            ON DELETE CASCADE ON UPDATE CASCADE
        ');
        
        $this->db->query('
            ALTER TABLE appointments 
            ADD CONSTRAINT fk_appointments_service 
            FOREIGN KEY (service_id) REFERENCES services(id) 
            ON DELETE CASCADE ON UPDATE CASCADE
        ');
    }

    /**
     * Reverte a migration - remove a tabela
     */
    public function down(): void
    {
        // Remove FKs primeiro
        $this->db->query('ALTER TABLE appointments DROP FOREIGN KEY fk_appointments_unit');
        $this->db->query('ALTER TABLE appointments DROP FOREIGN KEY fk_appointments_professional');
        $this->db->query('ALTER TABLE appointments DROP FOREIGN KEY fk_appointments_service');
        
        $this->forge->dropTable('appointments', true);
    }
}
