<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

/**
 * ServicesSeeder - Popula a tabela de serviços com dados de exemplo
 */
class ServicesSeeder extends Seeder
{
    public function run()
    {
        // Busca todas as unidades
        $units = $this->db->table('units')->get()->getResultArray();
        
        if (empty($units)) {
            echo "⚠️ Execute UnitsSeeder primeiro!\n";
            return;
        }

        $servicesTemplate = [
            [
                'name'        => 'Consulta Geral',
                'description' => 'Consulta médica geral para avaliação de saúde',
                'duration'    => 30,
                'price'       => 150.00,
            ],
            [
                'name'        => 'Consulta Especializada',
                'description' => 'Consulta com médico especialista',
                'duration'    => 45,
                'price'       => 250.00,
            ],
            [
                'name'        => 'Exame de Rotina',
                'description' => 'Check-up completo com exames laboratoriais',
                'duration'    => 60,
                'price'       => 350.00,
            ],
            [
                'name'        => 'Fisioterapia',
                'description' => 'Sessão de fisioterapia para reabilitação',
                'duration'    => 50,
                'price'       => 120.00,
            ],
            [
                'name'        => 'Massagem Terapêutica',
                'description' => 'Massagem relaxante e terapêutica',
                'duration'    => 60,
                'price'       => 180.00,
            ],
            [
                'name'        => 'Acupuntura',
                'description' => 'Sessão de acupuntura tradicional chinesa',
                'duration'    => 45,
                'price'       => 150.00,
            ],
            [
                'name'        => 'Nutrição',
                'description' => 'Consulta com nutricionista',
                'duration'    => 40,
                'price'       => 200.00,
            ],
            [
                'name'        => 'Psicologia',
                'description' => 'Sessão de psicoterapia',
                'duration'    => 50,
                'price'       => 220.00,
            ],
        ];

        $data = [];
        foreach ($units as $unit) {
            // Cada unidade terá 4-6 serviços aleatórios
            $numServices = rand(4, 6);
            $selectedServices = array_rand($servicesTemplate, $numServices);
            
            foreach ($selectedServices as $index) {
                $service = $servicesTemplate[$index];
                $data[] = [
                    'unit_id'     => $unit['id'],
                    'name'        => $service['name'],
                    'description' => $service['description'],
                    'duration'    => $service['duration'],
                    'price'       => $service['price'],
                    'active'      => 1,
                    'created_at'  => date('Y-m-d H:i:s'),
                    'updated_at'  => date('Y-m-d H:i:s'),
                ];
            }
        }

        $this->db->table('services')->insertBatch($data);

        echo "✅ " . count($data) . " serviços criados com sucesso!\n";
    }
}
