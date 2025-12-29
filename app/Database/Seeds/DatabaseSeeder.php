<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

/**
 * DatabaseSeeder - Seeder principal que executa todos os seeders na ordem correta
 * 
 * Uso: php spark db:seed DatabaseSeeder
 */
class DatabaseSeeder extends Seeder
{
    public function run()
    {
        echo "\n🚀 Iniciando população do banco de dados...\n\n";
        
        // Ordem de execução é importante devido às dependências
        $seeders = [
            'UserSeeder',         // Usuários do sistema (admin, gerente, etc)
            'UnitsSeeder',        // Unidades/Clínicas
            'ServicesSeeder',     // Serviços (depende de Units)
            'ProfessionalsSeeder', // Profissionais (depende de Units e Services)
            'ClientsSeeder',      // Clientes
            'AppointmentsSeeder', // Agendamentos (depende de todos acima)
        ];
        
        foreach ($seeders as $seeder) {
            echo "📦 Executando {$seeder}...\n";
            $this->call($seeder);
        }
        
        echo "\n✅ Banco de dados populado com sucesso!\n\n";
    }
}
