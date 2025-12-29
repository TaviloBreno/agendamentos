<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

/**
 * ProfessionalsSeeder - Popula a tabela de profissionais com dados de exemplo
 */
class ProfessionalsSeeder extends Seeder
{
    public function run()
    {
        // Busca todas as unidades
        $units = $this->db->table('units')->get()->getResultArray();
        
        if (empty($units)) {
            echo "⚠️ Execute UnitsSeeder primeiro!\n";
            return;
        }

        $professionals = [
            ['name' => 'Dr. Carlos Silva', 'specialty' => 'Clínico Geral', 'email' => 'carlos.silva'],
            ['name' => 'Dra. Maria Santos', 'specialty' => 'Cardiologista', 'email' => 'maria.santos'],
            ['name' => 'Dr. João Oliveira', 'specialty' => 'Ortopedista', 'email' => 'joao.oliveira'],
            ['name' => 'Dra. Ana Costa', 'specialty' => 'Dermatologista', 'email' => 'ana.costa'],
            ['name' => 'Dr. Pedro Almeida', 'specialty' => 'Neurologista', 'email' => 'pedro.almeida'],
            ['name' => 'Dra. Juliana Lima', 'specialty' => 'Pediatra', 'email' => 'juliana.lima'],
            ['name' => 'Dr. Fernando Souza', 'specialty' => 'Fisioterapeuta', 'email' => 'fernando.souza'],
            ['name' => 'Dra. Camila Rocha', 'specialty' => 'Nutricionista', 'email' => 'camila.rocha'],
            ['name' => 'Dr. Ricardo Martins', 'specialty' => 'Psicólogo', 'email' => 'ricardo.martins'],
            ['name' => 'Dra. Patrícia Dias', 'specialty' => 'Ginecologista', 'email' => 'patricia.dias'],
            ['name' => 'Dr. Bruno Ferreira', 'specialty' => 'Oftalmologista', 'email' => 'bruno.ferreira'],
            ['name' => 'Dra. Letícia Mendes', 'specialty' => 'Endocrinologista', 'email' => 'leticia.mendes'],
        ];

        $data = [];
        $profIndex = 0;
        
        foreach ($units as $unit) {
            // Buscar serviços desta unidade
            $services = $this->db->table('services')
                                 ->where('unit_id', $unit['id'])
                                 ->get()
                                 ->getResultArray();
            
            $serviceIds = array_column($services, 'id');
            
            // Cada unidade terá 2-4 profissionais
            $numProfessionals = rand(2, 4);
            
            for ($i = 0; $i < $numProfessionals; $i++) {
                $prof = $professionals[$profIndex % count($professionals)];
                
                // Selecionar 2-4 serviços aleatórios que este profissional atende
                $numServices = min(rand(2, 4), count($serviceIds));
                $selectedServiceIds = [];
                if ($numServices > 0 && !empty($serviceIds)) {
                    $randomKeys = array_rand($serviceIds, $numServices);
                    if (!is_array($randomKeys)) {
                        $randomKeys = [$randomKeys];
                    }
                    foreach ($randomKeys as $key) {
                        $selectedServiceIds[] = $serviceIds[$key];
                    }
                }
                
                $data[] = [
                    'unit_id'     => $unit['id'],
                    'name'        => $prof['name'],
                    'email'       => $prof['email'] . '.' . $unit['id'] . '@clinica.com',
                    'phone'       => '(11) 9' . rand(1000, 9999) . '-' . rand(1000, 9999),
                    'specialty'   => $prof['specialty'],
                    'bio'         => 'Profissional experiente com mais de ' . rand(5, 20) . ' anos de atuação na área de ' . $prof['specialty'] . '.',
                    'services'    => json_encode($selectedServiceIds),
                    'active'      => 1,
                    'created_at'  => date('Y-m-d H:i:s'),
                    'updated_at'  => date('Y-m-d H:i:s'),
                ];
                
                $profIndex++;
            }
        }

        $this->db->table('professionals')->insertBatch($data);

        echo "✅ " . count($data) . " profissionais criados com sucesso!\n";
    }
}
