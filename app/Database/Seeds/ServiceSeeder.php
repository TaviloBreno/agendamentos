<?php

namespace App\Database\Seeds;

use App\Entities\Service;
use App\Models\ServiceModel;
use CodeIgniter\Database\Seeder;

/**
 * ServiceSeeder - Popula tabela de serviços
 * 
 * =========================================================================
 * PROPÓSITO
 * =========================================================================
 * 
 * Cria serviços de exemplo para o sistema.
 * 
 * =========================================================================
 * USO
 * =========================================================================
 * 
 * php spark db:seed ServiceSeeder
 * 
 * @package    App\Database\Seeds
 */
class ServiceSeeder extends Seeder
{
    public function run()
    {
        $serviceModel = new ServiceModel();

        // =====================================================================
        // SERVIÇOS PARA SEED
        // =====================================================================
        
        $services = [
            [
                'name'        => 'Consulta Geral',
                'description' => 'Consulta de rotina com profissional de saúde para avaliação geral.',
                'duration'    => 30,
                'price'       => 150.00,
                'active'      => 1,
            ],
            [
                'name'        => 'Consulta Especializada',
                'description' => 'Consulta com especialista para diagnóstico e tratamento específico.',
                'duration'    => 45,
                'price'       => 250.00,
                'active'      => 1,
            ],
            [
                'name'        => 'Exame de Rotina',
                'description' => 'Exames laboratoriais e de imagem para check-up.',
                'duration'    => 60,
                'price'       => 100.00,
                'active'      => 1,
            ],
            [
                'name'        => 'Fisioterapia',
                'description' => 'Sessão de fisioterapia para reabilitação física.',
                'duration'    => 60,
                'price'       => 120.00,
                'active'      => 1,
            ],
            [
                'name'        => 'Avaliação Nutricional',
                'description' => 'Avaliação completa com nutricionista e plano alimentar personalizado.',
                'duration'    => 45,
                'price'       => 180.00,
                'active'      => 1,
            ],
            [
                'name'        => 'Terapia Psicológica',
                'description' => 'Sessão de psicoterapia individual.',
                'duration'    => 50,
                'price'       => 200.00,
                'active'      => 1,
            ],
            [
                'name'        => 'Massagem Relaxante',
                'description' => 'Massagem terapêutica para relaxamento e alívio de tensões.',
                'duration'    => 60,
                'price'       => 90.00,
                'active'      => 1,
            ],
            [
                'name'        => 'Acupuntura',
                'description' => 'Sessão de acupuntura para tratamento de dores e equilíbrio energético.',
                'duration'    => 45,
                'price'       => 130.00,
                'active'      => 1,
            ],
            [
                'name'        => 'Retorno',
                'description' => 'Consulta de retorno para acompanhamento.',
                'duration'    => 15,
                'price'       => 80.00,
                'active'      => 1,
            ],
            [
                'name'        => 'Procedimento Ambulatorial',
                'description' => 'Pequenos procedimentos realizados em consultório.',
                'duration'    => 90,
                'price'       => 350.00,
                'active'      => 0, // Inativo para testes
            ],
        ];

        // =====================================================================
        // INSERÇÃO DOS SERVIÇOS
        // =====================================================================
        
        foreach ($services as $serviceData) {
            // Verifica se o serviço já existe
            $existing = $serviceModel->where('name', $serviceData['name'])->first();
            
            if ($existing !== null) {
                echo "Serviço '{$serviceData['name']}' já existe. Pulando...\n";
                continue;
            }

            // Insere no banco
            if ($serviceModel->insert($serviceData)) {
                $status = $serviceData['active'] ? 'ativo' : 'inativo';
                echo "✓ Serviço criado: {$serviceData['name']} ({$status})\n";
            } else {
                echo "✗ Erro ao criar '{$serviceData['name']}': " . implode(', ', $serviceModel->errors()) . "\n";
            }
        }

        echo "\n========================================\n";
        echo "Seed de serviços concluído!\n";
        echo "========================================\n";
    }
}
