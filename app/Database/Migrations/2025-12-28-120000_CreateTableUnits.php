<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;
use CodeIgniter\CLI\CLI;

/**
 * Migration: CreateTableUnits
 * 
 * Cria a tabela `units` para armazenar as unidades/filiais/lojas
 * onde os usuários podem realizar agendamentos.
 * 
 * =========================================================================
 * GUIA DE MIGRATIONS NO CODEIGNITER 4
 * =========================================================================
 * 
 * 1. CRIAÇÃO DA MIGRATION:
 *    ----------------------
 *    Comando: php spark make:migration CreateTableUnits
 *    
 *    O CodeIgniter cria automaticamente um arquivo com timestamp único:
 *    app/Database/Migrations/YYYY-MM-DD-HHMMSS_CreateTableUnits.php
 *    
 *    O timestamp garante a ordem de execução das migrations.
 * 
 * 2. ESTRUTURA DA MIGRATION:
 *    -----------------------
 *    - up()   : Executa as alterações (CREATE TABLE, ADD COLUMN, etc.)
 *    - down() : Desfaz as alterações (DROP TABLE, DROP COLUMN, etc.)
 * 
 * 3. CONFIGURAÇÃO DO BANCO DE DADOS:
 *    --------------------------------
 *    Antes de executar, configure o arquivo .env:
 *    
 *    database.default.hostname = localhost
 *    database.default.database = agendamentos
 *    database.default.username = root
 *    database.default.password = root
 *    database.default.DBDriver = MySQLi
 *    database.default.port = 3306
 * 
 * 4. EXECUÇÃO DAS MIGRATIONS:
 *    -------------------------
 *    php spark migrate              → Executa todas as migrations pendentes
 *    php spark migrate:rollback     → Desfaz o último batch de migrations
 *    php spark migrate:refresh      → Rollback + Migrate (recria tudo)
 *    php spark migrate:status       → Mostra status das migrations
 * 
 * 5. TABELA DE CONTROLE:
 *    --------------------
 *    O CodeIgniter cria automaticamente a tabela `migrations` para
 *    controlar quais migrations já foram executadas (batch system).
 * 
 * @package    App\Database\Migrations
 * @author     Sistema de Agendamentos
 * @created    2025-12-28
 */
class CreateTableUnits extends Migration
{
    /**
     * Método UP - Cria a tabela units
     * 
     * Executado quando rodamos: php spark migrate
     * 
     * @return void
     */
    public function up()
    {
        /**
         * Definição dos campos da tabela usando Database Forge
         * 
         * Documentação: https://codeigniter.com/user_guide/dbmgmt/forge.html
         */
        $this->forge->addField([
            
            // ===== CHAVE PRIMÁRIA =====
            'id' => [
                'type'           => 'INT',
                'constraint'     => 11,
                'unsigned'       => true,
                'auto_increment' => true,
                'comment'        => 'ID único da unidade',
            ],
            
            // ===== DADOS DA UNIDADE =====
            'name' => [
                'type'       => 'VARCHAR',
                'constraint' => 70,
                'null'       => false,
                'comment'    => 'Nome da unidade/filial/loja',
            ],
            
            'email' => [
                'type'       => 'VARCHAR',
                'constraint' => 100,
                'null'       => false,
                'comment'    => 'E-mail de contato da unidade',
            ],
            
            'phone' => [
                'type'       => 'VARCHAR',
                'constraint' => 14,
                'null'       => false,
                'comment'    => 'Telefone no formato 99999-9999 ou (99) 99999-9999',
            ],
            
            'coordinator' => [
                'type'       => 'VARCHAR',
                'constraint' => 70,
                'null'       => false,
                'comment'    => 'Nome do coordenador/gerente/diretor responsável',
            ],
            
            'address' => [
                'type'       => 'VARCHAR',
                'constraint' => 255,
                'null'       => false,
                'comment'    => 'Endereço completo da unidade',
            ],
            
            // ===== SERVIÇOS E HORÁRIOS =====
            'services' => [
                'type'    => 'JSON',
                'null'    => true,
                'default' => null,
                'comment' => 'Array JSON com IDs dos serviços oferecidos. Ex: [1, 2, 3]',
            ],
            
            'start_time' => [
                'type'       => 'VARCHAR',
                'constraint' => 5,
                'null'       => false,
                'comment'    => 'Horário de início do expediente. Ex: 08:00',
            ],
            
            'end_time' => [
                'type'       => 'VARCHAR',
                'constraint' => 5,
                'null'       => false,
                'comment'    => 'Horário de fim do expediente. Ex: 18:00',
            ],
            
            'service_time' => [
                'type'       => 'VARCHAR',
                'constraint' => 20,
                'null'       => false,
                'comment'    => 'Tempo/intervalo por atendimento. Formato válido para DateInterval. Ex: 10 minutes, 01:00, 30 minutes',
            ],
            
            // ===== STATUS =====
            'active' => [
                'type'       => 'TINYINT',
                'constraint' => 1,
                'default'    => 0,
                'null'       => false,
                'comment'    => 'Status da unidade: 0 = Inativa, 1 = Ativa',
            ],
            
            // ===== TIMESTAMPS =====
            'created_at' => [
                'type'    => 'DATETIME',
                'null'    => true,
                'default' => null,
                'comment' => 'Data de criação do registro',
            ],
            
            'updated_at' => [
                'type'    => 'DATETIME',
                'null'    => true,
                'default' => null,
                'comment' => 'Data da última atualização',
            ],
        ]);
        
        /**
         * Define a chave primária
         */
        $this->forge->addPrimaryKey('id');
        
        /**
         * Adiciona índices (keys) para otimizar buscas
         * 
         * Índices melhoram a performance em consultas WHERE, ORDER BY e JOIN
         */
        $this->forge->addKey('name', false, false, 'idx_units_name');
        $this->forge->addKey('email', false, false, 'idx_units_email');
        $this->forge->addKey('phone', false, false, 'idx_units_phone');
        $this->forge->addKey('active', false, false, 'idx_units_active');
        
        /**
         * Cria a tabela no banco de dados
         * 
         * Parâmetros:
         * - 'units'   : Nome da tabela
         * - true      : IF NOT EXISTS (evita erro se já existir)
         * - ['ENGINE' => 'InnoDB'] : Define o engine do MySQL
         */
        $this->forge->createTable('units', true, [
            'ENGINE' => 'InnoDB',
            'COMMENT' => 'Tabela de unidades/filiais/lojas para agendamentos',
        ]);
    }

    /**
     * Método DOWN - Remove a tabela units
     * 
     * Executado quando rodamos: php spark migrate:rollback
     * 
     * IMPORTANTE: O rollback desfaz as alterações na ordem inversa.
     * Use com cuidado em produção, pois APAGA TODOS OS DADOS da tabela!
     * 
     * @return void
     */
    public function down()
    {
        /**
         * Remove a tabela do banco de dados
         * 
         * Parâmetros:
         * - 'units' : Nome da tabela a ser removida
         * - true    : IF EXISTS (evita erro se não existir)
         */
        $this->forge->dropTable('units', true);
    }
}

/**
 * =========================================================================
 * COMANDOS ÚTEIS - REFERÊNCIA RÁPIDA
 * =========================================================================
 * 
 * CRIAR MIGRATION:
 * php spark make:migration CreateTableUnits
 * php spark make:migration AddColumnToUsers
 * php spark make:migration CreateTableServices
 * 
 * EXECUTAR MIGRATIONS:
 * php spark migrate                    → Executa todas pendentes
 * php spark migrate -n App             → Executa do namespace App
 * php spark migrate:rollback           → Desfaz último batch
 * php spark migrate:rollback -b 2      → Desfaz batch específico
 * php spark migrate:refresh            → Rollback total + Migrate
 * php spark migrate:status             → Lista status das migrations
 * 
 * CRIAR BANCO NO PHPMYADMIN:
 * 1. Acesse http://localhost/phpmyadmin
 * 2. Clique em "Novo" ou "New"
 * 3. Nome do banco: agendamentos
 * 4. Collation: utf8mb4_general_ci
 * 5. Clique em "Criar"
 * 
 * CONFIGURAR .env:
 * database.default.hostname = localhost
 * database.default.database = agendamentos
 * database.default.username = root
 * database.default.password = root
 * database.default.DBDriver = MySQLi
 * database.default.port = 3306
 * 
 * FLUXO DE TRABALHO TÍPICO:
 * 1. Criar banco no phpMyAdmin
 * 2. Configurar credenciais no .env
 * 3. php spark make:migration CreateTableNome
 * 4. Editar a migration (definir campos)
 * 5. php spark migrate
 * 6. Verificar tabela criada no phpMyAdmin
 * 
 * =========================================================================
 */
