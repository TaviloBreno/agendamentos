<?php

namespace App\Models;

use App\Entities\Appointment;
use CodeIgniter\Model;

/**
 * AppointmentModel - Model para operações com Agendamentos
 * 
 * =========================================================================
 * RESPONSABILIDADES
 * =========================================================================
 * 
 * - CRUD de agendamentos
 * - Validação de dados
 * - Queries para calendário e listagem
 * - Verificação de conflitos de horário
 * 
 * @package    App\Models
 */
class AppointmentModel extends Model
{
    protected $table            = 'appointments';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = Appointment::class;
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    
    protected $allowedFields = [
        'unit_id',
        'professional_id',
        'service_id',
        'client_name',
        'client_email',
        'client_phone',
        'date',
        'start_time',
        'end_time',
        'status',
        'notes',
    ];

    // Dates
    protected $useTimestamps = true;
    protected $dateFormat    = 'datetime';
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';

    // Validation
    protected $validationRules = [
        'unit_id'         => 'required|integer',
        'professional_id' => 'required|integer',
        'service_id'      => 'required|integer',
        'client_name'     => 'required|min_length[3]|max_length[100]',
        'client_email'    => 'required|valid_email|max_length[100]',
        'client_phone'    => 'permit_empty|max_length[20]',
        'date'            => 'required|valid_date[Y-m-d]',
        'start_time'      => 'required',
        'end_time'        => 'required',
        'status'          => 'permit_empty|in_list[scheduled,confirmed,completed,cancelled,no_show]',
    ];

    protected $validationMessages = [
        'client_name' => [
            'required'   => 'O nome do cliente é obrigatório.',
            'min_length' => 'O nome deve ter pelo menos 3 caracteres.',
        ],
        'client_email' => [
            'required'    => 'O e-mail do cliente é obrigatório.',
            'valid_email' => 'Informe um e-mail válido.',
        ],
        'date' => [
            'required'   => 'A data do agendamento é obrigatória.',
            'valid_date' => 'Informe uma data válida.',
        ],
    ];

    // =========================================================================
    // MÉTODOS CUSTOMIZADOS
    // =========================================================================

    /**
     * Busca agendamento ou lança 404
     * 
     * @param int $id
     * @return Appointment
     * @throws \CodeIgniter\Exceptions\PageNotFoundException
     */
    public function findOrFail(int $id): Appointment
    {
        $appointment = $this->find($id);
        
        if ($appointment === null) {
            throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound(
                "Agendamento ID {$id} não encontrado."
            );
        }
        
        return $appointment;
    }

    /**
     * Retorna agendamentos de uma data específica
     * 
     * @param string $date Data no formato Y-m-d
     * @param int|null $professionalId
     * @return array
     */
    public function getByDate(string $date, ?int $professionalId = null): array
    {
        $builder = $this->where('date', $date);
        
        if ($professionalId) {
            $builder->where('professional_id', $professionalId);
        }
        
        return $builder->orderBy('start_time', 'ASC')->findAll();
    }

    /**
     * Retorna agendamentos de um período (para calendário)
     * 
     * @param string $startDate
     * @param string $endDate
     * @param int|null $professionalId
     * @param int|null $unitId
     * @return array
     */
    public function getByPeriod(string $startDate, string $endDate, ?int $professionalId = null, ?int $unitId = null): array
    {
        $builder = $this->where('date >=', $startDate)
                        ->where('date <=', $endDate);
        
        if ($professionalId) {
            $builder->where('professional_id', $professionalId);
        }
        
        if ($unitId) {
            $builder->where('unit_id', $unitId);
        }
        
        return $builder->orderBy('date', 'ASC')
                       ->orderBy('start_time', 'ASC')
                       ->findAll();
    }

    /**
     * Retorna agendamentos de hoje
     * 
     * @param int|null $professionalId
     * @return array
     */
    public function getToday(?int $professionalId = null): array
    {
        return $this->getByDate(date('Y-m-d'), $professionalId);
    }

    /**
     * Retorna próximos agendamentos
     * 
     * @param int $limit
     * @param int|null $professionalId
     * @return array
     */
    public function getUpcoming(int $limit = 10, ?int $professionalId = null): array
    {
        $builder = $this->where('date >=', date('Y-m-d'))
                        ->whereIn('status', ['scheduled', 'confirmed']);
        
        if ($professionalId) {
            $builder->where('professional_id', $professionalId);
        }
        
        return $builder->orderBy('date', 'ASC')
                       ->orderBy('start_time', 'ASC')
                       ->limit($limit)
                       ->findAll();
    }

    /**
     * Retorna agendamentos para o calendário (formato FullCalendar)
     * 
     * @param string $startDate
     * @param string $endDate
     * @param int|null $professionalId
     * @param int|null $unitId
     * @return array
     */
    public function getForCalendar(string $startDate, string $endDate, ?int $professionalId = null, ?int $unitId = null): array
    {
        $appointments = $this->getByPeriod($startDate, $endDate, $professionalId, $unitId);
        
        $events = [];
        foreach ($appointments as $appointment) {
            $events[] = $appointment->toCalendarEvent();
        }
        
        return $events;
    }

    /**
     * Verifica se há conflito de horário
     * 
     * @param int $professionalId
     * @param string $date
     * @param string $startTime
     * @param string $endTime
     * @param int|null $excludeId ID a ser excluído (para edição)
     * @return bool true se houver conflito
     */
    public function hasConflict(int $professionalId, string $date, string $startTime, string $endTime, ?int $excludeId = null): bool
    {
        $builder = $this->where('professional_id', $professionalId)
                        ->where('date', $date)
                        ->whereNotIn('status', ['cancelled'])
                        ->groupStart()
                            // Novo agendamento começa durante outro existente
                            ->where('start_time <=', $startTime)
                            ->where('end_time >', $startTime)
                        ->groupEnd()
                        ->orGroupStart()
                            // Novo agendamento termina durante outro existente
                            ->where('start_time <', $endTime)
                            ->where('end_time >=', $endTime)
                        ->groupEnd()
                        ->orGroupStart()
                            // Novo agendamento engloba outro existente
                            ->where('start_time >=', $startTime)
                            ->where('end_time <=', $endTime)
                        ->groupEnd();
        
        if ($excludeId) {
            $builder->where('id !=', $excludeId);
        }
        
        return $builder->countAllResults() > 0;
    }

    /**
     * Conta agendamentos por status
     * 
     * @param string|null $date Data específica ou null para todos
     * @return array
     */
    public function countByStatus(?string $date = null): array
    {
        $statuses = ['scheduled', 'confirmed', 'completed', 'cancelled', 'no_show'];
        $counts = [];
        
        foreach ($statuses as $status) {
            $builder = $this->where('status', $status);
            
            if ($date) {
                $builder->where('date', $date);
            }
            
            $counts[$status] = $builder->countAllResults(false);
        }
        
        $counts['total'] = array_sum($counts);
        
        return $counts;
    }

    /**
     * Retorna agendamentos com dados relacionados (JOIN)
     * 
     * @param int|null $limit
     * @return array
     */
    public function getWithRelations(?int $limit = null): array
    {
        $builder = $this->select('appointments.*, 
                                  units.name as unit_name,
                                  professionals.name as professional_name,
                                  services.name as service_name')
                        ->join('units', 'units.id = appointments.unit_id', 'left')
                        ->join('professionals', 'professionals.id = appointments.professional_id', 'left')
                        ->join('services', 'services.id = appointments.service_id', 'left')
                        ->orderBy('appointments.date', 'DESC')
                        ->orderBy('appointments.start_time', 'DESC');
        
        if ($limit) {
            $builder->limit($limit);
        }
        
        return $builder->findAll();
    }

    /**
     * Atualiza o status de um agendamento
     * 
     * @param int $id
     * @param string $status
     * @return bool
     */
    public function updateStatus(int $id, string $status): bool
    {
        return $this->update($id, ['status' => $status]);
    }

    /**
     * Retorna horários disponíveis para um profissional em uma data
     * 
     * @param int $professionalId
     * @param string $date
     * @param int $duration Duração em minutos
     * @param string $startTime Início do expediente
     * @param string $endTime Fim do expediente
     * @return array
     */
    public function getAvailableSlots(int $professionalId, string $date, int $duration, string $startTime = '08:00', string $endTime = '18:00'): array
    {
        // Busca agendamentos existentes do dia
        $existing = $this->where('professional_id', $professionalId)
                         ->where('date', $date)
                         ->whereNotIn('status', ['cancelled'])
                         ->orderBy('start_time', 'ASC')
                         ->findAll();
        
        // Gera todos os slots possíveis
        $slots = [];
        $current = strtotime($startTime);
        $end = strtotime($endTime);
        $durationSeconds = $duration * 60;
        
        while ($current + $durationSeconds <= $end) {
            $slotStart = date('H:i', $current);
            $slotEnd = date('H:i', $current + $durationSeconds);
            
            // Verifica se o slot está livre
            $isFree = true;
            foreach ($existing as $appointment) {
                $apptStart = strtotime($appointment->start_time);
                $apptEnd = strtotime($appointment->end_time);
                
                if (($current >= $apptStart && $current < $apptEnd) ||
                    ($current + $durationSeconds > $apptStart && $current + $durationSeconds <= $apptEnd)) {
                    $isFree = false;
                    break;
                }
            }
            
            if ($isFree) {
                $slots[] = [
                    'start' => $slotStart,
                    'end'   => $slotEnd,
                    'label' => "{$slotStart} - {$slotEnd}",
                ];
            }
            
            $current += $durationSeconds;
        }
        
        return $slots;
    }
}
