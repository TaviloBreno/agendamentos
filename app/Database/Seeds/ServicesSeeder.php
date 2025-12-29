<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

/**
 * ServicesSeeder - Popula a tabela de serviços e associa às unidades
 */
class ServicesSeeder extends Seeder
{
    public function run()
    {
        // Lista de serviços disponíveis
        $servicesData = [
            [
                'name'        => 'Consulta Geral',
                'description' => 'Consulta médica geral para avaliação de saúde',
                'duration'    => 30,
                'price'       => 150.00,
                'active'      => 1,
                'created_at'  => date('Y-m-d H:i:s'),
                'updated_at'  => date('Y-m-d H:i:s'),
            ],
            [
                'name'        => 'Consulta Especializada',
                'description' => 'Consulta com médico especialista',
                'duration'    => 45,
                'price'       => 250.00,
                'active'      => 1,
                'created_at'  => date('Y-m-d H:i:s'),
                'updated_at'  => date('Y-m-d H:i:s'),
            ],
            [
                'name'        => 'Exame de Rotina',
                'description' => 'Check-up completo com exames laboratoriais',
                'duration'    => 60,
                'price'       => 350.00,
                'active'      => 1,
                'created_at'  => date('Y-m-d H:i:s'),
                'updated_at'  => date('Y-m-d H:i:s'),
            ],
            [
                'name'        => 'Fisioterapia',
                'description' => 'Sessão de fisioterapia para reabilitação',
                'duration'    => 50,
                'price'       => 120.00,
                'active'      => 1,
                'created_at'  => date('Y-m-d H:i:s'),
                'updated_at'  => date('Y-m-d H:i:s'),
            ],
            [
                'name'        => 'Massagem Terapêutica',
                'description' => 'Massagem relaxante e terapêutica',
                'duration'    => 60,
                'price'       => 180.00,
                'active'      => 1,
                'created_at'  => date('Y-m-d H:i:s'),
                'updated_at'  => date('Y-m-d H:i:s'),
            ],
            [
                'name'        => 'Acupuntura',
                'description' => 'Sessão de acupuntura tradicional chinesa',
                'duration'    => 45,
                'price'       => 150.00,
                'active'      => 1,
                'created_at'  => date('Y-m-d H:i:s'),
                'updated_at'  => date('Y-m-d H:i:s'),
            ],
            [
                'name'        => 'Nutrição',
                'description' => 'Consulta com nutricionista',
                'duration'    => 40,
                'price'       => 200.00,
                'active'      => 1,
                'created_at'  => date('Y-m-d H:i:s'),
                'updated_at'  => date('Y-m-d H:i:s'),
            ],
            [
                'name'        => 'Psicologia',
                'description' => 'Sessão de psicoterapia',
                'duration'    => 50,
                'price'       => 220.00,
                'active'      => 1,
                'created_at'  => date('Y-m-d H:i:s'),
                'updated_at'  => date('Y-m-d H:i:s'),
            ],
        ];

        // Inserir serviços
        $this->db->table('services')->insertBatch($servicesData);
        echo "✅ " . count($servicesData) . " serviços criados com sucesso!\n";

        // Buscar os IDs dos serviços inseridos
        $services = $this->db->table('services')->get()->getResultArray();
        $serviceIds = array_column($services, 'id');

        // Associar serviços às unidades
        $units = $this->db->table('units')->get()->getResultArray();
        
        if (!empty($units)) {
            foreach ($units as $unit) {
                // Cada unidade terá 4-6 serviços aleatórios
                $numServices = min(rand(4, 6), count($serviceIds));
                $randomKeys = array_rand($serviceIds, $numServices);
                
                if (!is_array($randomKeys)) {
                    $randomKeys = [$randomKeys];
                }
                
                $selectedServiceIds = [];
                foreach ($randomKeys as $key) {
                    $selectedServiceIds[] = (int) $serviceIds[$key];
                }
                
                // Atualizar a unidade com os serviços associados
                $this->db->table('units')
                    ->where('id', $unit['id'])
                    ->update(['services' => json_encode($selectedServiceIds)]);
            }
            echo "✅ Serviços associados a " . count($units) . " unidades!\n";
        }
    }
}
