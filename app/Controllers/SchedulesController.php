<?php

namespace App\Controllers;

use App\Models\UnitModel;
use App\Models\ServiceModel;
use App\Models\ProfessionalModel;
use App\Models\AppointmentModel;
use App\Models\ClientModel;
use CodeIgniter\I18n\Time;

/**
 * SchedulesController - Controller para área pública de agendamentos
 * 
 * =========================================================================
 * PROPÓSITO
 * =========================================================================
 * 
 * Gerencia o fluxo de agendamento na área pública:
 * - Seleção de unidade, serviço e profissional
 * - Exibição de calendário e horários disponíveis
 * - Criação de agendamentos
 * - Listagem e cancelamento de agendamentos do usuário
 * 
 * @package    App\Controllers
 * @author     Sistema de Agendamentos
 */
class SchedulesController extends BaseController
{
    protected UnitModel $unitModel;
    protected ServiceModel $serviceModel;
    protected ProfessionalModel $professionalModel;
    protected AppointmentModel $appointmentModel;
    protected ClientModel $clientModel;

    /**
     * Construtor - Inicializa dependências
     */
    public function __construct()
    {
        $this->unitModel = model('UnitModel');
        $this->serviceModel = model('ServiceModel');
        $this->professionalModel = model('ProfessionalModel');
        $this->appointmentModel = model('AppointmentModel');
        $this->clientModel = model('ClientModel');
    }

    // =========================================================================
    // HOME E PÁGINA DE AGENDAMENTO
    // =========================================================================

    /**
     * Página inicial pública
     */
    public function home()
    {
        $units = $this->unitModel->getActiveUnits();
        
        return view('Front/home', [
            'title' => 'Início',
            'units' => $units,
        ]);
    }

    /**
     * Página de agendamento (wizard)
     */
    public function schedule()
    {
        $units = $this->unitModel->getActiveUnits();
        $preSelectedUnit = $this->request->getGet('unit');
        
        return view('Front/schedule', [
            'title' => 'Agendar Horário',
            'units' => $units,
            'preSelectedUnit' => $preSelectedUnit,
        ]);
    }

    // =========================================================================
    // API ENDPOINTS - AJAX
    // =========================================================================

    /**
     * Retorna serviços de uma unidade (JSON)
     */
    public function getServices()
    {
        $unitId = $this->request->getGet('unit_id');
        
        if (empty($unitId)) {
            return $this->response->setJSON(['error' => 'Unidade não informada']);
        }
        
        $unit = $this->unitModel->find($unitId);
        if (!$unit || !$unit->isActive()) {
            return $this->response->setJSON(['error' => 'Unidade não encontrada']);
        }
        
        // Buscar serviços ativos da unidade
        $services = $this->serviceModel
            ->where('unit_id', $unitId)
            ->where('active', 1)
            ->orderBy('name', 'ASC')
            ->findAll();
        
        $result = [];
        foreach ($services as $service) {
            $result[] = [
                'id' => $service->id,
                'name' => $service->name,
                'description' => $service->description,
                'duration' => $service->duration,
                'price' => $service->price,
                'price_formatted' => $service->priceFormatted(),
                'duration_formatted' => $service->durationFormatted(),
            ];
        }
        
        return $this->response->setJSON($result);
    }

    /**
     * Retorna profissionais disponíveis para um serviço (JSON)
     */
    public function getProfessionals()
    {
        $unitId = $this->request->getGet('unit_id');
        $serviceId = $this->request->getGet('service_id');
        
        if (empty($unitId) || empty($serviceId)) {
            return $this->response->setJSON(['error' => 'Dados incompletos']);
        }
        
        // Buscar profissionais ativos da unidade que atendem o serviço
        $professionals = $this->professionalModel
            ->where('unit_id', $unitId)
            ->where('active', 1)
            ->findAll();
        
        $result = [];
        foreach ($professionals as $professional) {
            // Verifica se o profissional atende o serviço
            if ($professional->hasService($serviceId)) {
                $result[] = [
                    'id' => $professional->id,
                    'name' => $professional->name,
                    'specialty' => $professional->specialty,
                    'avatar' => $professional->avatarUrl(60),
                ];
            }
        }
        
        return $this->response->setJSON($result);
    }

    /**
     * Retorna os meses disponíveis para agendamento
     */
    public function getMonths()
    {
        $months = [];
        $currentDate = Time::now();
        
        // Disponibilizar 3 meses a partir do atual
        for ($i = 0; $i < 3; $i++) {
            $date = $currentDate->addMonths($i);
            $months[] = [
                'year' => $date->getYear(),
                'month' => $date->getMonth(),
                'name' => $this->getMonthName($date->getMonth()),
                'label' => $this->getMonthName($date->getMonth()) . '/' . $date->getYear(),
            ];
        }
        
        return $this->response->setJSON($months);
    }

    /**
     * Retorna o calendário de um mês específico (JSON)
     */
    public function getCalendar()
    {
        $year = (int) $this->request->getGet('year') ?: date('Y');
        $month = (int) $this->request->getGet('month') ?: date('m');
        $professionalId = $this->request->getGet('professional_id');
        
        $calendar = $this->buildCalendar($year, $month, $professionalId);
        
        return $this->response->setJSON($calendar);
    }

    /**
     * Retorna horários disponíveis para uma data (JSON)
     */
    public function getAvailableHours()
    {
        $date = $this->request->getGet('date');
        $professionalId = $this->request->getGet('professional_id');
        $serviceId = $this->request->getGet('service_id');
        $unitId = $this->request->getGet('unit_id');
        
        if (empty($date) || empty($professionalId) || empty($serviceId) || empty($unitId)) {
            return $this->response->setJSON(['error' => 'Dados incompletos']);
        }
        
        // Validar data
        $selectedDate = Time::parse($date);
        $today = Time::today();
        
        if ($selectedDate < $today) {
            return $this->response->setJSON(['error' => 'Data inválida']);
        }
        
        // Verificar se é fim de semana
        if ($this->isWeekend($selectedDate)) {
            return $this->response->setJSON(['hours' => [], 'message' => 'Não há atendimento em finais de semana']);
        }
        
        // Buscar unidade para horários de funcionamento
        $unit = $this->unitModel->find($unitId);
        if (!$unit) {
            return $this->response->setJSON(['error' => 'Unidade não encontrada']);
        }
        
        // Buscar serviço para duração
        $service = $this->serviceModel->find($serviceId);
        if (!$service) {
            return $this->response->setJSON(['error' => 'Serviço não encontrado']);
        }
        
        // Gerar horários disponíveis
        $hours = $this->generateAvailableHours($unit, $service, $professionalId, $date);
        
        return $this->response->setJSON(['hours' => $hours]);
    }

    // =========================================================================
    // CRIAÇÃO DE AGENDAMENTO
    // =========================================================================

    /**
     * Processa a criação do agendamento
     */
    public function createSchedule()
    {
        // Validar se usuário está logado
        $user = session()->get('user');
        
        // Obter dados do request
        $data = $this->request->getJSON(true) ?? $this->request->getPost();
        
        // Validação dos campos
        $validation = \Config\Services::validation();
        
        $rules = [
            'unit_id'        => 'required|integer',
            'service_id'     => 'required|integer',
            'professional_id'=> 'required|integer',
            'date'           => 'required|valid_date',
            'time'           => 'required',
            'client_name'    => 'required|min_length[3]|max_length[150]',
            'client_email'   => 'required|valid_email',
            'client_phone'   => 'permit_empty|max_length[20]',
        ];
        
        // Se não estiver logado, nome e email são obrigatórios
        if (!$user) {
            $rules['client_name'] = 'required|min_length[3]|max_length[150]';
            $rules['client_email'] = 'required|valid_email';
        }
        
        if (!$this->validate($rules)) {
            return $this->response->setStatusCode(422)->setJSON([
                'success' => false,
                'errors' => $validation->getErrors(),
            ]);
        }
        
        // Verificar se o horário ainda está disponível
        $date = $data['date'];
        $time = $data['time'];
        $professionalId = $data['professional_id'];
        $serviceId = $data['service_id'];
        $unitId = $data['unit_id'];
        
        $service = $this->serviceModel->find($serviceId);
        if (!$service) {
            return $this->response->setStatusCode(400)->setJSON([
                'success' => false,
                'message' => 'Serviço não encontrado',
            ]);
        }
        
        // Calcular horário de término
        $startTime = Time::parse($date . ' ' . $time);
        $endTime = $startTime->addMinutes($service->duration ?? 60);
        
        // Verificar conflito
        $hasConflict = $this->appointmentModel->hasConflict(
            $professionalId,
            $date,
            $time,
            $endTime->format('H:i:s')
        );
        
        if ($hasConflict) {
            return $this->response->setStatusCode(409)->setJSON([
                'success' => false,
                'message' => 'Este horário não está mais disponível. Por favor, escolha outro horário.',
            ]);
        }
        
        // Dados do cliente
        $clientName = $user ? $user['name'] : $data['client_name'];
        $clientEmail = $user ? $user['email'] : $data['client_email'];
        $clientPhone = $data['client_phone'] ?? null;
        
        // Criar ou atualizar cliente
        $this->clientModel->findOrCreate([
            'name'  => $clientName,
            'email' => $clientEmail,
            'phone' => $clientPhone,
        ]);
        
        // Criar agendamento
        $appointmentData = [
            'unit_id'         => $unitId,
            'professional_id' => $professionalId,
            'service_id'      => $serviceId,
            'client_name'     => $clientName,
            'client_email'    => $clientEmail,
            'client_phone'    => $clientPhone,
            'date'            => $date,
            'start_time'      => $time,
            'end_time'        => $endTime->format('H:i:s'),
            'status'          => 'scheduled',
            'notes'           => $data['notes'] ?? null,
        ];
        
        $appointmentId = $this->appointmentModel->insert($appointmentData);
        
        if (!$appointmentId) {
            return $this->response->setStatusCode(500)->setJSON([
                'success' => false,
                'message' => 'Erro ao criar agendamento. Tente novamente.',
                'errors' => $this->appointmentModel->errors(),
            ]);
        }
        
        // TODO: Enviar email de confirmação
        // $this->sendConfirmationEmail($appointmentId);
        
        return $this->response->setJSON([
            'success' => true,
            'message' => 'Agendamento realizado com sucesso!',
            'appointment_id' => $appointmentId,
        ]);
    }

    // =========================================================================
    // MEUS AGENDAMENTOS
    // =========================================================================

    /**
     * Lista os agendamentos do usuário logado
     */
    public function mySchedules()
    {
        $user = session()->get('user');
        
        if (!$user) {
            return redirect()->to(base_url('login'))
                           ->with('warning', 'Faça login para ver seus agendamentos.');
        }
        
        // Buscar agendamentos do usuário por email
        $appointments = $this->appointmentModel
            ->where('client_email', $user['email'])
            ->orderBy('date', 'DESC')
            ->orderBy('start_time', 'DESC')
            ->findAll();
        
        // Agrupar por status
        $upcoming = [];
        $past = [];
        $cancelled = [];
        
        $today = Time::today();
        
        foreach ($appointments as $appointment) {
            $appointmentDate = Time::parse($appointment->date);
            
            if ($appointment->status === 'cancelled') {
                $cancelled[] = $appointment;
            } elseif ($appointmentDate >= $today && in_array($appointment->status, ['scheduled', 'confirmed'])) {
                $upcoming[] = $appointment;
            } else {
                $past[] = $appointment;
            }
        }
        
        return view('Front/my-schedules', [
            'title' => 'Meus Agendamentos',
            'upcoming' => $upcoming,
            'past' => $past,
            'cancelled' => $cancelled,
        ]);
    }

    /**
     * Cancela um agendamento
     */
    public function cancelSchedule($id)
    {
        $user = session()->get('user');
        
        if (!$user) {
            return $this->response->setStatusCode(401)->setJSON([
                'success' => false,
                'message' => 'Não autorizado',
            ]);
        }
        
        $appointment = $this->appointmentModel->find($id);
        
        if (!$appointment) {
            return $this->response->setStatusCode(404)->setJSON([
                'success' => false,
                'message' => 'Agendamento não encontrado',
            ]);
        }
        
        // Verificar se o agendamento pertence ao usuário
        if ($appointment->client_email !== $user['email']) {
            return $this->response->setStatusCode(403)->setJSON([
                'success' => false,
                'message' => 'Você não tem permissão para cancelar este agendamento',
            ]);
        }
        
        // Verificar se pode ser cancelado
        if (!$appointment->canCancel()) {
            return $this->response->setStatusCode(400)->setJSON([
                'success' => false,
                'message' => 'Este agendamento não pode mais ser cancelado',
            ]);
        }
        
        // Atualizar status
        $this->appointmentModel->update($id, ['status' => 'cancelled']);
        
        return $this->response->setJSON([
            'success' => true,
            'message' => 'Agendamento cancelado com sucesso',
        ]);
    }

    // =========================================================================
    // MÉTODOS AUXILIARES
    // =========================================================================

    /**
     * Constrói o calendário de um mês
     */
    protected function buildCalendar(int $year, int $month, ?int $professionalId = null): array
    {
        $firstDay = Time::create($year, $month, 1);
        // Obter número de dias no mês
        $daysInMonth = (int) $firstDay->format('t');
        
        // Dia da semana do primeiro dia (0 = domingo)
        $firstDayOfWeek = (int) $firstDay->format('w');
        
        $today = Time::today();
        $calendar = [
            'year' => $year,
            'month' => $month,
            'month_name' => $this->getMonthName($month),
            'days_in_month' => $daysInMonth,
            'first_day_of_week' => $firstDayOfWeek,
            'days' => [],
        ];
        
        // Preencher dias vazios no início
        for ($i = 0; $i < $firstDayOfWeek; $i++) {
            $calendar['days'][] = [
                'day' => null,
                'date' => null,
                'is_empty' => true,
            ];
        }
        
        // Preencher os dias do mês
        for ($day = 1; $day <= $daysInMonth; $day++) {
            $date = Time::create($year, $month, $day);
            $dateStr = $date->format('Y-m-d');
            
            $isToday = $date->format('Y-m-d') === $today->format('Y-m-d');
            $isPast = $date < $today;
            $isWeekend = $this->isWeekend($date);
            
            // Verificar disponibilidade (simplificado)
            $isAvailable = !$isPast && !$isWeekend;
            
            $calendar['days'][] = [
                'day' => $day,
                'date' => $dateStr,
                'is_empty' => false,
                'is_today' => $isToday,
                'is_past' => $isPast,
                'is_weekend' => $isWeekend,
                'is_available' => $isAvailable,
            ];
        }
        
        return $calendar;
    }

    /**
     * Gera horários disponíveis para uma data
     */
    protected function generateAvailableHours($unit, $service, int $professionalId, string $date): array
    {
        // Horários padrão de funcionamento (8h às 18h)
        $startHour = 8;
        $endHour = 18;
        $interval = $service->duration ?? 60; // minutos
        
        $hours = [];
        $now = Time::now();
        $selectedDate = Time::parse($date);
        $isToday = $selectedDate->format('Y-m-d') === $now->format('Y-m-d');
        
        // Buscar agendamentos existentes do profissional nesta data
        $existingAppointments = $this->appointmentModel
            ->where('professional_id', $professionalId)
            ->where('date', $date)
            ->whereNotIn('status', ['cancelled'])
            ->findAll();
        
        // Criar array de horários ocupados
        $busySlots = [];
        foreach ($existingAppointments as $apt) {
            $busySlots[] = [
                'start' => $apt->start_time,
                'end' => $apt->end_time,
            ];
        }
        
        // Gerar slots
        $current = Time::parse($date . ' ' . sprintf('%02d:00:00', $startHour));
        $end = Time::parse($date . ' ' . sprintf('%02d:00:00', $endHour));
        
        while ($current < $end) {
            $timeStr = $current->format('H:i');
            $slotEnd = $current->addMinutes($interval);
            
            // Verificar se já passou (para hoje)
            $isPast = $isToday && $current <= $now;
            
            // Verificar se está ocupado
            $isBusy = false;
            foreach ($busySlots as $busy) {
                $busyStart = substr($busy['start'], 0, 5);
                $busyEnd = substr($busy['end'], 0, 5);
                
                if ($timeStr >= $busyStart && $timeStr < $busyEnd) {
                    $isBusy = true;
                    break;
                }
            }
            
            // Verificar se o slot cabe antes do fim do expediente
            $fitsInSchedule = $slotEnd <= $end;
            
            $hours[] = [
                'time' => $timeStr,
                'formatted' => $timeStr,
                'available' => !$isPast && !$isBusy && $fitsInSchedule,
            ];
            
            $current = $current->addMinutes($interval);
        }
        
        return $hours;
    }

    /**
     * Verifica se é fim de semana
     */
    protected function isWeekend(Time $date): bool
    {
        $dayOfWeek = (int) $date->format('w');
        return $dayOfWeek === 0 || $dayOfWeek === 6; // Domingo ou Sábado
    }

    /**
     * Retorna nome do mês em português
     */
    protected function getMonthName(int $month): string
    {
        $months = [
            1 => 'Janeiro', 2 => 'Fevereiro', 3 => 'Março',
            4 => 'Abril', 5 => 'Maio', 6 => 'Junho',
            7 => 'Julho', 8 => 'Agosto', 9 => 'Setembro',
            10 => 'Outubro', 11 => 'Novembro', 12 => 'Dezembro',
        ];
        
        return $months[$month] ?? '';
    }
}
