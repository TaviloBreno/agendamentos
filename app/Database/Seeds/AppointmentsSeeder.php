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
        $professionals = $this->db->table('professionals')->get()->getResultArray();
        $clients = $this->db->table('clients')->get()->getResultArray();
        $services = $this->db->table('services')->get()->getResultArray();
        
        if (empty($professionals) || empty($clients) || empty($services)) {
            echo "⚠️ Execute os seeders de Unidades, Serviços, Profissionais e Clientes primeiro!\n";
            return;
        }

        $statuses = ['scheduled', 'confirmed', 'completed', 'cancelled'];
        $data = [];
        
        // Criar agendamentos para os próximos 30 dias e últimos 30 dias
        $startDate = Time::now()->subDays(30);
        $endDate = Time::now()->addDays(30);
        
        $currentDate = $startDate;
        
        while ($currentDate <= $endDate) {
            // Pular finais de semana
            if ($currentDate->getDayOfWeek() !== 0 && $currentDate->getDayOfWeek() !== 6) {
                
                // Criar 5-15 agendamentos por dia útil
                $numAppointments = rand(5, 15);
                
                for ($i = 0; $i < $numAppointments; $i++) {
                    // Selecionar profissional aleatório
                    $professional = $professionals[array_rand($professionals)];
                    
                    // Buscar serviços que este profissional atende
                    $profServices = json_decode($professional['services'] ?? '[]', true);
                    if (empty($profServices)) {
                        continue;
                    }
                    
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
                        continue;
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
                        // Agendamentos passados: 70% completed, 20% cancelled, 10% no-show
                        $rand = rand(1, 100);
                        if ($rand <= 70) {
                            $status = 'completed';
                        } elseif ($rand <= 90) {
                            $status = 'cancelled';
                        } else {
                            $status = 'no_show';
                        }
                    } else {
                        // Agendamentos futuros: 60% scheduled, 40% confirmed
                        $status = rand(1, 100) <= 60 ? 'scheduled' : 'confirmed';
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
                        'notes'           => rand(1, 10) <= 2 ? 'Observação do agendamento #' . count($data) : null,
                        'created_at'      => date('Y-m-d H:i:s'),
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
