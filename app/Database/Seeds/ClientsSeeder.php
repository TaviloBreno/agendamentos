<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;
use Faker\Factory;

/**
 * ClientsSeeder - Popula a tabela de clientes com dados de exemplo
 */
class ClientsSeeder extends Seeder
{
    public function run()
    {
        $faker = Factory::create('pt_BR');

        $data = [];
        $numClients = 100; // Criar 100 clientes

        for ($i = 0; $i < $numClients; $i++) {
            $gender = $faker->randomElement(['M', 'F']);
            $firstName = $gender === 'M' ? $faker->firstNameMale : $faker->firstNameFemale;
            $lastName = $faker->lastName;
            
            $data[] = [
                'name'       => $firstName . ' ' . $lastName,
                'email'      => strtolower($faker->unique()->userName) . '@email.com',
                'phone'      => $faker->cellphoneNumber,
                'cpf'        => $faker->cpf(false),
                'birth_date' => $faker->dateTimeBetween('-70 years', '-18 years')->format('Y-m-d'),
                'gender'     => $gender,
                'address'    => $faker->streetAddress,
                'city'       => $faker->city,
                'state'      => $faker->stateAbbr,
                'zip_code'   => $faker->postcode,
                'notes'      => $faker->optional(0.3)->sentence,
                'active'     => 1,
                'created_at' => $faker->dateTimeBetween('-1 year', 'now')->format('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ];
        }

        $this->db->table('clients')->insertBatch($data);

        echo "✅ " . count($data) . " clientes criados com sucesso!\n";
    }
}
