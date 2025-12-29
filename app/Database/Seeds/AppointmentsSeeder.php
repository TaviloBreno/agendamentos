<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;
use CodeIgniter\I18n\Time;

/**
 * AppointmentsSeeder - Popula a tabela de agendamentos com dados de exemplo
 */
class AppointmentsSeeder extends Seeder
{
    public function run()
    {
        // Buscar dados necessários
        $professionals = $this->db->table('professionals')->where('active', 1)->get()->getResultArray();
        $clients = $this->db->table('clients')->where('active', 1)->get()->getResultArray();
        $services = $this->db->table('services')->where('active', 1)->get()->getResultArray();
        
        if (empty($professionals)) {
            echo "⚠️ Execute ProfessionalsSeeder primeiro!\n";
            return;
        }
        
        if (empty($clients)) {
            echo "⚠️ Execute ClientsSeeder primeiro!\n";
            return;
        }
        
        if (empty($services)) {
            echo "⚠️ Execute ServicesSeeder primeiro!\n";
            return;
        }

        $data = [];
        
        // Criar agendamentos para os últimos 60 dias e próximos 30 dias
        $startDate = Time::now()->subDays(60);
        $endDate = Time::now()->addDays(30);
        
        $currentDate = $startDate;
        
        while ($currentDate <= $endDate) {
            // Pular finais de semana
            if ($currentDate->getDayOfWeek() !== 0 && $currentDate->getDayOfWeek() !== 6) {
                
                // Criar 8-20 agendamentos por dia útil
                $numAppointments = rand(8, 20);
                
                for ($i = 0; $i < $numAppointments; $i++) {
                    // Selecionar profissional aleatório
                    $professional = $professionals[array_rand($professionals)];
                    
                    // Buscar serviços que este profissional atende
                    $profServices = json_decode($professional['services'] ?? '[]', true);
                    if (empty($profServices)) {
                        // Se não tem serviços definidos, pegar aleatório
                        $service = $services[array_rand($services)];
                    } else {
                        // Selecionar um serviço que o profissional atende
                        $serviceId = $profServices[array_rand($profServices)];
                        $service = null;
                        foreach ($services as $s) {
                            if ($s['id'] == $serviceId) {
                                $service = $s;
                                break;
                            }
                        }
                        if (!$service) {
                            $service = $services[array_rand($services)];
                        }
                    }
                    
                    // Selecionar cliente aleatório
                    $client = $clients[array_rand($clients)];
                    
                    // Gerar horário aleatório entre 8h e 17h
                    $hour = rand(8, 17);
                    $minute = rand(0, 1) * 30; // 0 ou 30 minutos
                    
                    $startTime = sprintf('%02d:%02d:00', $hour, $minute);
                    $duration = $service['duration'] ?? 30;
                    $endTime = Time::parse($currentDate->format('Y-m-d') . ' ' . $startTime)
                                   ->addMinutes($duration)
                                   ->format('H:i:s');
                    
                    // Determinar status baseado na data
                    $dateStr = $currentDate->format('Y-m-d');
                    $today = Time::now()->format('Y-m-d');
                    
                    if ($dateStr < $today) {
                        // Agendamentos passados: 75% completed, 15% cancelled, 10% no-show
                        $rand = rand(1, 100);
                        if ($rand <= 75) {
                            $status = 'completed';
                        } elseif ($rand <= 90) {
                            $status = 'cancelled';
                        } else {
                            $status = 'no_show';
                        }
                    } elseif ($dateStr === $today) {
                        // Agendamentos de hoje: 40% confirmed, 30% completed, 20% scheduled, 10% cancelled
                        $rand = rand(1, 100);
                        if ($rand <= 40) {
                            $status = 'confirmed';
                        } elseif ($rand <= 70) {
                            $status = 'completed';
                        } elseif ($rand <= 90) {
                            $status = 'scheduled';
                        } else {
                            $status = 'cancelled';
                        }
                    } else {
                        // Agendamentos futuros: 50% scheduled, 45% confirmed, 5% cancelled
                        $rand = rand(1, 100);
                        if ($rand <= 50) {
                            $status = 'scheduled';
                        } elseif ($rand <= 95) {
                            $status = 'confirmed';
                        } else {
                            $status = 'cancelled';
                        }
                    }
                    
                    // Notas opcionais
                    $notes = null;
                    if (rand(1, 10) <= 2) {
                        $noteOptions = [
                            'Primeira consulta do paciente.',
                            'Retorno para avaliação.',
                            'Paciente solicitou encaixe.',
                            'Confirmar resultados de exames.',
                            'Paciente preferencial.',
                            'Acompanhante necessário.',
                            'Trazer exames anteriores.',
                            'Consulta de rotina.',
                        ];
                        $notes = $noteOptions[array_rand($noteOptions)];
                    }
                    
                    $data[] = [
                        'unit_id'         => $professional['unit_id'],
                        'professional_id' => $professional['id'],
                        'service_id'      => $service['id'],
                        'client_name'     => $client['name'],
                        'client_email'    => $client['email'],
                        'client_phone'    => $client['phone'],
                        'date'            => $dateStr,
                        'start_time'      => $startTime,
                        'end_time'        => $endTime,
                        'status'          => $status,
                        'notes'           => $notes,
                        'created_at'      => $currentDate->subDays(rand(1, 14))->format('Y-m-d H:i:s'),
                        'updated_at'      => date('Y-m-d H:i:s'),
                    ];
                }
            }
            
            $currentDate = $currentDate->addDays(1);
        }

        // Inserir em lotes de 100
        $chunks = array_chunk($data, 100);
        foreach ($chunks as $chunk) {
            $this->db->table('appointments')->insertBatch($chunk);
        }

        echo "✅ " . count($data) . " agendamentos criados com sucesso!\n";
    }
}
