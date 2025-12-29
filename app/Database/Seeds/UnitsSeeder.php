<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

/**
 * UnitsSeeder - Popula a tabela de unidades com dados de exemplo
 */
class UnitsSeeder extends Seeder
{
    public function run()
    {
        $data = [
            [
                'name'       => 'Clínica Centro',
                'email'      => 'centro@clinica.com',
                'phone'      => '(11) 3333-1001',
                'address'    => 'Av. Paulista, 1000',
                'city'       => 'São Paulo',
                'state'      => 'SP',
                'zip_code'   => '01310-100',
                'active'     => 1,
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ],
            [
                'name'       => 'Clínica Zona Sul',
                'email'      => 'zonasul@clinica.com',
                'phone'      => '(11) 3333-1002',
                'address'    => 'Av. Santo Amaro, 500',
                'city'       => 'São Paulo',
                'state'      => 'SP',
                'zip_code'   => '04506-001',
                'active'     => 1,
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ],
            [
                'name'       => 'Clínica Zona Norte',
                'email'      => 'zonanorte@clinica.com',
                'phone'      => '(11) 3333-1003',
                'address'    => 'Av. Tucuruvi, 200',
                'city'       => 'São Paulo',
                'state'      => 'SP',
                'zip_code'   => '02304-002',
                'active'     => 1,
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ],
            [
                'name'       => 'Clínica Campinas',
                'email'      => 'campinas@clinica.com',
                'phone'      => '(19) 3333-2001',
                'address'    => 'Rua Barão de Jaguara, 100',
                'city'       => 'Campinas',
                'state'      => 'SP',
                'zip_code'   => '13015-001',
                'active'     => 1,
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ],
            [
                'name'       => 'Clínica Rio de Janeiro',
                'email'      => 'rio@clinica.com',
                'phone'      => '(21) 3333-3001',
                'address'    => 'Av. Rio Branco, 150',
                'city'       => 'Rio de Janeiro',
                'state'      => 'RJ',
                'zip_code'   => '20040-006',
                'active'     => 1,
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ],
        ];

        // Usando Query Builder para inserir
        $this->db->table('units')->insertBatch($data);

        echo "✅ " . count($data) . " unidades criadas com sucesso!\n";
    }
}
