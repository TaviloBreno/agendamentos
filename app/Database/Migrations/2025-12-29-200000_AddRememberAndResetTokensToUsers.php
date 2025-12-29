<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

/**
 * Migration: Adicionar campos de Lembrar-me e Recuperação de Senha
 * 
 * =========================================================================
 * NOVOS CAMPOS NA TABELA USERS
 * =========================================================================
 * 
 * - remember_token    → Token para funcionalidade "Lembrar-me"
 * - reset_token       → Token para recuperação de senha
 * - reset_expires_at  → Data de expiração do token de reset
 * 
 * @package    App\Database\Migrations
 */
class AddRememberAndResetTokensToUsers extends Migration
{
    /**
     * Executa a migration (adiciona os campos)
     */
    public function up()
    {
        $this->forge->addColumn('users', [
            // Token "Lembrar-me" (cookie persistente)
            'remember_token' => [
                'type'       => 'VARCHAR',
                'constraint' => 64,
                'null'       => true,
                'after'      => 'active',
            ],
            
            // Token para recuperação de senha
            'reset_token' => [
                'type'       => 'VARCHAR',
                'constraint' => 64,
                'null'       => true,
                'after'      => 'remember_token',
            ],
            
            // Data de expiração do token de reset
            'reset_expires_at' => [
                'type' => 'DATETIME',
                'null' => true,
                'after' => 'reset_token',
            ],
        ]);
        
        // Adicionar índice no remember_token para busca rápida
        $this->forge->addKey('remember_token', false, false, 'idx_remember_token');
        $this->forge->addKey('reset_token', false, false, 'idx_reset_token');
    }

    /**
     * Reverte a migration (remove os campos)
     */
    public function down()
    {
        $this->forge->dropColumn('users', ['remember_token', 'reset_token', 'reset_expires_at']);
    }
}
