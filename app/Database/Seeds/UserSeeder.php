<?php

namespace App\Database\Seeds;

use App\Entities\User;
use App\Models\UserModel;
use CodeIgniter\Database\Seeder;

/**
 * UserSeeder - Popula tabela de usuários
 * 
 * =========================================================================
 * PROPÓSITO
 * =========================================================================
 * 
 * Cria usuários iniciais para o sistema:
 * - Super Admin para acesso total
 * - Admin para gerenciamento
 * - Usuário comum para testes
 * 
 * =========================================================================
 * USO
 * =========================================================================
 * 
 * php spark db:seed UserSeeder
 * 
 * CREDENCIAIS PADRÃO:
 * - Super Admin: admin@sistema.com / admin123
 * - Admin: gerente@sistema.com / gerente123
 * - User: usuario@sistema.com / usuario123
 * 
 * ⚠️ IMPORTANTE: Altere as senhas em produção!
 * 
 * @package    App\Database\Seeds
 */
class UserSeeder extends Seeder
{
    public function run()
    {
        $userModel = new UserModel();

        // =====================================================================
        // USUÁRIOS PARA SEED
        // =====================================================================
        
        $users = [
            // Super Admin - Acesso total ao sistema
            [
                'name'     => 'Administrador',
                'email'    => 'admin@sistema.com',
                'password' => 'admin123',
                'role'     => 'super',
                'active'   => 1,
            ],
            
            // Admin - Gerenciamento
            [
                'name'     => 'Gerente',
                'email'    => 'gerente@sistema.com',
                'password' => 'gerente123',
                'role'     => 'admin',
                'active'   => 1,
            ],
            
            // User - Usuário comum
            [
                'name'     => 'Usuário Teste',
                'email'    => 'usuario@sistema.com',
                'password' => 'usuario123',
                'role'     => 'user',
                'active'   => 1,
            ],
            
            // Usuário inativo para testes
            [
                'name'     => 'Inativo Teste',
                'email'    => 'inativo@sistema.com',
                'password' => 'inativo123',
                'role'     => 'user',
                'active'   => 0,
            ],
        ];

        // =====================================================================
        // INSERÇÃO DOS USUÁRIOS
        // =====================================================================
        
        foreach ($users as $userData) {
            // Verifica se o email já existe
            $existing = $userModel->where('email', $userData['email'])->first();
            
            if ($existing !== null) {
                echo "Usuário {$userData['email']} já existe. Pulando...\n";
                continue;
            }

            // Cria Entity para garantir hash da senha
            $user = new User($userData);
            
            // Insere no banco
            if ($userModel->insert($user)) {
                echo "✓ Usuário criado: {$userData['email']} ({$userData['role']})\n";
            } else {
                echo "✗ Erro ao criar {$userData['email']}: " . implode(', ', $userModel->errors()) . "\n";
            }
        }

        echo "\n========================================\n";
        echo "CREDENCIAIS DE ACESSO:\n";
        echo "========================================\n";
        echo "Super Admin: admin@sistema.com / admin123\n";
        echo "Admin:       gerente@sistema.com / gerente123\n";
        echo "User:        usuario@sistema.com / usuario123\n";
        echo "========================================\n";
    }
}
