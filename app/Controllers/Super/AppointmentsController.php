<?php

namespace App\Controllers\Super;

use App\Controllers\BaseController;
use App\Models\AppointmentModel;
use App\Models\ProfessionalModel;
use App\Models\ServiceModel;
use App\Models\UnitModel;
use App\Libraries\AppointmentService;

/**
 * AppointmentsController - CRUD de Agendamentos (Agenda)
 * 
 * =========================================================================
 * ROTAS MAPEADAS
 * =========================================================================
 * 
 * GET    /super/appointments              → index()    Lista/Calendário
 * GET    /super/appointments/new          → new()      Formulário de criação
 * POST   /super/appointments              → create()   Processa criação
 * GET    /super/appointments/(:num)       → show()     Exibe detalhes
 * GET    /super/appointments/(:num)/edit  → edit()     Formulário de edição
 * PUT    /super/appointments/(:num)       → update()   Processa atualização
 * GET    /super/appointments/(:num)/status→ status()   Altera status
 * DELETE /super/appointments/(:num)       → delete()   Processa exclusão
 * GET    /super/appointments/calendar     → calendar() Dados para calendário (JSON)
 * GET    /super/appointments/slots        → slots()    Horários disponíveis (JSON)
 * 
 * @package    App\Controllers\Super
 */
class AppointmentsController extends BaseController
{
    protected AppointmentModel $appointmentModel;
    protected AppointmentService $appointmentService;

    /**
     * Construtor - Inicializa dependências
     */
    public function __construct()
    {
        $this->appointmentModel = model(AppointmentModel::class);
        $this->appointmentService = new AppointmentService();
    }

    /**
     * Lista agendamentos com calendário
     * 
     * GET /super/appointments
     */
    public function index(): string
    {
        $appointments = $this->appointmentModel->getWithRelations(100);
        $stats = $this->appointmentService->getStats();

        $data = [
            'title'        => 'Agenda',
            'pageHeading'  => 'Gerenciar Agendamentos',
            'tableHtml'    => $this->appointmentService->renderAppointments($appointments),
            'stats'        => $stats,
        ];

        return view('Back/Appointments/index', $data);
    }

    /**
     * Exibe formulário de criação
     * 
     * GET /super/appointments/new
     */
    public function new(): string
    {
        $unitModel = model(UnitModel::class);
        $professionalModel = model(ProfessionalModel::class);
        $serviceModel = model(ServiceModel::class);

        $data = [
            'title'         => 'Novo Agendamento',
            'pageHeading'   => 'Cadastrar Novo Agendamento',
            'units'         => $unitModel->getForDropdown(),
            'professionals' => $professionalModel->getForDropdownDetailed(),
            'services'      => $serviceModel->getForDropdownDetailed(),
            'statusOptions' => $this->appointmentService->renderStatusDropdown('scheduled'),
        ];

        return view('Back/Appointments/form', $data);
    }

    /**
     * Processa criação de novo agendamento
     * 
     * POST /super/appointments
     */
    public function create()
    {
        $data = [
            'unit_id'         => $this->request->getPost('unit_id'),
            'professional_id' => $this->request->getPost('professional_id'),
            'service_id'      => $this->request->getPost('service_id'),
            'client_name'     => $this->request->getPost('client_name'),
            'client_email'    => $this->request->getPost('client_email'),
            'client_phone'    => $this->request->getPost('client_phone'),
            'date'            => $this->request->getPost('date'),
            'start_time'      => $this->request->getPost('start_time'),
            'end_time'        => $this->request->getPost('end_time'),
            'status'          => $this->request->getPost('status') ?? 'scheduled',
            'notes'           => $this->request->getPost('notes'),
        ];

        // Verifica conflito de horário
        if ($this->appointmentModel->hasConflict(
            $data['professional_id'],
            $data['date'],
            $data['start_time'],
            $data['end_time']
        )) {
            return redirect()->back()
                           ->withInput()
                           ->with('danger', 'Conflito de horário! O profissional já possui um agendamento neste horário.');
        }

        $insertId = $this->appointmentModel->insert($data);

        if ($insertId === false) {
            return redirect()->back()
                           ->withInput()
                           ->with('errorsValidation', $this->appointmentModel->errors())
                           ->with('danger', 'Verifique os erros de validação.');
        }

        return redirect()->to(route_to('super.appointments.show', $insertId))
                       ->with('success', 'Agendamento cadastrado com sucesso!');
    }

    /**
     * Exibe detalhes de um agendamento
     * 
     * GET /super/appointments/(:num)
     */
    public function show(int $id): string
    {
        $appointment = $this->appointmentModel->findOrFail($id);

        $data = [
            'title'       => "Agendamento #{$appointment->id}",
            'pageHeading' => 'Detalhes do Agendamento',
            'appointment' => $appointment,
        ];

        return view('Back/Appointments/show', $data);
    }

    /**
     * Exibe formulário de edição
     * 
     * GET /super/appointments/(:num)/edit
     */
    public function edit(int $id): string
    {
        $appointment = $this->appointmentModel->findOrFail($id);

        if (! $appointment->canEdit()) {
            return redirect()->to(route_to('super.appointments'))
                           ->with('warning', 'Este agendamento não pode ser editado.');
        }

        $unitModel = model(UnitModel::class);
        $professionalModel = model(ProfessionalModel::class);
        $serviceModel = model(ServiceModel::class);

        $data = [
            'title'         => 'Editar Agendamento',
            'pageHeading'   => "Editar Agendamento #{$appointment->id}",
            'appointment'   => $appointment,
            'units'         => $unitModel->getForDropdown(),
            'professionals' => $professionalModel->getForDropdownDetailed(),
            'services'      => $serviceModel->getForDropdownDetailed(),
            'statusOptions' => $this->appointmentService->renderStatusDropdown($appointment->status),
        ];

        return view('Back/Appointments/edit', $data);
    }

    /**
     * Processa atualização de agendamento
     * 
     * PUT /super/appointments/(:num)
     */
    public function update(int $id)
    {
        $appointment = $this->appointmentModel->findOrFail($id);

        if (! $appointment->canEdit()) {
            return redirect()->to(route_to('super.appointments'))
                           ->with('warning', 'Este agendamento não pode ser editado.');
        }

        $data = [
            'unit_id'         => $this->request->getPost('unit_id'),
            'professional_id' => $this->request->getPost('professional_id'),
            'service_id'      => $this->request->getPost('service_id'),
            'client_name'     => $this->request->getPost('client_name'),
            'client_email'    => $this->request->getPost('client_email'),
            'client_phone'    => $this->request->getPost('client_phone'),
            'date'            => $this->request->getPost('date'),
            'start_time'      => $this->request->getPost('start_time'),
            'end_time'        => $this->request->getPost('end_time'),
            'status'          => $this->request->getPost('status'),
            'notes'           => $this->request->getPost('notes'),
        ];

        // Verifica conflito de horário (excluindo o próprio agendamento)
        if ($this->appointmentModel->hasConflict(
            $data['professional_id'],
            $data['date'],
            $data['start_time'],
            $data['end_time'],
            $id
        )) {
            return redirect()->back()
                           ->withInput()
                           ->with('danger', 'Conflito de horário! O profissional já possui um agendamento neste horário.');
        }

        $saved = $this->appointmentModel->update($id, $data);

        if ($saved === false) {
            return redirect()->back()
                           ->withInput()
                           ->with('errorsValidation', $this->appointmentModel->errors())
                           ->with('danger', 'Verifique os erros de validação.');
        }

        return redirect()->to(route_to('super.appointments'))
                       ->with('success', 'Agendamento atualizado com sucesso!');
    }

    /**
     * Altera status do agendamento
     * 
     * GET /super/appointments/(:num)/status?status=confirmed
     */
    public function status(int $id)
    {
        $appointment = $this->appointmentModel->findOrFail($id);
        $newStatus = $this->request->getGet('status');

        $validStatuses = ['scheduled', 'confirmed', 'completed', 'cancelled', 'no_show'];
        
        if (! in_array($newStatus, $validStatuses)) {
            return redirect()->back()
                           ->with('danger', 'Status inválido.');
        }

        $this->appointmentModel->updateStatus($id, $newStatus);

        $statusLabels = [
            'scheduled'  => 'reagendado',
            'confirmed'  => 'confirmado',
            'completed'  => 'concluído',
            'cancelled'  => 'cancelado',
            'no_show'    => 'marcado como não compareceu',
        ];

        return redirect()->back()
                       ->with('success', "Agendamento {$statusLabels[$newStatus]} com sucesso!");
    }

    /**
     * Processa exclusão de agendamento
     * 
     * DELETE /super/appointments/(:num)
     */
    public function delete(int $id)
    {
        $appointment = $this->appointmentModel->findOrFail($id);

        $this->appointmentModel->delete($id);

        return redirect()->to(route_to('super.appointments'))
                       ->with('success', 'Agendamento excluído com sucesso!');
    }

    /**
     * Retorna eventos para FullCalendar (JSON)
     * 
     * GET /super/appointments/calendar
     */
    public function calendar()
    {
        $start = $this->request->getGet('start');
        $end = $this->request->getGet('end');
        $professionalId = $this->request->getGet('professional_id');
        $unitId = $this->request->getGet('unit_id');

        $events = $this->appointmentModel->getForCalendar(
            $start ?? date('Y-m-01'),
            $end ?? date('Y-m-t'),
            $professionalId ? (int) $professionalId : null,
            $unitId ? (int) $unitId : null
        );

        return $this->response->setJSON($events);
    }

    /**
     * Retorna horários disponíveis (JSON)
     * 
     * GET /super/appointments/slots
     */
    public function slots()
    {
        $professionalId = $this->request->getGet('professional_id');
        $date = $this->request->getGet('date');
        $duration = $this->request->getGet('duration') ?? 30;

        if (! $professionalId || ! $date) {
            return $this->response->setJSON(['error' => 'Parâmetros inválidos']);
        }

        $slots = $this->appointmentModel->getAvailableSlots(
            (int) $professionalId,
            $date,
            (int) $duration
        );

        return $this->response->setJSON($slots);
    }
}
