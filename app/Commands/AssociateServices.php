<?php

namespace App\Commands;

use CodeIgniter\CLI\BaseCommand;
use CodeIgniter\CLI\CLI;

/**
 * AssociateServices - Associa serviços às unidades
 * 
 * Uso: php spark services:associate
 * 
 * Este comando associa todos os serviços ativos a todas as unidades ativas.
 * Útil para corrigir dados existentes ou após rodar migrations.
 */
class AssociateServices extends BaseCommand
{
    protected $group       = 'App';
    protected $name        = 'services:associate';
    protected $description = 'Associa serviços às unidades existentes';
    protected $usage       = 'services:associate [--all]';
    protected $arguments   = [];
    protected $options     = [
        '--all' => 'Associa TODOS os serviços a TODAS as unidades',
    ];

    public function run(array $params)
    {
        $db = \Config\Database::connect();
        
        // Buscar serviços ativos
        $services = $db->table('services')
            ->where('active', 1)
            ->get()
            ->getResultArray();
        
        if (empty($services)) {
            CLI::error('Nenhum serviço ativo encontrado!');
            return;
        }
        
        $serviceIds = array_column($services, 'id');
        CLI::write('Encontrados ' . count($serviceIds) . ' serviços ativos.', 'green');
        
        // Buscar unidades ativas
        $units = $db->table('units')
            ->where('active', 1)
            ->get()
            ->getResultArray();
        
        if (empty($units)) {
            CLI::error('Nenhuma unidade ativa encontrada!');
            return;
        }
        
        CLI::write('Encontradas ' . count($units) . ' unidades ativas.', 'green');
        
        $associateAll = CLI::getOption('all');
        
        foreach ($units as $unit) {
            $currentServices = [];
            
            if (!empty($unit['services'])) {
                $currentServices = json_decode($unit['services'], true) ?? [];
            }
            
            if ($associateAll) {
                // Associar todos os serviços
                $newServices = array_map('intval', $serviceIds);
            } else {
                // Associar serviços aleatórios (4-6 serviços)
                $numServices = min(rand(4, 6), count($serviceIds));
                $randomKeys = array_rand($serviceIds, $numServices);
                
                if (!is_array($randomKeys)) {
                    $randomKeys = [$randomKeys];
                }
                
                $newServices = [];
                foreach ($randomKeys as $key) {
                    $newServices[] = (int) $serviceIds[$key];
                }
            }
            
            // Mesclar com serviços existentes e remover duplicatas
            $finalServices = array_unique(array_merge($currentServices, $newServices));
            $finalServices = array_values($finalServices);
            
            // Atualizar a unidade
            $db->table('units')
                ->where('id', $unit['id'])
                ->update(['services' => json_encode($finalServices)]);
            
            CLI::write("  ✅ {$unit['name']}: " . count($finalServices) . " serviços associados", 'yellow');
        }
        
        CLI::newLine();
        CLI::write('Serviços associados com sucesso a todas as unidades!', 'green');
    }
}
